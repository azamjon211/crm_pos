<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend;
use App\Http\Controllers\Frontend\PosController;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')->middleware('auth');

// POS
Route::middleware(['auth', 'set.shop'])
    ->prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/complete', [PosController::class, 'complete'])->name('complete');
        Route::post('/add-to-cart', [PosController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/remove-row', [PosController::class, 'removeRow'])->name('remove-row');
        Route::post('/save-cart', [PosController::class, 'saveCart'])->name('save-cart');
        Route::get('/receipt/{id}', [PosController::class, 'receipt'])->name('receipt');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::get('/products-by-category', [PosController::class, 'productsByCategory'])->name('products-by-category');
    });

// Backend
Route::middleware(['auth', 'set.shop', 'role:superadmin,admin,manager'])
    ->prefix('backend')->name('backend.')->group(function () {
        Route::get('/', [Backend\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', Backend\CategoryController::class);
        Route::resource('products', Backend\ProductController::class);
        Route::resource('suppliers', Backend\SupplierController::class);
        Route::resource('customers', Backend\CustomerController::class);
        Route::resource('purchases', Backend\PurchaseController::class);
        Route::get('sales', [Backend\SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/{sale}', [Backend\SaleController::class, 'show'])->name('sales.show');
        Route::delete('sales/{sale}', [Backend\SaleController::class, 'destroy'])->name('sales.destroy');
        Route::get('sales/{sale}/return', [Backend\SaleController::class, 'returnForm'])->name('sales.return');
        Route::post('sales/{sale}/return', [Backend\SaleController::class, 'processReturn'])->name('sales.process-return');
        Route::get('debts', [Backend\DebtController::class, 'index'])->name('debts.index');
        Route::get('debts/{sale}', [Backend\DebtController::class, 'show'])->name('debts.show');
        Route::post('debts/{sale}/pay', [Backend\DebtController::class, 'pay'])->name('debts.pay');
        Route::get('daily-closing', [Backend\DailyClosingController::class, 'index'])->name('daily-closing.index');
        Route::post('daily-closing/close', [Backend\DailyClosingController::class, 'close'])->name('daily-closing.close');
        Route::get('reports/monthly', [Backend\ReportController::class, 'monthly'])->name('reports.monthly');
        Route::resource('users', Backend\UserController::class);
    });

Route::middleware(['auth', 'set.shop', 'role:superadmin'])
    ->prefix('backend')->name('backend.')->group(function () {
        Route::resource('shops', Backend\ShopController::class);
        Route::get('switch-shop', [Backend\ShopSwitchController::class, 'index'])->name('switch-shop');
        Route::post('switch-shop', [Backend\ShopSwitchController::class, 'switch'])->name('switch-shop.post');
    });

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isManagerOrAdmin()
            ? redirect()->route('backend.dashboard')
            : redirect()->route('pos.index');
    }
    return redirect()->route('login');
});

