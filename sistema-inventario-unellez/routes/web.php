<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;

// --- 1. PANEL PRINCIPAL ---
Route::get('/', [InventarioController::class, 'index'])->name('index');

// --- 2. GESTIÓN DE PRODUCTOS (CRUD) ---
Route::get('/productos/crear', [InventarioController::class, 'crear'])->name('productos.crear');
Route::post('/productos/guardar', [InventarioController::class, 'store'])->name('productos.store');
Route::get('/productos/editar/{id}', [InventarioController::class, 'editar'])->name('productos.editar');
Route::post('/productos/actualizar/{id}', [InventarioController::class, 'actualizar'])->name('productos.actualizar');
Route::get('/productos/eliminar/{codigo}', [InventarioController::class, 'eliminar'])->name('productos.eliminar');

// --- 3. BOTONES DE AJUSTE RÁPIDO (+ / -) VÍA AJAX ---
Route::post('/productos/ajustar/{id}', [InventarioController::class, 'ajustarStock'])->name('productos.ajustar');

// --- 4. HISTORIAL Y REPORTES (PDF, EXCEL Y RESPALDO) ---
Route::get('/historial', [InventarioController::class, 'historial'])->name('historial');
Route::get('/productos/pdf', [InventarioController::class, 'generarPdf'])->name('productos.pdf');
Route::get('/productos/excel', [InventarioController::class, 'exportarExcel'])->name('productos.excel');

// ✅ MEJORA 2: RUTA PARA EL RESPALDO DE LA BASE DE DATOS
Route::get('/productos/respaldo', [InventarioController::class, 'respaldarDB'])->name('productos.respaldo');

// --- 5. SISTEMA DE ESCÁNER MÓVIL ---
Route::get('/escaner', function () {
    return view('escaner');
})->name('escaner');

Route::post('/registrar-movimiento', [InventarioController::class, 'registrar'])->name('registrar.movimiento');
Route::post('/productos/guardar-rapido', [InventarioController::class, 'storeRapido'])->name('productos.guardarRapido');