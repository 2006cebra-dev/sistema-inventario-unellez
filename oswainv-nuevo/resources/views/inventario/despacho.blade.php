<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Despacho - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root { --bg: #0f0f0f; --card: #1a1a1a; --n-red: #E50914; --n-border: #2a2a2a; --text: #e5e5e5; --green: #00b894; }
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; }
        .bg-glow { position: fixed; top: -30%; left: -10%; width: 50%; height: 80%; background: radial-gradient(circle, rgba(229,9,20,0.06) 0%, transparent 70%); pointer-events: none; z-index: 0; }
        .bg-glow-2 { position: fixed; bottom: -30%; right: -10%; width: 50%; height: 80%; background: radial-gradient(circle, rgba(255,193,7,0.04) 0%, transparent 70%); pointer-events: none; z-index: 0; }
        .page { position: relative; z-index: 1; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 2rem; border-bottom: 1px solid var(--n-border); background: rgba(15,15,15,0.8); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 10; }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-logo { font-weight: 800; font-size: 1.3rem; color: var(--n-red); }
        .topbar-logo span { color: #fff; font-weight: 300; }
        .container-fluid { max-width: 1400px; margin: 0 auto; padding: 1.5rem 2rem 2rem; width: 100%; flex: 1; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideLeft { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }
        @keyframes notifIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes scanPulse { 0%,100%{box-shadow:0 0 0 0 rgba(229,9,20,0.4)} 50%{box-shadow:0 0 0 12px rgba(229,9,20,0)} }
        @keyframes itemAdded { 0%{transform:scale(0.95);opacity:0} 50%{transform:scale(1.02)} 100%{transform:scale(1);opacity:1} }

        .panel-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.4); animation: slideUp 0.5s ease; height: 100%; }
        .panel-card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid #1f1f1f; display: flex; align-items: center; gap: 10px; }
        .panel-card-header h5 { margin: 0; font-weight: 700; color: #fff; font-size: 1rem; }
        .panel-card-body { padding: 1.5rem; }

        #reader { border: none !important; background: transparent; padding: 0; }
        #reader video { border-radius: 12px; object-fit: cover; }
        #reader__dashboard_section_csr span { color: #a3a3a3 !important; font-size: 0.9rem; }
        #reader__dashboard_section_swaplink { color: var(--n-red) !important; text-decoration: none; font-weight: bold; }
        #reader button { background: var(--n-red) !important; color: white !important; border: none !important; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 10px; margin-bottom: 10px; font-size: 0.9rem; }
        #reader button:hover { background: #ff0f1b !important; transform: translateY(-2px); }
        #reader select { background: #222 !important; color: white !important; border: 1px solid #444 !important; border-radius: 8px; padding: 8px; outline: none; margin-bottom: 12px; max-width: 100%; font-size: 0.9rem; }
        #reader a { color: var(--n-red) !important; }
        #html5-qrcode-anchor-scan-type-change { color: var(--n-red) !important; text-decoration: none; }

        .scan-box { background: #0a0a0a; border-radius: 12px; overflow: hidden; border: 1px solid var(--n-border); min-height: 260px; position: relative; display: flex; align-items: center; justify-content: center; transition: border-color 0.3s; animation: scanPulse 2s infinite; }
        .scan-box.has-video { animation: none; border-color: var(--n-red); }

        .input-nf { background: #0f0f0f; border: 1px solid var(--n-border); color: #fff; border-radius: 10px; padding: 0.75rem 1rem; transition: all 0.3s; width: 100%; }
        .input-nf:focus { border-color: var(--n-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.15); background: #0f0f0f; color: #fff; outline: none; }
        .input-group-text { background: #0f0f0f; border: 1px solid var(--n-border); color: #888; }

        .cart-item { display: flex; justify-content: space-between; align-items: center; background: #0f0f0f; border: 1px solid var(--n-border); border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; transition: all 0.3s; animation: itemAdded 0.3s ease; }
        .cart-item:hover { border-color: var(--n-red); box-shadow: 0 0 20px rgba(229,9,20,0.08); }
        .qty-controls { display: flex; align-items: center; background: #0f0f0f; border-radius: 8px; border: 1px solid #333; overflow: hidden; }
        .qty-btn { background: none; border: none; color: white; padding: 6px 14px; font-weight: bold; cursor: pointer; transition: background 0.2s; font-size: 1.1rem; }
        .qty-btn:hover { background: rgba(229,9,20,0.2); }
        .qty-input { width: 40px; background: transparent; border: none; color: white; text-align: center; font-weight: bold; outline: none; }

        .btn-primary { padding: 0.85rem 2rem; font-size: 0.95rem; font-weight: 600; border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-red { background: linear-gradient(135deg, var(--n-red), #b20710); color: #fff; }
        .btn-red:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(229,9,20,0.25); }
        .btn-red:disabled { opacity: 0.4; cursor: not-allowed; }
        .btn-outline { background: transparent; color: #888; border: 1px solid var(--n-border); }
        .btn-outline:hover { border-color: var(--n-red); color: #fff; }
        .btn-green { background: linear-gradient(135deg, var(--green), #009874); color: #fff; }
        .btn-green:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,184,148,0.25); }

        .form-label { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: 600; }
        .form-select { background: #0f0f0f; border: 1px solid var(--n-border); color: #fff; border-radius: 12px; padding: 0.8rem 1rem; font-size: 0.95rem; transition: all 0.3s; width: 100%; }
        .form-select:focus { border-color: var(--n-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.15); background: #0f0f0f; color: #fff; }
        .form-control { background: #0f0f0f; border: 1px solid var(--n-border); color: #fff; border-radius: 12px; padding: 0.8rem 1rem; font-size: 0.95rem; transition: all 0.3s; width: 100%; }
        .form-control:focus { border-color: var(--n-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.15); background: #0f0f0f; color: #fff; }

        .product-preview { background: #0f0f0f; border: 1px solid #1f1f1f; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; display: none; animation: fadeIn 0.4s ease; }
        .product-preview.show { display: block; }
        .product-preview-name { font-size: 1.2rem; font-weight: 700; color: #fff; }
        .product-preview-code { color: #666; font-size: 0.75rem; font-family: monospace; }
        .product-preview-stats { display: flex; gap: 1rem; margin-top: 0.5rem; }
        .product-preview-stat { text-align: center; flex: 1; background: #0a0a0a; border-radius: 8px; padding: 0.5rem; border: 1px solid #1a1a1a; }
        .product-preview-stat-value { font-size: 1.1rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .product-preview-stat-label { font-size: 0.55rem; color: #666; text-transform: uppercase; letter-spacing: 0.3px; }

        .info-box { background: rgba(0,184,148,0.06); border: 1px solid rgba(0,184,148,0.2); border-radius: 12px; padding: 1rem; display: none; animation: fadeIn 0.4s ease; }
        .info-box.show { display: block; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.85rem; border-bottom: 1px solid #1f1f1f; }
        .info-row:last-child { border: none; }
        .info-label { color: #888; }
        .info-value { color: #fff; font-weight: 600; }

        .history-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1.5rem; animation: slideUp 0.6s ease; }
        .history-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #1a1a1a; font-size: 0.85rem; }
        .history-item:last-child { border: none; }
        .history-motive { color: var(--n-red); font-weight: 600; }
        .history-meta { color: #666; font-size: 0.75rem; }

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

        .badge-pill { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 600; }
        .badge-pill.success { background: rgba(0,184,148,0.15); color: var(--green); }
        .badge-pill.danger { background: rgba(229,9,20,0.15); color: var(--n-red); }
        .badge-pill.warning { background: rgba(253,203,110,0.15); color: #fdcb6e; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--card); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

        .motivo-options { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
        .motivo-chip { padding: 5px 14px; border: 1px solid var(--n-border); border-radius: 20px; font-size: 0.75rem; color: #aaa; cursor: pointer; transition: all 0.2s; background: transparent; }
        .motivo-chip:hover { border-color: var(--n-red); color: #fff; }
        .motivo-chip.selected { background: rgba(229,9,20,0.1); border-color: var(--n-red); color: #fff; }

        @media (max-width: 900px) { .container-fluid { padding: 1rem; } }
        @media (max-width: 480px) { #reader video { max-height: 200px; } .cart-item { flex-direction: column; align-items: flex-start; gap: 12px; } .qty-controls { width: 100%; justify-content: space-between; } }
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
        <a href="{{ route('catalogo') }}" class="topbar-back" style="color:#888;text-decoration:none;font-size:0.85rem;display:flex;align-items:center;gap:6px;padding:6px 14px;border:1px solid var(--n-border);border-radius:8px;transition:all 0.2s;"><i class="bi bi-arrow-left"></i> Catálogo</a>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="color:#555;font-size:0.8rem;"><i class="bi bi-upc-scan me-1" style="color:var(--n-red);"></i>Despacho de Productos</span>
    </div>
</div>

<div class="container-fluid">
    <div class="row g-4">

        <!-- COLUMNA IZQUIERDA: ESCÁNER + DATOS DEL DESPACHO -->
        <div class="col-lg-6">
            <div class="panel-card">
                <div class="panel-card-header">
                    <span style="width:36px;height:36px;border-radius:10px;background:rgba(229,9,20,0.12);color:var(--n-red);display:flex;align-items:center;justify-content:center;"><i class="bi bi-upc-scan"></i></span>
                    <h5>Escanear Producto</h5>
                    <span class="badge bg-dark border border-secondary px-3 py-2 text-secondary ms-auto">
                        <i class="bi bi-circle-fill text-success me-1" style="font-size:0.5rem;"></i> Terminal Activa
                    </span>
                </div>
                <div class="panel-card-body">
                    <div class="scan-box mb-3" id="scanBox">
                        <div id="reader" style="width: 100%;"></div>
                    </div>

                    <label class="form-label mt-2"><i class="bi bi-keyboard me-1"></i>Ingreso Manual</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-upc"></i></span>
                        <input type="text" class="input-nf border-secondary" placeholder="Escribe o escanea el código..." id="manualInput" style="border:1px solid var(--n-border);border-radius:10px;">
                        <button class="btn btn-red fw-bold px-4" type="button" onclick="buscarPorCodigo()" style="border-radius:10px;">
                            <i class="bi bi-plus-lg me-1"></i>Añadir
                        </button>
                    </div>

                    <!-- Preview del último producto escaneado -->
                    <div class="product-preview" id="productPreview">
                        <div style="display:flex;gap:1rem;align-items:flex-start;">
                            <div id="previewImagenContainer" style="width:80px;height:80px;border-radius:10px;background:#0a0a0a;border:1px solid #1f1f1f;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                                <img id="previewImagen" src="" alt="" style="max-width:100%;max-height:100%;object-fit:contain;display:none;">
                                <div id="previewImagenPlaceholder" style="font-size:2rem;color:#333;"><i class="bi bi-box-seam"></i></div>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <div>
                                        <div class="product-preview-name" id="previewNombre">—</div>
                                        <div class="product-preview-code" id="previewCodigo">—</div>
                                    </div>
                                    <span class="badge-pill success" id="previewCategoria">—</span>
                                </div>
                                <div class="product-preview-stats" style="margin-top:0;">
                                    <div class="product-preview-stat">
                                        <div class="product-preview-stat-value" style="color:var(--green);" id="previewStock">0</div>
                                        <div class="product-preview-stat-label">Stock</div>
                                    </div>
                                    <div class="product-preview-stat">
                                        <div class="product-preview-stat-value" style="color:#ffd700;" id="previewPrecio">$0.00</div>
                                        <div class="product-preview-stat-label">Precio</div>
                                    </div>
                                    <div class="product-preview-stat">
                                        <div class="product-preview-stat-value" style="color:var(--n-red);" id="previewCarrito">0</div>
                                        <div class="product-preview-stat-label">En salida</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de configuración del despacho -->
            <div class="panel-card mt-4">
                <div class="panel-card-header">
                    <span style="width:36px;height:36px;border-radius:10px;background:rgba(0,184,148,0.1);color:var(--green);display:flex;align-items:center;justify-content:center;"><i class="bi bi-gear"></i></span>
                    <h5>Configuración del Despacho</h5>
                </div>
                <div class="panel-card-body">
                    <div class="mb-3">
                        <div class="form-label"><i class="bi bi-tag me-1"></i>Motivo de Salida</div>
                        <div class="motivo-options" id="motivoOptions">
                            <div class="motivo-chip selected" onclick="seleccionarMotivo(this, 'Despacho Rápido (Lote)')">Despacho Rápido</div>
                            <div class="motivo-chip" onclick="seleccionarMotivo(this, 'Venta Directa')">Venta Directa</div>
                            <div class="motivo-chip" onclick="seleccionarMotivo(this, 'Donación')">Donación</div>
                            <div class="motivo-chip" onclick="seleccionarMotivo(this, 'Devolución a Proveedor')">Devolución</div>
                            <div class="motivo-chip" onclick="seleccionarMotivo(this, 'Merma / Daño')">Merma / Daño</div>
                            <div class="motivo-chip" onclick="seleccionarMotivo(this, '')">Otro...</div>
                        </div>
                        <input type="text" class="input-nf mt-2" id="motivoCustom" placeholder="Especifica el motivo..." style="display:none;border:1px solid var(--n-border);border-radius:10px;">
                        <input type="hidden" id="motivo" value="Despacho Rápido (Lote)">
                    </div>

                    <div class="mb-3">
                        <div class="form-label"><i class="bi bi-geo-alt me-1"></i>Destino (opcional)</div>
                        <select class="form-select" id="sucursal" onchange="actualizarInfo()">
                            <option value="">Sin destino específico</option>
                            @foreach($sucursales as $nombre => $data)
                            <option value="{{ $nombre }}" data-dist="{{ $data['dist'] }}">{{ $nombre }} @if($data['dist'] > 0)({{ number_format($data['dist']) }} km)@else (Sede Principal)@endif</option>
                            @endforeach
                        </select>
                        <div class="info-box mt-2" id="infoBox">
                            <div class="info-row"><span class="info-label"><i class="bi bi-signpost-2 me-1"></i>Distancia</span><span class="info-value" id="distanciaLabel">—</span></div>
                            <div class="info-row"><span class="info-label"><i class="bi bi-truck me-1"></i>Destino</span><span class="info-value" id="destinoLabel" style="color:var(--green);">—</span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-label"><i class="bi bi-chat-text me-1"></i>Nota (opcional)</div>
                        <textarea class="form-control" id="nota" rows="2" placeholder="Ej: Productos para promoción..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-label"><i class="bi bi-camera me-1"></i>Soporte foto (opcional)</div>
                        <div style="display:flex;gap:10px;">
                            <input type="file" class="form-control" id="soporteFoto" accept="image/*" capture="environment" style="flex:1;">
                            <button type="button" class="btn" style="background:#0f0f0f;border:1px solid var(--n-border);border-radius:12px;color:#888;width:48px;" onclick="document.getElementById('soporteFoto').value='';notificar('info','Foto eliminada')"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: LISTA DE SALIDA -->
        <div class="col-lg-6">
            <div class="panel-card d-flex flex-column">
                <div class="panel-card-header">
                    <span style="width:36px;height:36px;border-radius:10px;background:rgba(255,193,7,0.12);color:#ffc107;display:flex;align-items:center;justify-content:center;"><i class="bi bi-cart-dash"></i></span>
                    <h5>Lista de Salida</h5>
                    <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="vaciarCarrito()" style="border-color:var(--n-border);border-radius:8px;color:#888;font-size:0.75rem;">
                        <i class="bi bi-trash me-1"></i>Limpiar
                    </button>
                    <span style="background:#0f0f0f;border:1px solid var(--n-border);border-radius:20px;padding:2px 12px;font-size:0.7rem;color:#888;" id="cartBadge">0</span>
                </div>
                <div class="panel-card-body flex-grow-1 d-flex flex-column">
                    <div class="flex-grow-1 overflow-auto pe-1 mb-3" style="min-height: 250px; max-height: 400px;" id="cartList">
                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-secondary">
                            <div class="d-flex align-items-center justify-content-center border border-secondary rounded-circle" style="width: 80px; height: 80px;">
                                <i class="bi bi-box-seam text-secondary" style="font-size: 2.5rem;"></i>
                            </div>
                            <p class="mt-3 fw-bold text-white mb-1">No hay productos en la lista</p>
                            <span class="small" style="color:#555;">Escanea o ingresa productos para agregarlos</span>
                        </div>
                    </div>

                    <div class="border-top border-secondary pt-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                            <span class="text-secondary fw-semibold fs-5">Total:</span>
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fs-6 fw-bold" id="totalItems">0</span>
                        </div>
                        <button class="btn btn-red w-100 fs-5 glow-btn" id="btnProcesar" onclick="procesarDespacho()" disabled style="position:relative;overflow:hidden;">
                            <i class="bi bi-check2-circle me-2"></i>Procesar Salida
                        </button>
                    </div>
                </div>
            </div>

            @if($ultimosDespachos->count() > 0)
            <div class="history-card mt-4">
                <div style="font-size:1rem;font-weight:600;margin-bottom:1rem;color:#fff;"><i class="bi bi-clock-history me-2" style="color:var(--n-red);"></i>Últimos despachos</div>
                @foreach($ultimosDespachos as $d)
                <div class="history-item">
                    <div><span class="history-motive">{{ $d->motivo }}</span> <span style="color:#888;">{{ $d->cantidad }} uds</span></div>
                    <div><span class="history-meta">{{ $d->created_at->format('d/m/Y H:i') }} · {{ $d->usuario_accion }}</span></div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
</div>

<script>
const productosDB = @json($productos);
let carrito = [];
let html5QrcodeScanner = null;
let ultimoProducto = null;

document.addEventListener('DOMContentLoaded', () => {
    html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 100 } }, false);
    html5QrcodeScanner.render(onScanSuccess);
    document.getElementById('manualInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarPorCodigo();
    });
    document.getElementById('manualInput').addEventListener('focus', function() {
        document.getElementById('scanBox').classList.add('has-video');
    });
});

function onScanSuccess(decodedText) {
    agregarAlCarrito(decodedText);
    if (html5QrcodeScanner) {
        html5QrcodeScanner.pause();
        setTimeout(() => { try { html5QrcodeScanner.resume(); } catch(e) {} }, 1500);
    }
}

function buscarPorCodigo() {
    const code = document.getElementById('manualInput').value.trim();
    if (code) agregarAlCarrito(code);
    document.getElementById('manualInput').value = '';
    document.getElementById('manualInput').focus();
}

function agregarAlCarrito(codigo) {
    const prod = productosDB.find(p => p.codigo === codigo || p.codigo_barras === codigo || p.id == codigo);
    if (!prod) { notificar('err', 'Producto no encontrado'); return; }

    const index = carrito.findIndex(item => item.id === prod.id);
    if (index !== -1) {
        if (carrito[index].cantidad < prod.stock) {
            carrito[index].cantidad++;
        } else {
            notificar('warn', 'Stock máximo: ' + prod.stock);
            return;
        }
    } else {
        if (prod.stock <= 0) { notificar('warn', 'Producto sin stock'); return; }
        carrito.unshift({ id: prod.id, codigo: prod.codigo, nombre: prod.nombre, stock: prod.stock, cantidad: 1, precio: prod.precio, categoria: prod.categoria || 'General', unidad: prod.unidad_medida || 'uds' });
    }

    ultimoProducto = prod;
    mostrarPreview(prod);
    reproducirBeep();
    renderizarCarrito();
}

function mostrarPreview(prod) {
    const pv = document.getElementById('productPreview');
    pv.classList.add('show');
    document.getElementById('previewNombre').textContent = prod.nombre;
    document.getElementById('previewCodigo').textContent = prod.codigo;
    document.getElementById('previewCategoria').textContent = prod.categoria || 'General';
    document.getElementById('previewStock').textContent = prod.stock;
    document.getElementById('previewStock').style.color = prod.stock <= (prod.stock_minimo || 5) ? 'var(--n-red)' : 'var(--green)';
    document.getElementById('previewPrecio').textContent = '$' + parseFloat(prod.precio || 0).toFixed(2);
    const enCarrito = carrito.find(i => i.id === prod.id);
    document.getElementById('previewCarrito').textContent = enCarrito ? enCarrito.cantidad : 0;
    const img = document.getElementById('previewImagen');
    const placeholder = document.getElementById('previewImagenPlaceholder');
    if (prod.imagen) {
        img.src = '/storage/' + prod.imagen;
        img.style.display = 'block';
        placeholder.style.display = 'none';
        img.onerror = function() { img.style.display = 'none'; placeholder.style.display = 'flex'; };
    } else {
        img.style.display = 'none';
        placeholder.style.display = 'flex';
    }
}

function cambiarCantidad(id, delta) {
    const index = carrito.findIndex(item => item.id === id);
    if (index === -1) return;
    const nuevaCant = carrito[index].cantidad + delta;
    if (nuevaCant === 0) {
        carrito.splice(index, 1);
    } else if (nuevaCant <= carrito[index].stock) {
        carrito[index].cantidad = nuevaCant;
    } else {
        notificar('warn', 'Stock máximo: ' + carrito[index].stock);
    }
    renderizarCarrito();
    if (ultimoProducto && ultimoProducto.id === id) mostrarPreview(ultimoProducto);
}

function renderizarCarrito() {
    const container = document.getElementById('cartList');
    const totalItemsSpan = document.getElementById('totalItems');
    const btnProcesar = document.getElementById('btnProcesar');
    const badge = document.getElementById('cartBadge');

    if (carrito.length === 0) {
        container.innerHTML = '<div class="h-100 d-flex flex-column align-items-center justify-content-center text-secondary"><div class="d-flex align-items-center justify-content-center border border-secondary rounded-circle" style="width:80px;height:80px;"><i class="bi bi-box-seam text-secondary" style="font-size:2.5rem;"></i></div><p class="mt-3 fw-bold text-white mb-1">No hay productos en la lista</p><span class="small" style="color:#555;">Escanea o ingresa productos para agregarlos</span></div>';
        totalItemsSpan.innerText = '0';
        badge.textContent = '0';
        btnProcesar.disabled = true;
        return;
    }

    let html = '';
    let totalQty = 0;
    carrito.forEach(item => {
        totalQty += item.cantidad;
        const stockColor = item.stock <= (item.stock_minimo || 5) ? 'var(--n-red)' : 'var(--green)';
        html += `<div class="cart-item">
            <div>
                <h6 class="text-white mb-1 fw-bold" style="font-size:0.9rem;">${item.nombre}</h6>
                <div class="d-flex gap-3">
                    <small class="text-secondary font-monospace"><i class="bi bi-upc"></i> ${item.codigo}</small>
                    <small class="text-info"><i class="bi bi-box"></i> Disp: <span style="color:${stockColor};">${item.stock}</span></small>
                </div>
            </div>
            <div class="qty-controls">
                <button class="qty-btn" onclick="cambiarCantidad(${item.id}, -1)">−</button>
                <input type="text" class="qty-input" value="${item.cantidad}" readonly>
                <button class="qty-btn" onclick="cambiarCantidad(${item.id}, 1)">+</button>
            </div>
        </div>`;
    });

    container.innerHTML = html;
    totalItemsSpan.innerText = totalQty;
    badge.textContent = totalQty;
    btnProcesar.disabled = false;
}

function vaciarCarrito() {
    if (carrito.length === 0) return;
    Swal.fire({
        title: '¿Limpiar lista?',
        text: 'Se eliminarán todos los productos de la lista de salida.',
        icon: 'question', showCancelButton: true, confirmButtonColor: '#E50914', cancelButtonColor: '#444',
        confirmButtonText: 'Sí, limpiar', cancelButtonText: 'No',
        background: '#1a1a1a', color: '#fff',
        customClass: { popup: 'border border-secondary shadow-lg' }
    }).then(r => {
        if (r.isConfirmed) { carrito = []; renderizarCarrito(); document.getElementById('productPreview').classList.remove('show'); notificar('info', 'Lista limpiada'); }
    });
}

function seleccionarMotivo(el, val) {
    document.querySelectorAll('.motivo-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('motivo').value = val;
    document.getElementById('motivoCustom').style.display = val === '' ? 'block' : 'none';
    if (val !== '') document.getElementById('motivoCustom').value = '';
}

function actualizarInfo() {
    const sel = document.getElementById('sucursal');
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('infoBox');
    if (opt && opt.value) {
        const dist = parseInt(opt.getAttribute('data-dist')) || 0;
        document.getElementById('distanciaLabel').textContent = dist > 0 ? dist.toLocaleString() + ' km' : 'Sede Principal';
        document.getElementById('destinoLabel').textContent = opt.value;
        box.classList.add('show');
    } else {
        box.classList.remove('show');
    }
}

function procesarDespacho() {
    if (carrito.length === 0) return;
    const motivo = document.getElementById('motivo').value || document.getElementById('motivoCustom').value || 'Despacho Rápido (Lote)';
    const sucursal = document.getElementById('sucursal').value;
    const nota = document.getElementById('nota').value;
    const soporte = document.getElementById('soporteFoto').files[0];

    let html = '<div style="text-align:left;color:#ccc;font-size:0.9rem;">';
    html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Motivo</span><span style="color:var(--n-red);font-weight:600;">' + motivo + '</span></div>';
    html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Productos</span><span style="color:#fff;font-weight:600;">' + carrito.length + '</span></div>';
    html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Unidades</span><span style="color:#ffd700;font-weight:600;">' + carrito.reduce((s,i) => s + i.cantidad, 0) + '</span></div>';
    if (sucursal) html += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Destino</span><span style="color:var(--green);font-weight:600;">' + sucursal + '</span></div>';
    if (nota) html += '<div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.8rem;"><span style="color:#888;">Nota</span><span style="color:#999;">' + nota + '</span></div>';
    html += '</div>';

    Swal.fire({
        title: '¿Confirmar Salida?', html,
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#E50914', cancelButtonColor: '#444',
        confirmButtonText: '<i class="bi bi-check2-circle me-1"></i>Sí, procesar', cancelButtonText: 'Cancelar',
        background: '#1a1a1a', color: '#fff',
        customClass: { popup: 'border border-secondary shadow-lg' }
    }).then((result) => {
        if (!result.isConfirmed) return;

        const btn = document.getElementById('btnProcesar');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Procesando...';
        btn.disabled = true;

        const reader = new FileReader();
        reader.onload = async function(e) {
            try {
                const res = await fetch('{{ route("despacho.procesar") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({
                        items: carrito.map(i => ({ id: i.id, cantidad: i.cantidad })),
                        motivo, sucursal, nota, soporte_base64: e.target?.result ?? null
                    })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success', title: 'Despacho Procesado', html: data.html || '<div style="color:#ccc;">Salida registrada correctamente</div>',
                        confirmButtonColor: '#00b894', confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Listo',
                        background: '#1a1a1a', color: '#fff',
                        customClass: { popup: 'border border-secondary shadow-lg' }
                    }).then(() => { window.location.reload(); });
                } else {
                    btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Procesar Salida'; btn.disabled = false;
                    notificar('err', data.message || 'Error al procesar');
                }
            } catch(e) {
                btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Procesar Salida'; btn.disabled = false;
                notificar('err', 'Error de conexión');
            }
        };
        if (soporte) reader.readAsDataURL(soporte); else reader.onload({ target: { result: null } });
    });
}

function notificar(tipo, mensaje) {
    const tray = document.getElementById('notifTray');
    const iconos = { info: 'bi-info-circle', warn: 'bi-exclamation-triangle', err: 'bi-x-circle', success: 'bi-check-circle-fill' };
    const titulos = { info: 'Información', warn: 'Atención', err: 'Error', success: 'Éxito' };
    const n = document.createElement('div');
    n.className = 'notif';
    n.innerHTML = '<div class="notif-icon ' + tipo + '"><i class="' + (iconos[tipo] || 'bi-info-circle') + '"></i></div><div class="notif-body"><div class="notif-title">' + (titulos[tipo] || '') + '</div><div class="notif-text">' + mensaje + '</div></div>';
    n.onclick = function() { n.style.transform = 'translateX(100%)'; n.style.opacity = '0'; n.style.transition = 'all 0.3s'; setTimeout(() => n.remove(), 300); };
    tray.appendChild(n);
    setTimeout(() => { if (n.parentNode) { n.style.transform = 'translateX(100%)'; n.style.opacity = '0'; n.style.transition = 'all 0.3s'; setTimeout(() => n.remove(), 300); } }, 4000);
}

function reproducirBeep() {
    try {
        const context = new (window.AudioContext || window.webkitAudioContext)();
        const osc = context.createOscillator();
        const gain = context.createGain();
        osc.type = 'sine'; osc.frequency.value = 1200;
        gain.gain.setValueAtTime(0.1, context.currentTime);
        osc.connect(gain); gain.connect(context.destination);
        osc.start(); osc.stop(context.currentTime + 0.1);
    } catch (e) {}
}

const styleGlow = document.createElement('style');
styleGlow.textContent = '.glow-btn::after { content:""; position:absolute; top:-50%; left:-50%; width:200%; height:200%; background:radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%); opacity:0; transition:opacity 0.4s; } .glow-btn:hover::after { opacity:1; }';
document.head.appendChild(styleGlow);
</script>

<script>
function checkNetworkStatus() {
    const isOnline = navigator.onLine;
    const btnProcesar = document.getElementById('btnProcesar');
    if (!isOnline) { if (btnProcesar) btnProcesar.disabled = true; }
    else { if (btnProcesar && typeof carrito !== 'undefined' && carrito.length > 0) btnProcesar.disabled = false; }
}
window.addEventListener('online', checkNetworkStatus);
window.addEventListener('offline', checkNetworkStatus);
document.addEventListener('DOMContentLoaded', checkNetworkStatus);
</script>
</body>
</html>