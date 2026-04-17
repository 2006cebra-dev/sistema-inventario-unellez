<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 
use Barryvdh\DomPDF\Facade\Pdf; 

class InventarioController extends Controller
{
    public function index() 
    {
        $productos = DB::table('productos')->orderBy('updated_at', 'desc')->get();
        
        $totalProductos = $productos->count();
        $totalUnidades = $productos->sum('stock');
        $bajoStock = $productos->where('stock', '<=', 5)->count();

        // --- 📊 LÓGICA PARA GRÁFICAS ---
        $conteoCategorias = DB::table('productos')
            ->select('categoria', DB::raw('count(*) as total'))
            ->groupBy('categoria')
            ->get();

        $labelsCat = $conteoCategorias->pluck('categoria');
        $dataCat = $conteoCategorias->pluck('total');

        return view('index', compact('productos', 'totalProductos', 'totalUnidades', 'bajoStock', 'labelsCat', 'dataCat'));
    }

    public function crear() { return view('crear'); }

    public function store(Request $request) 
    {
        $codigoLimpio = trim($request->codigo);
        $request->validate([
            'codigo' => 'required|unique:productos,codigo', 
            'nombre' => 'required|string|max:255',
            'stock'  => 'required|integer|min:0',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '-' . $codigoLimpio . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $fotoPath = '/uploads/' . $filename;
        }

        DB::table('productos')->insert([
            'codigo'            => $codigoLimpio,
            'nombre'            => $request->nombre,
            'marca'             => $request->marca,
            'categoria'         => $request->categoria ?? 'General',
            'descripcion'       => $request->descripcion,
            'imagen'            => $fotoPath,
            'stock'             => $request->stock,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'created_at'        => now(), 
            'updated_at'        => now(),
        ]);

        DB::table('movimientos')->insert([
            'codigo_producto' => $codigoLimpio,
            'tipo'            => 'Entrada',
            'cantidad'        => $request->stock,
            'motivo'          => 'Registro inicial del producto',
            'created_at'      => now(), 'updated_at' => now(),
        ]);

        return redirect('/')->with('success', '✅ Producto guardado con éxito');
    }

    public function ajustarStock(Request $request, $id) 
    {
        $producto = DB::table('productos')->where('id', $id)->first();
        if (!$producto) return response()->json(['status' => 'error', 'mensaje' => 'No encontrado']);

        $accion = $request->accion; 
        $cantidad = 1;
        $nuevoStock = ($accion == 'sumar') ? $producto->stock + $cantidad : $producto->stock - $cantidad;

        if ($accion == 'restar' && $producto->stock <= 0) {
            return response()->json(['status' => 'error', 'mensaje' => 'Sin stock disponible']);
        }

        DB::table('productos')->where('id', $id)->update([
            'stock' => $nuevoStock,
            'updated_at' => now()
        ]);

        DB::table('movimientos')->insert([
            'codigo_producto' => $producto->codigo,
            'tipo'            => $accion == 'sumar' ? 'Entrada' : 'Salida',
            'cantidad'        => $cantidad,
            'motivo'          => 'Ajuste rápido (+/-) desde Dashboard',
            'created_at'      => now(), 'updated_at' => now(),
        ]);

        if ($nuevoStock <= 2) {
            $this->enviarAlertaTelegram($producto->nombre, $nuevoStock);
        }

        return response()->json(['status' => 'success', 'nuevo_stock' => $nuevoStock]);
    }

    public function registrar(Request $request) {
        $codigo = trim($request->codigo_barras);
        $producto = DB::table('productos')->where('codigo', $codigo)->first();

        if ($producto) {
            $nuevoStock = $producto->stock + 1;
            DB::table('productos')->where('codigo', $codigo)->increment('stock', 1);

            DB::table('movimientos')->insert([
                'codigo_producto' => $codigo, 'tipo' => 'Entrada', 'cantidad' => 1,
                'motivo' => 'Escaneo rápido (Suma)', 'created_at' => now(), 'updated_at' => now(),
            ]);

            return response()->json([
                'status'  => 'success', 'nombre'  => $producto->nombre, 'marca'   => $producto->marca ?? 'Genérico',
                'stock'   => $nuevoStock, 'foto'    => $producto->imagen, 'mensaje' => "✅ Stock +1"
            ]);
        }

        $urlFood = "https://world.openfoodfacts.org/api/v2/product/{$codigo}.json";
        $responseFood = Http::get($urlFood);

        if ($responseFood->successful() && isset($responseFood['product'])) {
            $api = $responseFood['product'];
            return $this->guardarProductoDetectado($codigo, $api['product_name'] ?? 'Nuevo', $api['brands'] ?? 'Genérico', $api['image_url'] ?? null, $this->detectarCategoriaFood($api));
        }

        $urlGeneral = "https://api.upcitemdb.com/prod/trial/lookup?upc={$codigo}";
        $responseGeneral = Http::get($urlGeneral);

        if ($responseGeneral->successful() && isset($responseGeneral['items']) && count($responseGeneral['items']) > 0) {
            $item = $responseGeneral['items'][0];
            return $this->guardarProductoDetectado($codigo, $item['title'] ?? 'Nuevo', $item['brand'] ?? 'Genérico', $item['images'][0] ?? null, 'General');
        }

        return response()->json(['status' => 'not_found', 'codigo' => $codigo]); 
    }

    public function exportarExcel() {
        $productos = DB::table('productos')->get();
        $filename = "Inventario_OSWA_" . date('d-m-Y') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Código', 'Nombre', 'Marca', 'Categoría', 'Stock', 'Última Actualización'], ';');
        foreach ($productos as $p) {
            fputcsv($output, [$p->codigo, $p->nombre, $p->marca, $p->categoria, $p->stock, $p->updated_at], ';');
        }
        fclose($output);
    }

    // --- 🔐 MEJORA 2: RESPALDO DE BASE DE DATOS (CORREGIDO PARA XAMPP) ---
    public function respaldarDB() 
    {
        $nombreArchivo = "Respaldo_OSWA_" . date('d-m-Y_H-i-s') . ".sql";
        $ruta = storage_path('app/' . $nombreArchivo);

        $usuario = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $baseDeDatos = env('DB_DATABASE', 'inventario_db');
        $host = env('DB_HOST', '127.0.0.1');

        // Dirección exacta de la herramienta en XAMPP
        $dumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe"; 

        if (empty($password)) {
            $comando = "{$dumpPath} --user={$usuario} --host={$host} {$baseDeDatos} > \"{$ruta}\"";
        } else {
            $comando = "{$dumpPath} --user={$usuario} --password={$password} --host={$host} {$baseDeDatos} > \"{$ruta}\"";
        }

        try {
            exec($comando);

            if (file_exists($ruta) && filesize($ruta) > 0) {
                return response()->download($ruta)->deleteFileAfterSend(true);
            } else {
                return redirect('/')->with('error', '❌ El archivo no se generó. Revisa la ruta de XAMPP.');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function generarPdf() {
        $productos = DB::table('productos')->get();
        $totalUnidades = $productos->sum('stock');
        $pdf = Pdf::loadView('pdf.reporte', ['productos' => $productos, 'totalUnidades' => $totalUnidades, 'fecha' => date('d-m-Y')]);
        return $pdf->stream('Reporte_Inventario_Barinas.pdf');
    }

    public function eliminar($codigo) {
        DB::table('productos')->where('codigo', $codigo)->delete();
        return redirect('/')->with('success', 'Producto eliminado');
    }

    public function editar($id) {
        $producto = DB::table('productos')->where('id', $id)->first();
        return view('editar', compact('producto'));
    }

    public function actualizar(Request $request, $id) {
        $producto = DB::table('productos')->where('id', $id)->first();
        $diferencia = $request->stock - $producto->stock;
        
        $datos = [
            'nombre' => $request->nombre, 'marca' => $request->marca, 'categoria' => $request->categoria,
            'descripcion' => $request->descripcion, 'stock' => $request->stock, 
            'fecha_vencimiento' => $request->fecha_vencimiento, 'updated_at' => now()
        ];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '-' . str_replace(' ', '_', $request->nombre) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $datos['imagen'] = '/uploads/' . $filename;
        }

        DB::table('productos')->where('id', $id)->update($datos);

        if ($diferencia != 0) {
            DB::table('movimientos')->insert([
                'codigo_producto' => $producto->codigo, 'tipo' => $diferencia > 0 ? 'Entrada' : 'Salida',
                'cantidad' => abs($diferencia), 'motivo' => 'Ajuste manual desde edición', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        if ($request->stock <= 2) { $this->enviarAlertaTelegram($request->nombre, $request->stock); }

        return redirect('/')->with('success', '✅ Actualizado correctamente');
    }

    public function historial() {
        $movimientos = DB::table('movimientos')
            ->join('productos', 'movimientos.codigo_producto', '=', 'productos.codigo')
            ->select('movimientos.*', 'productos.nombre as producto_nombre', 'productos.marca')
            ->orderBy('movimientos.created_at', 'desc')->get();
        return view('historial', compact('movimientos'));
    }

    private function enviarAlertaTelegram($nombre, $stock) {
        $token = "8481616425:AAEzPw-TDuXlVfog9I7uPxTL215KAxM6VTo"; 
        $destinatarios = [
            "6958813406",          // Carlos Braca
            "-1003945277827"       // Grupo UNELLEZ
        ];

        $mensaje = "⚠️ *ALERTA OSWA-INV*\n\n" .
                   "El producto *{$nombre}* está agotándose.\n" .
                   "Stock actual: *{$stock}* unidades.\n\n" .
                   "📍 _Depósito Alto Barinas_";
        
        try {
            foreach ($destinatarios as $chat_id) {
                Http::get("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chat_id,
                    'text' => $mensaje,
                    'parse_mode' => 'Markdown'
                ]);
            }
        } catch (\Exception $e) { }
    }

    private function guardarProductoDetectado($codigo, $nombre, $marca, $foto, $categoria) {
        DB::table('productos')->insert([
            'codigo' => $codigo, 'nombre' => $nombre, 'marca' => $marca, 
            'categoria' => $categoria, 'imagen' => $foto, 'stock' => 1, 
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['status' => 'success', 'nombre' => $nombre, 'marca' => $marca, 'stock' => 1, 'foto' => $foto, 'mensaje' => "✨ ¡Guardado automático!"]);
    }

    private function detectarCategoriaFood($api) {
        if (!isset($api['categories_tags'])) return 'General';
        $tags = strtolower(implode(" ", $api['categories_tags']));
        if (str_contains($tags, 'beverage') || str_contains($tags, 'drink') || str_contains($tags, 'bebida')) return 'Bebidas';
        if (str_contains($tags, 'food') || str_contains($tags, 'snack') || str_contains($tags, 'alimento')) return 'Alimentos';
        return 'General';
    }
}