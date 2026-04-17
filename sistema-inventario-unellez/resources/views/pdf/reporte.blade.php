<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario - OSWA-INV</title>
    <style>
        /* Estilos optimizados para DomPDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0d6efd;
            font-size: 28px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .info-reporte {
            margin-bottom: 20px;
            width: 100%;
        }
        .info-reporte td {
            font-size: 12px;
        }
        .text-right { text-align: right; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #212529;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            font-size: 12px;
            text-align: right;
        }
        .total-box {
            background-color: #e9ecef;
            padding: 10px;
            display: inline-block;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>OSWA-INV</h1>
        <p>Universidad Nacional Experimental de los Llanos Occidentales "Ezequiel Zamora"</p>
        <p><strong>Ingeniería en Informática - Barinas</strong></p>
    </div>

    <table class="info-reporte">
        <tr>
            <td><strong>Analista:</strong> Carlos Braca</td>
            <td class="text-right"><strong>Fecha de Emisión:</strong> {{ $fecha }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="20%">Código</th>
                <th width="45%">Descripción del Producto</th>
                <th width="15%" style="text-align: center;">Stock</th>
                <th width="20%">Estado de Inventario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $p)
            <tr>
                <td>{{ $p->codigo }}</td>
                <td>{{ $p->nombre }}</td>
                <td style="text-align: center;">{{ $p->stock }} Unid.</td>
                <td class="{{ $p->stock <= 5 ? 'badge-danger' : '' }}">
                    @if($p->stock <= 5)
                        ⚠️ REPOSICIÓN INMEDIATA
                    @else
                        ✅ STOCK DISPONIBLE
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="total-box">
            Total Unidades en Depósito: {{ $totalUnidades }}
        </div>
        <p style="text-align: center; color: #999; margin-top: 50px; font-size: 10px;">
            Este reporte fue generado automáticamente por el Sistema OSWA-INV
        </p>
    </div>

</body>
</html>