<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario - OSWA Inv</title>
    <style>
        /* --- ESTILOS PREMIUM PARA PDF --- */
        body { 
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif; 
            font-size: 12px; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }
        
        /* Cabecera */
        .header { 
            width: 100%; 
            border-bottom: 3px solid #E50914; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .logo { 
            width: 75px; 
            float: left; 
        }
        .title-container { 
            float: left; 
            margin-left: 20px; 
            padding-top: 10px;
        }
        .title { 
            color: #1a1a1a; 
            font-size: 24px; 
            font-weight: 800; 
            margin: 0; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        .title span { color: #E50914; }
        .subtitle { 
            color: #666; 
            font-size: 11px; 
            margin-top: 5px; 
        }
        .clear { clear: both; }

        /* Tarjetas de Resumen */
        .summary-container { 
            width: 100%; 
            margin-bottom: 25px; 
            text-align: center; 
        }
        .summary-box { 
            display: inline-block; 
            width: 30%; 
            background: #f8f9fa; 
            border: 1px solid #ddd; 
            border-top: 3px solid #E50914; 
            padding: 12px 0; 
            margin: 0 1%; 
            border-radius: 4px; 
        }
        .summary-value { 
            font-size: 18px; 
            font-weight: bold; 
            color: #111; 
            margin-bottom: 4px; 
            font-family: monospace;
        }
        .summary-label { 
            font-size: 9px; 
            color: #777; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            font-weight: bold;
        }

        /* Tabla de Productos */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th { 
            background-color: #1a1a1a; 
            color: #ffffff; 
            padding: 10px; 
            text-align: left; 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        td { 
            padding: 8px 10px; 
            border-bottom: 1px solid #eee; 
            font-size: 11px; 
        }
        tr:nth-child(even) { background-color: #fafafa; }

        /* Etiquetas de Estado */
        .badge { 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 9px; 
            font-weight: bold; 
            color: white; 
            text-align: center; 
            display: inline-block; 
            min-width: 55px;
        }
        .badge-critico { background-color: #E50914; }
        .badge-bajo { background-color: #f39c12; }
        .badge-normal { background-color: #00b894; }

        /* Footer */
        .footer { 
            position: fixed; 
            bottom: -15px; 
            left: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 9px; 
            color: #888; 
            border-top: 1px solid #ddd; 
            padding-top: 8px; 
        }
        .bold-foot { font-weight: bold; color: #555; }
    </style>
</head>
<body>

    @php
        // Cálculos para el resumen
        $totalProductos = $productos->count();
        $unidades = $productos->sum('stock');
        $capital = $productos->sum(function($p) { return $p->stock * $p->precio; });

        // Truco seguro para cargar la imagen en DomPDF
        $rutaLogo = public_path('img/logo-unellez.png');
        $imagenBase64 = '';
        if(file_exists($rutaLogo)) {
            $tipo = pathinfo($rutaLogo, PATHINFO_EXTENSION);
            $data = file_get_contents($rutaLogo);
            $imagenBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($data);
        }
    @endphp

    <!-- CABECERA DEL REPORTE -->
    <div class="header">
        @if($imagenBase64)
            <img src="{{ $imagenBase64 }}" class="logo" alt="Logo UNELLEZ">
        @endif
        <div class="title-container">
            <h1 class="title">Reporte de <span>Inventario</span></h1>
            <p class="subtitle">Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} &nbsp;|&nbsp; Sistema: OSWA Inv</p>
        </div>
        <div class="clear"></div>
    </div>

    <!-- CAJAS DE RESUMEN (ESTILO DASHBOARD) -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-value">{{ $totalProductos }}</div>
            <div class="summary-label">Total de Productos</div>
        </div>
        <div class="summary-box">
            <div class="summary-value">{{ number_format($unidades, 0, ',', '.') }}</div>
            <div class="summary-label">Unidades en Almacén</div>
        </div>
        <div class="summary-box" style="border-top-color: #00b894;">
            <div class="summary-value">${{ number_format($capital, 2, ',', '.') }}</div>
            <div class="summary-label">Capital Invertido</div>
        </div>
    </div>

    <!-- TABLA DE DATOS -->
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio Unit.</th>
                <th style="text-align: center;">Stock</th>
                <th style="text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $prod)
                @php
                    if ($prod->stock == 0) {
                        $estado = 'CRÍTICO';
                        $clase = 'badge-critico';
                    } elseif ($prod->stock <= 5) {
                        $estado = 'BAJO';
                        $clase = 'badge-bajo';
                    } else {
                        $estado = 'NORMAL';
                        $clase = 'badge-normal';
                    }
                @endphp
                <tr>
                    <td style="font-family: monospace; color: #555; font-size: 10px;">{{ $prod->codigo }}</td>
                    <td style="font-weight: bold; color: #222;">{{ $prod->nombre }}</td>
                    <td>{{ $prod->categoria ?? 'General' }}</td>
                    <td style="color: #00b894; font-weight: bold;">${{ number_format($prod->precio, 2, ',', '.') }}</td>
                    <td style="font-weight: bold; text-align: center; font-size: 12px;">{{ $prod->stock }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $clase }}">{{ $estado }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- PIE DE PÁGINA (Se repite en todas las hojas) -->
    <div class="footer">
        <span class="bold-foot">OSWA Inv</span> - Sistema de Gestión de Inventario | Desarrollado por: <span class="bold-foot">Carlos Braca & Yorgelys Blanco</span> | UNELLEZ
    </div>
    
    <!-- NUMERACIÓN DE PÁGINAS (Script nativo de DomPDF) -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 25;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.5, 0.5, 0.5));
        }
    </script>
</body>
</html>