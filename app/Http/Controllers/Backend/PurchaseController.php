<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchaseService) {}

    public function index(Request $request): View
    {
        $query = Purchase::with(['product', 'supplier'])->orderByDesc('id');

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', (int) $productId);
        }

        $purchases = $query->paginate(20)->withQueryString();
        $products  = Product::active()->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('backend.purchases.index', compact('purchases', 'products', 'suppliers'));
    }

    public function create(): View
    {
        $products  = Product::active()->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('backend.purchases.create', compact('products', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'   => 'required|integer|exists:products,id',
            'supplier_id'  => 'nullable|integer|exists:suppliers,id',
            'quantity'     => 'required|numeric|min:0.01',
            'unit_cost'    => 'required|numeric|min:0',
            'note'         => 'nullable|string',
            'purchased_at' => 'nullable|date',
        ]);

        try {
            $this->purchaseService->create($data);
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('backend.purchases.index')->with('success', 'Xarid kiritildi.');
    }

    public function edit(Purchase $purchase): View
    {
        $products  = Product::active()->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('backend.purchases.edit', compact('purchase', 'products', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->validate([
            'product_id'   => 'required|integer|exists:products,id',
            'supplier_id'  => 'nullable|integer|exists:suppliers,id',
            'quantity'     => 'required|numeric|min:0.01',
            'unit_cost'    => 'required|numeric|min:0',
            'note'         => 'nullable|string',
            'purchased_at' => 'nullable|date',
        ]);

        try {
            $this->purchaseService->update($purchase, $data);
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('backend.purchases.index')->with('success', 'Xarid yangilandi.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->delete($purchase);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('backend.purchases.index')->with('success', 'Xarid o\'chirildi.');
    }
}
