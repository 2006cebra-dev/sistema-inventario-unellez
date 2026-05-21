<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra - {{ $producto->codigo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        
        .header { 
            text-align: center; 
            border-bottom: 3px solid #6c5ce7; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        .header h1 { 
            font-size: 28px; 
            color: #6c5ce7; 
            font-weight: 700;
            letter-spacing: 2px;
        }
        .header p { 
            color: #666; 
            font-size: 11px; 
            margin-top: 5px;
        }
        
        .info-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .info-box h3 {
            color: #6c5ce7;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        th { 
            background: #6c5ce7; 
            color: white; 
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) { background: #f5f5f5; }
        
        .product-details td:first-child {
            font-weight: 600;
            width: 30%;
            color: #666;
        }
        
        .sugerido {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .sugerido strong { 
            font-size: 24px; 
            color: #856404;
        }
        
        .firma-box {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        .firma-box p { 
            margin-top: 40px; 
            color: #666;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 100px;
            color: rgba(108,92,231,0.1);
            z-index: -1;
            font-weight: bold;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="watermark">OSWA-INV</div>
    
    <div class="header">
        <h1>ORDEN DE COMPRA</h1>
        <p>Sistema de Gestión de Inventario - OSWA-INV</p>
        <p>Fecha: {{ $fecha }}</p>
    </div>
    
    <div class="info-box">
        <h3>Datos del Producto</h3>
        <table class="product-details">
            <tr>
                <td>Código:</td>
                <td>{{ $producto->codigo }}</td>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td>{{ $producto->nombre }}</td>
            </tr>
            <tr>
                <td>Marca:</td>
                <td>{{ $producto->marca ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Categoría:</td>
                <td>{{ $producto->categoria }}</td>
            </tr>
            <tr>
                <td>Precio Unitario:</td>
                <td>${{ number_format($producto->precio, 2) }}</td>
            </tr>
            <tr>
                <td>Stock Actual:</td>
                <td>{{ $producto->stock }} unidades</td>
            </tr>
            <tr>
                <td>Fecha Vencimiento:</td>
                <td>{{ $producto->fecha_vencimiento ? $producto->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="sugerido">
        <p>CANTIDAD SUGERIDA PARA REABASTECIMIENTO</p>
        <strong>{{ $cantidadSugerida }} unidades</strong>
        <p>(Stock ideal: {{ $stockIdeal }} - Stock actual: {{ $producto->stock }})</p>
    </div>
    
    <div class="firma-box">
        <table>
            <tr>
                <td width="50%">
                    <p style="border-top: 1px solid #333; width: 200px; padding-top: 5px; margin-top: 30px;">Firma del Gerente</p>
                </td>
                <td width="50%">
                    <p style="border-top: 1px solid #333; width: 200px; padding-top: 5px; margin-top: 30px;">Firma del Solicitante</p>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Documento generado automáticamente por OSWA-INV • Sistema de Gestión de Inventario</p>
        <p>Imprimir copia para archivo • Adjuntar copia al pedido</p>
    </div>
</body>
</html>