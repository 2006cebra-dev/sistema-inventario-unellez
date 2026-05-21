<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Códigos QR - OSWA Inv</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #fff; padding: 20px; }
        h2 { text-align: center; color: #E50914; margin-bottom: 30px; }
        .qr-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .qr-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; page-break-inside: avoid; }
        .qr-card img { max-width: 150px; max-height: 150px; }
        .qr-card h6 { margin-top: 10px; margin-bottom: 4px; font-size: 0.85rem; }
        .qr-card small { color: #666; font-size: 0.75rem; }
        @media print {
            body { padding: 0; }
            .qr-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>
</head>
<body>
    <h2>OSWA Inv - Códigos QR</h2>
    <div class="qr-grid">
        @forelse($productos as $producto)
            <div class="qr-card">
                <img src="{{ $qrCodes[$producto->id] ?? '' }}" alt="QR {{ $producto->codigo }}">
                <h6>{{ $producto->nombre }}</h6>
                <small>Código: {{ $producto->codigo }}</small>
            </div>
        @empty
            <p style="text-align: center; grid-column: 1/-1; color: #999;">No hay productos registrados.</p>
        @endforelse
    </div>
</body>
</html>