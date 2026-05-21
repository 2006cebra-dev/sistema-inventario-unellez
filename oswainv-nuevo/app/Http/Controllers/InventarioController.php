<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Requisicion;
use App\Models\Mision;
use App\Models\Notification;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PDF;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer; 

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    // --- FUNCIÓN PRIVADA: BUSCAR TASA BCV REAL EN VIVO ---
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

    // --- DASHBOARD PRINCIPAL ---
    public function index()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();
        
        // --- BI: PREDICCIÓN DE DÍAS ESTIMADOS DE STOCK ---
        $treintaDiasAtras = now()->subDays(30);
        foreach ($productos as $producto) {
            $salidasRecientes = Movimiento::where('codigo_producto', $producto->codigo)
                ->where('tipo', 'Salida')
                ->where('created_at', '>=', $treintaDiasAtras)
                ->sum('cantidad');
            $promedioDiario = $salidasRecientes / 30;
            if ($promedioDiario > 0) {
                $producto->dias_estimados = round($producto->stock / $promedioDiario);
            } else {
                $producto->dias_estimados = null;
            }
        }
        
        $totalProductos = $productos->count();
        $stockTotal = $productos->sum('stock');
        $alertasStock = $productos->where('stock', '<=', 5)->count();
        $capitalInvertido = $productos->sum(fn($p) => $p->stock * $p->precio);
        
        $tasaBcv = $this->obtenerTasaBcv();
        $capitalInvertidoBs = $capitalInvertido * $tasaBcv;

        $stockSaludable = $productos->where('stock', '>', 5)->count();
        $stockCritico = $productos->where('stock', '<=', 5)->count();
        $categorias = $productos->groupBy('categoria')->map(fn($group) => $group->count());
        $ultimoMovimiento = Movimiento::with('producto')->latest()->first();
        $esAdmin = Auth::check() && Auth::user()->rol === 'admin';

        // Datos para Alertas Visuales
        $productosBajoStock = Producto::where('stock', '<=', 5)->get();
        $productosPorVencer = Producto::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', \Carbon\Carbon::now()->addDays(30))
            ->get();

        // Top 5 Productos Más Vendidos (por Salidas)
        $topVentas = Movimiento::select('codigo_producto', DB::raw('SUM(cantidad) as total_salidas'))
            ->where('tipo', 'Salida')
            ->groupBy('codigo_producto')
            ->orderByDesc('total_salidas')
            ->take(5)
            ->get();

        $nombresProductos = [];
        $ventasProductos = [];
        foreach ($topVentas as $venta) {
            $prod = Producto::where('codigo', $venta->codigo_producto)->first();
            $nombresProductos[] = $prod ? $prod->nombre : $venta->codigo_producto;
            $ventasProductos[] = (int) $venta->total_salidas;
        }

        $users = User::where('is_active', true)->orderBy('name')->get();

        // --- DATOS ESPECÍFICOS PARA EMPLEADO ---
        $misRequisiciones = collect();
        $movimientosHoy = 0;
        $requisicionesHoy = 0;
        $notificaciones = collect();
        if (Auth::check() && Auth::user()->rol === 'empleado') {
            $userId = Auth::id();
            $misRequisiciones = Requisicion::with('producto')->where('user_id', $userId)->orderBy('created_at', 'desc')->take(5)->get();
            $movimientosHoy = Movimiento::where('user_id', $userId)->whereDate('created_at', today())->count();
            $requisicionesHoy = Requisicion::where('user_id', $userId)->whereDate('created_at', today())->count();

            $nuevasMisiones = Mision::where('user_id', $userId)->where('created_at', '>=', now()->subDays(3))->where('estado', 'pendiente')->count();
            $reqAprobadas = Requisicion::where('user_id', $userId)->where('estado', 'Aprobada')->where('updated_at', '>=', now()->subDays(3))->count();
            $reqRechazadas = Requisicion::where('user_id', $userId)->where('estado', 'Rechazada')->where('updated_at', '>=', now()->subDays(3))->count();

            if ($nuevasMisiones > 0) $notificaciones->push(['icon' => 'bi-flag-fill', 'color' => '#ffc107', 'text' => "{$nuevasMisiones} misión(es) nueva(s) asignada(s)"]);
            if ($reqAprobadas > 0) $notificaciones->push(['icon' => 'bi-check-circle-fill', 'color' => '#00b894', 'text' => "{$reqAprobadas} requisición(es) aprobada(s)"]);
            if ($reqRechazadas > 0) $notificaciones->push(['icon' => 'bi-x-circle-fill', 'color' => '#E50914', 'text' => "{$reqRechazadas} requisición(es) rechazada(s)"]);
            if ($movimientosHoy > 0) $notificaciones->push(['icon' => 'bi-arrow-left-right', 'color' => '#0984e3', 'text' => "{$movimientosHoy} movimiento(s) registrado(s) hoy"]);
        }

        $labelsTendencia = [];
        $datosTendencia = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = \Carbon\Carbon::now()->subDays($i);
            $labelsTendencia[] = $fecha->translatedFormat('d-M');
            $datosTendencia[] = (int) Movimiento::where('tipo', 'Salida')
                ->whereDate('created_at', $fecha->format('Y-m-d'))
                ->sum('cantidad');
        }

        return view('inventario.index', compact(
            'productos', 'totalProductos', 'stockTotal', 'alertasStock',
            'capitalInvertido', 'tasaBcv', 'capitalInvertidoBs', 'stockSaludable', 'stockCritico',
            'categorias', 'ultimoMovimiento', 'esAdmin',
            'productosBajoStock', 'productosPorVencer', 'nombresProductos', 'ventasProductos',
            'users', 'labelsTendencia', 'datosTendencia',
            'misRequisiciones', 'movimientosHoy', 'requisicionesHoy', 'notificaciones'
        ));
    }

    public function getChartsData(Request $request)
    {
        $rango = $request->query('rango', '7_dias');
        $fechaInicio = now()->subDays(6)->startOfDay();
        $fechaFin = now()->endOfDay();

        if ($rango === 'este_mes') {
            $fechaInicio = now()->startOfMonth();
        } elseif ($rango === 'mes_pasado') {
            $fechaInicio = now()->subMonth()->startOfMonth();
            $fechaFin = now()->subMonth()->endOfMonth();
        }

        // Top 5 Productos Más Vendidos en ese rango
        $topVentas = Movimiento::select('codigo_producto', DB::raw('SUM(cantidad) as total_salidas'))
            ->where('tipo', 'Salida')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('codigo_producto')
            ->orderByDesc('total_salidas')
            ->take(5)
            ->get();

        $topLabels = [];
        $topData = [];
        foreach ($topVentas as $venta) {
            $prod = Producto::where('codigo', $venta->codigo_producto)->first();
            $topLabels[] = $prod ? $prod->nombre : $venta->codigo_producto;
            $topData[] = (int) $venta->total_salidas;
        }

        // Distribución por Categorías (Stock Actual global)
        $productos = Producto::all();
        $categorias = $productos->groupBy('categoria')->map(fn($group) => $group->count());

        // Tendencia Diaria de Salidas
        $diasLabels = [];
        $salidasData = [];
        
        if ($rango === '7_dias') {
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i);
                $diasLabels[] = $fecha->translatedFormat('d-M');
                $salidasData[] = (int) Movimiento::where('tipo', 'Salida')->whereDate('created_at', $fecha)->sum('cantidad');
            }
        } else {
            // Para meses completos, agrupamos por los días de ese mes
            $diasEnMes = $fechaInicio->daysInMonth;
            for ($i = 1; $i <= $diasEnMes; $i++) {
                $fecha = $fechaInicio->copy()->addDays($i - 1);
                if ($fecha > now() && $rango === 'este_mes') break; // No mostrar futuro
                $diasLabels[] = $fecha->format('d-M');
                $salidasData[] = (int) Movimiento::where('tipo', 'Salida')->whereDate('created_at', $fecha)->sum('cantidad');
            }
        }

        return response()->json([
            'top_productos' => $topData,
            'top_labels' => $topLabels,
            'categorias' => array_values($categorias->toArray()),
            'categorias_labels' => array_keys($categorias->toArray()),
            'tendencias' => $salidasData,
            'tendencias_labels' => $diasLabels
        ]);
    }

    // --- CATÁLOGO GENERAL DE PRODUCTOS ---
    public function catalogo()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();
        $esAdmin = Auth::check() && Auth::user()->rol === 'admin';
        $auditorias = Movimiento::with(['producto', 'usuario'])->orderBy('created_at', 'desc')->limit(200)->get();
        $proveedores = \App\Models\Proveedor::all();
        $requisicionesPendientes = $esAdmin ? Requisicion::with(['user', 'producto'])->where('estado', 'Pendiente')->latest()->get() : [];

        // --- B.I. PREDICCIÓN DE STOCK (Solo para la vista del Catálogo) ---
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

    // --- PROVEEDORES ---
    public function proveedores()
    {
        $proveedores = \App\Models\Proveedor::with('productos')->orderBy('created_at', 'desc')->get();
        $productos = \App\Models\Producto::all();
        return view('inventario.proveedores', compact('proveedores'));
    }

    public function storeProveedor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'required|string|unique:proveedores,rif',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'rif' => $request->rif,
            'contacto' => $request->contacto,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('proveedores', 'public');
        }

        \App\Models\Proveedor::create($data);

        return response()->json(['success' => true]);
    }

    public function updateProveedor(Request $request, $id)
    {
        $proveedor = \App\Models\Proveedor::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'required|string|unique:proveedores,rif,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $data = $request->only(['nombre', 'rif', 'contacto', 'telefono', 'direccion']);

        if ($request->hasFile('logo')) {
            if ($proveedor->logo) {
                Storage::disk('public')->delete($proveedor->logo);
            }
            $data['logo'] = $request->file('logo')->store('proveedores', 'public');
        }

        $proveedor->update($data);
        return response()->json(['success' => true]);
    }

    public function destroyProveedor($id)
    {
        $proveedor = \App\Models\Proveedor::findOrFail($id);
        $proveedor->delete();
        return response()->json(['success' => true]);
    }

    public function procesarAbastecimiento(Request $request)
    {
        $request->validate([ 'producto_id' => 'required', 'cantidad' => 'required|numeric|min:1' ]);

        $producto = Producto::findOrFail($request->producto_id);
        $producto->stock += $request->cantidad;
        $producto->save();

        try {
            // Abastecimiento de proveedor SIEMPRE es Entrada
            $tipoMovimiento = 'Entrada';
            $cantidadMovimiento = $request->cantidad;

            // Crear el movimiento
            $movimiento = new Movimiento();
            $movimiento->codigo_producto = $producto->codigo;
            $movimiento->tipo = $tipoMovimiento;
            $movimiento->cantidad = $cantidadMovimiento;
            $movimiento->motivo = 'Orden de Abastecimiento';
            $movimiento->usuario_accion = Auth::user()->name;
            $movimiento->user_id = Auth::id();
            $movimiento->save();

            // Generar la firma SHA-256 después de obtener el ID
            $cadena = $movimiento->id . $movimiento->codigo_producto . $movimiento->tipo . $movimiento->cantidad . $movimiento->motivo . $movimiento->usuario_accion;
            $movimiento->firma_hash = hash('sha256', $cadena);
            $movimiento->save();
        } catch (\Exception $e) {
            \Log::error('Error creando movimiento de abastecimiento: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    // --- AJUSTE MANUAL DE STOCK ---
    public function ajustarStock(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'accion' => 'required|string']);
        if (!Auth::check()) return response()->json(['success' => false], 401);

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

        // XP por ajuste
        if ($diferencia > 0) {
            GamificationService::addXp(Auth::user(), 'stock_entry', "Entrada de {$producto->nombre}");
        } elseif ($diferencia < 0) {
            GamificationService::addXp(Auth::user(), 'stock_exit', "Salida de {$producto->nombre}");
        }

        // Notificar a admins si stock es crítico
        if ($producto->stock <= 5 && $producto->stock > 0) {
            $admins = User::where('rol', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'stock_alert',
                    'title' => '🚨 Stock crítico: ' . $producto->nombre,
                    'message' => "Quedan {$producto->stock} unidades. Código: {$producto->codigo}",
                    'icon' => 'bi-exclamation-triangle-fill text-danger',
                    'link' => route('catalogo'),
                ]);
            }
        }

        // Disparador de Telegram si el stock cae a nivel crítico
        if ($producto->stock <= 5) {
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

        // 1. Calcular si es Entrada o Salida basado en la diferencia
        if ($diferencia > 0) {
            $tipoMovimiento = 'Entrada';
            $cantidadMovimiento = $diferencia;
        } elseif ($diferencia < 0) {
            $tipoMovimiento = 'Salida';
            $cantidadMovimiento = abs($diferencia);
        }

        // 2. Crear el movimiento solo si hubo cambio real
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

                // 3. Generar la firma SHA-256 después de obtener el ID
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
            'alertas_stock' => Producto::where('stock', '<=', 5)->count(),
            'capital_invertido' => $capitalInvertidoNuevo,
            'capital_invertido_bs' => $capitalInvertidoNuevo * $tasaBcv,
            'tasa_bcv' => $tasaBcv
        ]);
    }

    // --- TRANSFERENCIA ---
    public function transferirProducto(Request $request)
    {
        $request->validate(['producto_id' => 'required|integer', 'cantidad' => 'required|integer|min:1', 'sucursal' => 'required|string']);
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false], 403);

        $producto = Producto::find($request->producto_id);
        if (!$producto || $producto->stock < $request->cantidad) return response()->json(['success' => false, 'message' => 'Stock insuficiente'], 400);

        $sucursales = [
            'Caracas' => ['lat' => 10.4806, 'lng' => -66.8983, 'dist' => 500],
            'Maracaibo' => ['lat' => 10.6427, 'lng' => -71.6125, 'dist' => 450],
            'Valencia' => ['lat' => 10.1620, 'lng' => -68.0077, 'dist' => 350],
            'Barquisimeto' => ['lat' => 10.0678, 'lng' => -69.3474, 'dist' => 280],
            'San Cristóbal' => ['lat' => 7.7669, 'lng' => -72.2250, 'dist' => 320],
            'Mérida' => ['lat' => 8.5912, 'lng' => -71.1434, 'dist' => 170],
            'Puerto La Cruz' => ['lat' => 10.2167, 'lng' => -64.6333, 'dist' => 820],
            'Maturín' => ['lat' => 9.7458, 'lng' => -63.1767, 'dist' => 950],
            'Ciudad Guayana' => ['lat' => 8.2913, 'lng' => -62.7092, 'dist' => 1100],
            'Coro' => ['lat' => 11.4045, 'lng' => -69.6734, 'dist' => 480],
            'Cumaná' => ['lat' => 10.4616, 'lng' => -64.1824, 'dist' => 750],
            'Guanare' => ['lat' => 9.0418, 'lng' => -69.7421, 'dist' => 90],
            'San Juan de los Morros' => ['lat' => 9.9115, 'lng' => -67.3538, 'dist' => 430],
            'Trujillo' => ['lat' => 9.3701, 'lng' => -70.4350, 'dist' => 160],
            'San Felipe' => ['lat' => 10.3399, 'lng' => -68.7452, 'dist' => 310],
            'Barcelona' => ['lat' => 10.1362, 'lng' => -64.6862, 'dist' => 810],
            'Porlamar' => ['lat' => 10.9575, 'lng' => -63.8697, 'dist' => 900],
            'La Guaira' => ['lat' => 10.5992, 'lng' => -66.9347, 'dist' => 520],
            'San Fernando de Apure' => ['lat' => 7.8878, 'lng' => -67.4724, 'dist' => 380],
            'Puerto Ayacucho' => ['lat' => 5.6639, 'lng' => -67.6236, 'dist' => 600],
            'Tucupita' => ['lat' => 9.0611, 'lng' => -62.0510, 'dist' => 1200],
            'San Carlos' => ['lat' => 9.6593, 'lng' => -68.5833, 'dist' => 260],
        ];

        $destino = $sucursales[$request->sucursal] ?? ['lat' => 10.48, 'lng' => -66.89, 'dist' => 500];
        $costoFlete = $destino['dist'] * 0.25;

        $producto->decrement('stock', $request->cantidad);
        
        $motivoTransfer = 'Transferencia a ' . $request->sucursal;
        if ($request->filled('soporte_base64')) {
            $image_parts = explode(";base64,", $request->soporte_base64);
            if (isset($image_parts[1])) {
                $image_base64 = base64_decode($image_parts[1]);
                $nameSoporte = time() . '_soporte_' . $producto->codigo . '.jpg';
                Storage::disk('public')->put('soportes/' . $nameSoporte, $image_base64);
                $motivoTransfer .= " | Soporte: " . $nameSoporte;
            }
        }

        $movimiento = Movimiento::create([
            'codigo_producto' => $producto->codigo,
            'tipo' => 'Salida',
            'cantidad' => $request->cantidad,
            'motivo' => $motivoTransfer,
            'usuario_accion' => Auth::user()->name,
        ]);

        $movimiento->firma_hash = $movimiento->generarFirma();
        $movimiento->save();

        GamificationService::addXp(Auth::user(), 'transfer_made', "Transferencia a {$request->sucursal}: {$request->cantidad}x {$producto->nombre}");

        return response()->json([
            'success' => true,
            'producto' => $producto->nombre,
            'distancia' => $destino['dist'],
            'costo_flete' => $costoFlete,
            'lat' => $destino['lat'],
            'lng' => $destino['lng'],
            'fecha' => $fecha,
            'sucursal' => $request->sucursal,
            'sucursales_full' => $sucursales
        ]);
    }

    // --- GENERADOR DE PDF TRANSFERENCIA ---
    public function generarPdfTransferencia(Request $request)
    {
        $datos = $request->all();
        $pdf = PDF::loadView('inventario.pdf_transferencia', $datos);
        return $pdf->download('Guia_Despacho_'.time().'.pdf');
    }

    // --- GUARDAR PRODUCTO ---
    public function guardarProducto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|unique:productos,codigo',
            'precio' => 'required|numeric',
            'categoria' => 'required|string|max:255',
            'stock' => 'required|integer',
            'imagen' => 'nullable|image|max:2048',
            'imagen_url' => 'nullable|url',
            'fecha_vencimiento' => 'nullable|date'
        ]);
        
        $producto = new Producto();
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->categoria = $request->categoria ?? 'General';
        $producto->stock = $request->stock;
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

        GamificationService::addXp(Auth::user(), 'product_created', "Producto creado: {$producto->nombre}");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto registrado en el catálogo exitosamente.',
                'producto' => $producto
            ]);
        }

        return redirect()->back()->with('success', 'Producto registrado correctamente.');
    }

    // --- ACTUALIZAR PRODUCTO ---
    public function actualizarProducto(Request $request, $id)
    {
        $producto = \App\Models\Producto::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'imagen' => 'nullable|image|max:2048',
            'fecha_vencimiento' => 'nullable|date'
        ]);

        $oldPrice = $producto->precio;
        $data = $request->except(['_method', '_token', 'imagen']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        // Record price change
        if ((float)$oldPrice !== (float)$request->precio) {
            \App\Models\PriceHistory::create([
                'producto_id' => $producto->id,
                'precio_anterior' => $oldPrice,
                'precio_nuevo' => $request->precio,
                'user_id' => Auth::id(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    // --- SCANNER ---
    public function escanearProducto(Request $request)
    {
        $p = Producto::where('codigo', $request->codigo)->first();
        if (!$p) return response()->json(['success' => false, 'notFound' => true]);
        $p->increment('stock', 1);
        try {
            $movimiento = Movimiento::create([
                'codigo_producto' => $p->codigo, 'tipo' => 'Entrada', 'cantidad' => 1, 'motivo' => 'Escaneo (+1)',
                'usuario_accion' => Auth::user()->name ?? 'Sistema',
                'user_id' => Auth::id()
            ]);
            
            // Generar hash post-creación para tener el ID
            $movimiento->firma_hash = $movimiento->generarFirma();
            $movimiento->save();
        } catch (\Exception $e) {
            \Log::error('Error creando movimiento de escaneo: ' . $e->getMessage());
        }
        return response()->json(['success' => true, 'producto' => $p, 'nuevo_stock' => $p->stock]);
    }

    // --- OSWA-BOT HÍBRIDO (GEMINI + RESPALDO INTELIGENTE) ---
    public function oswaBot(Request $request)
    {
        $preguntaRaw = $request->pregunta;
        $p = mb_strtolower($preguntaRaw, 'UTF-8');
        $usuario = Auth::user();

        // --- LÓGICA DE RESPALDO (PALABRAS CLAVE Y REGEX) ---
        $respuestaRespaldo = function($p) use ($usuario) {
            // Saludos y cortesía
            if (preg_match('/hola|buenos|epa|saludos|que tal/', $p)) {
                return "¡Epa! Soy OSWA-Bot. ¿En qué te ayudo hoy con el inventario?";
            }

            // Perfil del usuario conectado
            if (preg_match('/quien soy|mi perfil|mi rol|mi nombre/', $p)) {
                return "Estás conectado como **{$usuario->name}** con privilegios de **{$usuario->rol}**.";
            }

            // Búsqueda de PRECIO de un producto específico
            if (preg_match('/precio de (.+)|cuanto cuesta (.+)/', $p, $matches)) {
                $nombreBusqueda = trim($matches[1] ?? $matches[2]);
                $producto = Producto::where('nombre', 'LIKE', "%{$nombreBusqueda}%")->first();
                return $producto ? "El precio de **{$producto->nombre}** es de **$" . number_format($producto->precio, 2) . "**." : "No conseguí ningún producto llamado '{$nombreBusqueda}'.";
            }

            // Búsqueda de STOCK de un producto específico
            if (preg_match('/cuanto queda de (.+)|stock de (.+)|cuantos (.+) hay/', $p, $matches)) {
                $nombreBusqueda = trim($matches[1] ?? $matches[2] ?? $matches[3]);
                $producto = Producto::where('nombre', 'LIKE', "%{$nombreBusqueda}%")->first();
                return $producto ? "Quedan **{$producto->stock}** unidades de **{$producto->nombre}** en el depósito." : "No encuentro ese producto para darte el stock.";
            }

            // Reportes generales (Stock, Vencimientos, Capital)
            if (preg_match('/vencen|vence|vencimiento|caduca/', $p)) {
                $vencen = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<=', now()->addDays(30))->count();
                return "Tienes **{$vencen}** productos próximos a vencer (en los próximos 30 días).";
            }

            if (preg_match('/bajo stock|falta|alertas|critico/', $p)) {
                $bajo = Producto::where('stock', '<=', 5)->count();
                return "Atención: tienes **{$bajo}** productos con stock crítico (5 o menos unidades).";
            }

            if (preg_match('/total|invertido|capital|dinero/', $p)) {
                $capital = Producto::all()->sum(fn($prod) => $prod->stock * $prod->precio);
                return "El capital total invertido es de **$" . number_format($capital, 2) . "**.";
            }

            if (preg_match('/cuanto producto|cantidad|cuantos hay/', $p)) {
                return "Actualmente tienes **" . Producto::count() . "** productos diferentes registrados.";
            }

            if (preg_match('/tasa|bcv|dolar|precio del dolar/', $p)) {
                return "La tasa oficial del BCV actual es de **" . $this->obtenerTasaBcv() . " Bs/USD**.";
            }

            // Ayuda
            if (preg_match('/ayuda|comandos|que haces/', $p)) {
                return "Puedo decirte el 'capital', 'vencimientos', 'bajo stock', 'tasa BCV' o el precio/stock de un producto si escribes 'precio de [nombre]'.";
            }

            return "Oye, no capté bien la idea. Intenta preguntarme por el capital, vencimientos o el precio de algo específico.";
        };

        // --- INTENTO CON GEMINI ---
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!empty($apiKey)) {
                $totalP = Producto::count();
                $bajoS = Producto::where('stock', '<=', 5)->count();
                $capitalT = Producto::all()->sum(fn($prod) => $prod->stock * $prod->precio);
                $vencenP = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<=', now()->addDays(30))->count();
                $ultimoM = Movimiento::latest()->first();
                $txtM = $ultimoM ? "Ult. Mov: {$ultimoM->tipo} ({$ultimoM->cantidad} unds) de {$ultimoM->codigo_producto}." : "Sin movs.";

                $prompt = "Eres OSWA-Bot. Contexto: {$totalP} productos, {$bajoS} bajo stock, {$vencenP} vencen pronto. Capital: $" . number_format($capitalT, 2) . ". {$txtM}. Responde corto a: '{$preguntaRaw}'";

                $response = Http::timeout(8)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $resIA = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($resIA) return response()->json(['success' => true, 'respuesta' => $resIA]);
                }
            }
        } catch (\Exception $e) {
            Log::info("Error Gemini: " . $e->getMessage());
        }

        // --- SIEMPRE RESPONDE EL RESPALDO SI LA IA FALLA ---
        return response()->json(['success' => true, 'respuesta' => $respuestaRespaldo($p)]);
    }

    // --- AUDITORÍA Y VENCIMIENTOS ---
    public function auditoria()
    {
        // 1. AUTO-FIRMAR REGISTROS ANTIGUOS (Usa el modelo Movimiento)
        // Buscamos donde firma_hash sea NULL
        $registrosSinFirma = Movimiento::whereNull('firma_hash')->get();

        foreach ($registrosSinFirma as $reg) {
            // Concatenamos la data VITAL con los nombres EXACTOS de la BD
            $cadenaOriginal = $reg->id . $reg->codigo_producto . $reg->tipo . $reg->cantidad . $reg->motivo . $reg->usuario_accion;
            
            // Generamos el hash y lo guardamos en la columna firma_hash
            $reg->firma_hash = hash('sha256', $cadenaOriginal);
            $reg->save();
        }

        // 2. Traer los movimientos para la vista
        $movimientos = Movimiento::orderBy('created_at', 'desc')->get();
        
        return view('inventario.auditoria', compact('movimientos'));
    }

    public function vencimientos()
    {
        $vencidos = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', now()->toDateString())->get();
        $porVencer = Producto::whereNotNull('fecha_vencimiento')->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays(30)->toDateString()])->get();
        $saludables = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '>', now()->addDays(30)->toDateString())->get();
        return view('inventario.vencimientos', compact('vencidos', 'porVencer', 'saludables'));
    }

    // --- QR ---
    public function imprimirQr() {
        $productos = Producto::all();
        $qrCodes = [];

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        foreach ($productos as $producto) {
            $svg = $writer->writeString($producto->codigo);
            $qrCodes[$producto->id] = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }

        $pdf = PDF::loadView('inventario.qr', compact('productos', 'qrCodes'));
        return $pdf->stream('codigos_qr_' . time() . '.pdf');
    }

    // --- OTROS MÉTODOS ---
    public function exportarPdf() {
        if (!Auth::check() || Auth::user()->rol !== 'admin') abort(403, 'No autorizado');
        $productos = Producto::all();
        return view('inventario.pdf', compact('productos'));
    }
    public function eliminarProducto(Request $request) { Producto::destroy($request->id); return response()->json(['success' => true]); }
    public function vistaEscaner() { return view('inventario.escaner'); }
    
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('inventario.editar', compact('producto'));
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
        $producto = Producto::findOrFail($id);
        $cantidadAnterior = $producto->stock;
        $nuevaCantidad = $request->cantidad;
        
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

        return response()->json(['success' => true, 'nueva_cantidad' => $producto->stock]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return redirect()->back()->with('success', 'Producto eliminado del sistema.');
    }
    
    // --- GENERAR ORDEN DE COMPRA (CORREGIDO DE NUEVO Y CON LÓGICA MATEMÁTICA) ---
    public function generarOrdenCompra($id) 
    { 
        $producto = Producto::findOrFail($id); 
        $fecha = now()->format('d/m/Y h:i A'); 
        
        // Lógica de inventario:
        $stockIdeal = 100; // Asumimos que el número ideal de mercancía en el depósito es 100
        $cantidadSugerida = max(0, $stockIdeal - $producto->stock); // Si tienes 20, te sugiere comprar 80. Si tienes más de 100, sugiere 0.
        
        $pdf = PDF::loadView('inventario.orden_compra', compact('producto', 'fecha', 'cantidadSugerida', 'stockIdeal')); 
        return $pdf->download('Orden_Compra_' . $producto->codigo . '.pdf'); 
    }
    
    public function indexUsuarios()
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') abort(403, 'No autorizado');
        $usuarios = User::orderBy('created_at', 'desc')->get();
        $logs = \App\Models\BitacoraAcceso::with('user')->latest()->take(20)->get();
        return view('usuarios.index', compact('usuarios', 'logs'));
    }
    
    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'cedula' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'rol' => 'required|in:admin,empleado',
            'password' => 'required|min:6',
            'profile_photo' => 'nullable|image|max:5120'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        User::create($data);
        
        return response()->json(['success' => true, 'message' => 'Empleado registrado correctamente']);
    }
    
    public function cambiarEstatusUsuario(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        
        if (Auth::id() == $request->id) {
            return response()->json(['success' => false, 'message' => 'No puedes cambiar tu propio estatus']);
        }
        
        $usuario = User::findOrFail($request->id);
        $usuario->is_active = !$usuario->is_active;
        $usuario->save();
        
        $accion = $usuario->is_active ? 'activado' : 'suspendido';
        return response()->json(['success' => true, 'message' => "Usuario {$accion}", 'is_active' => $usuario->is_active]);
    }

    // --- SISTEMA DE REQUISICIONES ---
    public function crearRequisicion()
    {
        $productos = Producto::where('stock', '>', 0)->orderBy('nombre', 'asc')->get();
        return view('inventario.requisiciones.crear', compact('productos'));
    }

    public function solicitarRequisicion(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*' => 'required|integer|min:1'
        ]);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Debes iniciar sesión para hacer una solicitud.');
        }

        try {
            DB::beginTransaction();

            foreach ($request->productos as $producto_id => $cantidad) {
                Requisicion::create([
                    'user_id' => Auth::id(),
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'estado' => 'Pendiente'
                ]);
            }

            DB::commit();

            $admins = User::where('rol', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'requisition_created',
                    'title' => '📋 Nueva requisición de ' . Auth::user()->name,
                    'message' => count($request->productos) . ' producto(s) solicitado(s)',
                    'icon' => 'bi-file-earmark-text-fill',
                    'link' => route('inventario'),
                ]);
            }

            return redirect()->route('catalogo')->with('success', '¡Solicitud enviada al Administrador exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al enviar la solicitud: ' . $e->getMessage());
        }
    }

    public function aprobarRequisicion($id)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        try {
            DB::beginTransaction();

            $requisicion = Requisicion::with('producto')->findOrFail($id);

            if ($requisicion->estado !== 'Pendiente') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'La requisición ya fue procesada.']);
            }

            $producto = $requisicion->producto;
            if (!$producto) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Producto no encontrado.']);
            }

            if ($producto->stock < $requisicion->cantidad) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Stock insuficiente para aprobar esta solicitud.']);
            }

            $producto->decrement('stock', $requisicion->cantidad);

            $mov = Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Salida',
                'cantidad' => $requisicion->cantidad,
                'motivo' => 'Requisición Aprobada #' . $requisicion->id,
                'usuario_accion' => Auth::user()->name,
            ]);
            
            $mov->firma_hash = $mov->generarFirma();
            $mov->save();

            $requisicion->estado = 'Aprobada';
            $requisicion->save();

            DB::commit();

            Notification::create([
                'user_id' => $requisicion->user_id,
                'type' => 'requisition_approved',
                'title' => '✅ Requisición aprobada',
                'message' => "Tu solicitud de {$requisicion->producto->nombre} fue aprobada",
                'icon' => 'bi-check-circle-fill text-success',
                'link' => route('catalogo'),
            ]);

            return response()->json(['success' => true, 'message' => 'Requisición aprobada y stock actualizado.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar: ' . $e->getMessage()]);
        }
    }

    public function rechazarRequisicion($id)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        $requisicion = Requisicion::findOrFail($id);
        $requisicion->estado = 'Rechazada';
        $requisicion->save();

        Notification::create([
            'user_id' => $requisicion->user_id,
            'type' => 'requisition_rejected',
            'title' => '❌ Requisición rechazada',
            'message' => "Tu solicitud de {$requisicion->producto->nombre} fue rechazada",
            'icon' => 'bi-x-circle-fill text-danger',
            'link' => route('catalogo'),
        ]);

        return response()->json(['success' => true, 'message' => 'Requisición rechazada.']);
    }

    public function respaldarBaseDatos()
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'No autorizado');
        }

        $sql = "-- Respaldo de Base de Datos OSWA Inv\n";
        $sql .= "-- Generado: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET time_zone = \"+00:00\";\n\n";
        $sql .= "START TRANSACTION;\n\n";

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";

        foreach ($tables as $tableRow) {
            $tableName = $tableRow->$key;

            $createStatement = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- Estructura de tabla: {$tableName}\n";
            $sql .= $createStatement[0]->{'Create Table'} . ";\n\n";

            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $sql .= "-- Datos de tabla: {$tableName}\n";
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = array_keys($rowArray);
                $values = array_map(function ($value) use ($pdo) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return $pdo->quote($value);
                }, $rowArray);

                $columnList = implode(', ', array_map(fn($col) => "`{$col}`", $columns));
                $valueList = implode(', ', $values);
                $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES ({$valueList});\n";
            }
            $sql .= "\n";
        }

        $sql .= "COMMIT;\n";

        $fileName = 'respaldo_inventario_' . now()->format('Ymd_His') . '.sql';
        $tempPath = storage_path('app/' . $fileName);
        file_put_contents($tempPath, $sql);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // --- MÓDULO DE DESPACHO RÁPIDO (BATCH) ---
    public function vistaDespacho()
    {
        // Se eliminó 'codigo_barras' para evitar el QueryException, la tabla solo usa 'codigo'
        $productos = Producto::where('stock', '>', 0)->get(['id', 'codigo', 'nombre', 'precio', 'stock']);
        return view('inventario.despacho', compact('productos'));
    }

    public function procesarDespachoBatch(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.cantidad' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $usuario = Auth::user()->name;
            $userId = Auth::id();

            foreach ($request->items as $item) {
                $producto = Producto::find($item['id']);
                
                if (!$producto || $producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto: " . ($producto ? $producto->nombre : 'Desconocido'));
                }

                $producto->decrement('stock', $item['cantidad']);

                $movimiento = Movimiento::create([
                    'codigo_producto' => $producto->codigo,
                    'tipo' => 'Salida',
                    'cantidad' => $item['cantidad'],
                    'motivo' => 'Despacho Rápido (Lote)',
                    'usuario_accion' => $usuario,
                    'user_id' => $userId
                ]);

                $cadena = $movimiento->id . $movimiento->codigo_producto . $movimiento->tipo . $movimiento->cantidad . $movimiento->motivo . $movimiento->usuario_accion;
                $movimiento->firma_hash = hash('sha256', $cadena);
                $movimiento->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Despacho procesado y encriptado exitosamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- MÁQUINA DEL TIEMPO (ROLLBACK CRIPTOGRÁFICO) ---
    public function revertirMovimiento(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            DB::beginTransaction();

            $movOriginal = Movimiento::findOrFail($id);
            $producto = Producto::where('codigo', $movOriginal->codigo_producto)->first();

            if (!$producto) throw new \Exception("El producto original ya no existe.");

            // La operación inversa: Si salió, entra. Si entró, sale.
            $tipoInverso = $movOriginal->tipo === 'Entrada' ? 'Salida' : 'Entrada';

            // Validar que haya stock si vamos a revertir una entrada (convertirla en salida)
            if ($tipoInverso === 'Salida' && $producto->stock < $movOriginal->cantidad) {
                throw new \Exception("Stock insuficiente para revertir esta entrada original.");
            }

            // Ajustar el stock real
            if ($tipoInverso === 'Entrada') {
                $producto->increment('stock', $movOriginal->cantidad);
            } else {
                $producto->decrement('stock', $movOriginal->cantidad);
            }

            // Crear el movimiento compensatorio
            $movNuevo = Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => $tipoInverso,
                'cantidad' => $movOriginal->cantidad,
                'motivo' => "REVERSIÓN AUDITADA (Compensa Mov. #" . $movOriginal->id . ")",
                'usuario_accion' => Auth::user()->name,
                'user_id' => Auth::id()
            ]);

            // Generar nuevo Hash SHA-256
            $cadena = $movNuevo->id . $movNuevo->codigo_producto . $movNuevo->tipo . $movNuevo->cantidad . $movNuevo->motivo . $movNuevo->usuario_accion;
            $movNuevo->firma_hash = hash('sha256', $cadena);
            $movNuevo->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transacción compensada y encriptada exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- ESTADÍSTICAS GLOBALES PARA TIEMPO REAL ---
    public function getGlobalStats() {
        $productos = Producto::all();
        $capitalInvertido = $productos->sum(fn($p) => $p->stock * $p->precio);
        $tasa = $this->obtenerTasaBcv();
        
        return response()->json([
            'totalProductos' => $productos->count(),
            'stockTotal' => $productos->sum('stock'),
            'alertasStock' => $productos->where('stock', '<=', 5)->count(),
            'capitalInvertido' => number_format($capitalInvertido, 2),
            'capitalBs' => number_format($capitalInvertido * $tasa, 2),
            'tasaBcv' => number_format($tasa, 2)
        ]);
    }

    // --- CIERRE DE CAJA DIARIO ---
    public function generarCierreDiario() {
        $hoy = now()->startOfDay();
        $movimientosHoy = Movimiento::where('created_at', '>=', $hoy)->with('producto')->get();
        $resumen = [
            'entradas' => $movimientosHoy->where('tipo', 'Entrada')->sum('cantidad'),
            'salidas' => $movimientosHoy->where('tipo', 'Salida')->sum('cantidad'),
            'operaciones' => $movimientosHoy->count(),
            'fecha' => now()->format('d/m/Y')
        ];
        
        // Usamos la misma vista de PDF pero filtrada solo para hoy
        $productos = Producto::all(); 
        $pdf = PDF::loadView('inventario.pdf_cierre', compact('resumen', 'movimientosHoy'));
        return $pdf->download('Cierre_Diario_'.now()->format('d_m_Y').'.pdf');
    }

    // --- REGISTRAR ORDEN DE COMPRA Y ABASTECER ---
    public function storeCompra(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required',
            'producto_id' => 'required',
            'cantidad' => 'required|numeric|min:1',
            'costo_total' => 'required|numeric',
            'fecha_vencimiento' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $producto = \App\Models\Producto::findOrFail($request->producto_id);
            $proveedor = \App\Models\Proveedor::findOrFail($request->proveedor_id);

            // 1. Aumentar el stock del producto
            $producto->increment('stock', $request->cantidad);

            // 2. Registrar el movimiento para la Auditoría
            $motivoCompra = "COMPRA a " . $proveedor->nombre . " | Costo: $" . $request->costo_total . " | Vence: " . $request->fecha_vencimiento;
            
            $movimiento = \App\Models\Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Entrada',
                'cantidad' => $request->cantidad,
                'motivo' => $motivoCompra,
                'usuario_accion' => Auth::user()->name,
                'user_id' => Auth::id()
            ]);

            // 3. Sellar con SHA-256
            $cadena = $movimiento->id . $movimiento->codigo_producto . $movimiento->tipo . $movimiento->cantidad . $movimiento->motivo . $movimiento->usuario_accion;
            $movimiento->firma_hash = hash('sha256', $cadena);
            $movimiento->save();

            DB::commit();
            return redirect()->back()->with('success', 'Orden de compra registrada. Stock actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }
}