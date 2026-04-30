<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        return view('inventario.index', compact(
            'productos', 'totalProductos', 'stockTotal', 'alertasStock',
            'capitalInvertido', 'tasaBcv', 'capitalInvertidoBs', 'stockSaludable', 'stockCritico',
            'categorias', 'ultimoMovimiento', 'esAdmin'
        ));
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

        if ($diferencia != 0) {
            $fecha = now()->format('Y-m-d H:i:s');
            $firmaDigital = hash('sha256', $producto->codigo . $tipo . abs($diferencia) . $fecha);
            Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => $tipo,
                'cantidad' => abs($diferencia),
                'motivo' => $motivo,
                'usuario_accion' => Auth::user()->name,
                'firma_digital' => $firmaDigital,
                'created_at' => $fecha
            ]);
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
        $fecha = now()->format('Y-m-d H:i:s');
        $firma = hash('sha256', $producto->codigo . 'Salida' . $request->cantidad . $fecha);

        Movimiento::create([
            'codigo_producto' => $producto->codigo,
            'tipo' => 'Salida',
            'cantidad' => $request->cantidad,
            'motivo' => 'Transferencia a ' . $request->sucursal,
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

    // --- GENERADOR DE PDF ---
    public function generarPdfTransferencia(Request $request)
    {
        $datos = $request->all();
        $pdf = PDF::loadView('inventario.pdf_transferencia', $datos);
        return $pdf->download('Guia_Despacho_'.time().'.pdf');
    }

    // --- GUARDAR PRODUCTO ---
    public function guardarProducto(Request $request)
    {
        $request->validate(['codigo' => 'required|unique:productos', 'nombre' => 'required', 'precio' => 'required|numeric', 'stock' => 'required|integer']);
        
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
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
    public function actualizarProducto(Request $request)
    {
        $producto = Producto::findOrFail($request->id);
        $data = $request->only(['nombre', 'codigo', 'marca', 'categoria', 'precio', 'fecha_vencimiento']);
        if ($request->hasFile('imagen')) {
            $name = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->storeAs('productos', $name, 'public');
            $data['imagen'] = 'productos/' . $name;
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
        $fecha = now()->format('Y-m-d H:i:s');
        Movimiento::create([
            'codigo_producto' => $p->codigo, 'tipo' => 'Entrada', 'cantidad' => 1, 'motivo' => 'Escaneo (+1)',
            'usuario_accion' => Auth::user()->name ?? 'Sistema',
            'firma_digital' => hash('sha256', $p->codigo . 'Entrada' . 1 . $fecha),
            'created_at' => $fecha
        ]);
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
    public function generarOrdenCompra($id) { $producto = Producto::findOrFail($id); return view('inventario.orden_compra', compact('producto')); }
    
    public function indexUsuarios()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
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
    
    public function eliminarUsuario(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        
        if (Auth::id() == $request->id) {
            return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta']);
        }
        
        User::destroy($request->id);
        return response()->json(['success' => true]);
    }
}