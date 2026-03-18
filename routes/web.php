<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::prefix('admin')->middleware('auth')->group(function () {
    // Dashboard — accessible to admin and cashier
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Settings — admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
        Route::resource('users', UserController::class);
    });

    // Products — view accessible to all, CRUD restricted to admin
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::middleware('role:admin')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Customers & Suppliers — admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('suppliers', SupplierController::class);
    });

    // Orders — admin and cashier can manage, kitchen can view and update cook status
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/update-cook-status', [OrderController::class, 'updateCookStatus'])->name('orders.update-cook-status');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'printReceipt'])->name('orders.receipt');
    Route::get('/orders/{order}/kitchen-receipt', [OrderController::class, 'printKitchenReceipt'])->name('orders.kitchen-receipt');

    Route::middleware('role:admin')->group(function () {
        Route::get('/orders/export-sold-items', [OrderController::class, 'exportSoldItems'])->name('orders.export-sold-items');
        Route::get('/orders/export-daily-orders', [OrderController::class, 'exportDailyOrders'])->name('orders.export-daily-orders');
    });

    Route::middleware('role:admin,cashier')->group(function () {
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/add-items', [OrderController::class, 'addItems'])->name('orders.add-items');
        Route::post('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
        Route::post('/orders/partial-payment', [OrderController::class, 'partialPayment'])->name('orders.partial-payment');
    });

    // Cart (POS) — admin and cashier
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
        Route::delete('/cart/delete', [CartController::class, 'delete']);
        Route::delete('/cart/empty', [CartController::class, 'empty']);
    });

    // Purchase
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.cart.index');

    // Translations route for React component
    Route::get('/locale/{type}', function ($type) {
        $translations = trans($type);
        return response()->json($translations);
    });
});
