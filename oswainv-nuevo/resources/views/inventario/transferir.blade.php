<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transferir - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root { --bg: #0f0f0f; --card: #1a1a1a; --n-red: #E50914; --n-border: #2a2a2a; --text: #e5e5e5; --green: #00b894; }
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; }
        .bg-glow { position: fixed; top: -30%; left: -10%; width: 50%; height: 80%; background: radial-gradient(circle, rgba(229,9,20,0.06) 0%, transparent 70%); pointer-events: none; z-index: 0; }
        .bg-glow-2 { position: fixed; bottom: -30%; right: -10%; width: 50%; height: 80%; background: radial-gradient(circle, rgba(0,184,148,0.05) 0%, transparent 70%); pointer-events: none; z-index: 0; }
        .page { position: relative; z-index: 1; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 2rem; border-bottom: 1px solid var(--n-border); background: rgba(15,15,15,0.8); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 10; }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-logo { font-weight: 800; font-size: 1.3rem; color: var(--n-red); }
        .topbar-logo span { color: #fff; font-weight: 300; }
        .topbar-back { color: #888; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid var(--n-border); border-radius: 8px; transition: all 0.2s; }
        .topbar-back:hover { border-color: var(--n-red); color: #fff; }
        .container { max-width: 1300px; margin: 0 auto; padding: 1.5rem 2rem 2rem; width: 100%; flex: 1; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideLeft { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }
        @keyframes notifIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes progressFill { from { width: 0%; } to { width: var(--pct); } }

        /* STEPPER */
        .stepper { display: flex; justify-content: center; gap: 0; margin-bottom: 2rem; position: relative; padding: 0 1rem; }
        .stepper::before { content: ''; position: absolute; top: 24px; left: 15%; right: 15%; height: 2px; background: #222; z-index: 0; }
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; position: relative; z-index: 1; cursor: pointer; flex: 1; }
        .step-circle { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; border: 2px solid #333; background: var(--card); color: #555; transition: all 0.4s; position: relative; }
        .step.active .step-circle { border-color: var(--n-red); background: var(--n-red); color: #fff; box-shadow: 0 0 30px rgba(229,9,20,0.3); animation: pulse 1.5s infinite; }
        .step.done .step-circle { border-color: var(--green); background: var(--green); color: #fff; }
        .step-label { font-size: 0.72rem; color: #555; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; transition: color 0.3s; }
        .step.active .step-label { color: #fff; }
        .step.done .step-label { color: var(--green); }
        .step-desc { font-size: 0.6rem; color: #444; margin-top: -4px; }

        /* SEARCH + CHIPS */
        .search-section { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; animation: slideUp 0.5s ease; }
        .search-section .search-row { display: flex; align-items: center; gap: 10px; }
        .search-section input { background: #0f0f0f; border: 1px solid var(--n-border); color: #fff; flex: 1; outline: none; font-size: 0.9rem; padding: 0.7rem 1rem; border-radius: 10px; transition: all 0.3s; }
        .search-section input:focus { border-color: var(--n-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.15); }
        .search-section input::placeholder { color: #555; }
        .search-section i { color: #555; font-size: 1.1rem; }
        .mini-grid { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; max-height: 200px; overflow-y: auto; padding-right: 4px; }
        .mini-chip { display: flex; align-items: center; gap: 6px; padding: 5px 12px; background: #0f0f0f; border: 1px solid var(--n-border); border-radius: 20px; font-size: 0.75rem; color: #ccc; cursor: pointer; transition: all 0.2s; }
        .mini-chip:hover { border-color: var(--n-red); transform: translateY(-1px); }
        .mini-chip.selected { border-color: var(--green); background: rgba(0,184,148,0.08); }
        .mini-chip .chip-add { color: var(--n-red); font-size: 0.8rem; font-weight: 700; }
        .mini-chip.selected .chip-add { color: var(--green); }

        /* HERO */
        .hero { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; animation: slideLeft 0.6s ease; }
        @media (max-width: 900px) { .hero { grid-template-columns: 1fr; } }
        .product-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 20px; padding: 2rem; position: relative; overflow: hidden; }
        .product-card::before { content: ''; position: absolute; top: -50%; right: -30%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(229,9,20,0.06) 0%, transparent 70%); pointer-events: none; }
        .product-img { width: 100%; height: 220px; object-fit: contain; background: #0a0a0a; border-radius: 12px; margin-bottom: 1rem; transition: opacity 0.4s; }
        .product-img-placeholder { width: 100%; height: 220px; background: #0a0a0a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #333; margin-bottom: 1rem; }
        .product-name { font-size: 1.6rem; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .product-code { color: #666; font-size: 0.8rem; font-family: monospace; margin-bottom: 1rem; }
        .product-stats { display: flex; gap: 1.5rem; margin-top: 1rem; flex-wrap: wrap; }
        .product-stat { text-align: center; flex: 1; min-width: 80px; background: #0f0f0f; border-radius: 12px; padding: 0.8rem 0.5rem; border: 1px solid #1f1f1f; }
        .product-stat-value { font-size: 1.5rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .product-stat-label { font-size: 0.6rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
        .badge-pill { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 600; }
        .badge-pill.success { background: rgba(0,184,148,0.15); color: var(--green); }
        .badge-pill.danger { background: rgba(229,9,20,0.15); color: var(--n-red); }
        .badge-pill.warning { background: rgba(253,203,110,0.15); color: #fdcb6e; }

        /* FORM CARD */
        .form-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 20px; padding: 2rem; }
        .form-label { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: 600; }
        .form-control, .form-select { background: #0f0f0f; border: 1px solid var(--n-border); color: #fff; border-radius: 12px; padding: 0.8rem 1rem; font-size: 0.95rem; transition: all 0.3s; width: 100%; }
        .form-control:focus, .form-select:focus { border-color: var(--n-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.15); background: #0f0f0f; color: #fff; }
        .cart-items-box { background: #0f0f0f; border: 1px solid var(--n-border); border-radius: 12px; margin-bottom: 1rem; max-height: 220px; overflow-y: auto; }
        .cart-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-bottom: 1px solid #1f1f1f; transition: background 0.2s; }
        .cart-item:last-child { border: none; }
        .cart-item:hover { background: rgba(255,255,255,0.02); }
        .cart-item-name { flex: 1; font-size: 0.85rem; font-weight: 500; color: #fff; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; }
        .cart-item-name:hover { color: var(--n-red); }
        .cart-item-qty { display: flex; align-items: center; gap: 4px; }
        .cart-item-qty button { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--n-border); background: #0f0f0f; color: #888; font-size: 0.9rem; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; }
        .cart-item-qty button:hover { border-color: var(--n-red); color: #fff; background: rgba(229,9,20,0.08); }
        .cart-item-qty input { width: 40px; height: 28px; border: 1px solid var(--n-border); border-radius: 6px; background: #0f0f0f; color: #fff; text-align: center; font-size: 0.8rem; font-weight: 600; outline: none; }
        .cart-item-remove { color: #e74c3c; cursor: pointer; font-size: 0.85rem; opacity: 0.4; transition: all 0.2s; }
        .cart-item-remove:hover { opacity: 1; transform: scale(1.2); }

        /* CONFIRM CARD */
        .confirm-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 20px; padding: 2rem; animation: slideLeft 0.6s ease; }
        .confirm-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .confirm-table th { text-align: left; padding: 10px 12px; color: #888; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #2a2a2a; }
        .confirm-table td { padding: 10px 12px; border-bottom: 1px solid #1f1f1f; }
        .confirm-table tr:last-child td { border: none; }
        .confirm-total-row { background: rgba(0,184,148,0.04); }

        /* BUTTONS */
        .btn-primary { padding: 0.85rem 2rem; font-size: 0.95rem; font-weight: 600; border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-red { background: linear-gradient(135deg, var(--n-red), #b20710); color: #fff; }
        .btn-red:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(229,9,20,0.25); }
        .btn-red:disabled { opacity: 0.4; cursor: not-allowed; }
        .btn-outline { background: transparent; color: #888; border: 1px solid var(--n-border); }
        .btn-outline:hover { border-color: var(--n-red); color: #fff; }
        .btn-green { background: linear-gradient(135deg, var(--green), #009874); color: #fff; }
        .btn-green:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,184,148,0.25); }
        .btn-nav { display: flex; gap: 12px; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #1f1f1f; }

        /* INFO BOX */
        .info-box { background: rgba(0,184,148,0.06); border: 1px solid rgba(0,184,148,0.2); border-radius: 12px; padding: 1rem; margin-bottom: 1rem; display: none; animation: fadeIn 0.4s ease; }
        .info-box.show { display: block; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.9rem; border-bottom: 1px solid #1f1f1f; }
        .info-row:last-child { border: none; }
        .info-label { color: #888; }
        .info-value { color: #fff; font-weight: 600; }

        /* HISTORY */
        .history-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1.5rem; animation: slideUp 0.6s ease; }
        .history-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #1a1a1a; font-size: 0.85rem; }
        .history-item:last-child { border: none; }
        .history-dest { color: var(--green); font-weight: 600; }
        .history-meta { color: #666; font-size: 0.75rem; }

        /* NOTIFICATION TRAY */
        .notif-tray { position: fixed; top: 80px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; max-width: 380px; pointer-events: none; }
        .notif { background: var(--card); border: 1px solid var(--n-border); border-radius: 14px; padding: 14px 20px; color: #fff; font-size: 0.85rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 8px 40px rgba(0,0,0,0.6); animation: notifIn 0.4s ease; pointer-events: auto; cursor: pointer; transition: all 0.3s; }
        .notif:hover { transform: translateX(-6px); }
        .notif-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
        .notif-icon.info { background: rgba(0,184,148,0.15); color: var(--green); }
        .notif-icon.warn { background: rgba(253,203,110,0.15); color: #fdcb6e; }
        .notif-icon.err { background: rgba(229,9,20,0.15); color: var(--n-red); }
        .notif-icon.success { background: rgba(0,184,148,0.15); color: var(--green); }
        .notif-body { flex: 1; }
        .notif-title { font-weight: 600; font-size: 0.8rem; }
        .notif-text { color: #999; font-size: 0.75rem; margin-top: 1px; }

        /* SCROLL */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--card); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

        .empty-cart { text-align: center; padding: 1.5rem; color: #555; font-size: 0.85rem; }
        .empty-cart i { font-size: 2rem; display: block; margin-bottom: 6px; }

        .step-panel { display: none; animation: slideLeft 0.5s ease; }
        .step-panel.active { display: block; }

        /* SUMMARY MINI */
        .summary-mini { background: #0f0f0f; border: 1px solid #1f1f1f; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; display: flex; gap: 1rem; flex-wrap: wrap; }
        .summary-mini-item { flex: 1; min-width: 100px; text-align: center; }
        .summary-mini-value { font-size: 1.3rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .summary-mini-label { font-size: 0.6rem; color: #666; text-transform: uppercase; letter-spacing: 0.3px; }

        .glow-btn { position: relative; overflow: hidden; }
        .glow-btn::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%); opacity: 0; transition: opacity 0.4s; }
        .glow-btn:hover::after { opacity: 1; }
    </style>
</head>
<body>
<div class="bg-glow"></div>
<div class="bg-glow-2"></div>
<div class="notif-tray" id="notifTray"></div>
<div class="page">
<div class="topbar">
    <div class="topbar-left">
        <div class="topbar-logo">OSWA <span>Inv</span></div>
        <a href="{{ route('catalogo') }}" class="topbar-back"><i class="bi bi-arrow-left"></i> Catálogo</a>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="color:#555;font-size:0.8rem;"><i class="bi bi-send-fill me-1" style="color:var(--n-red);"></i>Transferencia Múltiple</span>
    </div>
</div>

<div class="container">
    <div class="stepper">
        <div class="step active" data-step="1" onclick="irPaso(1)">
            <div class="step-circle">1</div>
            <div class="step-label">Productos</div>
            <div class="step-desc">Seleccionar</div>
        </div>
        <div class="step" data-step="2" onclick="irPaso(2)">
            <div class="step-circle">2</div>
            <div class="step-label">Destino</div>
            <div class="step-desc">Configurar</div>
        </div>
        <div class="step" data-step="3" onclick="irPaso(3)">
            <div class="step-circle">3</div>
            <div class="step-label">Confirmar</div>
            <div class="step-desc">Revisar</div>
        </div>
    </div>

    <!-- PASO 1: Productos -->
    <div class="step-panel active" data-panel="1">
        <div class="search-section">
            <div class="search-row">
                <i class="bi bi-search"></i>
                <input type="text" id="buscador" placeholder="Buscar productos para agregar a la transferencia..." oninput="filtrarChips(this.value)">
                <span style="color:#555;font-size:0.75rem;white-space:nowrap;" id="contadorChips">0 seleccionados</span>
            </div>
            <div class="mini-grid" id="chipGrid">
                @forelse($productos as $p)
                <div class="mini-chip{{ $producto && $p->id === $producto->id ? ' selected' : '' }}" data-nombre="{{ strtolower($p->nombre) }}" data-codigo="{{ $p->codigo }}" data-id="{{ $p->id }}" onclick="toggleProducto({{ $p->id }})">
                    <span class="chip-add">{{ $producto && $p->id === $producto->id ? '✓' : '+' }}</span>
                    {{ $p->nombre }}
                    <span style="color:#666;font-size:0.65rem;margin-left:4px;">{{ $p->stock }} {{ $p->unidad_medida ?? 'uds' }}</span>
                </div>
                @empty
                <div style="color:#555;font-size:0.8rem;">No hay productos con stock disponible</div>
                @endforelse
            </div>
        </div>

        <div class="hero">
            <div class="product-card">
                <div id="productImgContainer">
                    @if($producto && $producto->imagen && !str_starts_with($producto->imagen, 'http'))
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="" class="product-img" onerror="this.parentElement.innerHTML='<div class=\'product-img-placeholder\'><i class=\'bi bi-box-seam\'></i></div>'">
                    @elseif($producto && $producto->imagen && str_starts_with($producto->imagen, 'http'))
                        <img src="{{ $producto->imagen }}" alt="" class="product-img" onerror="this.parentElement.innerHTML='<div class=\'product-img-placeholder\'><i class=\'bi bi-box-seam\'></i></div>'">
                    @else
                        <div class="product-img-placeholder"><i class="bi bi-box-seam"></i></div>
                    @endif
                </div>
                <div class="product-name" id="detalleNombre">{{ $producto->nombre ?? 'Selecciona un producto' }}</div>
                <div class="product-code" id="detalleCodigo"><i class="bi bi-upc-scan me-1"></i>{{ $producto->codigo ?? '—' }} <span class="badge-pill success ms-2" id="detalleCategoria">{{ $producto->categoria ?? '' }}</span></div>
                <div class="product-stats">
                    <div class="product-stat">
                        <div class="product-stat-value" id="detalleStock" style="color:{{ ($producto && $producto->stock == 0) ? 'var(--n-red)' : 'var(--green)' }};">{{ $producto->stock ?? 0 }}</div>
                        <div class="product-stat-label">Stock Actual</div>
                    </div>
                    <div class="product-stat">
                        <div class="product-stat-value" id="detallePrecio" style="color:#ffd700;">{{ $producto ? '$' . number_format($producto->precio, 2) : '$0.00' }}</div>
                        <div class="product-stat-label">Precio USD</div>
                    </div>
                    <div class="product-stat">
                        <div class="product-stat-value" style="color:#888;" id="detalleMin">{{ $producto->stock_minimo ?? 0 }}</div>
                        <div class="product-stat-label">Stock Mínimo</div>
                    </div>
                    <div class="product-stat">
                        <div class="product-stat-value" id="detalleEnCarrito" style="color:var(--n-red);">0</div>
                        <div class="product-stat-label">En Carrito</div>
                    </div>
                </div>
                <div class="btn-nav">
                    <button class="btn-primary btn-red glow-btn" onclick="irPaso(2)" id="btnPaso1" disabled>
                        <i class="bi bi-arrow-right"></i> Continuar
                    </button>
                </div>
            </div>

            <div class="form-card">
                <div style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;color:#fff;display:flex;align-items:center;justify-content:space-between;">
                    <span><i class="bi bi-cart-fill me-2" style="color:var(--n-red);"></i>Carrito</span>
                    <span id="cartBadge" style="font-size:0.7rem;color:#888;background:#0f0f0f;padding:2px 10px;border-radius:20px;">0</span>
                </div>
                <div id="cartContainer">
                    <div class="empty-cart" id="cartEmpty">
                        <i class="bi bi-cart"></i>
                        Selecciona productos arriba
                    </div>
                    <div class="cart-items-box" id="cartItemsBox" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 2: Destino -->
    <div class="step-panel" data-panel="2">
        <div class="summary-mini" id="resumenMini">
            <div class="summary-mini-item">
                <div class="summary-mini-value" id="miniProductos" style="color:var(--green);">0</div>
                <div class="summary-mini-label">Productos</div>
            </div>
            <div class="summary-mini-item">
                <div class="summary-mini-value" id="miniUnidades" style="color:var(--n-red);">0</div>
                <div class="summary-mini-label">Unidades</div>
            </div>
            <div class="summary-mini-item">
                <div class="summary-mini-value" id="miniDistancia" style="color:#ffd700;">—</div>
                <div class="summary-mini-label">Distancia</div>
            </div>
            <div class="summary-mini-item">
                <div class="summary-mini-value" id="miniDestino" style="color:#888;font-size:0.9rem;">—</div>
                <div class="summary-mini-label">Destino</div>
            </div>
            <div class="summary-mini-item">
                <div class="summary-mini-value" id="miniDiesel" style="color:#f39c12;font-size:1rem;">—</div>
                <div class="summary-mini-label">Di&eacute;sel</div>
            </div>
            <div class="summary-mini-item">
                <div class="summary-mini-value" id="miniTiempo" style="color:#3498db;font-size:1rem;">—</div>
                <div class="summary-mini-label">Tiempo est.</div>
            </div>
        </div>

        <div class="hero">
            <div class="form-card">
                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-geo-alt me-1"></i> Sucursal destino</div>
                    <select class="form-select" id="sucursal" onchange="actualizarInfo()">
                        <option value="">Seleccionar sucursal...</option>
                        @foreach($sucursales as $nombre => $data)
                        <option value="{{ $nombre }}" data-dist="{{ $data['dist'] }}">{{ $nombre }} @if($data['dist'] > 0)({{ number_format($data['dist']) }} km)@else (Sede Principal)@endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-chat-text me-1"></i> Nota (opcional)</div>
                    <textarea class="form-control" id="nota" rows="2" placeholder="Ej: Productos para promoción..."></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-label"><i class="bi bi-camera me-1"></i> Soporte foto (opcional)</div>
                    <div style="display:flex;gap:10px;">
                        <input type="file" class="form-control" id="soporteFoto" accept="image/*" capture="environment" style="flex:1;">
                        <button type="button" class="btn" style="background:#0f0f0f;border:1px solid var(--n-border);border-radius:12px;color:#888;width:48px;" onclick="document.getElementById('soporteFoto').value='';notificar('info','Foto eliminada')"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                <div class="info-box" id="infoBox">
                    <div class="info-row"><span class="info-label"><i class="bi bi-signpost-2 me-1"></i>Distancia</span><span class="info-value" id="distanciaLabel">—</span></div>
                    <div class="info-row"><span class="info-label"><i class="bi bi-truck me-1"></i>Destino</span><span class="info-value" id="destinoLabel" style="color:var(--green);">—</span></div>
                    <div class="info-row"><span class="info-label"><i class="bi bi-box me-1"></i>Total unidades</span><span class="info-value" id="totalProductosLabel" style="color:#ffd700;">0</span></div>
                </div>
                <div class="btn-nav">
                    <button class="btn-primary btn-outline" onclick="irPaso(1)"><i class="bi bi-arrow-left"></i> Volver</button>
                    <button class="btn-primary btn-red glow-btn" onclick="irPaso(3)" id="btnPaso2">
                        <i class="bi bi-arrow-right"></i> Revisar
                    </button>
                </div>
            </div>
            <div class="form-card" style="display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;min-height:300px;background:var(--card);">
                <div style="font-size:3rem;color:#333;margin-bottom:1rem;"><i class="bi bi-truck"></i></div>
                <div style="color:#666;font-size:0.9rem;max-width:280px;">
                    Configura el destino y detalles del envío para los <strong style="color:#fff;" id="infoTotalProds">0</strong> productos seleccionados.
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 3: Confirmar -->
    <div class="step-panel" data-panel="3">
        <div class="confirm-card">
            <div style="font-size:1.2rem;font-weight:700;margin-bottom:1.5rem;color:#fff;display:flex;align-items:center;gap:12px;">
                <span style="width:40px;height:40px;border-radius:12px;background:rgba(0,184,148,0.15);color:var(--green);display:flex;align-items:center;justify-content:center;"><i class="bi bi-check-lg"></i></span>
                Revisión Final
                <span style="font-size:0.75rem;color:#555;font-weight:400;">— Verifica los datos antes de transferir</span>
            </div>

            <div class="summary-mini">
                <div class="summary-mini-item">
                    <div class="summary-mini-value" style="color:var(--green);" id="confirmProductos">0</div>
                    <div class="summary-mini-label">Productos</div>
                </div>
                <div class="summary-mini-item">
                    <div class="summary-mini-value" style="color:var(--n-red);" id="confirmUnidades">0</div>
                    <div class="summary-mini-label">Unidades</div>
                </div>
                <div class="summary-mini-item">
                    <div class="summary-mini-value" style="color:#ffd700;" id="confirmDistancia">—</div>
                    <div class="summary-mini-label">Distancia</div>
                </div>
                <div class="summary-mini-item">
                    <div class="summary-mini-value" style="color:#888;font-size:0.9rem;" id="confirmDestino">—</div>
                    <div class="summary-mini-label">Destino</div>
                </div>
                <div class="summary-mini-item">
                    <div class="summary-mini-value" style="color:#f39c12;font-size:1rem;" id="confirmDiesel">—</div>
                    <div class="summary-mini-label">Di&eacute;sel</div>
                </div>
                <div class="summary-mini-item">
                    <div class="summary-mini-value" style="color:#3498db;font-size:1rem;" id="confirmTiempo">—</div>
                    <div class="summary-mini-label">Tiempo est.</div>
                </div>
            </div>

            <div style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                <div style="flex:1;min-width:250px;border-radius:12px;overflow:hidden;border:1px solid #1f1f1f;background:#0a0a0a;">
                    <div id="mapaConfirm" style="width:100%;height:200px;"></div>
                </div>
                <div style="flex:0 0 auto;min-width:160px;display:flex;flex-direction:column;gap:8px;justify-content:center;">
                    <div style="background:#0f0f0f;border-radius:10px;padding:10px 14px;border:1px solid #1f1f1f;">
                        <div style="font-size:0.6rem;color:#666;text-transform:uppercase;letter-spacing:0.3px;">Rendimiento</div>
                        <div style="font-size:0.85rem;color:#fff;font-weight:600;"><span id="confirmLitros">0</span> L di&eacute;sel</div>
                    </div>
                    <div style="background:#0f0f0f;border-radius:10px;padding:10px 14px;border:1px solid #1f1f1f;">
                        <div style="font-size:0.6rem;color:#666;text-transform:uppercase;letter-spacing:0.3px;">Velocidad media</div>
                        <div style="font-size:0.85rem;color:#fff;font-weight:600;">50 km/h</div>
                    </div>
                </div>
            </div>
            <table class="confirm-table">
                <thead><tr><th>#</th><th>Producto</th><th>Código</th><th>Cantidad</th><th>Stock restante</th></tr></thead>
                <tbody id="confirmTableBody"></tbody>
            </table>

            <div style="display:flex;gap:8px;align-items:center;margin-bottom:1.5rem;padding:0.8rem 1rem;background:rgba(229,9,20,0.04);border:1px solid rgba(229,9,20,0.1);border-radius:12px;">
                <i class="bi bi-info-circle-fill" style="color:var(--n-red);font-size:1rem;"></i>
                <span style="color:#999;font-size:0.8rem;">Al confirmar se descontar&aacute; el stock de los productos seleccionados.</span>
            </div>

            <div class="btn-nav" style="border:none;padding-top:0;">
                <button class="btn-primary btn-outline" onclick="irPaso(2)"><i class="bi bi-arrow-left"></i> Atr&aacute;s</button>
                <button class="btn-primary btn-green glow-btn" onclick="imprimirGuia()" style="min-width:160px;" id="btnImprimirGuia">
                    <i class="bi bi-printer"></i> Imprimir Gu&iacute;a
                </button>
                <button class="btn-primary btn-red glow-btn" id="btnTransferir" onclick="confirmarTransferencia()" style="min-width:220px;">
                    <i class="bi bi-send-fill"></i> Transferir Ahora
                </button>
            </div>
        </div>
    </div>

    @if($ultimasTransferencias->count() > 0)
    <div class="history-card" style="margin-top:1.5rem;">
        <div style="font-size:1rem;font-weight:600;margin-bottom:1rem;color:#fff;"><i class="bi bi-clock-history me-2" style="color:var(--n-red);"></i>&Uacute;ltimas transferencias</div>
        @foreach($ultimasTransferencias as $t)
        @php $dest = str_replace('Transferencia a ', '', $t->motivo); $sep = strpos($dest, ' |'); if($sep !== false) $dest = substr($dest, 0, $sep); @endphp
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
const productosData = {};
@foreach($productos as $p)
productosData[{{ $p->id }}] = {
    id: {{ $p->id }}, nombre: @json($p->nombre), codigo: @json($p->codigo),
    stock: {{ $p->stock }}, unidad: @json($p->unidad_medida ?? "uds"),
    precio: {{ $p->precio }}, categoria: @json($p->categoria),
    stock_minimo: {{ $p->stock_minimo ?? 5 }},
    imagen: @json($p->imagen)
};
@endforeach

const carrito = {};
let selectedId = null;
let pasoActual = 1;
let ultimosMovimientosIds = [];

@if($producto && $producto->stock > 0)
selectedId = {{ $producto->id }};
carrito[{{ $producto->id }}] = { ...productosData[{{ $producto->id }}], cantidad: 1 };
actualizarDetalle({{ $producto->id }});
@endif

function toggleProducto(id) {
    const chip = document.querySelector(`.mini-chip[data-id="${id}"]`);
    if (!chip) return;
    const addSpan = chip.querySelector('.chip-add');
    if (carrito[id]) {
        delete carrito[id];
        chip.classList.remove('selected');
        if (addSpan) addSpan.textContent = '+';
        if (selectedId === id) {
            const keys = Object.keys(carrito);
            selectedId = keys.length > 0 ? parseInt(keys[0]) : null;
            if (selectedId) actualizarDetalle(selectedId);
        }
    } else {
        if (!productosData[id]) { notificar('err', 'Producto no encontrado'); return; }
        carrito[id] = { ...productosData[id], cantidad: 1 };
        chip.classList.add('selected');
        if (addSpan) addSpan.textContent = '\u2713';
        if (Object.keys(carrito).length === 1) { selectedId = id; actualizarDetalle(id); }
    }
    renderCarrito();
    actualizarContador();
}

function actualizarContador() {
    const n = Object.keys(carrito).length;
    document.getElementById('contadorChips').textContent = n + ' seleccionado' + (n !== 1 ? 's' : '');
}

function actualizarDetalle(id) {
    const p = productosData[id];
    if (!p) return;
    selectedId = id;
    document.getElementById('detalleNombre').textContent = p.nombre;
    document.getElementById('detalleCodigo').innerHTML = '<i class="bi bi-upc-scan me-1"></i>' + p.codigo + ' <span class="badge-pill success ms-2">' + p.categoria + '</span>';
    document.getElementById('detalleStock').textContent = p.stock;
    document.getElementById('detalleStock').style.color = p.stock <= p.stock_minimo ? 'var(--n-red)' : 'var(--green)';
    document.getElementById('detallePrecio').textContent = '$' + parseFloat(p.precio).toFixed(2);
    document.getElementById('detalleMin').textContent = p.stock_minimo;
    actualizarImagen(p.imagen);
    actualizarEnCarrito(id);
}

function actualizarImagen(imagen) {
    const container = document.getElementById('productImgContainer');
    if (!imagen) {
        container.innerHTML = '<div class="product-img-placeholder"><i class="bi bi-box-seam"></i></div>';
        return;
    }
    const src = imagen.startsWith('http') ? imagen : '/storage/' + imagen;
    const img = new Image();
    img.className = 'product-img';
    img.alt = '';
    img.onerror = function() { container.innerHTML = '<div class="product-img-placeholder"><i class="bi bi-box-seam"></i></div>'; };
    img.onload = function() { container.innerHTML = ''; container.appendChild(img); };
    img.src = src;
}

function actualizarEnCarrito(id) {
    const item = carrito[id];
    document.getElementById('detalleEnCarrito').textContent = item ? item.cantidad : 0;
}

function renderCarrito() {
    const box = document.getElementById('cartItemsBox');
    const empty = document.getElementById('cartEmpty');
    const badge = document.getElementById('cartBadge');
    const btnPaso1 = document.getElementById('btnPaso1');
    const keys = Object.keys(carrito);
    badge.textContent = keys.length;
    if (btnPaso1) btnPaso1.disabled = keys.length === 0;
    if (keys.length === 0) {
        empty.style.display = 'block'; box.style.display = 'none';
        return;
    }
    empty.style.display = 'none'; box.style.display = 'block';
    let html = '';
    for (const id of keys) {
        const item = carrito[id];
        html += `<div class="cart-item">
            <div class="cart-item-name" onclick="actualizarDetalle(${id})">${item.nombre}</div>
            <div class="cart-item-qty">
                <button onclick="cambiarCantidad(${id},-1)">−</button>
                <input type="number" value="${item.cantidad}" min="1" max="${item.stock}" onfocus="this.select()" onchange="cambiarCantidadInput(${id},this.value)">
                <button onclick="cambiarCantidad(${id},1)">+</button>
            </div>
            <div class="cart-item-remove" onclick="toggleProducto(${id})"><i class="bi bi-x-lg"></i></div>
        </div>`;
    }
    box.innerHTML = html;
    if (selectedId) actualizarEnCarrito(selectedId);
}

function cambiarCantidad(id, delta) {
    if (!carrito[id]) return;
    const n = carrito[id].cantidad + delta;
    if (n < 1) return;
    if (n > carrito[id].stock) { notificar('warn', 'Stock m\u00e1ximo: ' + carrito[id].stock); return; }
    carrito[id].cantidad = n;
    renderCarrito();
}

function cambiarCantidadInput(id, val) {
    const n = parseInt(val);
    if (!n || n < 1) { renderCarrito(); return; }
    if (n > carrito[id]?.stock) {
        notificar('warn', 'Stock m\u00e1ximo: ' + carrito[id].stock);
        carrito[id].cantidad = carrito[id].stock;
    } else { carrito[id].cantidad = n; }
    renderCarrito();
}

function filtrarChips(texto) {
    const q = texto.toLowerCase().trim();
    document.querySelectorAll('.mini-chip').forEach(c => {
        const n = c.getAttribute('data-nombre') || '';
        const cod = c.getAttribute('data-codigo') || '';
        c.style.display = (!q || n.includes(q) || cod.includes(q)) ? '' : 'none';
    });
}

function irPaso(n) {
    if (n === 2 && Object.keys(carrito).length === 0) {
        notificar('warn', 'Agrega al menos un producto al carrito');
        return;
    }
    pasoActual = n;
    document.querySelectorAll('.step').forEach(s => {
        const i = parseInt(s.dataset.step);
        s.classList.toggle('active', i === n);
        s.classList.toggle('done', i < n);
    });
    document.querySelectorAll('.step-panel').forEach(p => {
        p.classList.toggle('active', parseInt(p.dataset.panel) === n);
    });
    if (n === 2) actualizarMiniResumen();
    if (n === 3) renderConfirmacion();
}

const RENDIMIENTO_DIESEL = 5; // km por litro
const VELOCIDAD_MEDIA = 50; // km/h
let mapa = null;
let marcador = null;

function calcularDiesel(dist) {
    return dist > 0 ? Math.ceil(dist / RENDIMIENTO_DIESEL) : 0;
}

function calcularTiempo(dist) {
    if (dist <= 0) return '';
    const h = Math.floor(dist / VELOCIDAD_MEDIA);
    const m = Math.round((dist % VELOCIDAD_MEDIA) / VELOCIDAD_MEDIA * 60);
    if (h > 0) return h + 'h ' + m + 'm';
    return m + ' min';
}

function actualizarMiniResumen() {
    const keys = Object.keys(carrito);
    const totalUds = keys.reduce((s, id) => s + carrito[id].cantidad, 0);
    document.getElementById('miniProductos').textContent = keys.length;
    document.getElementById('miniUnidades').textContent = totalUds;
    document.getElementById('infoTotalProds').textContent = keys.length;
    const sel = document.getElementById('sucursal');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('miniDestino').textContent = opt.value;
        const dist = parseInt(opt.getAttribute('data-dist')) || 0;
        document.getElementById('miniDistancia').textContent = dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal';
        document.getElementById('miniDiesel').textContent = dist > 0 ? calcularDiesel(dist) + ' L' : '—';
        document.getElementById('miniTiempo').textContent = dist > 0 ? calcularTiempo(dist) : '—';
    }
    actualizarInfo();
}

function renderConfirmacion() {
    const keys = Object.keys(carrito);
    const totalUds = keys.reduce((s, id) => s + carrito[id].cantidad, 0);
    document.getElementById('confirmProductos').textContent = keys.length;
    document.getElementById('confirmUnidades').textContent = totalUds;
    const sel = document.getElementById('sucursal');
    const opt = sel.options[sel.selectedIndex];
    let dist = 0;
    if (opt && opt.value) {
        document.getElementById('confirmDestino').textContent = opt.value;
        dist = parseInt(opt.getAttribute('data-dist')) || 0;
        document.getElementById('confirmDistancia').textContent = dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal';
        document.getElementById('confirmDiesel').textContent = dist > 0 ? calcularDiesel(dist) + ' L' : '—';
        document.getElementById('confirmTiempo').textContent = dist > 0 ? calcularTiempo(dist) : '—';
        document.getElementById('confirmLitros').textContent = calcularDiesel(dist);
    }
    let html = '';
    let i = 0;
    for (const id of keys) {
        const item = carrito[id];
        i++;
        const restante = item.stock - item.cantidad;
        const colorStock = restante <= item.stock_minimo ? 'var(--n-red)' : 'var(--green)';
        html += `<tr>
            <td style="color:#555;">${i}</td>
            <td style="color:#fff;font-weight:500;">${item.nombre}</td>
            <td style="color:#888;font-family:monospace;font-size:0.75rem;">${item.codigo}</td>
            <td style="color:var(--n-red);font-weight:700;">${item.cantidad} ${item.unidad}</td>
            <td style="color:${colorStock};font-weight:600;">${restante} ${item.unidad}</td>
        </tr>`;
    }
    document.getElementById('confirmTableBody').innerHTML = html;
    const btn = document.getElementById('btnTransferir');
    btn.disabled = !opt?.value;
    inicializarMapa(opt);
}

const SUCURSALES_COORDS = {
    @foreach($sucursales as $nombre => $data)
    '{{ $nombre }}': { lat: {{ $data['lat'] }}, lng: {{ $data['lng'] }} },
    @endforeach
};

function inicializarMapa(opt) {
    const container = document.getElementById('mapaConfirm');
    if (!container) return;
    if (!opt || !opt.value) return;
    const coords = SUCURSALES_COORDS[opt.value];
    if (!coords) return;
    if (!mapa) {
        mapa = L.map('mapaConfirm', { zoomControl: false }).setView([coords.lat, coords.lng], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OSM', maxZoom: 18
        }).addTo(mapa);
        mapa.zoomControl = L.control.zoom({ position: 'bottomright' }).addTo(mapa);
    }
    if (marcador) mapa.removeLayer(marcador);
    marcador = L.marker([coords.lat, coords.lng])
        .addTo(mapa)
        .bindPopup('<b>' + opt.value + '</b><br>' + opt.getAttribute('data-dist') + ' km');
    mapa.setView([coords.lat, coords.lng], 7);
    setTimeout(() => mapa.invalidateSize(), 100);
}

function actualizarInfo() {
    const sel = document.getElementById('sucursal');
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('infoBox');
    if (opt && opt.value) {
        const dist = parseInt(opt.getAttribute('data-dist')) || 0;
        document.getElementById('distanciaLabel').textContent = dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal';
        document.getElementById('destinoLabel').textContent = opt.value;
        document.getElementById('totalProductosLabel').textContent = Object.keys(carrito).reduce((s, id) => s + carrito[id].cantidad, 0);
        box.classList.add('show');
        document.getElementById('miniDestino').textContent = opt.value;
        document.getElementById('miniDistancia').textContent = dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal';
        document.getElementById('miniDiesel').textContent = dist > 0 ? calcularDiesel(dist) + ' L' : '—';
        document.getElementById('miniTiempo').textContent = dist > 0 ? calcularTiempo(dist) : '—';
    } else {
        box.classList.remove('show');
    }
}

function notificar(tipo, mensaje) {
    const tray = document.getElementById('notifTray');
    const iconos = { info: 'bi-info-circle', warn: 'bi-exclamation-triangle', err: 'bi-x-circle', success: 'bi-check-circle-fill' };
    const titulos = { info: 'Informaci\u00f3n', warn: 'Atenci\u00f3n', err: 'Error', success: '\u00c9xito' };
    const n = document.createElement('div');
    n.className = 'notif';
    n.innerHTML = '<div class="notif-icon ' + tipo + '"><i class="' + (iconos[tipo] || 'bi-info-circle') + '"></i></div><div class="notif-body"><div class="notif-title">' + (titulos[tipo] || '') + '</div><div class="notif-text">' + mensaje + '</div></div>';
    n.onclick = function() { n.style.transform = 'translateX(100%)'; n.style.opacity = '0'; n.style.transition = 'all 0.3s'; setTimeout(() => n.remove(), 300); };
    tray.appendChild(n);
    setTimeout(() => { if (n.parentNode) { n.style.transform = 'translateX(100%)'; n.style.opacity = '0'; n.style.transition = 'all 0.3s'; setTimeout(() => n.remove(), 300); } }, 4000);
}

function imprimirGuia() {
    const keys = Object.keys(carrito);
    if (keys.length === 0) { notificar('warn', 'No hay productos en el carrito'); return; }
    const sel = document.getElementById('sucursal');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) { notificar('warn', 'Selecciona una sucursal destino'); return; }
    const dist = parseInt(opt.getAttribute('data-dist')) || 0;
    const sucursal = opt.value;
    const nota = document.getElementById('nota').value;
    const fecha = new Date().toLocaleString('es-VE', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    let rows = '';
    let totalUds = 0;
    keys.forEach((id, i) => {
        const item = carrito[id];
        totalUds += item.cantidad;
        rows += `<tr>
            <td style="padding:8px;border:1px solid #ddd;text-align:center;">${i+1}</td>
            <td style="padding:8px;border:1px solid #ddd;">${item.nombre}</td>
            <td style="padding:8px;border:1px solid #ddd;font-family:monospace;">${item.codigo}</td>
            <td style="padding:8px;border:1px solid #ddd;text-align:center;">${item.cantidad}</td>
            <td style="padding:8px;border:1px solid #ddd;">${item.unidad}</td>
            <td style="padding:8px;border:1px solid #ddd;text-align:center;">${item.stock - item.cantidad}</td>
        </tr>`;
    });
    const dataStr = JSON.stringify({ items: keys.map(id => ({ id, c: carrito[id].cantidad })), sucursal, fecha: new Date().toISOString() });
    const qrData = ultimosMovimientosIds.length > 0 ? JSON.stringify({ ids: ultimosMovimientosIds, sucursal }) : '';
    const qrUrl = qrData ? 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(qrData) : '';
    const enc = new TextEncoder();
    crypto.subtle.digest('SHA-256', enc.encode(dataStr)).then(hash => {
        const firma = Array.from(new Uint8Array(hash)).map(b => b.toString(16).padStart(2, '0')).join('');
        abrirGuia(firma);
    }).catch(() => abrirGuia('—'));
    function abrirGuia(firma) {
    const w = window.open('', '_blank');
    if (!w) { notificar('err', 'Bloqueador de ventanas emergentes detectado'); return; }
    w.document.write(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Gu\u00eda de Despacho</title><style>
        @page { margin: 20mm 15mm; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #222; padding: 0; margin: 0; }
        .header { text-align: center; border-bottom: 3px solid #E50914; padding-bottom: 12px; margin-bottom: 20px; }
        .logo { color: #E50914; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .logo span { color: #333; font-weight: 300; }
        .sub { color: #666; font-size: 11px; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: 700; color: #E50914; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; border-left: 4px solid #E50914; padding-left: 8px; }
        .info-grid { display: flex; flex-wrap: wrap; gap: 0; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }
        .info-cell { flex: 1; min-width: 140px; padding: 10px 14px; border-right: 1px solid #ddd; border-bottom: 1px solid #ddd; }
        .info-cell:nth-child(odd) { background: #fafafa; }
        .info-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.3px; }
        .info-value { font-size: 14px; font-weight: 700; color: #222; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #E50914; color: #fff; padding: 10px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
        th:first-child { text-align: center; width: 40px; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #fafafa; }
        .total-row { background: #f0f0f0 !important; font-weight: 700; }
        .total-row td { border-top: 2px solid #E50914; }
        .firma-box { margin-top: 40px; border: 1px dashed #ccc; border-radius: 8px; padding: 16px; background: #f9f9f9; font-size: 11px; }
        .firma-box code { font-size: 10px; word-break: break-all; color: #555; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 12px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .sign-line { width: 200px; border-top: 1px solid #333; padding-top: 6px; font-size: 10px; text-align: center; color: #555; }
        .badge-diesel { display: inline-block; background: #f39c12; color: #fff; font-size: 10px; padding: 2px 10px; border-radius: 12px; font-weight: 700; }
        .badge-time { display: inline-block; background: #3498db; color: #fff; font-size: 10px; padding: 2px 10px; border-radius: 12px; font-weight: 700; }
        @media print { .no-print { display: none; } }
    </style></head><body>
    <div class="header">
        <div class="logo">OSWA <span>Inv</span></div>
        <div class="sub">Sistema de Inventario — Guía de Despacho</div>
    </div>
    <div class="section">
        <div class="section-title">Datos del Envío</div>
        <div class="info-grid">
            <div class="info-cell"><div class="info-label">Destino</div><div class="info-value">${sucursal}</div></div>
            <div class="info-cell"><div class="info-label">Distancia</div><div class="info-value">${dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal'}</div></div>
            <div class="info-cell"><div class="info-label">Productos</div><div class="info-value">${keys.length}</div></div>
            <div class="info-cell"><div class="info-label">Unidades</div><div class="info-value">${totalUds}</div></div>
            <div class="info-cell"><div class="info-label">Combustible</div><div class="info-value"><span class="badge-diesel">${calcularDiesel(dist)} L</span></div></div>
            <div class="info-cell"><div class="info-label">Tiempo Est.</div><div class="info-value"><span class="badge-time">${calcularTiempo(dist)}</span></div></div>
            <div class="info-cell" style="flex:2;${nota ? '' : 'opacity:0.4;'}"><div class="info-label">Nota</div><div class="info-value" style="font-weight:400;font-size:12px;">${nota || 'Sin nota'}</div></div>
            <div class="info-cell"><div class="info-label">Fecha Emisión</div><div class="info-value" style="font-size:12px;font-weight:500;">${fecha}</div></div>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Productos</div>
        <table>
            <thead><tr><th>#</th><th>Producto</th><th>Código</th><th>Cant.</th><th>Und.</th><th>Stock Rest.</th></tr></thead>
            <tbody>${rows}</tbody>
        </table>
    </div>
    <div class="firma-box">
        <strong style="color:#E50914;">Firma Digital SHA-256</strong><br>
        <code>${firma}</code>
    </div>
    ${qrUrl ? '<div style="text-align:center;margin-top:20px;"><img src="' + qrUrl + '" style="width:120px;height:120px;" alt="QR"><div style="font-size:9px;color:#999;margin-top:4px;">Escanea para confirmar llegada</div></div>' : ''}
    <div class="signatures">
        <div class="sign-line">Firma del Almacenista</div>
        <div class="sign-line">Firma del Transportista</div>
        <div class="sign-line">Recibe Conforme</div>
    </div>
    <div class="footer">
        <p>Documento generado automáticamente por OSWA Inv — ${fecha}</p>
        <p style="margin-top:4px;">Barinas, Venezuela</p>
    </div>
    <div class="no-print" style="text-align:center;margin-top:20px;">
        <button onclick="window.print()" style="padding:12px 40px;background:#E50914;color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer;font-weight:600;">Imprimir</button>
        <button onclick="window.close()" style="padding:12px 40px;background:#333;color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer;margin-left:10px;">Cerrar</button>
    </div>
    <script>window.onload = function() { setTimeout(function() { window.print(); }, 500); };<\/script>
</body></html>`);
    w.document.close();
}
}

async function confirmarTransferencia() {
    const sucursal = document.getElementById('sucursal').value;
    if (!sucursal) { notificar('warn', 'Selecciona una sucursal destino'); return; }
    const keys = Object.keys(carrito);
    if (keys.length === 0) { notificar('warn', 'Agrega productos al carrito'); return; }
    const items = keys.map(id => ({ id: parseInt(id), cantidad: carrito[id].cantidad }));
    const btn = document.getElementById('btnTransferir');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
    const soporte = document.getElementById('soporteFoto').files[0];
    const nota = document.getElementById('nota').value;
    const reader = new FileReader();
    reader.onload = async function(e) {
        try {
            const res = await fetch('{{ route("transferir.productos") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') },
                body: JSON.stringify({ items, sucursal, nota, soporte_base64: e.target?.result ?? null })
            });
            const data = await res.json();
            if (data.success) {
                ultimosMovimientosIds = data.resultados.map(r => r.id).filter(id => id);
                let html = '<div style="text-align:left;color:#ccc;font-size:0.9rem;">';
                html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Destino</span><span style="color:#00b894;font-weight:600;">' + sucursal + '</span></div>';
                html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Productos</span><span style="color:#fff;font-weight:600;">' + data.resultados.length + '</span></div>';
                html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Unidades</span><span style="color:#ffd700;font-weight:600;">' + items.reduce((s, i) => s + i.cantidad, 0) + '</span></div>';
                if (data.errores?.length) {
                    html += '<div style="margin-top:8px;padding:8px;background:rgba(229,9,20,0.1);border-radius:8px;color:#e74c3c;font-size:0.8rem;"><i class="bi bi-exclamation-triangle me-1"></i>' + data.errores.join('<br>') + '</div>';
                }
                html += '</div>';
                Swal.fire({
                    icon: 'success', title: 'Transferencia Exitosa', html,
                    confirmButtonColor: '#00b894', confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Listo',
                    background: '#1a1a1a', color: '#fff',
                    customClass: { popup: 'border border-secondary shadow-lg' }
                }).then(() => { window.location.reload(); });
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Transferir Ahora';
                notificar('err', data.error || 'Error al transferir');
            }
        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Transferir Ahora';
            notificar('err', 'Error de conexi\u00f3n');
        }
    };
    if (soporte) reader.readAsDataURL(soporte); else reader.onload({ target: { result: null } });
}

@if($producto && $producto->stock > 0)
renderCarrito();
actualizarContador();
@endif
</script>
</body>
</html>