<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 

class InventarioController extends Controller
{
    public function index() 
    {
        $productos = DB::table('productos')->orderBy('updated_at', 'desc')->get();
        
        $tasaDolar = 36.50; 
        try {
            $response = Http::get('https://ve.dolarapi.com/v1/dolares/oficial');
            if ($response->successful()) { $tasaDolar = $response->json()['promedio']; }
        } catch (\Exception $e) { }

        $totalProductos = $productos->count();
        $totalUnidades = $productos->sum('stock');
        $bajoStock = $productos->where('stock', '<=', 5)->count();

        $capitalDolares = 0;
        foreach ($productos as $p) { $capitalDolares += ($p->stock * ($p->precio ?? 0)); }
        $capitalBolivares = $capitalDolares * $tasaDolar;

        foreach ($productos as $p) {
            $salidasSemanales = DB::table('movimientos')->where('codigo_producto', $p->codigo)->where('tipo', 'Salida')->where('created_at', '>=', now()->subDays(7))->sum('cantidad');
            $promedioDiario = $salidasSemanales / 7;
            $p->dias_restantes = $promedioDiario > 0 ? round($p->stock / $promedioDiario) : null;
        }

        $conteoCategorias = DB::table('productos')->select('categoria', DB::raw('count(*) as total'))->groupBy('categoria')->get();

        return view('index', compact(
            'productos', 'totalProductos', 'totalUnidades', 'bajoStock', 
            'capitalDolares', 'capitalBolivares', 'tasaDolar'
        ))->with([
            'labelsCat' => $conteoCategorias->pluck('categoria'),
            'dataCat' => $conteoCategorias->pluck('total')
        ]);
    }

