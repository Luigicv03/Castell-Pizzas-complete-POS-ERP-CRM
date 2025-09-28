<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

// Rutas de autenticación
Auth::routes();

// Redireccionar la raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/{order}/details', [DashboardController::class, 'orderDetails'])->name('orders.details');
    
    // Ruta /home que redirige al dashboard (compatibilidad con Laravel UI)
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');
    
           // POS - Sistema de Punto de Venta
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [App\Http\Controllers\PosController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\PosController::class, 'create'])->name('create');
    Route::post('/build-order', [App\Http\Controllers\PosController::class, 'buildOrder'])->name('build-order');
    Route::post('/', [App\Http\Controllers\PosController::class, 'store'])->name('store');
    Route::get('/{order}', [App\Http\Controllers\PosController::class, 'show'])->name('show');
    Route::get('/{order}/detail', [App\Http\Controllers\PosController::class, 'orderDetail'])->name('detail');
    Route::get('/{order}/edit', [App\Http\Controllers\PosController::class, 'edit'])->name('edit');
    Route::put('/{order}', [App\Http\Controllers\PosController::class, 'update'])->name('update');
    Route::put('/{order}/status', [App\Http\Controllers\PosController::class, 'updateOrderStatus'])->name('update-status');
    Route::delete('/{order}', [App\Http\Controllers\PosController::class, 'destroy'])->name('destroy');
    Route::get('/{order}/payment', [App\Http\Controllers\PosController::class, 'showPayment'])->name('payment');
    Route::post('/{order}/payment', [App\Http\Controllers\PosController::class, 'processPayment'])->name('process-payment');
    Route::post('/{order}/auto-print', [App\Http\Controllers\PosController::class, 'autoPrintOrders'])->name('auto-print');
    
    // Rutas para gestión de órdenes por mesa
    Route::get('/table/{tableId}/order', [App\Http\Controllers\PosController::class, 'showTableOrder'])->name('table.order');
    Route::post('/{orderId}/add-product', [App\Http\Controllers\PosController::class, 'addProductToOrder'])->name('add-product');
    Route::delete('/{orderId}/item/{itemId}', [App\Http\Controllers\PosController::class, 'removeItemFromOrder'])->name('remove-item');
    Route::put('/{orderId}/item/{itemId}/quantity', [App\Http\Controllers\PosController::class, 'updateItemQuantity'])->name('update-quantity');
    
    // Rutas para impresión
    Route::get('/{orderId}/print/kitchen', [App\Http\Controllers\PosController::class, 'printKitchenOrder'])->name('print.kitchen');
    Route::get('/{orderId}/print/bar', [App\Http\Controllers\PosController::class, 'printBarOrder'])->name('print.bar');
});
    
    // Mesas
    Route::prefix('tables')->name('tables.')->group(function () {
        Route::get('/', [App\Http\Controllers\TableController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TableController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TableController::class, 'store'])->name('store');
        Route::get('/{table}', [App\Http\Controllers\TableController::class, 'show'])->name('show');
        Route::get('/{table}/edit', [App\Http\Controllers\TableController::class, 'edit'])->name('edit');
        Route::put('/{table}', [App\Http\Controllers\TableController::class, 'update'])->name('update');
        Route::delete('/{table}', [App\Http\Controllers\TableController::class, 'destroy'])->name('destroy');
        Route::put('/{table}/status', [App\Http\Controllers\TableController::class, 'updateStatus'])->name('update-status');
        Route::post('/sync-statuses', [App\Http\Controllers\TableController::class, 'syncStatuses'])->name('sync-statuses');
    });
    
    // Productos
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ProductController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('destroy');
    });
    
    // Categorías
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\CategoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [App\Http\Controllers\CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [App\Http\Controllers\CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('destroy');
    });
    
    // Clientes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [App\Http\Controllers\CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [App\Http\Controllers\CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [App\Http\Controllers\CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [App\Http\Controllers\CustomerController::class, 'destroy'])->name('destroy');
    });

    
    // Inventario - Ingredientes
    Route::prefix('ingredients')->name('ingredients.')->group(function () {
        Route::get('/', [App\Http\Controllers\IngredientController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\IngredientController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\IngredientController::class, 'store'])->name('store');
        Route::get('/low-stock', [App\Http\Controllers\IngredientController::class, 'lowStock'])->name('low-stock');
        Route::get('/{ingredient}', [App\Http\Controllers\IngredientController::class, 'show'])->name('show');
        Route::get('/{ingredient}/edit', [App\Http\Controllers\IngredientController::class, 'edit'])->name('edit');
        Route::put('/{ingredient}', [App\Http\Controllers\IngredientController::class, 'update'])->name('update');
        Route::delete('/{ingredient}', [App\Http\Controllers\IngredientController::class, 'destroy'])->name('destroy');
        Route::post('/{ingredient}/adjust-stock', [App\Http\Controllers\IngredientController::class, 'adjustStock'])->name('adjust-stock');
    });
    
    // Inventario - Proveedores
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [App\Http\Controllers\SupplierController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\SupplierController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [App\Http\Controllers\SupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [App\Http\Controllers\SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [App\Http\Controllers\SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [App\Http\Controllers\SupplierController::class, 'destroy'])->name('destroy');
        Route::post('/{supplier}/toggle-status', [App\Http\Controllers\SupplierController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Inventario - Recetas
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', [App\Http\Controllers\RecipeController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\RecipeController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\RecipeController::class, 'store'])->name('store');
        Route::get('/{recipe}', [App\Http\Controllers\RecipeController::class, 'show'])->name('show');
        Route::get('/{recipe}/edit', [App\Http\Controllers\RecipeController::class, 'edit'])->name('edit');
        Route::put('/{recipe}', [App\Http\Controllers\RecipeController::class, 'update'])->name('update');
        Route::delete('/{recipe}', [App\Http\Controllers\RecipeController::class, 'destroy'])->name('destroy');
        Route::get('/{recipe}/check-availability', [App\Http\Controllers\RecipeController::class, 'checkAvailability'])->name('check-availability');
        Route::get('/{recipe}/calculate-cost', [App\Http\Controllers\RecipeController::class, 'calculateCost'])->name('calculate-cost');
    });
    
    // Inventario - Transacciones
    Route::prefix('inventory-transactions')->name('inventory-transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\InventoryTransactionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\InventoryTransactionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\InventoryTransactionController::class, 'store'])->name('store');
        Route::get('/report', [App\Http\Controllers\InventoryTransactionController::class, 'report'])->name('report');
        Route::get('/{inventoryTransaction}', [App\Http\Controllers\InventoryTransactionController::class, 'show'])->name('show');
        Route::get('/{inventoryTransaction}/edit', [App\Http\Controllers\InventoryTransactionController::class, 'edit'])->name('edit');
        Route::put('/{inventoryTransaction}', [App\Http\Controllers\InventoryTransactionController::class, 'update'])->name('update');
        Route::delete('/{inventoryTransaction}', [App\Http\Controllers\InventoryTransactionController::class, 'destroy'])->name('destroy');
    });
    
    // Reportes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/sales', [App\Http\Controllers\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [App\Http\Controllers\ReportController::class, 'products'])->name('products');
        Route::get('/inventory', [App\Http\Controllers\ReportController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [App\Http\Controllers\ReportController::class, 'customers'])->name('customers');
        Route::post('/export', [App\Http\Controllers\ReportController::class, 'export'])->name('export');
    });
    
    // CRM
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', [App\Http\Controllers\CrmController::class, 'index'])->name('index');
        Route::get('/segmentation', [App\Http\Controllers\CrmController::class, 'segmentation'])->name('segmentation');
        Route::get('/behavior', [App\Http\Controllers\CrmController::class, 'behavior'])->name('behavior');
        Route::get('/campaigns', [App\Http\Controllers\CrmController::class, 'campaigns'])->name('campaigns');
        Route::get('/retention', [App\Http\Controllers\CrmController::class, 'retention'])->name('retention');
    });
});

// API Routes (sin autenticación para uso interno)
Route::prefix('api')->group(function () {
    Route::get('/customers/search', [App\Http\Controllers\CustomerController::class, 'search'])->name('api.customers.search');
    Route::get('/orders/active', [App\Http\Controllers\PosController::class, 'getActiveOrders'])->name('api.orders.active');
    Route::get('/delivery/costs', [App\Http\Controllers\PosController::class, 'getDeliveryCosts'])->name('api.delivery.costs');
    Route::get('/delivery/calculate', [App\Http\Controllers\PosController::class, 'calculateDeliveryCost'])->name('api.delivery.calculate');
});
