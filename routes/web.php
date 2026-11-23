<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
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
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('notifications/{notification}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');

    Route::patch('notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.read-all');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('products/exportar', [ExcelController::class, 'exportProducts'])
        ->middleware('permission:exportar-productos')
        ->name('products.export');

    Route::get('products/importar', [ExcelController::class, 'importForm'])
        ->middleware('permission:importar-productos')
        ->name('products.import');

    Route::get('products/plantilla', [ExcelController::class, 'template'])
        ->middleware('permission:importar-productos')
        ->name('products.template');

    Route::post('products/importar', [ExcelController::class, 'import'])
        ->middleware('permission:importar-productos')
        ->name('products.import.store');

    Route::get('products', [ProductController::class, 'index'])
        ->middleware('permission:ver-productos')
        ->name('products.index');

    Route::get('products/create', [ProductController::class, 'create'])
        ->middleware('permission:crear-productos')
        ->name('products.create');

    Route::post('products', [ProductController::class, 'store'])
        ->middleware('permission:crear-productos')
        ->name('products.store');

    Route::get('products/{product}/edit', [ProductController::class, 'edit'])
        ->middleware('permission:editar-productos')
        ->name('products.edit');

    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])
        ->middleware('permission:editar-productos')
        ->name('products.update');

    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:eliminar-productos')
        ->name('products.destroy');

    Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
        ->middleware('permission:restaurar-productos')
        ->name('products.restore');

    Route::get('categories', [CategoryController::class, 'index'])
        ->middleware('permission:ver-categorias')
        ->name('categories.index');

    Route::get('categories/create', [CategoryController::class, 'create'])
        ->middleware('permission:crear-categorias')
        ->name('categories.create');

    Route::post('categories', [CategoryController::class, 'store'])
        ->middleware('permission:crear-categorias')
        ->name('categories.store');

    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:editar-categorias')
        ->name('categories.edit');

    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:editar-categorias')
        ->name('categories.update');

    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:eliminar-categorias')
        ->name('categories.destroy');

    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->middleware('permission:restaurar-categorias')
        ->name('categories.restore');

    Route::get('clients', [ClientController::class, 'index'])
            ->middleware('permission:ver-clientes')
            ->name('clients.index');

    Route::get('clients/create', [ClientController::class, 'create'])
        ->middleware('permission:crear-clientes')
        ->name('clients.create');

    Route::post('clients', [ClientController::class, 'store'])
        ->middleware('permission:crear-clientes')
        ->name('clients.store');

    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])
        ->middleware('permission:editar-clientes')
        ->name('clients.edit');

    Route::match(['put', 'patch'], 'clients/{client}', [ClientController::class, 'update'])
        ->middleware('permission:editar-clientes')
        ->name('clients.update');

    Route::delete('clients/{client}', [ClientController::class, 'destroy'])
        ->middleware('permission:eliminar-clientes')
        ->name('clients.destroy');

    Route::get('sales/exportar', [ExcelController::class, 'exportSales'])
        ->middleware('permission:exportar-ventas')
        ->name('sales.export');

    Route::get('clients/lookup', [SaleController::class, 'lookupClient'])
        ->middleware(['permission:crear-ventas', 'throttle:60,1'])
        ->name('clients.lookup');

    Route::get('sales', [SaleController::class, 'index'])
        ->middleware('permission:ver-ventas')
        ->name('sales.index');

    Route::get('sales/create', [SaleController::class, 'create'])
        ->middleware('permission:crear-ventas')
        ->name('sales.create');

    Route::post('sales', [SaleController::class, 'store'])
        ->middleware('permission:crear-ventas')
        ->name('sales.store');

    Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])
        ->withTrashed()
        ->middleware('permission:ver-ventas')
        ->name('sales.pdf');

    Route::get('sales/{sale}', [SaleController::class, 'show'])
        ->withTrashed()
        ->middleware('permission:ver-ventas')
        ->name('sales.show');

    Route::delete('sales/{sale}', [SaleController::class, 'destroy'])
        ->withTrashed()
        ->middleware('permission:eliminar-ventas')
        ->name('sales.destroy');

    Route::get('purchases', [PurchaseController::class, 'index'])
        ->middleware('permission:ver-compras')
        ->name('purchases.index');

    Route::get('purchases/create', [PurchaseController::class, 'create'])
        ->middleware('permission:crear-compras')
        ->name('purchases.create');

    Route::post('purchases', [PurchaseController::class, 'store'])
        ->middleware('permission:crear-compras')
        ->name('purchases.store');

    Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])
        ->middleware('permission:ver-compras')
        ->name('purchases.pdf');

    Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])
        ->middleware('permission:ver-compras')
        ->name('purchases.show');

    Route::delete('purchases/{purchase}', [PurchaseController::class, 'destroy'])
        ->middleware('permission:eliminar-compras')
        ->name('purchases.destroy');

    Route::patch('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])
        ->middleware('permission:recibir-compras')
        ->name('purchases.receive');

    Route::get('providers', [ProviderController::class, 'index'])
        ->middleware('permission:ver-proveedores')
        ->name('providers.index');

    Route::get('providers/create', [ProviderController::class, 'create'])
        ->middleware('permission:crear-proveedores')
        ->name('providers.create');

    Route::post('providers', [ProviderController::class, 'store'])
        ->middleware('permission:crear-proveedores')
        ->name('providers.store');

    Route::get('providers/{provider}/edit', [ProviderController::class, 'edit'])
        ->middleware('permission:editar-proveedores')
        ->name('providers.edit');

    Route::match(['put', 'patch'], 'providers/{provider}', [ProviderController::class, 'update'])
        ->middleware('permission:editar-proveedores')
        ->name('providers.update');

    Route::delete('providers/{provider}', [ProviderController::class, 'destroy'])
        ->middleware('permission:eliminar-proveedores')
        ->name('providers.destroy');

    Route::get('payment-methods', [PaymentMethodController::class, 'index'])
        ->middleware('permission:ver-metodos-pago')
        ->name('payment-methods.index');

    Route::post('payment-methods', [PaymentMethodController::class, 'store'])
        ->middleware('permission:crear-metodos-pago')
        ->name('payment-methods.store');

    Route::match(
        ['put', 'patch'],
        'payment-methods/{paymentMethod}',
        [PaymentMethodController::class, 'update']
    )
        ->middleware('permission:editar-metodos-pago')
        ->name('payment-methods.update');

    Route::delete('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])
        ->middleware('permission:eliminar-metodos-pago')
        ->name('payment-methods.destroy');

    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:gestionar-usuarios')
        ->name('users.index');

    Route::patch('users/permissions', [UserController::class, 'updatePermissions'])
        ->middleware(['role:superadmin', 'permission:gestionar-permisos'])
        ->name('users.permissions.update');

    Route::patch('users/{user}', [UserController::class, 'update'])
        ->middleware(['role:superadmin', 'permission:gestionar-roles'])
        ->name('users.update');

    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware(['auth', 'role:superadmin'])
        ->name('users.destroy');

    Route::get('inventory', [InventoryController::class, 'index'])
        ->middleware('permission:ver-inventario')
        ->name('inventory.index');

    Route::post('inventory/{product}/adjust', [InventoryController::class, 'adjust'])
        ->middleware('permission:ajustar-inventario')
        ->name('inventory.adjust');

    Route::get('inventory/{product}/pdf', [InventoryController::class, 'pdf'])
        ->withTrashed()
        ->middleware('permission:ver-inventario')
        ->name('inventory.pdf');

    Route::get('inventory/{product}/adjust', [InventoryController::class, 'editAdjustment'])
        ->middleware('permission:ajustar-inventario')
        ->name('inventory.adjust.form');

    Route::post('inventory/{product}/adjust/store', [InventoryController::class, 'adjust'])
        ->middleware('permission:ajustar-inventario')
        ->name('inventory.adjust.store');

    Route::get('company-settings', [CompanySettingController::class, 'edit'])
        ->middleware('permission:ver-identidad')
        ->name('company-settings.edit');

    Route::match(['put', 'patch'], 'company-settings', [CompanySettingController::class, 'update'])
        ->middleware('permission:editar-identidad')
        ->name('company-settings.update');

    Route::get('audit', [AuditController::class, 'index'])
        ->middleware('permission:ver-auditoria')
        ->name('audit.index');

    Route::middleware('role:superadmin|admin')->group(function () {});
});

require __DIR__.'/auth.php';