    public function iaComando(Request $request) 
    {
        $orden = strtolower(trim($request->orden));
        
        if (str_contains($orden, 'vence') || str_contains($orden, 'vencimiento')) {
            $data = DB::table('productos')->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<=', now()->addDays(30))->get();
            return response()->json(['status' => 'success', 'msg' => '<span class="text-white">Estos productos vencen pronto:</span>', 'data' => $data]);
        }

        if (str_contains($orden, 'quien') || str_contains($orden, 'responsable') || str_contains($orden, 'ultimo')) {
            $log = DB::table('movimientos')->latest()->first();
            return response()->json(['status' => 'success', 'msg' => "<span class='text-white'>La última acción fue de: {$log->usuario_accion} (Producto: {$log->codigo_producto}).</span>"]);
        }

        if (str_contains($orden, 'bajo') || str_contains($orden, 'falta') || str_contains($orden, 'agotado')) {
            $data = DB::table('productos')->where('stock', '<=', 5)->get();
            return response()->json(['status' => 'success', 'msg' => '<span class="text-white">Estos productos tienen stock crítico:</span>', 'data' => $data]);
        }

        if (str_contains($orden, 'ruta a') || str_contains($orden, 'despacho a')) {
            $partes = explode(' a ', $orden);
            if (count($partes) == 2) {
                $destinoOriginal = trim($partes[1]);
                $destino = str_replace(' ', '_', $destinoOriginal);

                $coordenadas = [
                    'amazonas' => '5.6667,-67.5833', 'anzoategui' => '10.1333,-64.6833', 'apure' => '7.8833,-67.4667',
                    'aragua' => '10.2500,-67.6000', 'barinas' => '8.6333,-70.2833', 'bolivar' => '8.1167,-63.5333',
                    'carabobo' => '10.1667,-68.0000', 'cojedes' => '9.6500,-68.5833', 'delta_amacuro' => '9.0500,-62.0500',
                    'falcon' => '11.4167,-69.6667', 'guarico' => '9.9167,-67.3667', 'lara' => '10.0667,-69.3333',
                    'merida' => '8.6000,-71.1500', 'miranda' => '10.3333,-66.8333', 'monagas' => '9.7500,-63.1667',
                    'nueva_esparta' => '11.0333,-63.8667', 'portuguesa' => '9.0333,-69.7333', 'sucre' => '10.4500,-64.1833',
                    'tachira' => '7.7667,-72.2333', 'trujillo' => '9.3667,-70.4333', 'la_guaira' => '10.6000,-66.9333',
                    'yaracuy' => '10.3333,-68.7333', 'zulia' => '10.6333,-71.6333', 'caracas' => '10.4806,-66.9036',
                    'socopo' => '8.2289,-70.8361', 'cabimas' => '10.3958,-71.4397', 'valle_la_pascua' => '9.9111,-67.3608'
                ];

                $resultado = $this->calcularRutaMasCorta('barinas', $destino);
                
                if ($resultado && isset($coordenadas[$destino])) {
                    $coordOrigen = $coordenadas['barinas'];
                    $coordDestino = $coordenadas[$destino];
                    
                    $puntosRuta = [];
                    foreach ($resultado['nodos'] as $nodo) {
                        if (isset($coordenadas[$nodo])) {
                            $puntosRuta[] = $coordenadas[$nodo];
                        }
                    }
                    $pathString = implode('%7C', $puntosRuta); 
                    
                    $apiKey = "AIzaSyDnxMWZA56z9F_4RsHWVEnx2wWnvilMA0Q"; 
                    
                    $estiloMapa = "&style=feature:administrative%7Celement:labels.text.fill%7Ccolor:0xffffff"; 
                    $estiloMapa .= "&style=feature:administrative%7Celement:labels.text.stroke%7Ccolor:0x0f172a%7Cweight:4"; 
                    $estiloMapa .= "&style=feature:landscape%7Celement:all%7Ccolor:0x334155"; 
                    $estiloMapa .= "&style=feature:water%7Celement:all%7Ccolor:0x1e293b"; 
                    $estiloMapa .= "&style=feature:road%7Celement:geometry%7Ccolor:0x475569"; 

                    $mapaUrl = "https://maps.googleapis.com/maps/api/staticmap?";
                    $mapaUrl .= "size=600x350&maptype=roadmap&format=png"; 
                    $mapaUrl .= "&markers=color:0xdc3545%7C{$coordOrigen}"; 
                    $mapaUrl .= "&markers=color:0x0d6efd%7C{$coordDestino}"; 
                    $mapaUrl .= "&path=color:0x0ea5e9ff%7Cweight:6%7Cgeodesic:true%7C{$pathString}"; 
                    $mapaUrl .= "&key={$apiKey}{$estiloMapa}";

                    $distancia = $resultado['distancia'];
                    $diesel = round($distancia / 8.5, 2);
                    $horas = floor($distancia / 70);
                    $minutos = round((($distancia / 70) - $horas) * 60);
                    $tiempoTxt = "{$horas}h {$minutos}m";
                    $costoOperativo = number_format($distancia * 0.15, 2);

                    $msg = "<div class='text-start text-white'>";
                    $msg .= "<h5 class='fw-bold text-info mb-3'><i class='bi bi-geo-alt-fill text-danger'></i> Orden de Despacho: SUCURSAL " . strtoupper($destinoOriginal) . "</h5>";
                    $msg .= "<p class='small mb-2' style='color: #94a3b8;'>📍 <b>Puntos de Control (Grafo Logístico):</b></p>";
                    $msg .= "<div class='d-flex flex-wrap gap-1 align-items-center mb-3'>{$resultado['camino_html']}</div>";
                    $msg .= "<img src='{$mapaUrl}' class='img-fluid rounded-4 shadow-lg mb-3 w-100' style='border: 2px solid #475569;' alt='Ruta'>";
                    
                    $msg .= "<div class='row text-center mt-2 border-top border-secondary pt-3'>";
                    $msg .= "<div class='col-6 border-end border-bottom border-secondary pb-2 mb-2'><span class='d-block fs-5 fw-bold text-warning'>{$distancia} Km</span><small style='color: #cbd5e1;'>Distancia Total</small></div>";
                    $msg .= "<div class='col-6 border-bottom border-secondary pb-2 mb-2'><span class='d-block fs-5 fw-bold text-success'>{$tiempoTxt}</span><small style='color: #cbd5e1;'>Tiempo Estimado</small></div>";
                    $msg .= "<div class='col-6 border-end border-secondary pt-1'><span class='d-block fs-5 fw-bold text-danger'>{$diesel} Lts</span><small style='color: #cbd5e1;'>Consumo Diésel</small></div>";
                    $msg .= "<div class='col-6 pt-1'><span class='d-block fs-5 fw-bold text-info'>\${$costoOperativo}</span><small style='color: #cbd5e1;'>Costo Logístico</small></div>";
                    $msg .= "</div></div>";
                    
                    return response()->json(['status' => 'success', 'msg' => $msg]);
                } else {
                    return response()->json(['status' => 'error', 'msg' => "<span class='text-white'>No existe conexión registrada.</span>"]);
                }
            }
        }
        return response()->json(['status' => 'error', 'msg' => '<span class="text-white">Comando no reconocido.</span>']);
    }

