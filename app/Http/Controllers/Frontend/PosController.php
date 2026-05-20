<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        private SaleService    $saleService,
        private ReceiptService $receiptService,
    ) {}

    public function index(Request $request): View
    {
        $cart       = $this->getCartNumber($request);
        $sessionKey = 'pos_cart_' . $cart;
        $categories = Category::active()->orderBy('name')->get();
        $customers  = Customer::active()->orderBy('name')->get(['id', 'name', 'phone']);
        $cartData   = session($sessionKey, $this->emptyCart());
        $lastSaleId = session('last_sale_id');

        return view('pos.index', compact('categories', 'customers', 'cart', 'cartData', 'lastSaleId'));
    }

    public function search(Request $request): JsonResponse
    {
        $q          = trim($request->input('q', ''));
        $categoryId = (int) $request->input('category_id', 0);
        $showAll    = filter_var($request->input('show_all', false), FILTER_VALIDATE_BOOLEAN);

        // "Barchasi" yoki kategoriya tanlanganda, lekin qidiruv bo'sh — barcha mahsulotlarni ko'rsat
        if ($q === '' && $categoryId === 0 && !$showAll) {
            return response()->json(['items' => []]);
        }

        $products = Product::active()
            ->when($categoryId > 0, fn($qry) => $qry->where('category_id', $categoryId))
            ->when($q !== '', fn($qry) => $qry->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            }))
            ->select('id', 'name', 'sale_price', 'discount', 'stock_quantity', 'barcode')
            ->orderBy('name')
            ->limit(100)
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'final_price'    => $p->getFinalPrice(),
                'stock_quantity' => (float) $p->stock_quantity,
                'barcode'        => $p->barcode,
            ]);

        return response()->json(['items' => $products]);
    }

    public function productsByCategory(Request $request): JsonResponse
    {
        return $this->search($request);
    }

    public function addToCart(Request $request): JsonResponse|RedirectResponse
    {
        $cart       = $this->getCartNumber($request);
        $sessionKey = 'pos_cart_' . $cart;
        $productId  = (int) $request->input('product_id', 0);

        if ($productId > 0) {
            $product  = Product::active()->findOrFail($productId);
            $cartData = session($sessionKey, $this->emptyCart());

            $found = false;
            foreach ($cartData['items'] as &$item) {
                if ((int) $item['product_id'] === $productId) {
                    $item['quantity'] = (float) $item['quantity'] + 1;
                    $found = true;
                    break;
                }
            }
            unset($item);

            if (! $found) {
                $cartData['items'][] = [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'unit_price' => $product->getFinalPrice(),
                    'quantity'   => 1.0,
                ];
            }

            session([$sessionKey => $cartData]);
        }

        if ($request->expectsJson()) {
            return response()->json(['cart' => session($sessionKey, $this->emptyCart())]);
        }

        return redirect()->route('pos.index', ['cart' => $cart]);
    }

    public function saveCart(Request $request): RedirectResponse
    {
        $cart       = $this->getCartNumber($request);
        $sessionKey = 'pos_cart_' . $cart;
        $cartData   = $this->buildCartData($request);
        session([$sessionKey => $cartData]);

        return redirect()->route('pos.index', ['cart' => $cart])
            ->with('success', 'Korzinka saqlandi.');
    }

    public function removeRow(Request $request): JsonResponse|RedirectResponse
    {
        $cart       = $this->getCartNumber($request);
        $sessionKey = 'pos_cart_' . $cart;
        $index      = (int) $request->input('deleteRow', -1);
        $cartData   = session($sessionKey, $this->emptyCart());

        if (isset($cartData['items'][$index])) {
            array_splice($cartData['items'], $index, 1);
            session([$sessionKey => $cartData]);
        }

        if ($request->expectsJson()) {
            return response()->json(['cart' => session($sessionKey, $this->emptyCart())]);
        }

        return redirect()->route('pos.index', ['cart' => $cart]);
    }

    public function complete(Request $request): RedirectResponse
    {
        //dd($request->all());
        $cart        = $this->getCartNumber($request);
        $sessionKey  = 'pos_cart_' . $cart;
        $cartData    = $this->buildCartData($request);
        $paymentType = $request->input('payment_type', Sale::PAYMENT_CASH);
        $customerId  = $request->input('customer_id') ? (int) $request->input('customer_id') : null;
        $cleanItems = array_filter($cartData['items'], function ($row) {
            return (int) ($row['product_id'] ?? 0) > 0
                && (float) ($row['quantity'] ?? 0) > 0;
        });

        if (empty($cleanItems)) {
            return redirect()->route('pos.index', ['cart' => $cart])
                ->with('error', 'Korzinkada mahsulot yo\'q.');
        }

        try {
            $sale = $this->saleService->createFromCart(
                array_values($cleanItems),
                $paymentType,
                $cartData['note'] ?? null,
                $customerId
            );

            session()->forget($sessionKey);
            session(['last_sale_id' => $sale->id]);

            // Check if any sold products are now low stock
            $soldProductIds = $sale->saleItems->pluck('product_id');
            $lowNow = Product::whereIn('id', $soldProductIds)->lowStock(5)->pluck('name');

            $redirect = redirect()->route('pos.index', ['cart' => $cart])
                ->with('success', 'Sotuv muvaffaqiyatli yakunlandi.');

            if ($lowNow->isNotEmpty()) {
                $redirect->with('low_stock_warning', $lowNow->all());
            }

            return $redirect;

        } catch (\DomainException $e) {
            return redirect()->route('pos.index', ['cart' => $cart])
                ->with('error', $e->getMessage());
        }
    }

    public function receipt(int $id): View
    {
        $sale = Sale::with(['saleItems.product', 'shop', 'cashier', 'customer'])
            ->findOrFail($id);
        $html = $this->receiptService->generateHtml($sale);

        return view('pos.receipt', compact('sale', 'html'));
    }

    private function getCartNumber(Request $request): int
    {
        return max(1, min(3, (int) $request->input('cart', 1)));
    }

    private function emptyCart(): array
    {
        return ['items' => [], 'note' => ''];
    }

    private function buildCartData(Request $request): array
    {
        $rows = $request->input('items', []);

        $productIds = array_values(array_unique(array_filter(
            array_map(fn($r) => (int) ($r['product_id'] ?? 0), $rows)
        )));

        $products = empty($productIds)
            ? collect()
            : Product::active()->whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        foreach ($rows as $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            $q = str_replace(',', '.', trim((string) ($row['quantity'] ?? '')));
            $qty = $q === '' ? 1.0 : (float) $q;

            if ($productId > 0 && $products->has($productId)) {
                $product = $products[$productId];
                $items[] = [
                    'product_id' => $productId,
                    'name'       => $product->name,
                    'unit_price' => $product->getFinalPrice(),
                    'quantity'   => max(0.01, $qty),
                ];
            }
        }

        return ['items' => $items, 'note' => trim($request->input('note', ''))];
    }
}
