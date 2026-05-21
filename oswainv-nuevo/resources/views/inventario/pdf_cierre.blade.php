<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre de Caja Diario - OSWA Inv</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #E50914; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-text { font-size: 24px; font-weight: bold; color: #E50914; }
        .title { font-size: 18px; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px; }
        
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .stats-table td { padding: 15px; border: 1px solid #eee; text-align: center; }
        .stat-val { font-size: 20px; font-weight: bold; display: block; color: #000; }
        .stat-lab { font-size: 10px; color: #666; text-transform: uppercase; }

        .mov-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .mov-table th { background-color: #f8f8f8; border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        .mov-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .mov-table tbody tr { page-break-inside: avoid; }
        h4 { page-break-after: avoid; }
        
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .bg-success { background-color: #d1fae5; color: #065f46; }
        .bg-danger { background-color: #fee2e2; color: #991b1b; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-text">OSWA Inv</div>
        <div class="title">Reporte de Cierre Diario - {{ $resumen['fecha'] }}</div>
    </div>

    <table class="stats-table">
        <tr>
            <td>
                <span class="stat-val">{{ $resumen['operaciones'] }}</span>
                <span class="stat-lab">Operaciones Totales</span>
            </td>
            <td>
                <span class="stat-val" style="color: #059669;">+ {{ $resumen['entradas'] }}</span>
                <span class="stat-lab">Unidades Entrantes</span>
            </td>
            <td>
                <span class="stat-val" style="color: #dc2626;">- {{ $resumen['salidas'] }}</span>
                <span class="stat-lab">Unidades Despachadas</span>
            </td>
        </tr>
    </table>

    <h4 style="border-left: 4px solid #E50914; padding-left: 10px;">Detalle de Movimientos del Día</h4>
    <table class="mov-table">
        <thead>
            <tr>
                <th>Hora</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cant.</th>
                <th>Usuario</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientosHoy as $mov)
            <tr>
                <td>{{ $mov->created_at->format('h:i A') }}</td>
                <td><strong>{{ $mov->producto->nombre ?? 'N/A' }}</strong></td>
                <td>
                    <span class="badge {{ $mov->tipo == 'Entrada' ? 'bg-success' : 'bg-danger' }}">
                        {{ strtoupper($mov->tipo) }}
                    </span>
                </td>
                <td>{{ $mov->cantidad }}</td>
                <td>{{ $mov->usuario_accion }}</td>
                <td>{{ $mov->motivo }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema OSWA Inv — Auditoría SHA-256 Activa.
    </div>

</body>
</html>