    public function procesarOcr(Request $request) 
    {
        $texto = $request->texto;
        $sugerencia = "Desconocido";
        if (str_contains(strtolower($texto), 'harina')) $sugerencia = "Harina PAN";
        if (str_contains(strtolower($texto), 'aceite')) $sugerencia = "Aceite Portumesa";
        if (str_contains(strtolower($texto), 'arroz')) $sugerencia = "Arroz Primor";
        return response()->json(['status' => 'success', 'sugerencia' => $sugerencia]);
    }

    // 🔒 --- LÓGICA DE AUDITORÍA INMUTABLE (SHA-256) ---
    private function registrarAuditoria($codigo, $tipo, $cantidad, $motivo, $usuario) {
        $fechaStr = now()->toDateTimeString();
        
        $cadenaSecreta = $codigo . $tipo . $cantidad . $fechaStr . 'OSWA2026';
        $firmaHash = hash('sha256', $cadenaSecreta);

        DB::table('movimientos')->insert([
            'codigo_producto' => $codigo,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'usuario_accion' => $usuario,
            'firma_digital' => $firmaHash,
            'created_at' => $fechaStr,
            'updated_at' => $fechaStr,
        ]);
    }

    // 📱 --- MÉTODOS DEL ESCÁNER MÓVIL ---
    public function registrarMovimientoScanner(Request $request) 
    {
        $codigo = trim($request->codigo_barras);
        $producto = DB::table('productos')->where('codigo', $codigo)->first();

        if ($producto) {
            $nuevoStock = $producto->stock + 1;
            DB::table('productos')->where('id', $producto->id)->update(['stock' => $nuevoStock, 'updated_at' => now()]);
            
            $this->registrarAuditoria($codigo, 'Entrada', 1, 'Escáner Móvil', Auth::user()->name ?? 'Sistema');

            $imagen = $producto->descripcion ?? 'https://cdn-icons-png.flaticon.com/512/1174/1174466.png';
            if (!str_contains($imagen, 'http')) { $imagen = 'https://cdn-icons-png.flaticon.com/512/1174/1174466.png'; }

            return response()->json(['status' => 'success', 'nombre' => $producto->nombre, 'marca' => $producto->marca, 'stock' => $nuevoStock, 'foto' => $imagen, 'mensaje' => '+1 Entrada registrada']);
        } 
        else {
            try {
                $apiResponse = Http::timeout(4)->get("https://world.openfoodfacts.org/api/v0/product/{$codigo}.json");
                if ($apiResponse->successful() && $apiResponse->json('status') == 1) {
                    $datosApi = $apiResponse->json('product');
                    $nombreGlobal = $datosApi['product_name'] ?? 'Producto Nuevo';
                    $marcaGlobal = explode(',', $datosApi['brands'] ?? 'Genérico')[0]; 
                    $categoriaGlobal = explode(',', $datosApi['categories'] ?? 'General')[0]; 
                    $fotoGlobal = $datosApi['image_url'] ?? 'https://cdn-icons-png.flaticon.com/512/1174/1174466.png';
                    $fechaVencimiento = now()->addMonths(6)->format('Y-m-d');

                    DB::table('productos')->insert([
                        'codigo' => $codigo, 'nombre' => substr($nombreGlobal, 0, 100), 'marca' => substr($marcaGlobal, 0, 50),
                        'stock' => 1, 'categoria' => substr($categoriaGlobal, 0, 50), 'precio' => 0, 'fecha_vencimiento' => $fechaVencimiento,
                        'descripcion' => $fotoGlobal, 'created_at' => now(), 'updated_at' => now()
                    ]);

                    $this->registrarAuditoria($codigo, 'Entrada', 1, 'Auto-Registro Global (API)', Auth::user()->name ?? 'API Externa');

                    return response()->json(['status' => 'success', 'nombre' => $nombreGlobal, 'marca' => $marcaGlobal, 'stock' => 1, 'foto' => $fotoGlobal, 'mensaje' => '¡Detectado y Registrado por API!']);
                }
            } catch (\Exception $e) { }

            return response()->json(['status' => 'not_found']);
        }
    }

