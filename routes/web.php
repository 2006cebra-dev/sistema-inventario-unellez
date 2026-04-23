<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventarioController;
use Illuminate\Support\Facades\Route;

// 1. Redirección inteligente al Panel Principal
Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔒 2. OPERACIÓN CANDADO: Rutas Protegidas
Route::middleware('auth')->group(function () {
    
    // --- 🏠 PANEL PRINCIPAL Y BÁSICOS ---
    Route::get('/', [InventarioController::class, 'index']);
    Route::get('/escaner', function () { return view('escaner'); });
    Route::get('/historial', [InventarioController::class, 'historial']);
    
    // --- 📦 GESTIÓN DE PRODUCTOS ---
    Route::get('/productos/crear', [InventarioController::class, 'crear']);
    Route::post('/productos/guardar', [InventarioController::class, 'store']); 
    Route::get('/productos/editar/{id}', [InventarioController::class, 'editar']);
    Route::post('/productos/actualizar/{id}', [InventarioController::class, 'actualizar']);
    Route::get('/productos/eliminar/{codigo}', [InventarioController::class, 'eliminar']);
    
    // --- ⚡ OPERACIONES RÁPIDAS Y LOGÍSTICA ---
    Route::post('/productos/ajustar/{id}', [InventarioController::class, 'ajustarStock']);
    Route::post('/productos/transferir/{id}', [InventarioController::class, 'transferir']); // 🚚 NUEVA RUTA DE TRANSFERENCIA
    
    // 📱 --- RUTAS DEL ESCÁNER MÓVIL (CORREGIDAS) ---
    // Estas son las puertas exactas que el JavaScript de tu celular está buscando
    Route::post('/registrar-movimiento', [InventarioController::class, 'registrarMovimientoScanner']);
    Route::post('/productos/guardar-rapido', [InventarioController::class, 'guardarRapidoScanner']);
    
    // --- 📄 REPORTES Y ÓRDENES DE COMPRA ---
    Route::get('/productos/pdf', [InventarioController::class, 'generarPdf']);
    Route::get('/productos/excel', [InventarioController::class, 'exportarExcel']);
    Route::get('/respaldar-db', [InventarioController::class, 'respaldarDB']);
    Route::get('/orden-compra/{id}', [InventarioController::class, 'generarOrdenCompra']); // 👈 RUTA B2B

    // --- 🤖 MÓDULO DE INTELIGENCIA ARTIFICIAL ---
    // Ruta para el OSWA-Bot (Procesamiento de Lenguaje)
    Route::post('/ia/comando', [InventarioController::class, 'iaComando']);
    // Ruta para el análisis OCR de facturas
    Route::post('/ia/ocr-analisis', [InventarioController::class, 'procesarOcr']);

    // --- 👤 PERFIL DE USUARIO ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';