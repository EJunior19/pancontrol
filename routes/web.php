<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplyMovementController;

/*
|--------------------------------------------------------------------------
| Web Routes - PanControl
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| LOGIN (PUBLICO)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| SISTEMA (PROTEGIDO)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Ventas
    |--------------------------------------------------------------------------
    */
    Route::prefix('sales')->name('sales.')->group(function () {

        // Venta rápida
        Route::get('/', [SaleController::class, 'index'])
            ->name('index');

        Route::post('/', [SaleController::class, 'store'])
            ->name('store');

        // Historial de ventas
        Route::get('/history', [SaleController::class, 'history'])
            ->name('history');

        // Lookup por código de barras
        Route::get('/lookup', [SaleController::class, 'lookup'])
            ->name('lookup');

        // Ticket
        Route::get('/{sale}/receipt', [SaleController::class, 'receipt'])
            ->name('receipt');

        // Factura
        Route::get('/{sale}/invoice', [SaleController::class, 'invoice'])
            ->name('invoice');

        Route::get('/{sale}/invoice-test', [SaleController::class, 'invoiceTest'])
            ->name('invoice.test');
    });

    /*
    |--------------------------------------------------------------------------
    | Producción / Reportes
    |--------------------------------------------------------------------------
    */
    Route::prefix('production')->name('production.')->group(function () {

        Route::get('/', [ProductionController::class, 'index'])
            ->name('index');

        Route::get('/create', [ProductionController::class, 'create'])
            ->name('create');

        Route::post('/', [ProductionController::class, 'store'])
            ->name('store');

        // Reportes
        Route::get('/top-products', [ProductionController::class, 'topProducts'])
            ->name('top-products');

        Route::get('/products-history', [ProductionController::class, 'productsHistory'])
            ->name('products-history');
    });

    /*
    |--------------------------------------------------------------------------
    | Caja
    |--------------------------------------------------------------------------
    */
    Route::prefix('caja')->name('cash.')->group(function () {

        Route::get('/', [CashRegisterController::class, 'index'])
            ->name('index');

        // Abrir caja
        Route::get('/abrir', [CashRegisterController::class, 'openForm'])
            ->name('open.form');

        Route::post('/abrir', [CashRegisterController::class, 'open'])
            ->name('open');

        // Cerrar caja
        Route::get('/cerrar', [CashRegisterController::class, 'closeForm'])
            ->name('close.form');

        Route::post('/cerrar', [CashRegisterController::class, 'close'])
            ->name('close');

        // Resumen / Arqueo
        Route::get('/resumen', [CashRegisterController::class, 'summary'])
            ->name('summary');

        // Historial de cajas
        Route::get('/historial', [CashRegisterController::class, 'history'])
            ->name('history');
    });

    /*
    |--------------------------------------------------------------------------
    | Clientes (AJAX)
    |--------------------------------------------------------------------------
    */
    Route::get('/clients/by-ruc/{ruc}', function ($ruc) {
        return \App\Models\Client::where('ruc', $ruc)->first();
    })->name('clients.by-ruc');

    /*
    |--------------------------------------------------------------------------
    | Productos
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->name('products.')->group(function () {

        Route::get('/', [ProductController::class, 'index'])
            ->name('index');

        Route::get('/create', [ProductController::class, 'create'])
            ->name('create');

        Route::post('/', [ProductController::class, 'store'])
            ->name('store');

        Route::get('/{product}/edit', [ProductController::class, 'edit'])
            ->name('edit');

        Route::put('/{product}', [ProductController::class, 'update'])
            ->name('update');
    });

    // Stock bajo
    Route::get('/stock/low', [ProductController::class, 'lowStock'])
        ->name('stock.low');

    /*
    |--------------------------------------------------------------------------
    | Variantes de productos
    |--------------------------------------------------------------------------
    */
    Route::prefix('product-variants')->name('product-variants.')->group(function () {

        Route::get('/', [ProductVariantController::class, 'index'])
            ->name('index');

        Route::get('/create', [ProductVariantController::class, 'create'])
            ->name('create');

        Route::post('/', [ProductVariantController::class, 'store'])
            ->name('store');

        Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])
            ->name('edit');

        Route::put('/{variant}', [ProductVariantController::class, 'update'])
            ->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Insumos (rutas sueltas, como estaban)
    |--------------------------------------------------------------------------
    */
    Route::get('/supplies', [SupplyController::class, 'index'])
        ->name('supplies.index');

    Route::get('/supplies/create', [SupplyController::class, 'create'])
        ->name('supplies.create');

    Route::post('/supplies', [SupplyController::class, 'store'])
        ->name('supplies.store');

    // Movimientos
    Route::get('/supplies/movements/create', [SupplyMovementController::class, 'create'])
        ->name('supplies.movements.create');

    Route::post('/supplies/movements', [SupplyMovementController::class, 'store'])
        ->name('supplies.movements.store');

    /*
    |--------------------------------------------------------------------------
    | Ajuste manual de stock (grupo supplies, como estaba)
    |--------------------------------------------------------------------------
    */
    Route::prefix('supplies')->name('supplies.')->group(function () {

        Route::get('/', [SupplyController::class, 'index'])->name('index');
        Route::get('/create', [SupplyController::class, 'create'])->name('create');
        Route::post('/', [SupplyController::class, 'store'])->name('store');

        Route::prefix('movements')->name('movements.')->group(function () {
            Route::get('/create', [SupplyMovementController::class, 'create'])->name('create');
            Route::post('/', [SupplyMovementController::class, 'store'])->name('store');
        });

        Route::post('/adjust', [SupplyMovementController::class, 'adjust'])
            ->name('adjust');
    });

    /*
    |--------------------------------------------------------------------------
    | PDF Caja
    |--------------------------------------------------------------------------
    */
    Route::get('/caja/{id}/reporte/pdf', [CashRegisterController::class, 'pdf'])
        ->name('cash.report.pdf');

});
