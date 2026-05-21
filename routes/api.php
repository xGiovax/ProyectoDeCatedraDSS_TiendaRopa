<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\HistoryController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Administrador
    Route::middleware('role:administrador')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('warehouses', WarehouseController::class);
        Route::get('reports/dashboard', [ReportController::class, 'dashboard']);
        Route::get('reports/ventas-diarias', [ReportController::class, 'ventasDiarias']);
        Route::get('reports/productos-mas-vendidos', [ReportController::class, 'productosMasVendidos']);
        Route::get('reports/inventario', [ReportController::class, 'inventario']);
        Route::get('history', [HistoryController::class, 'index']);
        Route::get('history/{history}', [HistoryController::class, 'show']);
    });

    // Administrador y Vendedor
    Route::middleware('role:administrador,vendedor')->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);
        Route::post('products/{product}/reserve', [ProductController::class, 'reserve']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::post('orders/{order}/items', [OrderController::class, 'addItem']);
        Route::delete('orders/{order}/items/{item}', [OrderController::class, 'removeItem']);
        Route::post('orders/{order}/send-to-cashier', [OrderController::class, 'sendToCashier']);
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    // Solo Administrador puede hacer CRUD de productos
    Route::middleware('role:administrador')->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);
    });

    // Cajero
    Route::middleware('role:cajero,administrador')->group(function () {
        Route::get('sales', [SaleController::class, 'index']);
        Route::get('sales/{sale}', [SaleController::class, 'show']);
        Route::post('orders/{order}/process-payment', [SaleController::class, 'process']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
    });
});