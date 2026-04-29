<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía de Despacho - OSWA Inv</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #E50914; padding-bottom: 10px; }
        .logo { color: #E50914; font-size: 24px; font-weight: bold; }
        .info-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .info-table td { padding: 8px; border: 1px solid #ddd; }
        .title { background-color: #f4f4f4; font-weight: bold; width: 30%; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #777; }
        .signature { margin-top: 30px; border-top: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">OSWA Inv - SISTEMA DE INVENTARIO</div>
        <p>Guía de Despacho y Control de Transferencia</p>
    </div>

    <h3>Detalles de la Carga</h3>
    <table class="info-table">
        <tr>
            <td class="title">Producto:</td>
            <td>{{ $producto }}</td>
        </tr>
        <tr>
            <td class="title">Cantidad:</td>
            <td>{{ $cantidad }} unidades</td>
        </tr>
        <tr>
            <td class="title">Origen:</td>
            <td>Sede Central (Barinas)</td>
        </tr>
        <tr>
            <td class="title">Destino:</td>
            <td>{{ $sucursal ?? $destino }}</td>
        </tr>
    </table>

    <h3>Datos de Logística (Cálculo de Grafo)</h3>
    <table class="info-table">
        <tr>
            <td class="title">Distancia Recorrida:</td>
            <td>{{ $distancia }} km</td>
        </tr>
        <tr>
            <td class="title">Costo de Flete:</td>
            <td>${{ number_format((float)$costo, 2) }}</td>
        </tr>
        <tr>
            <td class="title">Fecha de Emisión:</td>
            <td>{{ $fecha }}</td>
        </tr>
    </table>

    <div style="margin-top: 40px; background: #f9f9f9; padding: 15px; border: 1px dashed #E50914;">
        <strong>Firma Digital de Seguridad (SHA-256):</strong><br>
        <code style="font-size: 11px;">{{ hash('sha256', $producto . $fecha) }}</code>
    </div>

    <div class="footer">
        <p>Este documento es un comprobante generado automáticamente por el sistema OSWA Inv.</p>
        <div class="signature">Firma del Almacenista</div>
    </div>
</body>
</html>