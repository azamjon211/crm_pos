<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DebtPayment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DebtController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with(['customer', 'debtPayments'])
            ->where('payment_type', Sale::PAYMENT_DEBT)
            ->orderByDesc('id');

        if (!$request->boolean('show_cleared')) {
            $query->whereRaw(
                '(SELECT COALESCE(SUM(amount), 0) FROM debt_payments WHERE sale_id = sales.id) < total_amount'
            );
        }

        $debts = $query->paginate(20)->withQueryString();

        return view('backend.debts.index', compact('debts'));
    }

    public function show(Sale $sale): View
    {
        abort_if($sale->payment_type !== Sale::PAYMENT_DEBT, 404);

        $sale->load(['customer', 'saleItems.product', 'cashier', 'debtPayments']);

        return view('backend.debts.show', compact('sale'));
    }

    public function pay(Request $request, Sale $sale): RedirectResponse
    {
        abort_if($sale->payment_type !== Sale::PAYMENT_DEBT, 404);

        $sale->load('debtPayments');
        $remaining = $sale->debtBalance();

        if ($remaining <= 0) {
            return back()->with('error', 'Bu qarz allaqachon to\'liq to\'langan.');
        }

        $data = $request->validate([
            'amount'  => "required|numeric|min:0.01|max:{$remaining}",
            'note'    => 'nullable|string|max:500',
            'paid_at' => 'nullable|date',
        ]);

        DebtPayment::create([
            'shop_id' => $sale->shop_id,
            'sale_id' => $sale->id,
            'amount'  => $data['amount'],
            'note'    => $data['note'] ?? null,
            'paid_at' => $data['paid_at'] ?? now(),
        ]);

        return redirect()->route('backend.debts.show', $sale)
            ->with('success', 'To\'lov qabul qilindi.');
    }
}
