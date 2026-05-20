<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('total_profit', 12, 2)->default(0);
            $table->decimal('total_returns', 12, 2)->default(0);
            $table->decimal('net_sales', 12, 2)->default(0);
            $table->timestamp('created_at')->nullable();

            // Har bir shop uchun har kunda faqat 1 ta yopish
            $table->unique(['shop_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
};
