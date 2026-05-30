<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Notification;
use App\Models\Proveedor;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function catalogo()
    {
        $productos = Producto::orderBy('created_at', 'desc')->paginate(48);
        $esAdmin = Auth::check() && Auth::user()->rol === 'admin';
        $auditorias = Movimiento::with(['producto', 'usuario'])->orderBy('created_at', 'desc')->limit(200)->get();
        $proveedores = Proveedor::all();
        $requisicionesPendientes = $esAdmin ? Requisicion::with(['user', 'producto'])->where('estado', 'Pendiente')->latest()->get() : [];

        $treintaDiasAtras = now()->subDays(30);
        foreach ($productos as $producto) {
            $salidasRecientes = Movimiento::where('codigo_producto', $producto->codigo)
                ->where('tipo', 'Salida')
                ->where('created_at', '>=', $treintaDiasAtras)
                ->sum('cantidad');

            $promedioDiario = $salidasRecientes / 30;

            if ($promedioDiario > 0 && $producto->stock > 0) {
                $diasRestantes = round($producto->stock / $promedioDiario);
                $producto->fecha_agotamiento = now()->addDays($diasRestantes)->translatedFormat('d \d\e F, Y');
            } else {
                $producto->fecha_agotamiento = null;
            }
        }

        return view('inventario.catalogo', compact('productos', 'esAdmin', 'auditorias', 'proveedores', 'requisicionesPendientes'));
    }

    public function guardarProducto(Request $request)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|unique:productos,codigo',
            'precio' => 'required|numeric',
            'precio_costo' => 'nullable|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'stock' => 'required|integer',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'unidad_medida' => 'nullable|string|max:20',
            'imagen' => 'nullable|image|max:2048',
            'imagen_url' => 'nullable|url',
            'fecha_vencimiento' => 'nullable|date'
        ]);

        if ($request->stock_maximo && (int)$request->stock > (int)$request->stock_maximo) {
            return response()->json([
                'success' => false,
                'error' => "El stock ({$request->stock}) supera el máximo permitido ({$request->stock_maximo})."
            ], 422);
        }

        $producto = new Producto();
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->precio_costo = $request->has('precio_costo') && $request->precio_costo !== '' && $request->precio_costo !== null ? $request->precio_costo : null;
        $producto->categoria = $request->categoria ?? 'General';
        $producto->stock = $request->stock;
        $producto->stock_minimo = $request->has('stock_minimo') && $request->stock_minimo !== '' && $request->stock_minimo !== null ? $request->stock_minimo : 5;
        $producto->stock_maximo = $request->has('stock_maximo') && $request->stock_maximo !== '' && $request->stock_maximo !== null ? $request->stock_maximo : null;
        $producto->unidad_medida = $request->has('unidad_medida') && $request->unidad_medida !== '' && $request->unidad_medida !== null ? $request->unidad_medida : 'unidad';
        $producto->descripcion = $request->nombre;
        $producto->proveedor_id = $request->proveedor_id ?? null;

        if ($request->filled('fecha_vencimiento')) {
            $producto->fecha_vencimiento = $request->fecha_vencimiento;
        }

        if ($request->hasFile('imagen')) {
            $ruta = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $ruta;
        } elseif ($request->filled('imagen_url')) {
            try {
                $content = file_get_contents($request->imagen_url);
                if ($content) {
                    $name = time() . '_api_' . $request->codigo . '.jpg';
                    Storage::disk('public')->put('productos/' . $name, $content);
                    $producto->imagen = 'productos/' . $name;
                }
            } catch (\Exception $e) { $producto->imagen = null; }
        } elseif ($request->filled('imagen_base64')) {
            $image_parts = explode(";base64,", $request->imagen_base64);
            if (isset($image_parts[1])) {
                $image_base64 = base64_decode($image_parts[1]);
                $name = time() . '_camara_' . $request->codigo . '.jpg';
                Storage::disk('public')->put('productos/' . $name, $image_base64);
                $producto->imagen = 'productos/' . $name;
            }
        }

        $producto->save();

        if ($request->proveedor_id) {
            $producto->proveedores()->syncWithoutDetaching([
                $request->proveedor_id => ['precio_costo' => $request->precio_costo]
            ]);
        }

        if ($request->stock > 0) {
            $mov = Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Entrada',
                'cantidad' => $request->stock,
                'motivo' => 'Stock inicial',
                'usuario_accion' => Auth::user()->name,
            ]);
            $mov->firma_hash = $mov->generarFirma();
            $mov->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto registrado en el catálogo exitosamente.',
                'producto' => $producto
            ]);
        }

        return redirect()->back()->with('success', 'Producto registrado correctamente.');
    }

    public function actualizarProducto(Request $request, $id)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $producto = \App\Models\Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'precio_costo' => 'nullable|numeric|min:0',
            'stock' => 'required|integer',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'unidad_medida' => 'nullable|string|max:20',
            'imagen' => 'nullable|image|max:2048',
            'fecha_vencimiento' => 'nullable|date'
        ]);

        if ($request->stock_maximo && (int)$request->stock > (int)$request->stock_maximo) {
            return response()->json([
                'success' => false,
                'error' => "El stock ({$request->stock}) supera el máximo permitido ({$request->stock_maximo})."
            ], 422);
        }

        $oldPrice = $producto->precio;
        $data = $request->except(['_method', '_token', 'imagen']);

        foreach (['precio_costo', 'stock_minimo', 'stock_maximo', 'unidad_medida', 'proveedor_id', 'marca'] as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        if (empty($data['unidad_medida'])) {
            $data['unidad_medida'] = 'unidad';
        }
        if (empty($data['stock_minimo'])) {
            $data['stock_minimo'] = 5;
        }

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        if ($request->proveedor_id) {
            $producto->proveedores()->syncWithoutDetaching([
                $request->proveedor_id => ['precio_costo' => $request->precio_costo]
            ]);
        }

        if ((float)$oldPrice !== (float)$request->precio) {
            \App\Models\PriceHistory::create([
                'producto_id' => $producto->id,
                'precio_anterior' => $oldPrice,
                'precio_nuevo' => $request->precio,
                'user_id' => Auth::id(),
            ]);
        }

        if ($producto->stock_maximo && $producto->stock > $producto->stock_maximo) {
            $admins = \App\Models\User::where('rol', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'stock_alert',
                    'title' => '📦 Stock excede máximo: ' . $producto->nombre,
                    'message' => "Stock actual: {$producto->stock} {$producto->unidad_medida}. Máximo: {$producto->stock_maximo}.",
                    'icon' => 'bi bi-exclamation-triangle-fill text-warning',
                    'link' => route('catalogo'),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $oldPrice = $producto->precio;
        $producto->update($request->all());

        if ((float)$oldPrice !== (float)$request->precio) {
            \App\Models\PriceHistory::create([
                'producto_id' => $producto->id,
                'precio_anterior' => $oldPrice,
                'precio_nuevo' => $request->precio,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect('/catalogo')->with('success', 'Producto actualizado correctamente.');
    }

    public function updateStock(Request $request, $id)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $producto = Producto::findOrFail($id);
        $cantidadAnterior = $producto->stock;
        $nuevaCantidad = (int)$request->cantidad;

        if ($producto->stock_maximo && $nuevaCantidad > $producto->stock_maximo) {
            return response()->json([
                'success' => false,
                'error' => "Stock máximo: {$producto->stock_maximo} {$producto->unidad_medida}. No puede excederlo.",
                'stock_actual' => $producto->stock
            ], 422);
        }

        $diferencia = $nuevaCantidad - $cantidadAnterior;

        $producto->stock = $nuevaCantidad;
        $producto->save();

        if ($diferencia < 0) {
            $mov = Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Salida',
                'cantidad' => abs($diferencia),
                'motivo' => 'Ajuste manual de stock',
                'usuario_accion' => Auth::user()->name ?? 'Sistema',
                'user_id' => Auth::id(),
            ]);
            $mov->firma_hash = $mov->generarFirma();
            $mov->save();
        }

        if ($producto->stock_maximo && $nuevaCantidad > $producto->stock_maximo) {
            $admins = \App\Models\User::where('rol', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'stock_alert',
                    'title' => '📦 Stock excede máximo: ' . $producto->nombre,
                    'message' => "Stock actual: {$nuevaCantidad} {$producto->unidad_medida}. Máximo: {$producto->stock_maximo}.",
                    'icon' => 'bi bi-exclamation-triangle-fill text-warning',
                    'link' => route('catalogo'),
                ]);
            }
        }

        return response()->json(['success' => true, 'nueva_cantidad' => $producto->stock]);
    }

    public function destroy($id)
    {
        if (Auth::user()->rol !== 'admin') {
            return redirect()->back()->with('error', 'No autorizado');
        }
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return redirect()->back()->with('success', 'Producto eliminado del sistema.');
    }

    public function eliminarProducto(Request $request)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        Producto::destroy($request->id);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('inventario.editar', compact('producto'));
    }

    public function ajustarStock(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'accion' => 'required|string']);
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }

        $producto = Producto::find($request->id);
        if (!$producto) return response()->json(['success' => false], 404);

        $stockAnterior = $producto->stock;
        $diferencia = 0;
        $tipoMovimiento = '';
        $cantidadMovimiento = 0;
        $motivo = '';

        if ($request->accion === 'sumar') {
            $producto->stock += 1;
            $diferencia = 1;
            $motivo = 'Ajuste rápido (+1)';
        } elseif ($request->accion === 'restar' && $producto->stock > 0) {
            $producto->stock -= 1;
            $diferencia = -1;
            $motivo = 'Ajuste rápido (-1)';
        } elseif ($request->accion === 'set') {
            $nuevoStock = max(0, (int) $request->valor);
            $diferencia = $nuevoStock - $stockAnterior;
            $motivo = "Stock establecido a $nuevoStock";
            $producto->stock = $nuevoStock;
        }

        $producto->save();

        if ($producto->stock_bajo && $producto->stock > 0) {
            $admins = User::where('rol', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'stock_alert',
                    'title' => '🚨 Stock crítico: ' . $producto->nombre,
                    'message' => "Quedan {$producto->stock} unidades. Mínimo: {$producto->stock_minimo}. Código: {$producto->codigo}",
                    'icon' => 'bi-exclamation-triangle-fill text-danger',
                    'link' => route('catalogo'),
                ]);
            }
        }

        if ($producto->stock_bajo) {
            $mensaje = "🚨 *ALERTA DE INVENTARIO OSWA Inv* 🚨\n\n";
            $mensaje .= "El producto *{$producto->nombre}* ha alcanzado un nivel crítico de stock.\n";
            $mensaje .= "📦 Unidades restantes: *{$producto->stock}*\n";
            $mensaje .= "Recomendación: Emitir orden de abastecimiento pronto.";

            $telegramToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if ($telegramToken && $chatId) {
                try {
                    Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $mensaje,
                        'parse_mode' => 'Markdown'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error enviando alerta de Telegram: ' . $e->getMessage());
                }
            }
        }

        if ($request->filled('soporte_base64')) {
            $image_parts = explode(";base64,", $request->soporte_base64);
            if (isset($image_parts[1])) {
                $image_base64 = base64_decode($image_parts[1]);
                $nameSoporte = time() . '_soporte_' . $producto->codigo . '.jpg';
                Storage::disk('public')->put('soportes/' . $nameSoporte, $image_base64);
                $motivo .= " | Soporte: " . $nameSoporte;
            }
        }

        if ($diferencia > 0) {
            $tipoMovimiento = 'Entrada';
            $cantidadMovimiento = $diferencia;
        } elseif ($diferencia < 0) {
            $tipoMovimiento = 'Salida';
            $cantidadMovimiento = abs($diferencia);
        }

        if ($diferencia != 0) {
            try {
                $movimiento = new Movimiento();
                $movimiento->codigo_producto = $producto->codigo;
                $movimiento->tipo = $tipoMovimiento;
                $movimiento->cantidad = $cantidadMovimiento;
                $movimiento->motivo = $motivo;
                $movimiento->usuario_accion = Auth::user()->name;
                $movimiento->user_id = Auth::id();
                $movimiento->save();

                $cadena = $movimiento->id . $movimiento->codigo_producto . $movimiento->tipo . $movimiento->cantidad . $movimiento->motivo . $movimiento->usuario_accion;
                $movimiento->firma_hash = hash('sha256', $cadena);
                $movimiento->save();
            } catch (\Exception $e) {
                \Log::error('Error creando movimiento de auditoría: ' . $e->getMessage());
            }
        }

        $capitalInvertidoNuevo = Producto::all()->sum(function ($p) { return $p->stock * $p->precio; });
        $tasaBcv = $this->obtenerTasaBcv();

        return response()->json([
            'success' => true,
            'nuevo_stock' => $producto->stock,
            'stock_total' => Producto::sum('stock'),
            'alertas_stock' => Producto::bajoStock()->count(),
            'capital_invertido' => $capitalInvertidoNuevo,
            'capital_invertido_bs' => $capitalInvertidoNuevo * $tasaBcv,
            'tasa_bcv' => $tasaBcv
        ]);
    }

    public function buscarPorCodigo(Request $request)
    {
        $producto = Producto::where('codigo', $request->codigo)->first();

        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado']);
        }

        return response()->json(['success' => true, 'producto' => $producto]);
    }

    public function escanearProducto(Request $request)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $p = Producto::where('codigo', $request->codigo)->first();
        if (!$p) return response()->json(['success' => false, 'notFound' => true]);
        $p->increment('stock', 1);
        try {
            $movimiento = Movimiento::create([
                'codigo_producto' => $p->codigo, 'tipo' => 'Entrada', 'cantidad' => 1, 'motivo' => 'Escaneo (+1)',
                'usuario_accion' => Auth::user()->name ?? 'Sistema',
                'user_id' => Auth::id()
            ]);

            $movimiento->firma_hash = $movimiento->generarFirma();
            $movimiento->save();
        } catch (\Exception $e) {
            \Log::error('Error creando movimiento de escaneo: ' . $e->getMessage());
        }
        return response()->json(['success' => true, 'producto' => $p, 'nuevo_stock' => $p->stock]);
    }

    public function vistaEscaner()
    {
        return view('inventario.escaner');
    }

    public function obtenerPreciosProveedor($id)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $producto = \App\Models\Producto::with('proveedores')->findOrFail($id);
        return response()->json($producto->proveedores);
    }

    public function guardarPreciosProveedor(Request $request, $id)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $producto = \App\Models\Producto::findOrFail($id);
        $proveedorId = $request->proveedor_id;
        $precioCosto = $request->precio_costo;
        $codigoProveedor = $request->codigo_proveedor;

        if (!$proveedorId) {
            return response()->json(['success' => false, 'error' => 'Selecciona un proveedor.']);
        }

        $producto->proveedores()->syncWithoutDetaching([
            $proveedorId => [
                'precio_costo' => $precioCosto ?: null,
                'codigo_proveedor' => $codigoProveedor ?: null,
            ]
        ]);

        if ($producto->proveedor_id == $proveedorId && $precioCosto) {
            $producto->precio_costo = $precioCosto;
            $producto->save();
        }

        return response()->json(['success' => true]);
    }

    private function obtenerTasaBcv()
    {
        try {
            $response = Http::timeout(3)->get('https://ve.dolarapi.com/v1/dolares/oficial');
            if ($response->successful()) {
                return $response->json()['promedio'];
            }
        } catch (\Exception $e) {
            return 39.50;
        }
        return 39.50;
    }
}