    public function guardarRapidoScanner(Request $request) 
    {
        $codigo = trim($request->codigo);
        
        DB::table('productos')->insert([
            'codigo' => $codigo, 'nombre' => $request->nombre, 'marca' => $request->marca, 'stock' => 1, 'categoria' => 'General', 'precio' => 0, 'created_at' => now(), 'updated_at' => now()
        ]);

        $this->registrarAuditoria($codigo, 'Entrada', 1, 'Registro Inicial Escáner', Auth::user()->name ?? 'Sistema');
        return response()->json(['status' => 'success']);
    }

    // --- MÉTODOS ESTÁNDAR ---
    public function ajustarStock(Request $request, $id) 
    {
        $producto = DB::table('productos')->where('id', $id)->first();
        if (!$producto) return response()->json(['status' => 'error']);

        // FEFO
        if ($request->accion == 'restar') {
            if ($producto->stock <= 0) return response()->json(['status' => 'error']);
            $productoMasViejo = DB::table('productos')->where('nombre', $producto->nombre)->where('id', '!=', $id)->where('stock', '>', 0)
                ->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', $producto->fecha_vencimiento)->orderBy('fecha_vencimiento', 'asc')->first();

            if ($productoMasViejo && !$request->has('forzar_fefo')) {
                $fechaFormateada = \Carbon\Carbon::parse($productoMasViejo->fecha_vencimiento)->format('d/m/Y');
                return response()->json(['status' => 'fefo_warning', 'msg' => "¡ALERTA FEFO! Tienes otro lote que caduca antes ($fechaFormateada).", 'id_sugerido' => $productoMasViejo->id]);
            }
        }

        $nuevoStock = ($request->accion == 'sumar') ? $producto->stock + 1 : $producto->stock - 1;
        DB::table('productos')->where('id', $id)->update(['stock' => $nuevoStock, 'updated_at' => now()]);

        $tipoMovimiento = $request->accion == 'sumar' ? 'Entrada' : 'Salida';
        $motivoMovimiento = $request->has('forzar_fefo') ? 'Salida Forzada (FEFO Ignorado)' : 'Ajuste rápido';
        $this->registrarAuditoria($producto->codigo, $tipoMovimiento, 1, $motivoMovimiento, Auth::user()->name);

        if ($nuevoStock <= 2) { $this->enviarAlertaTelegram($producto->nombre, $nuevoStock); }

        $productosActuales = DB::table('productos')->get();
        return response()->json([
            'status' => 'success', 'nuevo_stock' => $nuevoStock, 'total_productos' => $productosActuales->count(),
            'total_unidades' => $productosActuales->sum('stock'), 'bajo_stock' => $productosActuales->where('stock', '<=', 5)->count(),
            'capital_invertido' => number_format($productosActuales->sum(fn($p) => $p->stock * ($p->precio ?? 0)), 2)
        ]);
    }

