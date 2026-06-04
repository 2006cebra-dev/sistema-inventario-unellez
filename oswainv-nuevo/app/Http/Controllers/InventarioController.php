<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Requisicion;
use App\Models\Mision;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\BitacoraAcceso;
use App\Models\Proveedor;
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

    public function index()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();

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
        $alertasStock = $productos->filter(fn($p) => $p->stock_bajo)->count();
        $capitalInvertido = $productos->sum(fn($p) => $p->stock * ($p->precio_costo ?: $p->precio));

        $tasaBcv = $this->obtenerTasaBcv();
        $capitalInvertidoBs = $capitalInvertido * $tasaBcv;

        $stockSaludable = $productos->filter(fn($p) => !$p->stock_bajo)->count();
        $stockCritico = $productos->filter(fn($p) => $p->stock_bajo)->count();
        $categorias = $productos->groupBy('categoria')->map(fn($group) => $group->count());
        $ultimoMovimiento = Movimiento::with('producto')->latest()->first();
        $esAdmin = Auth::check() && Auth::user()->tienePermiso('gestionar_productos');

        $productosBajoStock = $productos->filter(fn($p) => $p->stock_bajo);
        $productosPorVencer = Producto::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', \Carbon\Carbon::now()->addDays(30))
            ->get();

        $topVentas = Movimiento::select('codigo_producto', DB::raw('SUM(cantidad) as total_salidas'))
            ->where('tipo', 'Salida')
            ->whereIn('codigo_producto', Producto::pluck('codigo'))
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

        $misRequisiciones = collect();
        $movimientosHoy = 0;
        $requisicionesHoy = 0;
        $notificaciones = collect();
        if (Auth::check() && !Auth::user()->tienePermiso('gestionar_usuarios')) {
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

    public function mapaSucursales()
    {
        $sucursales = config('sucursales');
        $productos = Producto::select('id', 'nombre', 'codigo', 'stock', 'stock_minimo', 'precio')->get();
        return view('inventario.mapa-sucursales', compact('sucursales', 'productos'));
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

        $productos = Producto::all();
        $categorias = $productos->groupBy('categoria')->map(fn($group) => $group->count());

        $diasLabels = [];
        $salidasData = [];

        if ($rango === '7_dias') {
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i);
                $diasLabels[] = $fecha->format('d/m');
                $salidasData[] = (int) Movimiento::where('tipo', 'Salida')
                    ->whereDate('created_at', $fecha)
                    ->sum('cantidad');
            }
        } else {
            $diasEnMes = $fechaInicio->daysInMonth;
            for ($i = 1; $i <= $diasEnMes; $i++) {
                $fecha = $fechaInicio->copy()->addDays($i - 1);
                if ($fecha > now() && $rango === 'este_mes') break;
                $diasLabels[] = $fecha->format('d/m');
                $salidasData[] = (int) Movimiento::where('tipo', 'Salida')
                    ->whereDate('created_at', $fecha)
                    ->sum('cantidad');
            }
        }

        $topVentas = Movimiento::select('codigo_producto', DB::raw('SUM(cantidad) as total_salidas'))
            ->where('tipo', 'Salida')
            ->whereIn('codigo_producto', Producto::pluck('codigo'))
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('codigo_producto')
            ->orderByDesc('total_salidas')
            ->take(5)
            ->get();

        $topLabels = [];
        $topData = [];
        $topPhotos = [];
        $topIds = [];
        foreach ($topVentas as $venta) {
            $prod = Producto::where('codigo', $venta->codigo_producto)->first();
            $topLabels[] = $prod ? $prod->nombre : $venta->codigo_producto;
            $topData[] = (int) $venta->total_salidas;
            $topPhotos[] = $prod && $prod->imagen ? asset('storage/' . $prod->imagen) : null;
            $topIds[] = $prod ? $prod->id : null;
        }

        return response()->json([
            'top_productos' => $topData,
            'top_labels' => $topLabels,
            'top_photos' => $topPhotos,
            'top_ids' => $topIds,
            'categorias' => array_values($categorias->toArray()),
            'categorias_labels' => array_keys($categorias->toArray()),
            'tendencias' => $salidasData,
            'tendencias_labels' => $diasLabels
        ]);
    }

    public function transferirProducto(Request $request)
    {
        $request->validate(['producto_id' => 'required|integer', 'cantidad' => 'required|integer|min:1', 'sucursal' => 'required|string']);
        if (!Auth::check() || !Auth::user()->tienePermiso('gestionar_productos')) return response()->json(['success' => false], 403);

        $producto = Producto::find($request->producto_id);
        if (!$producto || $producto->stock < $request->cantidad) return response()->json(['success' => false, 'message' => 'Stock insuficiente'], 400);

        $sucursales = config('sucursales');
        $destino = $sucursales[$request->sucursal] ?? ['lat' => 10.48, 'lng' => -66.89, 'dist' => 500];
        $fecha = now()->toDateTimeString();

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
            'usuario_accion' => Auth::user()->display_name,
        ]);

        $movimiento->firma_hash = $movimiento->generarFirma();
        $movimiento->save();

        return response()->json([
            'success' => true,
            'producto' => $producto->nombre,
            'distancia' => $destino['dist'],
            'lat' => $destino['lat'],
            'lng' => $destino['lng'],
            'fecha' => $fecha,
            'sucursal' => $request->sucursal,
            'sucursales_full' => $sucursales
        ]);
    }

    public function generarPdfTransferencia(Request $request)
    {
        $datos = $request->all();
        $pdf = PDF::loadView('inventario.pdf_transferencia', $datos);
        return $pdf->download('Guia_Despacho_'.time().'.pdf');
    }

    public function oswaBot(Request $request)
    {
        $preguntaRaw = $request->pregunta;
        $p = mb_strtolower($preguntaRaw, 'UTF-8');
        $usuario = Auth::user();

        $respuestaRespaldo = function($p) use ($usuario) {
            if (preg_match('/hola|buenos|epa|saludos|que tal/', $p)) {
                return "¡Epa! Soy OSWA-Bot. ¿En qué te ayudo hoy con el inventario?";
            }

            if (preg_match('/quien soy|mi perfil|mi rol|mi nombre/', $p)) {
                return "Estás conectado como **{$usuario->display_name}** con privilegios de **{$usuario->rol}**.";
            }

            if (preg_match('/precio de (.+)|cuanto cuesta (.+)/', $p, $matches)) {
                $nombreBusqueda = trim($matches[1] ?? $matches[2]);
                $producto = Producto::where('nombre', 'LIKE', "%{$nombreBusqueda}%")->first();
                return $producto ? "El precio de **{$producto->nombre}** es de **$" . number_format($producto->precio, 2) . "**." : "No conseguí ningún producto llamado '{$nombreBusqueda}'.";
            }

            if (preg_match('/cuanto queda de (.+)|stock de (.+)|cuantos (.+) hay/', $p, $matches)) {
                $nombreBusqueda = trim($matches[1] ?? $matches[2] ?? $matches[3]);
                $producto = Producto::where('nombre', 'LIKE', "%{$nombreBusqueda}%")->first();
                return $producto ? "Quedan **{$producto->stock}** unidades de **{$producto->nombre}** en el depósito." : "No encuentro ese producto para darte el stock.";
            }

            if (preg_match('/vencen|vence|vencimiento|caduca/', $p)) {
                $vencen = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<=', now()->addDays(30))->count();
                return "Tienes **{$vencen}** productos próximos a vencer (en los próximos 30 días).";
            }

            if (preg_match('/bajo stock|falta|alertas|critico/', $p)) {
                $bajo = Producto::bajoStock()->count();
                return "Atención: tienes **{$bajo}** productos con stock crítico (por debajo de su mínimo).";
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

            if (preg_match('/ayuda|comandos|que haces/', $p)) {
                return "Puedo decirte el 'capital', 'vencimientos', 'bajo stock', 'tasa BCV' o el precio/stock de un producto si escribes 'precio de [nombre]'.";
            }

            return "Oye, no capté bien la idea. Intenta preguntarme por el capital, vencimientos o el precio de algo específico.";
        };

        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!empty($apiKey)) {
                $totalP = Producto::count();
                $bajoS = Producto::bajoStock()->count();
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

        return response()->json(['success' => true, 'respuesta' => $respuestaRespaldo($p)]);
    }

    public function auditoria()
    {
        $registrosSinFirma = Movimiento::whereNull('firma_hash')->get();

        foreach ($registrosSinFirma as $reg) {
            $cadenaOriginal = $reg->id . $reg->codigo_producto . $reg->tipo . $reg->cantidad . $reg->motivo . $reg->usuario_accion;

            $reg->firma_hash = hash('sha256', $cadenaOriginal);
            $reg->save();
        }

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

    public function exportarPdf() {
        if (!Auth::check() || !Auth::user()->tienePermiso('exportar_pdf')) abort(403, 'No autorizado');
        $productos = Producto::all();
        return view('inventario.pdf', compact('productos'));
    }

    public function generarOrdenCompra($id)
    {
        $producto = Producto::findOrFail($id);
        $fecha = now()->format('d/m/Y h:i A');

        $stockIdeal = 100;
        $cantidadSugerida = max(0, $stockIdeal - $producto->stock);

        $pdf = PDF::loadView('inventario.orden_compra', compact('producto', 'fecha', 'cantidadSugerida', 'stockIdeal'));
        return $pdf->download('Orden_Compra_' . $producto->codigo . '.pdf');
    }

    public function indexUsuarios()
    {
        if (!Auth::check() || !Auth::user()->tienePermiso('gestionar_usuarios')) abort(403, 'No autorizado');
        $usuarios = User::orderBy('created_at', 'desc')->get();
        $logs = \App\Models\BitacoraAcceso::with('user')->latest()->take(20)->get();
        $rolesUsados = $usuarios->pluck('rol')->unique()->values();
        $rolesCache = \Illuminate\Support\Facades\Cache::get('roles_custom', []);
        $rolesExtra = collect(array_merge(
            $rolesUsados->filter(fn($r) => !in_array($r, ['admin','empleado']))->toArray(),
            $rolesCache,
            ['desarrollador']
        ))->unique()->values();

        $rolesPermisos = \Illuminate\Support\Facades\Cache::get('roles_permisos', []);
        $defaultPermisos = [
            'admin' => ['ver_dashboard','ver_catalogo','ver_proveedores','gestionar_productos','aprobar_requisiciones','ver_auditoria','gestionar_misiones','exportar_pdf','chat'],
            'empleado' => ['ver_dashboard','ver_catalogo','ver_proveedores','chat'],
            'desarrollador' => ['ver_dashboard','ver_catalogo','ver_proveedores','gestionar_productos','gestionar_proveedores','aprobar_requisiciones','gestionar_usuarios','ver_auditoria','gestionar_misiones','gestionar_precios','exportar_pdf','respaldar_bd','chat'],
        ];
        $changed = false;
        foreach ($defaultPermisos as $r => $perms) {
            if (!isset($rolesPermisos[$r])) {
                $rolesPermisos[$r] = $perms;
                $changed = true;
            }
        }
        if ($changed) {
            \Illuminate\Support\Facades\Cache::forever('roles_permisos', $rolesPermisos);
        }

        return view('usuarios.index', compact('usuarios', 'logs', 'rolesExtra'));
    }

    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nick' => 'nullable|string|max:50|unique:users,nick',
            'email' => 'required|email|unique:users',
            'cedula' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'rol' => 'required|string|max:50',
            'password' => 'required|min:6',
            'profile_photo' => 'nullable|image|max:5120'
        ]);

        $data = [
            'name' => $request->name,
            'nick' => $request->nick,
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

    public function obtenerDatosUsuario($id)
    {
        $usuario = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'nick' => $usuario->nick,
                'email' => $usuario->email,
                'cedula' => $usuario->cedula,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol,
                'profile_photo_path' => $usuario->profile_photo_path,
            ]
        ]);
    }

    public function actualizarUsuario(Request $request)
    {
        $usuario = User::findOrFail($request->user_id);
        $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'nick' => 'nullable|string|max:50|unique:users,nick,' . $usuario->id,
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'cedula' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'rol' => 'required|string|max:50',
            'password' => 'nullable|min:6',
            'profile_photo' => 'nullable|image|max:5120'
        ]);

        $usuario->name = $request->name;
        $usuario->nick = $request->nick;
        $usuario->email = $request->email;
        $usuario->cedula = $request->cedula;
        $usuario->telefono = $request->telefono;
        $usuario->rol = $request->rol;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($usuario->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($usuario->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $usuario->profile_photo_path = $path;
        }

        $usuario->save();

        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    }

    public function eliminarUsuario(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        if (Auth::id() == $request->id) {
            return response()->json(['success' => false, 'message' => 'No puedes eliminarte a ti mismo']);
        }
        $usuario = User::findOrFail($request->id);
        if ($usuario->profile_photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($usuario->profile_photo_path);
        }
        $usuario->delete();
        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    }

    public function guardarRol(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'permisos' => 'nullable|array',
            'permisos.*' => 'string|max:50',
            'editar' => 'nullable|boolean',
        ]);
        $nombre = strtolower(trim($request->nombre));
        if (in_array($nombre, ['admin', 'empleado']) && !$request->editar) {
            return response()->json(['success' => false, 'message' => 'No puedes crear roles que ya existen (admin/empleado)']);
        }
        $roles = \Illuminate\Support\Facades\Cache::get('roles_custom', []);
        $rolesPermisos = \Illuminate\Support\Facades\Cache::get('roles_permisos', []);

        if (!$request->editar) {
            if (in_array($nombre, $roles)) {
                return response()->json(['success' => false, 'message' => "El rol '{$nombre}' ya existe"]);
            }
            $roles[] = $nombre;
            \Illuminate\Support\Facades\Cache::forever('roles_custom', $roles);
        }

        $rolesPermisos[$nombre] = $request->permisos ?? [];
        \Illuminate\Support\Facades\Cache::forever('roles_permisos', $rolesPermisos);

        $msg = $request->editar
            ? "Permisos de '{$nombre}' actualizados"
            : "Rol '{$nombre}' creado con " . count($request->permisos ?? []) . " permiso(s)";
        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function obtenerPermisosRol($nombre)
    {
        $rolesPermisos = \Illuminate\Support\Facades\Cache::get('roles_permisos', []);

        if (isset($rolesPermisos[$nombre])) {
            return response()->json(['success' => true, 'permisos' => $rolesPermisos[$nombre]]);
        }

        $defaults = [
            'admin' => ['ver_dashboard','ver_catalogo','ver_proveedores','gestionar_productos','aprobar_requisiciones','ver_auditoria','gestionar_misiones','exportar_pdf','chat'],
            'empleado' => ['ver_dashboard','ver_catalogo','ver_proveedores','chat'],
            'desarrollador' => ['ver_dashboard','ver_catalogo','ver_proveedores','gestionar_productos','gestionar_proveedores','aprobar_requisiciones','gestionar_usuarios','ver_auditoria','gestionar_misiones','gestionar_precios','exportar_pdf','respaldar_bd','chat'],
        ];

        return response()->json([
            'success' => true,
            'permisos' => $defaults[$nombre] ?? [],
        ]);
    }

    public function exportarUsuarios()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        $csv = "Nombre,Nick,Email,Cédula,Teléfono,Rol,Activo,XP,Nivel,Fecha Registro\n";
        foreach ($usuarios as $u) {
            $csv .= '"' . $u->name . '","' . ($u->nick ?? '') . '","' . $u->email . '","' . ($u->cedula ?? '') . '","' . ($u->telefono ?? '') . '","' . $u->rol . '","' . ($u->is_active ? 'Si' : 'No') . '","' . ($u->xp ?? 0) . '","' . ($u->nivel ?? 1) . '","' . $u->created_at->format('d/m/Y') . "\"\n";
        }
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="usuarios_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function respaldarBaseDatos()
    {
        if (!Auth::user()->tienePermiso('respaldar_bd')) {
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

    public function vistaDespacho()
    {
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

            $usuario = Auth::user()->display_name;
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

    public function revertirMovimiento(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->tienePermiso('gestionar_productos')) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            DB::beginTransaction();

            $movOriginal = Movimiento::findOrFail($id);
            $producto = Producto::where('codigo', $movOriginal->codigo_producto)->first();

            if (!$producto) throw new \Exception("El producto original ya no existe.");

            $tipoInverso = $movOriginal->tipo === 'Entrada' ? 'Salida' : 'Entrada';

            if ($tipoInverso === 'Salida' && $producto->stock < $movOriginal->cantidad) {
                throw new \Exception("Stock insuficiente para revertir esta entrada original.");
            }

            if ($tipoInverso === 'Entrada') {
                $producto->increment('stock', $movOriginal->cantidad);
            } else {
                $producto->decrement('stock', $movOriginal->cantidad);
            }

            $movNuevo = Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => $tipoInverso,
                'cantidad' => $movOriginal->cantidad,
                'motivo' => "REVERSIÓN AUDITADA (Compensa Mov. #" . $movOriginal->id . ")",
                'usuario_accion' => Auth::user()->display_name,
                'user_id' => Auth::id()
            ]);

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

    public function getGlobalStats() {
        $productos = Producto::all();
        $capitalInvertido = $productos->sum(fn($p) => $p->stock * ($p->precio_costo ?: $p->precio));
        $tasa = $this->obtenerTasaBcv();

        return response()->json([
            'totalProductos' => $productos->count(),
            'stockTotal' => $productos->sum('stock'),
            'alertasStock' => $productos->filter(fn($p) => $p->stock_bajo)->count(),
            'capitalInvertido' => number_format($capitalInvertido, 2),
            'capitalBs' => number_format($capitalInvertido * $tasa, 2),
            'tasaBcv' => number_format($tasa, 2)
        ]);
    }

    public function generarCierreDiario() {
        $hoy = now()->startOfDay();
        $movimientosHoy = Movimiento::where('created_at', '>=', $hoy)->with('producto')->get();
        $resumen = [
            'entradas' => $movimientosHoy->where('tipo', 'Entrada')->sum('cantidad'),
            'salidas' => $movimientosHoy->where('tipo', 'Salida')->sum('cantidad'),
            'operaciones' => $movimientosHoy->count(),
            'fecha' => now()->format('d/m/Y')
        ];

        $productos = Producto::all();
        $pdf = PDF::loadView('inventario.pdf_cierre', compact('resumen', 'movimientosHoy'));
        return $pdf->download('Cierre_Diario_'.now()->format('d_m_Y').'.pdf');
    }

    public function storeCompra(Request $request)
    {
        if (!Auth::user()->tienePermiso('gestionar_productos')) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
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

            $producto->increment('stock', $request->cantidad);

            $motivoCompra = "COMPRA a " . $proveedor->nombre . " | Costo: $" . $request->costo_total . " | Vence: " . $request->fecha_vencimiento;

            $movimiento = \App\Models\Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Entrada',
                'cantidad' => $request->cantidad,
                'motivo' => $motivoCompra,
                'usuario_accion' => Auth::user()->display_name,
                'user_id' => Auth::id()
            ]);

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

    public function perfil()
    {
        $user = Auth::user();
        $xpActual = $user->xp ?? 0;
        $nivel = $user->nivel ?? 1;
        $xpSiguiente = $nivel * 100;
        $xpBar = min(100, ($xpActual / max($xpSiguiente, 1)) * 100);

        $achievements = \App\Models\Achievement::all();
        $user->load('achievements');
        $desbloqueados = $user->achievements->count();
        $totalLogros = $achievements->count();

        $activityLabels = [];
        $activityData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityLabels[] = now()->subDays($i)->format('d/m');
            $activityData[] = Movimiento::where('user_id', $user->id)
                ->whereDate('created_at', $date)->count();
        }

        return view('inventario.perfil', compact(
            'user', 'xpActual', 'nivel', 'xpSiguiente', 'xpBar',
            'achievements', 'desbloqueados', 'totalLogros',
            'activityLabels', 'activityData'
        ));
    }
}
