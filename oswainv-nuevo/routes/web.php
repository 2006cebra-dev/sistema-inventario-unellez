<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RequisicionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MisionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PriceHistoryController;
use App\Http\Controllers\NotificationController;
use App\Models\Movimiento;

// Ruta pública de la Landing Page
Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Auth::routes();

Route::get('/home', fn() => redirect()->to('/dashboard'))->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [InventarioController::class, 'index'])->name('inventario');
    Route::get('/api/dashboard/graficas', [InventarioController::class, 'getChartsData'])->name('api.graficas');
    // --- CATÁLOGO Y PRODUCTOS ---
    Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('catalogo');
    Route::get('/escaner', [ProductoController::class, 'vistaEscaner'])->name('escaner');
    Route::post('/ajustar-stock', [ProductoController::class, 'ajustarStock'])->name('ajustar.stock');
    Route::post('/buscar-codigo', [ProductoController::class, 'buscarPorCodigo'])->name('buscar.codigo');
    Route::post('/productos/escanear', [ProductoController::class, 'escanearProducto'])->name('escanear.producto');
    Route::post('/productos/guardar', [ProductoController::class, 'guardarProducto'])->name('guardar.producto');
    Route::put('/productos/{id}/actualizar', [ProductoController::class, 'actualizarProducto'])->name('productos.actualizar');
    Route::delete('/eliminar-producto', [ProductoController::class, 'eliminarProducto'])->name('eliminar.producto');
    Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::patch('/productos/{id}/stock', [ProductoController::class, 'updateStock'])->name('productos.stock');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::post('/transferir-producto', [InventarioController::class, 'transferirProducto'])->name('transferir.producto');
    Route::get('/orden-compra/{id}', [InventarioController::class, 'generarOrdenCompra'])->name('orden.compra');
    // --- PROVEEDORES ---
    Route::get('/proveedores', [ProveedorController::class, 'proveedores'])->name('proveedores');
    Route::post('/proveedores/guardar', [ProveedorController::class, 'storeProveedor'])->name('proveedores.store');
    Route::post('/proveedores/{id}/actualizar', [ProveedorController::class, 'updateProveedor'])->name('proveedores.update');
    Route::delete('/proveedores/{id}/eliminar', [ProveedorController::class, 'destroyProveedor'])->name('proveedores.destroy');
    Route::post('/proveedores/abastecer', [ProveedorController::class, 'procesarAbastecimiento'])->name('proveedores.abastecer');
    Route::get('/vencimientos', [InventarioController::class, 'vencimientos'])->name('vencimientos');
    Route::get('/auditoria', [InventarioController::class, 'auditoria'])->name('auditoria');
    Route::post('/oswa-bot', [InventarioController::class, 'oswaBot'])->name('oswa.bot');
    Route::get('/exportar-pdf', [InventarioController::class, 'exportarPdf'])->name('exportar.pdf');
    
    // --- RUTA AGREGADA: PARA GENERAR EL COMPROBANTE DE TRANSFERENCIA ---
    Route::get('/transferencia/pdf', [InventarioController::class, 'generarPdfTransferencia'])->name('transferencia.pdf');
    
    // --- ADMINISTRACIÓN DE USUARIOS ---
    Route::get('/usuarios', [InventarioController::class, 'indexUsuarios'])->name('usuarios.index');
    Route::post('/usuarios/guardar', [InventarioController::class, 'guardarUsuario'])->name('usuarios.guardar');
    Route::post('/usuarios/estatus', [InventarioController::class, 'cambiarEstatusUsuario'])->name('usuarios.cambiarEstatus');
    Route::get('/usuarios/datos/{id}', [InventarioController::class, 'obtenerDatosUsuario'])->name('usuarios.datos');
    Route::post('/usuarios/actualizar', [InventarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::post('/usuarios/eliminar', [InventarioController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    Route::post('/roles/guardar', [InventarioController::class, 'guardarRol'])->name('roles.guardar');
    Route::get('/roles/permisos/{nombre}', [InventarioController::class, 'obtenerPermisosRol'])->name('roles.permisos');
    Route::get('/usuarios/exportar', [InventarioController::class, 'exportarUsuarios'])->name('usuarios.exportar');
    
    // --- SISTEMA DE REQUISICIONES ---
    Route::get('/requisiciones/crear', [RequisicionController::class, 'crearRequisicion'])->name('requisiciones.crear');
    Route::post('/requisiciones/solicitar', [RequisicionController::class, 'solicitarRequisicion'])->name('requisiciones.solicitar');
    Route::post('/requisiciones/{id}/aprobar', [RequisicionController::class, 'aprobarRequisicion'])->name('requisiciones.aprobar');
    Route::post('/requisiciones/{id}/rechazar', [RequisicionController::class, 'rechazarRequisicion'])->name('requisiciones.rechazar');
    
    // --- RESPALDO DE BASE DE DATOS (Solo Admin) ---
    Route::get('/respaldo-db', [InventarioController::class, 'respaldarBaseDatos'])->name('respaldo.db');
    
    // --- QR ---
    Route::get('/qr', [InventarioController::class, 'imprimirQr'])->name('productos.pdf_qr');

    // --- MÓDULO DE DESPACHO RÁPIDO ---
    Route::get('/despacho', [InventarioController::class, 'vistaDespacho'])->name('despacho.vista');
    Route::post('/despacho/procesar', [InventarioController::class, 'procesarDespachoBatch'])->name('despacho.procesar');
    // API interna para actualizar gráficas en vivo
    Route::get('/api/graficas', [InventarioController::class, 'getChartsData'])->name('api.graficas.v2');
    
    // Máquina del Tiempo (Rollback de Auditoría)
    Route::post('/auditoria/revertir/{id}', [InventarioController::class, 'revertirMovimiento'])->name('auditoria.revertir');
    
    // API para estadísticas en tiempo real
    Route::get('/api/stats/global', [InventarioController::class, 'getGlobalStats']);
    // Cierre de caja diario (Reporte de hoy)
    Route::get('/reporte/cierre-diario', [InventarioController::class, 'generarCierreDiario'])->name('reporte.cierre');
    
    // Registrar Orden de Compra (Abastecimiento)
    Route::post('/compras/store', [InventarioController::class, 'storeCompra'])->name('compras.store');
    
    // --- CAMBIO DE PERFIL NETFLIX ---
    Route::post('/cambiar-perfil-netflix', function (Request $request) {
        $request->validate([
            'user_id' => 'required|integer',
            'password' => 'required|string'
        ]);

        $targetUser = \App\Models\User::find($request->user_id);
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Verificar contraseña del perfil DESTINO
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $targetUser->password)) {
            return response()->json(['success' => false, 'message' => 'Contraseña incorrecta']);
        }

        if (!$targetUser->is_active) {
            return response()->json(['success' => false, 'message' => 'Esta cuenta está desactivada']);
        }

        $currentUser = Auth::user();

        Auth::login($targetUser);
        $request->session()->save();

        return response()->json([
            'success' => true,
            'user_name' => $targetUser->display_name,
            'redirect' => route('inventario')
        ]);
    })->name('perfil.cambiar');
    
    // --- GESTIÓN DE PERFILES ---
    Route::post('/perfiles/crear', [ProfileController::class, 'store'])->name('perfil.crear');
    Route::post('/perfiles/actualizar/{id}', [ProfileController::class, 'update'])->name('perfil.actualizar');
    Route::delete('/perfiles/eliminar/{id}', [ProfileController::class, 'destroy'])->name('perfil.eliminar');

    // --- MISIONES ---
    Route::get('/gestion/misiones', [MisionController::class, 'index'])->name('misiones.gestion');
    Route::post('/misiones', [MisionController::class, 'store'])->name('misiones.store');
    Route::post('/misiones/{id}/completar', [MisionController::class, 'completar'])->name('misiones.completar');
    Route::post('/gestion/misiones/{id}/aprobar', [MisionController::class, 'aprobar'])->name('misiones.aprobar');
    Route::post('/gestion/misiones/{id}/rechazar', [MisionController::class, 'rechazar'])->name('misiones.rechazar');
    Route::post('/gestion/misiones/{id}/revertir', [MisionController::class, 'revertir'])->name('misiones.revertir');
    
    // --- PRESENCIA EN VIVO (HEARTBEAT) ---
    Route::post('/api/heartbeat', [PresenceController::class, 'heartbeat'])->name('api.heartbeat');
    Route::get('/api/online-users', [PresenceController::class, 'online'])->name('api.online');

    // --- CHAT INTERNO ---
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/api/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('/api/chat/messages/{userId}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/api/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/api/chat/upload', [ChatController::class, 'upload'])->name('chat.upload');
    Route::get('/api/chat/unread', [ChatController::class, 'unreadCount'])->name('chat.unread');

    // --- HISTORIAL DE PRECIOS ---
    Route::get('/api/price-history/{productoId}', [PriceHistoryController::class, 'index'])->name('price.history');

    // --- OSWA PULSE (NOTIFICACIONES) ---
    Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/api/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    // Route::get('/api/notifications/sse', [NotificationController::class, 'sse'])->name('notifications.sse'); // Deshabilitado - consume proceso PHP con sleep(3)

    // --- PRECIOS POR PROVEEDOR ---
    Route::get('/productos/{id}/proveedores-precio', [ProductoController::class, 'obtenerPreciosProveedor'])->name('productos.proveedores.precio.obtener');
    Route::post('/productos/{id}/proveedores-precio', [ProductoController::class, 'guardarPreciosProveedor'])->name('productos.proveedores.precio');

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