<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['shop_id', 'created_at'], 'sales_shop_created_idx');
            $table->index(['shop_id', 'payment_type'], 'sales_shop_payment_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['shop_id', 'is_active'], 'products_shop_active_idx');
            $table->index(['shop_id', 'is_active', 'stock_quantity'], 'products_shop_active_stock_idx');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index(['shop_id', 'product_id'], 'sale_items_shop_product_idx');
        });

        Schema::table('sale_returns', function (Blueprint $table) {
            $table->index(['shop_id', 'created_at'], 'sale_returns_shop_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_shop_created_idx');
            $table->dropIndex('sales_shop_payment_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_shop_active_idx');
            $table->dropIndex('products_shop_active_stock_idx');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('sale_items_shop_product_idx');
        });

        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropIndex('sale_returns_shop_created_idx');
        });
    }
};