    public function transferir(Request $request, $id) {
        $producto = DB::table('productos')->where('id', $id)->first();
        $cantidad = (int) $request->cantidad;
        $destino = strtolower(str_replace(' ', '_', $request->destino));

        if (!$producto || $producto->stock < $cantidad || $cantidad <= 0) return response()->json(['status' => 'error', 'msg' => 'Stock insuficiente.']);

        $ruta = $this->calcularRutaMasCorta('barinas', $destino);
        if (!$ruta) return response()->json(['status' => 'error', 'msg' => 'Ruta no encontrada.']);

        DB::table('productos')->where('id', $id)->update(['stock' => $producto->stock - $cantidad, 'updated_at' => now()]);
        $costoFlete = $ruta['distancia'] * 0.15;

        $motivoTxt = "Transferencia a Sucursal " . strtoupper(str_replace('_', ' ', $destino)) . " (Distancia: {$ruta['distancia']} Km | Flete: $\$$costoFlete)";
        $this->registrarAuditoria($producto->codigo, 'Salida', $cantidad, $motivoTxt, Auth::user()->name);

        return response()->json(['status' => 'success', 'msg' => "Transferencia completada. {$cantidad} unidades enviadas.", 'costo_flete' => number_format($costoFlete, 2), 'distancia' => $ruta['distancia']]);
    }

    public function historial() {
        if (Auth::user()->role !== 'admin') abort(403);
        $movimientos = DB::table('movimientos')->join('productos', 'movimientos.codigo_producto', '=', 'productos.codigo')
            ->select('movimientos.*', 'productos.nombre as producto_nombre', 'productos.marca')->orderBy('movimientos.created_at', 'desc')->get();
        
        foreach ($movimientos as $m) {
            if (!empty($m->firma_digital)) {
                $fechaGuardada = \Carbon\Carbon::parse($m->created_at)->toDateTimeString();
                $cadenaSecreta = $m->codigo_producto . $m->tipo . $m->cantidad . $fechaGuardada . 'OSWA2026';
                $hashCalculado = hash('sha256', $cadenaSecreta);
                $m->es_valido = ($hashCalculado === $m->firma_digital);
            } else {
                $m->es_valido = 'sin_firma'; 
            }
        }
        
        return view('historial', compact('movimientos'));
    }

    public function editar($id) {
        $producto = DB::table('productos')->where('id', $id)->first();
        if (!$producto) return redirect('/')->with('error', 'No encontrado');
        return view('editar', compact('producto'));
    }

    public function actualizar(Request $request, $id) {
        $datos = [ 'nombre' => $request->nombre, 'marca' => $request->marca, 'categoria' => $request->categoria, 'descripcion' => $request->descripcion, 'fecha_vencimiento' => $request->fecha_vencimiento, 'updated_at' => now() ];
        if (Auth::user()->role === 'admin') { $datos['precio'] = $request->precio ?? 0; }
        DB::table('productos')->where('id', $id)->update($datos);
        return redirect('/')->with('success', '✅ Producto actualizado');
    }

    public function eliminar($codigo) {
        if (Auth::user()->role !== 'admin') abort(403);
        DB::table('productos')->where('codigo', $codigo)->delete();
        return redirect('/')->with('success', 'Producto eliminado');
    }

    public function generarPdf() {
        if (Auth::user()->role !== 'admin') abort(403);
        $productos = DB::table('productos')->get();
        $pdf = Pdf::loadView('pdf.reporte', ['productos' => $productos, 'totalUnidades' => $productos->sum('stock'), 'fecha' => date('d-m-Y')]);
        return $pdf->stream('Reporte_Inventario_Barinas.pdf');
    }

