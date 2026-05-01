<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PDF; 

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
        
        $requisicionesPendientes = [];
        if ($esAdmin) {
            $requisicionesPendientes = Requisicion::with(['user', 'producto'])->where('estado', 'Pendiente')->latest()->get();
        }

        // Datos para Alertas Visuales
        $productosBajoStock = Producto::where('stock', '<=', 5)->get();
        $productosPorVencer = Producto::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', \Carbon\Carbon::now()->addDays(30))
            ->get();

        return view('inventario.index', compact(
            'productos', 'totalProductos', 'stockTotal', 'alertasStock',
            'capitalInvertido', 'tasaBcv', 'capitalInvertidoBs', 'stockSaludable', 'stockCritico',
            'categorias', 'ultimoMovimiento', 'esAdmin', 'requisicionesPendientes',
            'productosBajoStock', 'productosPorVencer'
        ));
    }

    // --- CATÁLOGO GENERAL DE PRODUCTOS ---
    public function catalogo()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();
        $esAdmin = Auth::check() && Auth::user()->rol === 'admin';
        $auditorias = Movimiento::with(['producto', 'usuario'])->orderBy('created_at', 'desc')->limit(200)->get();
        $proveedores = \App\Models\Proveedor::all();
        return view('inventario.catalogo', compact('productos', 'esAdmin', 'auditorias', 'proveedores'));
    }

    // --- PROVEEDORES ---
    public function proveedores()
    {
        $proveedores = \App\Models\Proveedor::with('productos')->orderBy('created_at', 'desc')->get();
        return view('inventario.proveedores', compact('proveedores'));
    }

    public function storeProveedor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'required|string|unique:proveedores,rif'
        ]);

        \App\Models\Proveedor::create([
            'nombre' => $request->nombre,
            'rif' => $request->rif,
            'contacto' => $request->contacto,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateProveedor(Request $request, $id)
    {
        $proveedor = \App\Models\Proveedor::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'required|string|unique:proveedores,rif,' . $id
        ]);
        $proveedor->update($request->all());
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
            $fecha = now()->format('Y-m-d H:i:s');
            $movimiento = new Movimiento([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Entrada',
                'cantidad' => $request->cantidad,
                'motivo' => 'Orden de Abastecimiento',
                'usuario_accion' => Auth::user()->name,
                'user_id' => Auth::id(),
                'created_at' => $fecha
            ]);
            $movimiento->firma_hash = $movimiento->generarFirma();
            $movimiento->firma_digital = hash('sha256', $producto->codigo . 'Entrada' . $request->cantidad . $fecha);
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
        $diferencia = 0; $tipo = ''; $motivo = '';

        if ($request->accion === 'sumar') { 
            $producto->stock += 1; $diferencia = 1; $tipo = 'Entrada'; $motivo = 'Ajuste rápido (+1)'; 
        } elseif ($request->accion === 'restar' && $producto->stock > 0) { 
            $producto->stock -= 1; $diferencia = 1; $tipo = 'Salida'; $motivo = 'Ajuste rápido (-1)'; 
        } elseif ($request->accion === 'set') {
            $nuevoStock = max(0, (int) $request->valor);
            $diferencia = $nuevoStock - $stockAnterior;
            $tipo = $diferencia > 0 ? 'Entrada' : 'Salida';
            $motivo = "Stock establecido a $nuevoStock";
            $producto->stock = $nuevoStock;
        }

        $producto->save();

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

        if ($diferencia != 0) {
            try {
                $fecha = now()->format('Y-m-d H:i:s');
                $movimiento = new Movimiento([
                    'codigo_producto' => $producto->codigo,
                    'tipo' => $tipo,
                    'cantidad' => abs($diferencia),
                    'motivo' => $motivo,
                    'usuario_accion' => Auth::user()->name,
                    'user_id' => Auth::id(),
                    'created_at' => $fecha
                ]);
                $movimiento->firma_hash = $movimiento->generarFirma();
                $movimiento->firma_digital = hash('sha256', $producto->codigo . $tipo . abs($diferencia) . $fecha);
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

        $fecha = now()->format('Y-m-d H:i:s');
        $firma = hash('sha256', $producto->codigo . 'Salida' . $request->cantidad . $fecha);

        Movimiento::create([
            'codigo_producto' => $producto->codigo,
            'tipo' => 'Salida',
            'cantidad' => $request->cantidad,
            'motivo' => $motivoTransfer,
            'usuario_accion' => Auth::user()->name,
            'firma_digital' => $firma,
            'created_at' => $fecha
        ]);

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
        $request->validate(['codigo' => 'required|unique:productos', 'nombre' => 'required', 'precio' => 'required|numeric', 'stock' => 'required|integer', 'fecha_vencimiento' => 'nullable|date']);
        
        $imagenPath = null;
        if ($request->filled('imagen_base64')) {
            $image_parts = explode(";base64,", $request->imagen_base64);
            if (isset($image_parts[1])) {
                $image_base64 = base64_decode($image_parts[1]);
                $name = time() . '_camara_' . $request->codigo . '.jpg';
                Storage::disk('public')->put('productos/' . $name, $image_base64);
                $imagenPath = 'productos/' . $name;
            }
        } elseif ($request->hasFile('imagen')) {
            $name = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->storeAs('productos', $name, 'public');
            $imagenPath = 'productos/' . $name;
        } elseif ($request->filled('imagen_url')) {
            try {
                $content = file_get_contents($request->imagen_url);
                if ($content) {
                    $name = time() . '_api_' . $request->codigo . '.jpg';
                    Storage::disk('public')->put('productos/' . $name, $content);
                    $imagenPath = 'productos/' . $name;
                }
            } catch (\Exception $e) { $imagenPath = null; }
        }

        $producto = Producto::create(array_merge($request->all(), [
            'imagen' => $imagenPath, 
            'descripcion' => $request->nombre,
            'categoria' => $request->categoria ?? 'General'
        ]));

        if ($request->stock > 0) {
            $fecha = now()->format('Y-m-d H:i:s');
            Movimiento::create([
                'codigo_producto' => $producto->codigo, 'tipo' => 'Entrada', 'cantidad' => $request->stock,
                'motivo' => 'Stock inicial', 'usuario_accion' => Auth::user()->name,
                'firma_digital' => hash('sha256', $producto->codigo . 'Entrada' . $request->stock . $fecha),
                'created_at' => $fecha
            ]);
        }
        return response()->json(['success' => true]);
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

        $data = $request->except(['_method', '_token', 'imagen']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return response()->json(['success' => true]);
    }

    // --- SCANNER ---
    public function escanearProducto(Request $request)
    {
        $p = Producto::where('codigo', $request->codigo)->first();
        if (!$p) return response()->json(['success' => false, 'notFound' => true]);
        $p->increment('stock', 1);
        try {
            $fecha = now()->format('Y-m-d H:i:s');
            $movimiento = new Movimiento([
                'codigo_producto' => $p->codigo, 'tipo' => 'Entrada', 'cantidad' => 1, 'motivo' => 'Escaneo (+1)',
                'usuario_accion' => Auth::user()->name ?? 'Sistema',
                'user_id' => Auth::id(),
                'created_at' => $fecha
            ]);
            $movimiento->firma_hash = $movimiento->generarFirma();
            $movimiento->firma_digital = hash('sha256', $p->codigo . 'Entrada' . 1 . $fecha);
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
        $movimientos = Movimiento::orderBy('created_at', 'desc')->get();
        $movimientos = $movimientos->map(function ($mov) {
            $firmaVerificar = hash('sha256', $mov->codigo_producto . $mov->tipo . $mov->cantidad . $mov->created_at->format('Y-m-d H:i:s'));
            $mov->firma_valida = ($mov->firma_digital === $firmaVerificar);
            return $mov;
        });
        return view('inventario.auditoria', ['movimientos' => $movimientos, 'esAdmin' => Auth::user()?->rol === 'admin']);
    }

    public function vencimientos()
    {
        $vencidos = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', now()->toDateString())->get();
        $porVencer = Producto::whereNotNull('fecha_vencimiento')->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays(30)->toDateString()])->get();
        $saludables = Producto::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '>', now()->addDays(30)->toDateString())->get();
        return view('inventario.vencimientos', compact('vencidos', 'porVencer', 'saludables'));
    }

    // --- OTROS MÉTODOS ---
    public function exportarPdf() { $productos = Producto::all(); return view('inventario.pdf', compact('productos')); }
    public function eliminarProducto(Request $request) { Producto::destroy($request->id); return response()->json(['success' => true]); }
    public function vistaEscaner() { return view('inventario.escaner'); }
    
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
        $usuarios = User::orderBy('created_at', 'desc')->get();
        $logs = \App\Models\BitacoraAcceso::with('user')->latest()->take(20)->get();
        return view('usuarios.index', compact('usuarios', 'logs'));
    }
    
    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'rol' => 'required|in:admin,empleado',
            'password' => 'required|min:6'
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $request->rol,
            'password' => Hash::make($request->password)
        ]);
        
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
    public function solicitarRequisicion(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
            'cantidad' => 'required|integer|min:1'
        ]);

        if (!Auth::check()) return response()->json(['success' => false], 401);

        Requisicion::create([
            'user_id' => Auth::id(),
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'estado' => 'Pendiente'
        ]);

        return response()->json(['success' => true]);
    }

    public function aprobarRequisicion(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        $requisicion = Requisicion::with('producto')->findOrFail($request->id);
        $requisicion->estado = 'Aprobada';
        $requisicion->save();

        $producto = $requisicion->producto;
        if ($producto && $producto->stock >= $requisicion->cantidad) {
            $producto->decrement('stock', $requisicion->cantidad);
            $fecha = now()->format('Y-m-d H:i:s');
            $firma = hash('sha256', $producto->codigo . 'Salida' . $requisicion->cantidad . $fecha);
            Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Salida',
                'cantidad' => $requisicion->cantidad,
                'motivo' => 'Requisición Aprobada #' . $requisicion->id,
                'usuario_accion' => Auth::user()->name,
                'firma_digital' => $firma,
                'created_at' => $fecha
            ]);
            return response()->json(['success' => true, 'message' => 'Requisición aprobada y stock actualizado']);
        }

        return response()->json(['success' => false, 'message' => 'Stock insuficiente para aprobar esta requisición']);
    }

    public function rechazarRequisicion(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        $requisicion = Requisicion::findOrFail($request->id);
        $requisicion->estado = 'Rechazada';
        $requisicion->save();

        return response()->json(['success' => true, 'message' => 'Requisición rechazada']);
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
}