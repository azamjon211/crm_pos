<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\DailyClosingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DailyClosingService $closingService) {}

    public function index(): View
    {
        $todayStats = $this->closingService->getTodayStats();

        $todayCount = Sale::whereDate('created_at', today())->count();

        $activeDebts = Sale::where('payment_type', Sale::PAYMENT_DEBT)
            ->whereRaw(
                '(SELECT COALESCE(SUM(amount), 0) FROM debt_payments WHERE sale_id = sales.id) < total_amount'
            )
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_amount), 0) as total')
            ->first();

        $weekSales = Sale::selectRaw(
                'DATE(created_at) as date,
                 SUM(total_amount) as total,
                 COUNT(*) as count'
            )
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        $topProducts = SaleItem::selectRaw('product_id, SUM(quantity) as qty_sold, SUM(line_total) as revenue')
            ->whereHas('sale', fn($q) => $q->whereMonth('created_at', now()->month)
                                           ->whereYear('created_at', now()->year))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        $lowStockProducts = Product::lowStock(5)->with('category')->orderBy('stock_quantity')->limit(10)->get();
        $totalProducts    = Product::active()->count();
        $totalCustomers   = Customer::active()->count();

        return view('backend.dashboard', compact(
            'todayStats', 'todayCount', 'activeDebts',
            'weekSales', 'topProducts',
            'lowStockProducts', 'totalProducts', 'totalCustomers'
        ));
    }
}