    public function exportarExcel() {
        if (Auth::user()->role !== 'admin') abort(403);
        $productos = DB::table('productos')->get();
        $filename = "Inventario_OSWA_" . date('d-m-Y') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['Código', 'Nombre', 'Marca', 'Categoría', 'Stock', 'Precio ($)', 'Última Actualización'], ';');
        foreach ($productos as $p) { fputcsv($output, [$p->codigo, $p->nombre, $p->marca, $p->categoria, $p->stock, $p->precio ?? 0, $p->updated_at], ';'); }
        fclose($output);
    }

    // 💿 --- NUEVO: FUNCIÓN PARA RESPALDAR LA BASE DE DATOS (.SQL) ---
    public function respaldarDB() {
        if (Auth::user()->role !== 'admin') abort(403);

        $tables = DB::select('SHOW TABLES');
        $sql = "-- Respaldo de Base de Datos OSWA-INV\n";
        $sql .= "-- Fecha: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Generado automáticamente\n\n";

        foreach ($tables as $tableObj) {
            $tableArray = (array) $tableObj;
            $tableName = array_values($tableArray)[0];

            $create = DB::select("SHOW CREATE TABLE `$tableName`");
            $sql .= "\n\nDROP TABLE IF EXISTS `$tableName`;\n";
            $sql .= ((array) $create[0])['Create Table'] . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $values = array_map(function($val) {
                    return is_null($val) ? "NULL" : "'" . addslashes($val) . "'";
                }, array_values($rowArray));
                $sql .= "INSERT INTO `$tableName` VALUES(" . implode(", ", $values) . ");\n";
            }
        }

        $filename = "Respaldo_OSWA_" . date('d_m_Y_His') . ".sql";
        return response($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    public function generarOrdenCompra($id) {
        if (Auth::user()->role !== 'admin') abort(403);
        $producto = DB::table('productos')->where('id', $id)->first();
        if (!$producto) return back()->with('error', 'Producto no encontrado');

        $cantidadPedir = 50 - $producto->stock;
        if ($cantidadPedir <= 0) $cantidadPedir = 20; 

        $datosOrden = ['numero_orden' => 'ORD-'.strtoupper(uniqid()), 'fecha' => date('d/m/Y'), 'producto' => $producto, 'cantidad' => $cantidadPedir, 'costo_total' => $cantidadPedir * ($producto->precio > 0 ? $producto->precio : 1.50), 'usuario' => Auth::user()->name];
        $pdf = Pdf::loadView('pdf.orden_compra', $datosOrden);
        return $pdf->stream('Orden_Compra_'.$producto->codigo.'.pdf');
    }

    public function crear() { return view('crear'); }

    public function store(Request $request) {
        DB::table('productos')->insert(['codigo' => $request->codigo, 'nombre' => $request->nombre, 'stock' => $request->stock, 'marca' => $request->marca, 'categoria' => $request->categoria ?? 'General', 'precio' => Auth::user()->role === 'admin' ? ($request->precio ?? 0) : 0, 'fecha_vencimiento' => $request->fecha_vencimiento, 'created_at' => now(), 'updated_at' => now()]);
        return redirect('/')->with('success', '✅ Guardado');
    }

    private function enviarAlertaTelegram($nombre, $stock) {
        $token = "8481616425:AAEzPw-TDuXlVfog9I7uPxTL215KAxM6VTo"; 
        $destinatarios = ["6958813406", "-1003945277827"];
        try { foreach ($destinatarios as $chat_id) { Http::withoutVerifying()->get("https://api.telegram.org/bot{$token}/sendMessage", ['chat_id' => $chat_id, 'text' => "⚠️ *ALERTA OSWA-INV*\n\nEl producto *{$nombre}* está agotándose.\nStock actual: *{$stock}* unidades.\n\n📍 _Depósito Alto Barinas_", 'parse_mode' => 'Markdown']); } } catch (\Exception $e) { }
    }

    private function calcularRutaMasCorta($origen, $destino) {
        $grafo = ['barinas' => ['portuguesa' => 130, 'merida' => 165, 'socopo' => 85, 'trujillo' => 170, 'apure' => 180], 'socopo' => ['barinas' => 85, 'tachira' => 215], 'portuguesa' => ['barinas' => 130, 'cojedes' => 100, 'lara' => 170], 'cojedes' => ['portuguesa' => 100, 'carabobo' => 110, 'yaracuy' => 120], 'carabobo' => ['cojedes' => 110, 'aragua' => 50, 'yaracuy' => 80, 'falcon' => 250], 'aragua' => ['carabobo' => 50, 'caracas' => 120, 'guarico' => 150], 'caracas' => ['aragua' => 120, 'la_guaira' => 30, 'miranda' => 40], 'miranda' => ['caracas' => 40, 'anzoategui' => 280], 'anzoategui' => ['miranda' => 280, 'monagas' => 200, 'sucre' => 150, 'bolivar' => 300], 'monagas' => ['anzoategui' => 200, 'delta_amacuro' => 180, 'sucre' => 180], 'sucre' => ['anzoategui' => 150, 'nueva_esparta' => 50], 'nueva_esparta' => ['sucre' => 50], 'delta_amacuro' => ['monagas' => 180, 'bolivar' => 320], 'bolivar' => ['anzoategui' => 300, 'amazonas' => 700], 'amazonas' => ['bolivar' => 700, 'apure' => 450], 'apure' => ['barinas' => 180, 'amazonas' => 450], 'lara' => ['portuguesa' => 170, 'yaracuy' => 90, 'falcon' => 290, 'zulia' => 280, 'trujillo' => 230], 'yaracuy' => ['lara' => 90, 'carabobo' => 80], 'falcon' => ['carabobo' => 250, 'zulia' => 320], 'zulia' => ['lara' => 280, 'falcon' => 320, 'trujillo' => 160, 'tachira' => 430], 'trujillo' => ['barinas' => 170, 'merida' => 110, 'zulia' => 160], 'merida' => ['barinas' => 165, 'trujillo' => 110, 'tachira' => 240], 'tachira' => ['socopo' => 215, 'merida' => 240, 'zulia' => 430], 'la_guaira' => ['caracas' => 30], 'guarico' => ['aragua' => 150, 'bolivar' => 350, 'apure' => 250] ];
        if (!isset($grafo[$origen]) || !isset($grafo[$destino])) return null;
        $distancias = []; $rutas = []; $nodosNoVisitados = array_keys($grafo); foreach ($nodosNoVisitados as $nodo) { $distancias[$nodo] = INF; $rutas[$nodo] = null; } $distancias[$origen] = 0;
        while (!empty($nodosNoVisitados)) { $minNodo = null; foreach ($nodosNoVisitados as $nodo) { if ($minNodo === null || $distancias[$nodo] < $distancias[$minNodo]) { $minNodo = $nodo; } } if ($distancias[$minNodo] === INF) break; $nodosNoVisitados = array_diff($nodosNoVisitados, [$minNodo]); foreach ($grafo[$minNodo] as $vecino => $peso) { $alt = $distancias[$minNodo] + $peso; if ($alt < $distancias[$vecino]) { $distancias[$vecino] = $alt; $rutas[$vecino] = $minNodo; } } }
        $camino_keys = []; $u = $destino; while (isset($rutas[$u])) { array_unshift($camino_keys, $u); $u = $rutas[$u]; } array_unshift($camino_keys, $origen);
        $html_badges = ""; foreach ($camino_keys as $index => $nombre) { $nombreTxt = strtoupper(str_replace('_', ' ', $nombre)); if ($index === 0) { $html_badges .= "<span class='badge bg-danger rounded-pill px-3 py-2 shadow-sm'>Almacén {$nombreTxt}</span>"; } elseif ($index === count($camino_keys) - 1) { $html_badges .= " <i class='bi bi-arrow-right-short fs-5 text-secondary'></i> <span class='badge bg-primary rounded-pill px-3 py-2 shadow-sm'>Sucursal {$nombreTxt}</span>"; } else { $html_badges .= " <i class='bi bi-arrow-right-short fs-5 text-secondary'></i> <span class='badge bg-secondary bg-opacity-25 text-light rounded-pill px-3'>{$nombreTxt}</span>"; } }
        return ['distancia' => $distancias[$destino], 'camino_html' => $html_badges, 'nodos' => $camino_keys];
    }
}