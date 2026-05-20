<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $count = 0;
            if (auth()->check() && session('shop_id')) {
                $count = Product::lowStock(5)->count();
            }
            $view->with('lowStockCount', $count);
        });
    }
}
