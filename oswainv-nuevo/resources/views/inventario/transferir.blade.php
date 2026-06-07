<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transferir - {{ $producto->nombre }} | OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg: #0f0f0f; --card: #1a1a1a; --n-red: #E50914; --n-border: #2a2a2a; --text: #e5e5e5; }
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; }
        .bg-glow { position: fixed; top: -30%; left: -20%; width: 60%; height: 80%; background: radial-gradient(circle, rgba(229,9,20,0.06) 0%, transparent 70%); pointer-events: none; z-index: 0; }
        .bg-glow-2 { position: fixed; bottom: -30%; right: -20%; width: 60%; height: 80%; background: radial-gradient(circle, rgba(0,184,148,0.05) 0%, transparent 70%); pointer-events: none; z-index: 0; }
        .page { position: relative; z-index: 1; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 2rem; border-bottom: 1px solid var(--n-border); background: rgba(15,15,15,0.8); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 10; }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-logo { font-weight: 800; font-size: 1.3rem; color: var(--n-red); }
        .topbar-logo span { color: #fff; font-weight: 300; }
        .topbar-back { color: #888; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid var(--n-border); border-radius: 8px; transition: all 0.2s; }
        .topbar-back:hover { border-color: var(--n-red); color: #fff; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; width: 100%; flex: 1; }
        .hero { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        @media (max-width: 900px) { .hero { grid-template-columns: 1fr; } }
        .product-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 20px; padding: 2rem; position: relative; overflow: hidden; animation: slideUp 0.6s ease; }
        .product-card::before { content: ''; position: absolute; top: -50%; right: -30%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(229,9,20,0.06) 0%, transparent 70%); pointer-events: none; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .product-img { width: 100%; height: 220px; object-fit: contain; background: #0a0a0a; border-radius: 12px; margin-bottom: 1rem; }
        .product-img-placeholder { width: 100%; height: 220px; background: #0a0a0a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #333; margin-bottom: 1rem; }
        .product-name { font-size: 1.6rem; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .product-code { color: #666; font-size: 0.8rem; font-family: monospace; margin-bottom: 1rem; }
        .product-stats { display: flex; gap: 1.5rem; margin-top: 1rem; }
        .product-stat { text-align: center; }
        .product-stat-value { font-size: 1.8rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .product-stat-label { font-size: 0.65rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 20px; padding: 2rem; animation: slideUp 0.6s ease 0.2s both; }
        .form-label { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: 600; }
        .form-control, .form-select { background: #0f0f0f; border: 1px solid var(--n-border); color: #fff; border-radius: 12px; padding: 0.8rem 1rem; font-size: 0.95rem; transition: all 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--n-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.15); background: #0f0f0f; color: #fff; }
        .btn-submit { width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 700; border: none; border-radius: 14px; background: linear-gradient(135deg, var(--n-red), #b20710); color: #fff; cursor: pointer; transition: all 0.3s; position: relative; overflow: hidden; }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(229,9,20,0.3); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit.loading { pointer-events: none; }
        .btn-submit.loading::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); background-size: 200% 100%; animation: shimmer 1.2s infinite; }
        .info-box { background: rgba(0,184,148,0.06); border: 1px solid rgba(0,184,148,0.2); border-radius: 12px; padding: 1rem; margin-top: 1rem; display: none; animation: fadeIn 0.4s ease; }
        .info-box.show { display: block; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.9rem; border-bottom: 1px solid #1f1f1f; }
        .info-row:last-child { border: none; }
        .info-label { color: #888; }
        .info-value { color: #fff; font-weight: 600; }
        .history-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1.5rem; animation: slideUp 0.6s ease 0.4s both; }
        .history-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #1a1a1a; font-size: 0.85rem; }
        .history-item:last-child { border: none; }
        .history-dest { color: #00b894; font-weight: 600; }
        .history-meta { color: #666; font-size: 0.75rem; }
        .step-indicator { display: flex; gap: 1rem; margin-bottom: 2rem; animation: fadeIn 0.6s ease; }
        .step { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #555; }
        .step.active { color: var(--n-red); }
        .step.active .step-num { background: var(--n-red); color: #fff; }
        .step.completed { color: #00b894; }
        .step.completed .step-num { background: #00b894; color: #fff; }
        .step-num { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: #2a2a2a; color: #666; transition: all 0.3s; }
        .step-line { flex: 1; height: 1px; background: #2a2a2a; margin: 0 4px; }
        .step-line.completed { background: #00b894; }
        .sucursal-option { display: flex; justify-content: space-between; align-items: center; }
        .sucursal-option small { color: #666; font-size: 0.75rem; }
        .quantity-selector { display: flex; align-items: center; gap: 0; background: #0f0f0f; border: 1px solid var(--n-border); border-radius: 12px; overflow: hidden; }
        .quantity-selector button { width: 48px; height: 48px; border: none; background: transparent; color: #888; font-size: 1.4rem; cursor: pointer; transition: all 0.2s; }
        .quantity-selector button:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .quantity-selector input { width: 80px; height: 48px; border: none; border-left: 1px solid var(--n-border); border-right: 1px solid var(--n-border); background: transparent; color: #fff; text-align: center; font-size: 1.2rem; font-weight: 700; outline: none; }
        .badge-pill { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 600; }
        .badge-pill.success { background: rgba(0,184,148,0.15); color: #00b894; }
        .badge-pill.danger { background: rgba(229,9,20,0.15); color: var(--n-red); }
        .badge-pill.warning { background: rgba(253,203,110,0.15); color: #fdcb6e; }
    </style>
</head>
<body>
<div class="bg-glow"></div>
<div class="bg-glow-2"></div>
<div class="page">
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-logo">OSWA <span>Inv</span></div>
            <a href="{{ route('catalogo') }}" class="topbar-back"><i class="bi bi-arrow-left"></i> Catálogo</a>
        </div>
        <div style="color:#555;font-size:0.8rem;"><i class="bi bi-send-fill me-1" style="color:var(--n-red);"></i>Nueva Transferencia</div>
    </div>

    <div class="container">
        <div class="step-indicator">
            <div class="step active" id="step1"><div class="step-num">1</div> Producto</div>
            <div class="step-line" id="stepLine2"></div>
            <div class="step" id="step2"><div class="step-num">2</div> Destino</div>
            <div class="step-line" id="stepLine3"></div>
            <div class="step" id="step3"><div class="step-num">3</div> Confirmar</div>
        </div>

        <div class="hero">
            <div class="product-card">
                @if($producto->imagen && !str_starts_with($producto->imagen, 'http'))
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="" class="product-img" onerror="this.parentElement.innerHTML='<div class=product-img-placeholder><i class=bi bi-box-seam></i></div>'">
                @elseif($producto->imagen && str_starts_with($producto->imagen, 'http'))
                    <img src="{{ $producto->imagen }}" alt="" class="product-img" onerror="this.parentElement.innerHTML='<div class=product-img-placeholder><i class=bi bi-box-seam></i></div>'">
                @else
                    <div class="product-img-placeholder"><i class="bi bi-box-seam"></i></div>
                @endif
                <div class="product-name">{{ $producto->nombre }}</div>
                <div class="product-code"><i class="bi bi-upc-scan me-1"></i>{{ $producto->codigo }} <span class="badge-pill {{ $producto->stock > $producto->stock_minimo ? 'success' : ($producto->stock == 0 ? 'danger' : 'warning') }} ms-2">{{ $producto->categoria }}</span></div>
                <div class="product-stats">
                    <div class="product-stat"><div class="product-stat-value" style="color:{{ $producto->stock == 0 ? 'var(--n-red)' : '#00b894' }};">{{ $producto->stock }}</div><div class="product-stat-label">Stock Actual</div></div>
                    <div class="product-stat"><div class="product-stat-value" style="color:#ffd700;">${{ number_format($producto->precio, 2) }}</div><div class="product-stat-label">Precio USD</div></div>
                    <div class="product-stat"><div class="product-stat-value" style="color:#888;">{{ $producto->stock_minimo }}</div><div class="product-stat-label">Stock Mínimo</div></div>
                </div>
            </div>

            <div class="form-card">
                <div style="font-size:1.1rem;font-weight:700;margin-bottom:1.5rem;color:#fff;"><i class="bi bi-send-fill me-2" style="color:var(--n-red);"></i>Datos de Transferencia</div>

                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-box-seam me-1"></i> Cantidad a transferir</div>
                    <div class="quantity-selector">
                        <button onclick="const i=document.getElementById('cantidad'); if(parseInt(i.value)>1) i.value=parseInt(i.value)-1; i.dispatchEvent(new Event('input'));">−</button>
                        <input type="number" id="cantidad" value="1" min="1" max="{{ $producto->stock }}">
                        <button onclick="const i=document.getElementById('cantidad'); if(parseInt(i.value)<parseInt(i.max)) i.value=parseInt(i.value)+1; i.dispatchEvent(new Event('input'));">+</button>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-geo-alt me-1"></i> Sucursal destino</div>
                    <select class="form-select" id="sucursal" onchange="actualizarInfo()">
                        <option value="">Seleccionar sucursal...</option>
                        @foreach($sucursales as $nombre => $data)
                        <option value="{{ $nombre }}" data-dist="{{ $data['dist'] }}" data-lat="{{ $data['lat'] }}" data-lng="{{ $data['lng'] }}">
                            {{ $nombre }} @if($data['dist'] > 0)({{ number_format($data['dist']) }} km)@else (Sede Principal)@endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-chat-text me-1"></i> Nota (opcional)</div>
                    <textarea class="form-control" id="nota" rows="2" placeholder="Ej: Urgente, productos para promoción..."></textarea>
                </div>

                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-camera me-1"></i> Soporte foto (opcional)</div>
                    <div style="display:flex;gap:10px;">
                        <input type="file" class="form-control" id="soporteFoto" accept="image/*" capture="environment" style="flex:1;">
                        <button type="button" class="btn" style="background:#0f0f0f;border:1px solid var(--n-border);border-radius:12px;color:#888;width:48px;" onclick="document.getElementById('soporteFoto').value='';mostrarToast('Foto eliminada','bi bi-camera')"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>

                <div class="info-box" id="infoBox">
                    <div class="info-row"><span class="info-label"><i class="bi bi-signpost-2 me-1"></i>Distancia</span><span class="info-value" id="distanciaLabel">—</span></div>
                    <div class="info-row"><span class="info-label"><i class="bi bi-truck me-1"></i>Destino</span><span class="info-value" id="destinoLabel" style="color:#00b894;">—</span></div>
                    <div class="info-row"><span class="info-label"><i class="bi bi-box me-1"></i>Stock resultante</span><span class="info-value" id="stockResultante" style="color:#ffd700;">—</span></div>
                </div>

                <button class="btn-submit mt-3" id="btnTransferir" onclick="confirmarTransferencia()">
                    <i class="bi bi-send-fill me-2"></i>Transferir a Sucursal
                </button>
            </div>
        </div>

        @if($ultimasTransferencias->count() > 0)
        <div class="history-card">
            <div style="font-size:1rem;font-weight:600;margin-bottom:1rem;color:#fff;"><i class="bi bi-clock-history me-2" style="color:var(--n-red);"></i>Transferencias anteriores de este producto</div>
            @foreach($ultimasTransferencias as $t)
            @php $dest = str_replace('Transferencia a ', '', $t->motivo); @endphp
            <div class="history-item">
                <div><span class="history-dest">→ {{ $dest }}</span></div>
                <div><span style="color:#fff;font-weight:600;">{{ $t->cantidad }} uds</span> <span class="history-meta">· {{ $t->created_at->format('d/m/Y H:i') }} · {{ $t->usuario_accion }}</span></div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function mostrarToast(mensaje, icono) {
    const container = document.getElementById('toast-container') || (() => {
        const c = document.createElement('div');
        c.id = 'toast-container';
        c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(c);
        return c;
    })();
    const t = document.createElement('div');
    t.style.cssText = 'background:#1a1a1a;border:1px solid #2a2a2a;border-radius:12px;padding:12px 20px;color:#fff;font-size:0.9rem;display:flex;align-items:center;gap:10px;animation:slideUp 0.3s ease;box-shadow:0 8px 30px rgba(0,0,0,0.5);';
    t.innerHTML = '<i class="' + icono + '" style="color:var(--n-red);"></i> ' + mensaje;
    container.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity 0.3s'; setTimeout(() => t.remove(), 300); }, 3000);
}

let productoStock = {{ $producto->stock }};

function actualizarInfo() {
    const sel = document.getElementById('sucursal');
    const cant = parseInt(document.getElementById('cantidad').value) || 1;
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('infoBox');

    if (opt && opt.value) {
        avanzarPaso(2);
        const dist = parseInt(opt.getAttribute('data-dist')) || 0;
        document.getElementById('distanciaLabel').textContent = dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal';
        document.getElementById('destinoLabel').textContent = opt.value;
        document.getElementById('stockResultante').textContent = Math.max(0, productoStock - cant) + ' uds';
        box.classList.add('show');
    } else {
        avanzarPaso(1);
        box.classList.remove('show');
    }
}

function avanzarPaso(paso) {
    for (let i = 1; i <= 3; i++) {
        const s = document.getElementById('step' + i);
        const l = document.getElementById('stepLine' + (i + 1));
        if (s) {
            s.classList.remove('active', 'completed');
            if (i < paso) s.classList.add('completed');
            else if (i === paso) s.classList.add('active');
        }
        if (l) {
            l.classList.toggle('completed', i < paso);
        }
    }
}

function confirmarTransferencia() {
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const sucursal = document.getElementById('sucursal').value;
    const nota = document.getElementById('nota').value;
    const soporte = document.getElementById('soporteFoto').files[0];

    if (!sucursal) { mostrarToast('Selecciona una sucursal destino', 'bi bi-geo-alt'); return; }
    if (!cantidad || cantidad < 1) { mostrarToast('Cantidad inválida', 'bi bi-box'); return; }
    if (cantidad > productoStock) { mostrarToast('Stock insuficiente (disponible: ' + productoStock + ')', 'bi bi-exclamation-triangle'); return; }

    avanzarPaso(3);

    const opt = document.querySelector('#sucursal option[value="' + sucursal + '"]');
    const dist = opt ? parseInt(opt.getAttribute('data-dist')) : 0;

    const btn = document.getElementById('btnTransferir');
    btn.classList.add('loading');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

    const reader = new FileReader();
    reader.onload = function(e) {
        const soporteBase64 = e.target ? e.target.result : null;

        fetch('/transferir-producto', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') },
            body: JSON.stringify({
                producto_id: {{ $producto->id }},
                cantidad: cantidad,
                sucursal: sucursal,
                soporte_base64: soporteBase64,
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.classList.remove('loading');
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '✅ Transferencia Exitosa',
                    html:
                        '<div style="text-align:left;color:#ccc;font-size:0.9rem;">' +
                        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Producto</span><span style="color:#fff;font-weight:600;">{{ $producto->nombre }}</span></div>' +
                        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Destino</span><span style="color:#00b894;font-weight:600;">' + sucursal + '</span></div>' +
                        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Cantidad</span><span style="color:#fff;font-weight:600;">' + cantidad + ' uds</span></div>' +
                        (dist > 0 ? '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Distancia</span><span style="color:#fff;">' + dist.toLocaleString() + ' km</span></div>' : '') +
                        '<div style="display:flex;justify-content:space-between;padding:6px 0;"><span style="color:#888;">Fecha</span><span style="color:#888;">' + new Date(data.fecha).toLocaleString('es-ES') + '</span></div>' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonColor: '#00b894',
                    cancelButtonColor: '#444',
                    confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Listo',
                    cancelButtonText: '<i class="bi bi-printer me-1"></i>Imprimir Guía',
                    background: '#1a1a1a',
                    color: '#fff',
                    customClass: { popup: 'border border-secondary shadow-lg' }
                }).then((r) => {
                    if (r.isConfirmed) window.location.href = '{{ route('catalogo') }}';
                    else if (r.dismiss === Swal.DismissReason.cancel) {
                        const pdfUrl = '/transferencia/pdf?producto=' + encodeURIComponent('{{ $producto->nombre }}') +
                            '&cantidad=' + cantidad +
                            '&sucursal=' + encodeURIComponent(sucursal) +
                            '&distancia=' + dist +
                            '&fecha=' + encodeURIComponent(data.fecha);
                        window.open(pdfUrl, '_blank');
                    }
                });
            } else {
                btn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Transferir a Sucursal';
                mostrarToast(data.message || 'Error al transferir', 'bi bi-exclamation-triangle');
            }
        })
        .catch(() => {
            btn.classList.remove('loading');
            btn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Transferir a Sucursal';
            mostrarToast('Error de conexión', 'bi bi-exclamation-triangle');
        });
    };
    if (soporte) { reader.readAsDataURL(soporte); } else { reader.onload({ target: null }); }
}
</script>
</body>
</html>
