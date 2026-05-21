<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\BodegasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\HistorialController;

// Rutas públicas
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');

// Rutas protegidas
Route::middleware('auth.web')->group(function () {

    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Productos
    Route::get('/productos', [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/productos/crear', [ProductosController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductosController::class, 'store'])->name('productos.store');
    Route::get('/productos/{id}/editar', [ProductosController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{id}', [ProductosController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductosController::class, 'destroy'])->name('productos.destroy');

    // Órdenes
    Route::get('/ordenes', [OrdenesController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/crear', [OrdenesController::class, 'create'])->name('ordenes.create');
    Route::post('/ordenes', [OrdenesController::class, 'store'])->name('ordenes.store');
    Route::get('/ordenes/{id}', [OrdenesController::class, 'show'])->name('ordenes.show');
    Route::post('/ordenes/{id}/agregar-producto', [OrdenesController::class, 'addItem'])->name('ordenes.addItem');
    Route::delete('/ordenes/{id}/items/{itemId}', [OrdenesController::class, 'removeItem'])->name('ordenes.removeItem');
    Route::post('/ordenes/{id}/enviar-caja', [OrdenesController::class, 'sendToCashier'])->name('ordenes.sendToCashier');
    Route::post('/ordenes/{id}/cancelar', [OrdenesController::class, 'cancel'])->name('ordenes.cancel');

    // Ventas
    Route::get('/ventas', [VentasController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/{id}', [VentasController::class, 'show'])->name('ventas.show');
    Route::post('/ventas/{orderId}/procesar', [VentasController::class, 'process'])->name('ventas.process');

    // Usuarios (solo admin)
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuariosController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

    // Bodegas (solo admin)
    Route::get('/bodegas', [BodegasController::class, 'index'])->name('bodegas.index');
    Route::get('/bodegas/crear', [BodegasController::class, 'create'])->name('bodegas.create');
    Route::post('/bodegas', [BodegasController::class, 'store'])->name('bodegas.store');
    Route::get('/bodegas/{id}/editar', [BodegasController::class, 'edit'])->name('bodegas.edit');
    Route::put('/bodegas/{id}', [BodegasController::class, 'update'])->name('bodegas.update');
    Route::delete('/bodegas/{id}', [BodegasController::class, 'destroy'])->name('bodegas.destroy');

    // Reportes (solo admin)
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');

    // Historial (solo admin)
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
});