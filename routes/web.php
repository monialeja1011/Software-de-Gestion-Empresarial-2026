<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ----- Admin y vendedor -----
    Route::middleware('role:admin,vendedor')->group(function () {
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
    });

    // ----- Solo administrador -----
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('providers', ProviderController::class)->except(['show']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
        Route::resource('payment-methods', PaymentMethodController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['payment-methods' => 'paymentMethod']);
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});

require __DIR__.'/auth.php';
