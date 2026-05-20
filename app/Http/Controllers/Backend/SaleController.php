<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function index(Request $request): View
    {
        $query = Sale::with(['cashier', 'customer'])->orderByDesc('id');

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($paymentType = $request->input('payment_type')) {
            $query->where('payment_type', $paymentType);
        }

        $sales        = $query->paginate(20)->withQueryString();
        $paymentTypes = Sale::PAYMENT_TYPES;

        return view('backend.sales.index', compact('sales', 'paymentTypes'));
    }

    public function show(Sale $sale): View
    {
        $sale->load(['saleItems.product', 'cashier', 'customer', 'saleReturns.product']);
        return view('backend.sales.show', compact('sale'));
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        if ($sale->created_at->diffInMinutes(now()) > 60) {
            return back()->with('error', 'Sotuv 1 soatdan oshgach o\'chirib bo\'lmaydi.');
        }

        foreach ($sale->saleItems as $item) {
            $item->product?->increaseStock((float) $item->quantity);
        }

        $sale->delete();
        return redirect()->route('backend.sales.index')->with('success', 'Sotuv o\'chirildi.');
    }

    public function returnForm(Sale $sale): View
    {
        $sale->load(['saleItems.product']);
        return view('backend.sales.return', compact('sale'));
    }

    public function processReturn(Request $request, Sale $sale): RedirectResponse
    {
        $data = $request->validate([
            'sale_item_id' => 'required|integer|exists:sale_items,id',
            'quantity'     => 'required|numeric|min:0.01',
            'return_type'  => 'required|in:refund,exchange',
            'reason'       => 'nullable|string|max:500',
        ]);

        try {
            $this->saleService->processReturn(
                $data['sale_item_id'],
                (float) $data['quantity'],
                $data['return_type'],
                $data['reason'] ?? null
            );
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('backend.sales.show', $sale)->with('success', 'Qaytarish bajarildi.');
    }
}
