<?php

namespace App\Services;

use App\Models\DailyClosing;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\DB;

class DailyClosingService
{
    public function getTodayStats(?string $date = null): array
    {
        $date  = $date ?? today()->toDateString();
        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $sales = Sale::whereBetween('created_at', [$start, $end])
            ->selectRaw('COALESCE(sum(total_amount), 0) as total_sales, COALESCE(sum(total_cost), 0) as total_cost')
            ->first();

        $totalSales = (float) ($sales->total_sales ?? 0);
        $totalCost  = (float) ($sales->total_cost ?? 0);

        $returns = SaleReturn::whereBetween('sale_returns.created_at', [$start, $end])
            ->join('sale_items', 'sale_returns.sale_item_id', '=', 'sale_items.id')
            ->selectRaw('COALESCE(sum(sale_returns.quantity * sale_items.unit_price), 0) as return_revenue,
                         COALESCE(sum(sale_returns.quantity * sale_items.unit_cost), 0) as return_cost')
            ->first();

        $totalReturns = (float) ($returns->return_revenue ?? 0);
        $returnsCost  = (float) ($returns->return_cost ?? 0);

        $netSales = $totalSales - $totalReturns;
        $netCost  = $totalCost - $returnsCost;
        $profit   = $netSales - $netCost;

        return compact('totalSales', 'totalCost', 'totalReturns', 'netSales', 'profit');
    }
        public function close(?string $date = null){
            $date = $date ?? today()->toDateString();
            $stats = $this->getTodayStats($date);
            return DB::transaction(function () use ($stats, $date){
                return DailyClosing::updateOrCreate(
                    ['date' => $date],
                    [
                        'total_sales'   => $stats['totalSales'],
                        'total_cost'    => $stats['totalCost'],
                        'total_returns' => $stats['totalReturns'],
                        'net_sales'     => $stats['netSales'],
                        'total_profit'  => $stats['profit'],
                    ]
                );
            });
        }
    public function getMonthlyReport(int $year): array
    {
        $salesRows = DB::table('sales')
            ->selectRaw('
                EXTRACT(year  FROM created_at)::int AS year,
                EXTRACT(month FROM created_at)::int AS month,
                SUM(total_amount)               AS total_sales,
                SUM(total_cost)                 AS total_cost
            ')
            ->whereYear('created_at', $year)
            ->where('shop_id', session('shop_id') ?? auth()->user()?->shop_id)
            ->groupByRaw('EXTRACT(year FROM created_at), EXTRACT(month FROM created_at)')
            ->orderByRaw('EXTRACT(year FROM created_at) ASC, EXTRACT(month FROM created_at) ASC')
            ->get()
            ->keyBy('month');

        $returnRows = DB::table('sale_returns')
            ->join('sale_items', 'sale_returns.sale_item_id', '=', 'sale_items.id')
            ->selectRaw('
                EXTRACT(month FROM sale_returns.created_at)::int AS month,
                SUM(sale_returns.quantity * sale_items.unit_price) AS return_revenue,
                SUM(sale_returns.quantity * sale_items.unit_cost)  AS return_cost
            ')
            ->whereYear('sale_returns.created_at', $year)
            ->where('sale_returns.shop_id', session('shop_id') ?? auth()->user()?->shop_id)
            ->groupByRaw('EXTRACT(month FROM sale_returns.created_at)')
            ->get()
            ->keyBy('month');

        $rows = $salesRows->map(function ($s) use ($returnRows) {
            $r            = $returnRows->get($s->month);
            $totalSales   = (float) $s->total_sales;
            $totalCost    = (float) $s->total_cost;
            $totalReturns = $r ? (float) $r->return_revenue : 0.0;
            $returnsCost  = $r ? (float) $r->return_cost    : 0.0;
            $netSales     = $totalSales - $totalReturns;
            $netCost      = $totalCost  - $returnsCost;

            return [
                'year'          => (int) $s->year,
                'month'         => (int) $s->month,
                'total_sales'   => $totalSales,
                'total_returns' => $totalReturns,
                'total_cost'    => $totalCost,
                'profit'        => $netSales - $netCost,
            ];
        })->values()->toArray();

        return [
            'year'       => $year,
            'rows'       => $rows,
            'totalSales' => array_sum(array_column($rows, 'total_sales')),
            'totalCost'  => array_sum(array_column($rows, 'total_cost')),
            'profit'     => array_sum(array_column($rows, 'profit')),
        ];
    }

}
