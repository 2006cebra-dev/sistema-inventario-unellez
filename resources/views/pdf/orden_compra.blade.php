<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #3b82f6; }
        .info-empresa { font-size: 12px; color: #64748b; }
        .titulo { text-align: center; font-size: 20px; font-weight: bold; text-transform: uppercase; margin: 20px 0; background-color: #f1f5f9; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #cbd5e1; }
        th { background-color: #0f172a; color: white; }
        .footer { margin-top: 50px; font-size: 12px; text-align: center; color: #94a3b8; border-top: 1px solid #cbd5e1; padding-top: 10px; }
        .firma { margin-top: 60px; width: 200px; border-top: 1px solid #000; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <table style="border: none; margin: 0; padding: 0;">
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="logo">OSWA-INV SYSTEMS</div>
                    <div class="info-empresa">Sucursal Alto Barinas<br>Barinas, Venezuela<br>Contacto: compras@oswainv.com</div>
                </td>
                <td style="border: none; text-align: right; padding: 0;">
                    <strong>N° de Orden:</strong> {{ $numero_orden }}<br>
                    <strong>Fecha de Emisión:</strong> {{ $fecha }}
                </td>
            </tr>
        </table>
    </div>

    <div class="titulo">Orden de Compra B2B - Reposición de Inventario</div>

    <p>Estimado Proveedor de <strong>{{ $producto->marca ?? 'Genérico' }}</strong>,</p>
    <p>Mediante el presente documento, nuestro sistema automatizado de gestión de inventarios (OSWA-INV) ha detectado un nivel crítico de stock y solicita el despacho de la siguiente mercancía a nuestra sucursal principal:</p>

    <table>
        <thead>
            <tr>
                <th>Código / SKU</th>
                <th>Descripción del Producto</th>
                <th>Categoría</th>
                <th>Cant. Solicitada</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $producto->codigo }}</td>
                <td><strong>{{ $producto->nombre }}</strong></td>
                <td>{{ $producto->categoria ?? 'General' }}</td>
                <td style="font-size: 18px; font-weight: bold; text-align: center;">{{ $cantidad }} Unidades</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px; font-size: 18px;">
        <strong>Monto Estimado de Operación:</strong> ${{ number_format($costo_total, 2) }} USD
    </div>

    <div class="firma">
        Firma de Autorización<br>
        <span style="font-size: 12px; font-weight: normal;">Emitido por: {{ $usuario }}</span>
    </div>

    <div class="footer">
        Este documento es generado automáticamente por Inteligencia Logística OSWA-INV.<br>
        Las condiciones de pago serán acordadas contra factura.
    </div>

</body>
</html>