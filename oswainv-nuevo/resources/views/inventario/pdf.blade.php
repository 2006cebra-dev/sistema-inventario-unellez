<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario - OSWA Inv</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; font-size: 12px; color: #333; }
        
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #6c5ce7; }
        .header h1 { color: #6c5ce7; font-size: 24px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 11px; }
        
        .summary { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .summary-box { text-align: center; padding: 10px 20px; border-radius: 8px; background: #f8f9fa; }
        .summary-box .value { font-size: 18px; font-weight: bold; color: #6c5ce7; }
        .summary-box .label { font-size: 10px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6c5ce7; color: white; padding: 10px; text-align: left; font-size: 10px; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; }
        
        .stock-alto { background: rgba(0, 184, 148, 0.1); }
        .stock-medio { background: rgba(253, 203, 110, 0.1); }
        .stock-bajo { background: rgba(231, 76, 60, 0.1); }
        
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-normal { background: #00b894; color: white; }
        .badge-bajo { background: #fdcb6e; color: #333; }
        .badge-critico { background: #e74c3c; color: white; }
        
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 REPORTE DE INVENTARIO</h1>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ $productos->count() }}</div>
            <div class="label">TOTAL PRODUCTOS</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $productos->sum('stock') }}</div>
            <div class="label">UNIDADES</div>
        </div>
        <div class="summary-box">
            <div class="value">${{ number_format($productos->sum(function($p) { return $p->stock * $p->precio; }), 2) }}</div>
            <div class="label">CAPITAL</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $p)
                <tr class="{{ $p->stock <= 2 ? 'stock-bajo' : ($p->stock <= 5 ? 'stock-medio' : 'stock-alto') }}">
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->categoria }}</td>
                    <td>${{ number_format($p->precio, 2) }}</td>
                    <td><strong>{{ $p->stock }}</strong></td>
                    <td>
                        <span class="badge {{ $p->stock <= 2 ? 'badge-critico' : ($p->stock <= 5 ? 'badge-bajo' : 'badge-normal') }}">
                            {{ $p->stock <= 2 ? 'CRÍTICO' : ($p->stock <= 5 ? 'BAJO' : 'NORMAL') }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Sistema de Gestión de Inventario OSWA Inv v2</p>
    </div>
</body>
</html>