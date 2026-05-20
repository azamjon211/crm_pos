<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'products'       => ['cost_price', 'sale_price'],
            'sale_items'     => ['unit_price', 'unit_cost', 'line_total'],
            'sales'          => ['total_amount', 'total_cost'],
            'purchases'      => ['unit_cost', 'total_cost'],
            'daily_closings' => ['total_sales', 'total_cost', 'total_profit', 'total_returns', 'net_sales'],
        ];

        foreach ($columns as $table => $cols) {
            foreach ($cols as $col) {
                DB::statement("ALTER TABLE {$table} ALTER COLUMN {$col} TYPE numeric(20,2)");
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'products'       => ['cost_price', 'sale_price'],
            'sale_items'     => ['unit_price', 'unit_cost', 'line_total'],
            'sales'          => ['total_amount', 'total_cost'],
            'purchases'      => ['unit_cost', 'total_cost'],
            'daily_closings' => ['total_sales', 'total_cost', 'total_profit', 'total_returns', 'net_sales'],
        ];

        foreach ($columns as $table => $cols) {
            foreach ($cols as $col) {
                DB::statement("ALTER TABLE {$table} ALTER COLUMN {$col} TYPE numeric(12,2)");
            }
        }
    }
};
