<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%")
                    ->orWhere('barcode', 'ilike', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', (int) $categoryId);
        }

        if ($request->input('low_stock')) {
            $query->lowStock();
        }

        $products   = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        return view('backend.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('backend.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $shopId = session('shop_id') ?? auth()->user()->shop_id;

        $data = $request->validate([
            'category_id'    => 'required|integer|exists:categories,id',
            'name'           => 'required|string|max:255',
            'sku'            => ['nullable', 'string', 'max:64',
                Rule::unique('products')->where('shop_id', $shopId)],
            'barcode'        => ['nullable', 'string', 'max:64',
                Rule::unique('products')->where('shop_id', $shopId)],
            'cost_price'     => 'required|numeric|min:0',
            'sale_price'     => 'required|numeric|min:0',
            'discount'       => 'integer|min:0|max:100',
            'stock_quantity' => 'numeric|min:0',
            'is_active'      => 'boolean',
        ]);
        $data['shop_id']  = $shopId;
        $data['sku']      = $data['sku']     ?: null;
        $data['barcode']  = $data['barcode'] ?: null;
        $data['discount'] = $data['discount'] ?? 0;

        Product::create($data);

        return redirect()->route('backend.products.index')->with('success', 'Mahsulot qo\'shildi.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('backend.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $shopId = session('shop_id') ?? auth()->user()->shop_id;

        $data = $request->validate([
            'category_id'    => 'required|integer|exists:categories,id',
            'name'           => 'required|string|max:255',
            'sku'            => ['nullable', 'string', 'max:64',
                Rule::unique('products')->where('shop_id', $shopId)->ignore($product->id)],
            'barcode'        => ['nullable', 'string', 'max:64',
                Rule::unique('products')->where('shop_id', $shopId)->ignore($product->id)],
            'cost_price'     => 'required|numeric|min:0',
            'sale_price'     => 'required|numeric|min:0',
            'discount'       => 'integer|min:0|max:100',
            'stock_quantity' => 'numeric|min:0',
            'is_active'      => 'boolean',
        ]);

        $data['sku']     = $data['sku']     ?: null;
        $data['barcode'] = $data['barcode'] ?: null;

        $product->update($data);

        return redirect()->route('backend.products.index')->with('success', 'Mahsulot yangilandi.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->saleItems()->exists()) {
            return back()->with('error', 'Bu mahsulot sotuv tarixida mavjud.');
        }

        $product->delete();
        return redirect()->route('backend.products.index')->with('success', 'Mahsulot o\'chirildi.');
    }
}
