<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProfileController;
use App\Models\Movimiento; // Agregado para la reparación de firmas

// Ruta pública de la Landing Page
Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Auth::routes();

Route::get('/home', fn() => redirect()->to('/dashboard'))->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [InventarioController::class, 'index'])->name('inventario');
    Route::get('/api/dashboard/graficas', [InventarioController::class, 'getChartsData'])->name('api.graficas');
    Route::get('/catalogo', [InventarioController::class, 'catalogo'])->name('catalogo');
    Route::get('/proveedores', [InventarioController::class, 'proveedores'])->name('proveedores');
    Route::post('/proveedores/guardar', [InventarioController::class, 'storeProveedor'])->name('proveedores.store');
    Route::post('/proveedores/{id}/actualizar', [InventarioController::class, 'updateProveedor'])->name('proveedores.update');
    Route::delete('/proveedores/{id}/eliminar', [InventarioController::class, 'destroyProveedor'])->name('proveedores.destroy');
    Route::post('/proveedores/abastecer', [InventarioController::class, 'procesarAbastecimiento'])->name('proveedores.abastecer');
    Route::get('/vencimientos', [InventarioController::class, 'vencimientos'])->name('vencimientos');
    Route::get('/auditoria', [InventarioController::class, 'auditoria'])->name('auditoria');
    Route::get('/escaner', [InventarioController::class, 'vistaEscaner'])->name('escaner');
    Route::post('/ajustar-stock', [InventarioController::class, 'ajustarStock'])->name('ajustar.stock');
    Route::post('/buscar-codigo', [InventarioController::class, 'buscarPorCodigo'])->name('buscar.codigo');
    Route::post('/productos/escanear', [InventarioController::class, 'escanearProducto'])->name('escanear.producto');
    Route::post('/productos/guardar', [InventarioController::class, 'guardarProducto'])->name('guardar.producto');
    Route::put('/productos/{id}/actualizar', [InventarioController::class, 'actualizarProducto'])->name('productos.actualizar');
    Route::post('/oswa-bot', [InventarioController::class, 'oswaBot'])->name('oswa.bot');
    Route::get('/exportar-pdf', [InventarioController::class, 'exportarPdf'])->name('exportar.pdf');
    Route::delete('/eliminar-producto', [InventarioController::class, 'eliminarProducto'])->name('eliminar.producto');
    Route::get('/productos/{id}/editar', [InventarioController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{id}', [InventarioController::class, 'update'])->name('productos.update');
    Route::patch('/productos/{id}/stock', [InventarioController::class, 'updateStock'])->name('productos.stock');
    Route::delete('/productos/{id}', [InventarioController::class, 'destroy'])->name('productos.destroy');
    Route::post('/transferir-producto', [InventarioController::class, 'transferirProducto'])->name('transferir.producto');
    Route::get('/orden-compra/{id}', [InventarioController::class, 'generarOrdenCompra'])->name('orden.compra');
    
    // --- RUTA AGREGADA: PARA GENERAR EL COMPROBANTE DE TRANSFERENCIA ---
    Route::get('/transferencia/pdf', [InventarioController::class, 'generarPdfTransferencia'])->name('transferencia.pdf');
    
    // --- ADMINISTRACIÓN DE USUARIOS ---
    Route::get('/usuarios', [InventarioController::class, 'indexUsuarios'])->name('usuarios.index');
    Route::post('/usuarios/guardar', [InventarioController::class, 'guardarUsuario'])->name('usuarios.guardar');
    Route::post('/usuarios/estatus', [InventarioController::class, 'cambiarEstatusUsuario'])->name('usuarios.cambiarEstatus');
    
    // --- SISTEMA DE REQUISICIONES ---
    Route::get('/requisiciones/crear', [InventarioController::class, 'crearRequisicion'])->name('requisiciones.crear');
    Route::post('/requisiciones/solicitar', [InventarioController::class, 'solicitarRequisicion'])->name('requisiciones.solicitar');
    Route::post('/requisiciones/{id}/aprobar', [InventarioController::class, 'aprobarRequisicion'])->name('requisiciones.aprobar');
    Route::post('/requisiciones/{id}/rechazar', [InventarioController::class, 'rechazarRequisicion'])->name('requisiciones.rechazar');
    
    // --- RESPALDO DE BASE DE DATOS (Solo Admin) ---
    Route::get('/respaldo-db', [BackupController::class, 'download'])->name('respaldo.db');
    
    // --- CAMBIO DE PERFIL NETFLIX ---
    Route::post('/cambiar-perfil-netflix', function (Request $request) {
        $request->validate(['user_id' => 'required|integer']);
        
        $loginExitoso = Auth::loginUsingId($request->user_id);
        
        if ($loginExitoso) {
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
    })->name('perfil.cambiar');
    
    // --- GESTIÓN DE PERFILES ---
    Route::post('/perfiles/crear', [ProfileController::class, 'store'])->name('perfil.crear');
    Route::post('/perfiles/actualizar/{id}', [ProfileController::class, 'update'])->name('perfil.actualizar');
    Route::delete('/perfiles/eliminar/{id}', [ProfileController::class, 'destroy'])->name('perfil.eliminar');
    
    // --- RUTA TEMPORAL: REPARAR FIRMAS ANTIGUAS ---
    Route::get('/reparar-firmas', function () {
        $registros = Movimiento::whereNull('firma_hash')
                               ->orWhere('firma_hash', '')
                               ->orWhere('firma_hash', 'SIN FIRMA')
                               ->get();

        $contador = 0;

        foreach ($registros as $reg) {
            // Reconstruimos la cadena según la lógica de auditoría vigente
            $cadenaBase = $reg->id . $reg->codigo_producto . $reg->tipo . $reg->cantidad . $reg->motivo . $reg->usuario_accion;
            
            $hash = hash('sha256', $cadenaBase);

            $reg->firma_hash = $hash;
            $reg->save();
            $contador++;
        }

        return "¡Éxito Carlos! Se han recalculado y recuperado las firmas de {$contador} registros antiguos.";
    });
});