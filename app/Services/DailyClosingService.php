<?php

namespace App\Services;

use App\Models\DailyClosing;
use App\Models\Sale;

class DailyClosingService
{
    public function getTodayStats(?string $date = null)
    {
        $date  = $date ?? today()->toDateString();
        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $sales = Sale::whereBetween('created_at', [$start, $end])
            ->selectRaw('sum(total_amount) as total_sales, sum(total_cost) as total_cost')
            ->first();

        $totalSales = (float) ($sales->total_sales ?? 0);
        $totalCost  = (float) ($sales->total_cost ?? 0);
        $profit     = $totalSales - $totalCost;

        return compact('totalSales', 'totalCost', 'profit');
    }
        public function close(?string $date = null){
            $date = $date ?? today()->toDateString();
            $stats = $this->getTodayStats($date);
            return DB::transaction(function () use ($stats, $date){
                return DailyClosing::updateOrCreate(
                    ['date' => $date],
                    [
                        'total_sales' => $stats['total_sales'],
                        'total_cost' => $stats['total_cost'],
                        'total_profit' => $stats['total_profit'],
                    ]
                );
            });
        }
        public function getMonthlyReport(int $year){
            $row = Sale::selectRaw(
                'extract (year from created_at) as year,
                extract(month from created_at) as month,
                sum(total_amount) as total_sales,
                sum(total_cost) as total_cost,
                sum(total_amount - total_cost) as profit'
            )
                ->whereYear('created_at', $year)
                ->groupbyRaw('extract(year from created_at), extract(month from created_at)')
                ->orderbyRaw('year ASC, month ASC')
                ->get()
                ->toArray();
            return [
                'year' => $year,
                'rows' => $row,
                'totalSales' => array_sum(array_column($row, 'total_sales')),
                'totalCost' => array_sum(array_column($row, 'total_cost')),
                'profit' => array_sum(array_column($row, 'profit')),
            ];
        }

}
