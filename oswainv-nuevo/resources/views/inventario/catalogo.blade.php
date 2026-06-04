<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catálogo - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b;
            --bg-dark: #121212; --bg-input: #2a2a2a; --border-color: #2b2b2b;
            --text-primary: #e5e5e5; --text-secondary: #a3a3a3; --accent-primary: #E50914;
            --accent-success: #00b894; --accent-danger: #e74c3c; --accent-warning: #fdcb6e;
            --topbar-height: 68px;
        }
        * { font-family: 'Inter', sans-serif; }
        body, html { overflow-x: hidden !important; max-width: 100vw; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .product-card { background: var(--bg-card) !important; border: 1px solid var(--n-border) !important; border-radius: 12px !important; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; position: relative; }
        .product-card:hover { transform: translateY(-5px) scale(1.02); border-color: var(--n-red) !important; box-shadow: 0 10px 20px rgba(0,0,0,0.5); z-index: 5; }
        .product-card.stock-critical { border-left: 4px solid var(--accent-danger); }
        .product-card.stock-low { border-left: 4px solid var(--accent-warning); }
        .product-card.stock-normal { border-left: 4px solid var(--accent-success); }
        .product-card.scan-highlight { border: 2px solid #E50914 !important; box-shadow: 0 0 20px rgba(229,9,20,0.3); animation: scanPulse 1s ease-in-out 3; }
        @keyframes scanPulse { 0%, 100% { box-shadow: 0 0 10px rgba(229,9,20,0.2); } 50% { box-shadow: 0 0 25px rgba(229,9,20,0.5); } }
        
        .product-card-img { width: 100%; height: 220px; object-fit: contain; background-color: #050505; padding: 5px; border-bottom: 1px solid #222; }
        .product-card-img-placeholder { height: 180px; background: #222; display: flex; align-items: center; justify-content: center; color: #555; font-size: 3rem; }
        .product-card-info { padding: 1rem 1rem 0.5rem; }
        .product-card-title { font-weight: 600; font-size: 1.05rem; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-card-meta { color: var(--text-secondary); font-size: 0.8rem; }
        .product-card-code { color: #777; font-size: 0.75rem; font-family: monospace; margin-top: 4px; }
        
        .stock-pill { display: flex; align-items: center; background: #2a2a2a; border-radius: 6px; overflow: hidden; }
        .stock-pill-btn { background: none; border: none; color: #e5e5e5; padding: 6px 10px; cursor: pointer; font-size: 0.8rem; }
        .stock-pill-value { width: 40px; text-align: center; background: transparent; border: none; color: #e5e5e5; font-weight: 600; font-size: 0.9rem; outline: none;}
        
        .professional-footer { text-align: center; padding: 1.5rem 4%; margin-top: 2rem; border-top: 1px solid var(--border-color); color: var(--text-secondary); font-size: 0.85rem; }
        .professional-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .professional-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        .modal-content { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--n-border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--n-border); }
        .modal-footer { border-top: 1px solid var(--n-border); }
        .form-control { background: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; }
        .form-control:focus { background: #333; border-color: var(--accent-primary); color: #e5e5e5; box-shadow: none; }
        .form-label { color: var(--text-secondary); }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #B20710; border-radius: 10px; border: 2px solid #0a0a0a; }
        ::-webkit-scrollbar-thumb:hover { background: #E50914; }

        .nav-pills .nav-link { color: #a0a0a0; background-color: transparent; border: 1px solid #333; transition: all 0.3s ease; }
        .nav-pills .nav-link:hover { color: #fff; background-color: rgba(255, 255, 255, 0.05); }
        .nav-pills .nav-link.active { color: #fff; background-color: #E50914 !important; border-color: #E50914; box-shadow: 0 0 15px rgba(229, 9, 20, 0.4); }

        @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .animate-page-enter { animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }

        @keyframes stockSube { 0% { color: #25D366; transform: scale(1.5); } 100% { color: white; transform: scale(1); } }
        @keyframes stockBaja { 0% { color: #E50914; transform: scale(1.5); } 100% { color: white; transform: scale(1); } }
        .anim-stock-sube { animation: stockSube 0.4s ease-out; }
        .anim-stock-baja { animation: stockBaja 0.4s ease-out; }
        .oswa-stock-input::-webkit-outer-spin-button, .oswa-stock-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .oswa-stock-input { -moz-appearance: textfield; }

        /* Botón Flotante del Escáner */
        .scanner-fab { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg, var(--accent-primary), #B20710); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; border: none; cursor: pointer; box-shadow: 0 4px 20px rgba(229,9,20,0.4); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 999; }
        @media (max-width: 767.98px) { .scanner-fab { bottom: 85px; } }
        
        /* Paginación estilo Netflix */
        .pagination { gap: 4px; }
        .pagination .page-item .page-link { background: #1c1c1c; border: 1px solid #333; color: #a3a3a3; border-radius: 8px !important; padding: 8px 14px; font-weight: 500; transition: all 0.2s; margin: 0 2px; }
        .pagination .page-item .page-link:hover { background: #2a2a2a; color: #fff; border-color: #E50914; }
        .pagination .page-item.active .page-link { background: #E50914; border-color: #E50914; color: #fff; box-shadow: 0 0 10px rgba(229,9,20,0.3); }
        .pagination .page-item.disabled .page-link { opacity: 0.4; pointer-events: none; }
        .scanner-fab:hover { transform: scale(1.15) translateY(-5px); box-shadow: 0 8px 25px rgba(229,9,20,0.6); border: 1px solid #ff6b6b; }

        /* --- ESTILOS DEL ESCÁNER VIP (CORREGIDO) --- */
        .scanner-laser-zone { position: relative; width: 100%; max-height: 320px; overflow: hidden; background: #000; border-bottom: 2px solid #E50914; display: flex; align-items: center; justify-content: center; flex-direction: column; }
        
        #reader { width: 100%; border: none !important; background: transparent; }
        #reader video { max-height: 250px !important; width: 100% !important; object-fit: cover; }
        
        #reader__dashboard_section_csr span { color: #fff !important; font-size: 0.85rem; }
        #reader__dashboard_section_swaplink { color: #E50914 !important; text-decoration: none; font-weight: bold; }
        #reader button { background: #E50914 !important; color: white !important; border: none !important; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.2s; margin-top: 8px; margin-bottom: 8px; font-size: 0.9rem; }
        #reader button:hover { background: #ff0f1b !important; }
        #reader select { background: #2a2a2a !important; color: white !important; border: 1px solid #444 !important; border-radius: 6px; padding: 6px; outline: none; margin-bottom: 10px; max-width: 80%; }
        #reader a { color: #E50914 !important; }
        
        .scanner-laser-zone::after {
            content: ''; position: absolute; top: 15%; left: 10%; width: 80%; height: 2px;
            background: #E50914; box-shadow: 0 0 15px 2px #E50914;
            animation: scanLaser 2s ease-in-out infinite; pointer-events: none; z-index: 10;
        }
        @keyframes scanLaser {
            0%, 100% { top: 20%; opacity: 0.5; }
            50% { top: 80%; opacity: 1; }
        }

        @keyframes pulse-animation {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        .swal2-toast { border: 1px solid #333 !important; box-shadow: 0 8px 32px rgba(0,0,0,0.6) !important; border-radius: 12px !important; padding: 16px !important; }
        .swal2-toast .swal2-title { font-size: 0.9rem !important; margin: 0 !important; }
        .swal2-toast .swal2-html-container { font-size: 0.8rem !important; margin: 4px 0 !important; }
        .swal2-toast .swal2-actions { gap: 6px !important; margin-top: 10px !important; }
        .swal2-toast .swal2-confirm, .swal2-toast .swal2-cancel { font-weight: 600 !important; padding: 6px 14px !important; border-radius: 6px !important; font-size: 0.8rem !important; min-width: 80px !important; }
    </style>
</head>
<body data-theme="dark">
    
@include('partials.navbar')
    
    <main class="main-content">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-50 animate-page-enter">
            <div class="d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-grid-fill fs-4"></i>
                </div>
                <h2 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">Catálogo General</h2>
            </div>

            <div class="d-flex gap-2 align-items-center">
                @if(!Auth::check() || Auth::user()->rol === 'empleado')
                <a href="{{ route('requisiciones.crear') ?? '#' }}" class="btn btn-warning fw-bold px-4 py-2 d-flex align-items-center gap-2" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="bi bi-cart3 fs-5"></i>
                    <span>Mi Requisición</span>
                    <span class="badge bg-dark text-warning ms-1 border border-warning rounded-pill" id="contador-requisicion">0</span>
                </a>
                @endif

                @if(isset($esAdmin) && $esAdmin || (Auth::check() && Auth::user()->tienePermiso('aprobar_requisiciones')))
                <button type="button" class="btn btn-dark position-relative px-4 py-2 fw-bold" 
                    style="background-color: #1a1a1a; border: 1px solid #333; border-radius: 8px; transition: all 0.3s;" 
                    data-bs-toggle="modal" data-bs-target="#modalRequisiciones">
                    
                    <i class="bi bi-inbox-fill text-warning me-2"></i>Ver Solicitudes
                    
                    @if(isset($requisicionesPendientes) && $requisicionesPendientes->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-lg" 
                              style="animation: pulse-animation 2s infinite; font-size: 0.7rem; padding: 5px 8px;">
                            {{ $requisicionesPendientes->count() }}
                            <span class="visually-hidden">solicitudes nuevas</span>
                        </span>
                    @endif
                </button>
                @endif
            </div>
        </div>

        <ul class="nav nav-pills mb-4 animate-page-enter delay-1" id="catalogoTabs" role="tablist" style="gap: 10px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-2" id="productos-tab" data-bs-toggle="pill" data-bs-target="#tab-productos" type="button" role="tab" style="border-radius: 8px;">
                    <i class="bi bi-grid-fill me-2"></i> Productos
                </button>
            </li>
            @if(Auth::check() && Auth::user()->tienePermiso('ver_auditoria'))
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-2" id="auditoria-tab" data-bs-toggle="pill" data-bs-target="#tab-auditoria" type="button" role="tab" style="border-radius: 8px;">
                    <i class="bi bi-clock-history me-2"></i> Historial de Movimientos
                </button>
            </li>
            @endif
        </ul>

        <div class="tab-content animate-page-enter delay-2" id="catalogoTabsContent">
            
            <div class="tab-pane fade show active" id="tab-productos" role="tabpanel" tabindex="0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span style="color:var(--text-secondary);font-weight:500;">{{ $productos->count() ?? 0 }} productos</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('productos.pdf_qr') }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-qr-code"></i> Imprimir QR
                        </a>
                    </div>
                </div>

                <div class="products-grid" id="productsGrid">
                    @forelse($productos as $producto)
                    <div class="product-card {{ $producto->stock == 0 ? 'stock-critical' : ($producto->stock_bajo ? 'stock-low' : 'stock-normal') }}">
                        @if($producto->imagen)
                            @if(filter_var($producto->imagen, FILTER_VALIDATE_URL))
                                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="product-card-img" loading="lazy">
                            @else
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="product-card-img" loading="lazy">
                            @endif
                        @else
                            <div class="product-card-img-placeholder"><i class="bi bi-image"></i></div>
                        @endif
                        
                        <div class="product-card-info">
                            <div class="product-card-title">{{ $producto->nombre }}</div>
                            <div class="product-card-meta">{{ $producto->marca ?? 'Sin marca' }} • {{ $producto->categoria }}</div>
                            <div class="product-card-code"><i class="bi bi-upc-scan"></i> {{ $producto->codigo }} @if($producto->unidad_medida && $producto->unidad_medida !== 'unidad')<span class="badge ms-1" style="font-size:0.55rem;background:rgba(255,255,255,0.06);color:#888;vertical-align:middle;">{{ $producto->unidad_medida }}</span>@endif</div>
                            @if($producto->precio_costo && auth()->user()->tienePermiso('gestionar_precios'))
                            <div style="font-size:0.7rem;color:#666;margin-bottom:6px;">
                                <i class="bi bi-currency-dollar me-1"></i>Costo: ${{ number_format($producto->precio_costo, 2) }}
                                @if($producto->proveedor && $producto->proveedor->nombre)
                                    <span class="ms-2"><i class="bi bi-buildings me-1"></i>{{ $producto->proveedor->nombre }}</span>
                                @endif
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                @if(auth()->user()->tienePermiso('ver_catalogo'))
                                    <div class="fw-bold" style="color: #00b894; font-size: 1.1rem; display: flex; align-items: center; gap: 6px;">
                                        ${{ number_format($producto->precio, 2) }}
                                        @if(auth()->user()->tienePermiso('gestionar_precios') && $producto->margen !== null)
                                            <span class="badge" style="font-size:0.6rem;background:{{ $producto->margen >= 30 ? 'rgba(0,184,148,0.2)' : ($producto->margen >= 15 ? 'rgba(253,203,110,0.2)' : 'rgba(229,9,20,0.2)') }};color:{{ $producto->margen >= 30 ? '#00b894' : ($producto->margen >= 15 ? '#fdcb6e' : '#E50914') }};">
                                                {{ $producto->margen }}%
                                            </span>
                                        @endif
                                        @if(auth()->user()->tienePermiso('gestionar_precios'))
                                        <i class="bi bi-graph-up-arrow" style="font-size: 0.85rem; color: #666; cursor: pointer;" title="Ver historial de precios" onclick="verHistorialPrecios({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')"></i>
                                        @endif
                                    </div>
                                @else
                                    <div class="fw-bold text-secondary" style="font-size: 0.9rem;">
                                        <i class="bi bi-box-seam me-1"></i> Stock
                                    </div>
                                @endif

                                @if($producto->fecha_vencimiento)
                                    @php
                                        $fechaVenc = \Carbon\Carbon::parse($producto->fecha_vencimiento);
                                        $hoy = \Carbon\Carbon::now();
                                        $dias = $hoy->diffInDays($fechaVenc, false);
                                        $color = ''; $texto = ''; $icono = '';

                                        if ($dias < 0) {
                                            $color = '#ff4757'; $texto = '¡Vencido!'; $icono = 'bi-exclamation-octagon-fill';
                                        } elseif ($dias <= 30) {
                                            $color = '#ffa502'; $texto = 'Vence en ' . floor($dias) . ' d'; $icono = 'bi-clock-history';
                                        } else {
                                            $color = '#7bed9f'; $texto = $fechaVenc->format('d/m/y'); $icono = 'bi-calendar2-check';
                                        }
                                    @endphp
                                    <div class="fw-semibold px-2 py-1 rounded" style="color: {{ $color }}; background: rgba(0,0,0,0.2); font-size: 0.75rem; letter-spacing: 0.5px; border: 1px solid {{ $color }}40;">
                                        <i class="bi {{ $icono }} me-1"></i> {{ $texto }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 px-3 pb-3" style="border-top: 1px solid #2a2a2a; background-color: rgba(0,0,0,0.2);">
                            @if(Auth::user()->tienePermiso('gestionar_productos'))
                            
                            <!-- Control de Stock VIP -->
                            <div class="d-flex justify-content-between align-items-center mb-2 px-2 py-1" style="background-color: #0a0a0a; border-radius: 12px; border: 1px solid #333; box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);">
                                <button type="button" class="btn btn-sm text-secondary fs-5 px-3 border-0 transition-all" onmouseover="this.style.color='#E50914'" onmouseout="this.style.color='#6c757d'" onclick="actualizarStockRapido({{ $producto->id }}, -1)">-</button>
                                <input type="number" id="stock-input-{{ $producto->id }}" value="{{ $producto->stock }}" class="fw-bold fs-5 text-white text-center border-0 bg-transparent oswa-stock-input" style="width: 60px; outline: none;" onchange="guardarStockManual({{ $producto->id }})" data-stock-maximo="{{ $producto->stock_maximo }}" data-producto-nombre="{{ $producto->nombre }}">
                                <button type="button" class="btn btn-sm text-secondary fs-5 px-3 border-0 transition-all" onmouseover="this.style.color='#00b894'" onmouseout="this.style.color='#6c757d'" onclick="actualizarStockRapido({{ $producto->id }}, 1)">+</button>
                            </div>
                            @if($producto->stock_bajo)
                                <div class="mb-2 text-center" style="font-size:0.7rem;color:#E50914;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Stock mínimo: {{ $producto->stock_minimo }} {{ $producto->unidad_medida }}
                                </div>
                            @endif

                            <!-- Botones de Acción (Editar principal, Eliminar secundario) -->
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm flex-grow-1 text-dark fw-bold d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #fdcb6e, #ffeaa7); border: none; border-radius: 8px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" data-producto="{{ $producto->toJson() }}" onclick="editarProducto(JSON.parse(this.getAttribute('data-producto')))">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>
                                
                                <a href="{{ route('transferir.vista', $producto->id) }}" target="_blank" class="btn btn-sm d-flex align-items-center justify-content-center" title="Transferir a Sucursal" style="width: 40px; height: 38px; background: rgba(0,184,148,0.1); color: #00b894; border: 1px solid rgba(0,184,148,0.2); border-radius: 8px; transition: all 0.2s; text-decoration:none;" onmouseover="this.style.background='rgba(0,184,148,0.2)'" onmouseout="this.style.background='rgba(0,184,148,0.1)'">
                                    <i class="bi bi-send-fill"></i>
                                </a>
                                <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center" title="Eliminar Producto" style="width: 40px; height: 38px; background: rgba(229, 9, 20, 0.1); color: #E50914; border: 1px solid rgba(229, 9, 20, 0.2); border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='rgba(229, 9, 20, 0.2)'" onmouseout="this.style.background='rgba(229, 9, 20, 0.1)'" onclick="confirmarEliminacion({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                
                                <form id="form-eliminar-{{ $producto->id }}" action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>

                            <!-- Análisis y Proyección de Stock -->
                            @if($producto->fecha_agotamiento && $producto->stock <= 20)
                                <div class="mt-3 p-2 rounded w-100" style="background: rgba(253, 203, 110, 0.08); border: 1px solid rgba(253, 203, 110, 0.2);">
                                    <small class="text-warning d-block text-center" style="font-size: 0.75rem; line-height: 1.3;">
                                        <i class="bi bi-graph-down-arrow me-1"></i> <strong>Proyección de Inventario:</strong><br> Estimación de agotamiento para el <strong>{{ $producto->fecha_agotamiento }}</strong>.
                                    </small>
                                </div>
                            @endif

                            @else
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-info flex-grow-1" style="border-radius: 6px; border-color: #17a2b8;" onclick="verDetallesProducto('{{ $producto->nombre }}', '{{ $producto->categoria }}', '{{ $producto->codigo }}', '{{ $producto->stock }}', '{{ $producto->imagen ? asset('storage/'.$producto->imagen) : 'https://via.placeholder.com/150' }}')">
                                        <i class="bi bi-eye me-1"></i> Detalles
                                    </button>
                                    <button type="button" class="btn btn-outline-warning fw-bold flex-grow-1" onclick="agregarARequisicion({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->stock }})" style="border-radius: 6px;">
                                        <i class="bi bi-plus-lg me-1"></i> Pedir
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div style="grid-column: 1 / -1; text-align:center; padding:4rem 0; color:var(--text-secondary);">
                        <i class="bi bi-inbox" style="font-size:3rem;"></i>
                        <p class="mt-3">Aún no hay productos registrados en el inventario.</p>
                    </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $productos->links('pagination::bootstrap-5') }}
                </div>
            </div>

            @if(Auth::check() && Auth::user()->tienePermiso('ver_auditoria'))
            <div class="tab-pane fade" id="tab-auditoria" role="tabpanel" tabindex="0">
                <div class="p-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-white m-0"><i class="bi bi-file-earmark-text text-danger me-2"></i> Registro de Operaciones</h5>
                        <button onclick="exportarTablaCSV('Auditoria_OSWA_Inv.csv')" class="btn btn-success d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-excel"></i> Exportar CSV
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle" id="tablaAuditoria" style="background: transparent; border-color: #333;">
                            <thead class="text-secondary" style="font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase;">
                                <tr style="border-bottom: 2px solid #333;">
                                    <th class="py-3">Fecha</th>
                                    <th class="py-3">Producto</th>
                                    <th class="py-3">Tipo</th>
                                    <th class="py-3">Cantidad</th>
                                    <th class="py-3">Motivo</th>
                                    <th class="py-3">Usuario</th>
                                    <th class="py-3">Firma SHA-256</th>
                                    <th class="py-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: none; font-size: 0.95rem;">
                                @if(isset($auditorias))
                                    @foreach($auditorias as $audit)
                                    <tr style="border-bottom: 1px solid #222;">
                                        <td class="text-light">
                                            {{ \Carbon\Carbon::parse($audit->created_at)->format('d/m/Y') }}<br>
                                            <span class="text-secondary" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($audit->created_at)->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-light">{{ $audit->producto->codigo_barras ?? 'N/A' }}</span><br>
                                            <span class="text-secondary" style="font-size: 0.85rem;">{{ $audit->producto->nombre ?? 'Producto Eliminado' }}</span>
                                        </td>
                                        <td>
                                            @if($audit->tipo == 'Entrada')
                                                <span class="badge border border-success text-success px-2 py-1" style="background: rgba(25, 135, 84, 0.1);">↙ Entrada</span>
                                            @else
                                                <span class="badge border border-danger text-danger px-2 py-1" style="background: rgba(220, 53, 69, 0.1);">↗ Salida</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-white">{{ $audit->cantidad }}</td>
                                        <td class="text-light">{{ $audit->motivo ?? 'Ajuste de inventario' }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 px-3 py-2" style="font-weight: 500;">
                                                {{ $audit->usuario->display_name ?? 'Sistema' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-secondary" style="font-family: monospace; font-size: 0.85rem; letter-spacing: 0.5px;" title="{{ $audit->firma_hash }}">
                                                {{ $audit->firma_hash ? substr($audit->firma_hash, 0, 15) . '...' : 'SIN FIRMA' }}
                                            </span>
                                            @if($esAdmin && !str_contains($audit->motivo, 'REVERSIÓN'))
                                                <button class="btn btn-sm btn-outline-danger ms-2" onclick="revertirTransaccion({{ $audit->id }})" title="Revertir Transacción">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$audit->firma_hash)
                                                <span class="badge border border-secondary text-secondary px-3 py-2 d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.05); border-radius: 6px;"><i class="bi bi-clock-history"></i> Antiguo</span>
                                            @elseif($audit->esValida())
                                                <span class="badge border border-success text-success px-3 py-2 d-inline-flex align-items-center gap-1" style="background: rgba(25, 135, 84, 0.05); border-radius: 6px; box-shadow: 0 0 10px rgba(25,135,84,0.2);"><i class="bi bi-shield-check"></i> Válida</span>
                                            @else
                                                <span class="badge border border-danger text-danger px-3 py-2 d-inline-flex align-items-center gap-1" style="background: rgba(220, 53, 69, 0.1); border-radius: 6px; box-shadow: 0 0 10px rgba(220,53,69,0.4); animation: pulseRed 2s infinite;"><i class="bi bi-shield-x"></i> ALTERADA</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>

<!-- FOOTER GLOBAL OSWA INV -->
<footer class="professional-footer mt-5 pt-4 pb-4" style="text-align: center; border-top: 1px solid #2b2b2b; color: #a3a3a3; font-size: 0.85rem; background-color: transparent;">
    <div class="mb-2">
        &copy; <script>document.write(new Date().getFullYear())</script> <strong class="text-white">OSWA Inv</strong>. Todos los derechos reservados.
    </div>
    <div class="mb-2">
        Desarrollado con <i class="bi bi-code-slash text-secondary"></i> y <i class="bi bi-heart-fill text-danger"></i> por <span class="text-white fw-bold">Carlos Braca & Yorgelys Blanco</span>
    </div>
    
    <div class="d-flex align-items-center justify-content-center gap-3 mt-3" style="font-size: 0.85rem;">
        <span style="color: #888888;">Ingeniería en Informática — V Semestre</span>
        
        <div style="width: 1px; height: 16px; background-color: #444444;"></div>
        
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 22px; filter: brightness(0) invert(1) opacity(0.9);">
            <strong class="text-white" style="letter-spacing: 1px;">UNELLEZ</strong>
        </div>
    </div>
</footer>

    <!-- MODAL: NUEVO / EDITAR PRODUCTO -->
        <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold" id="modalProductoTitle"><i class="bi bi-box-seam text-danger me-2"></i> Gestión de Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-md-none d-flex gap-2 mb-3 sticky-top" style="z-index:5;">
                        <button type="button" id="btnGuardarMobile" class="btn btn-danger fw-bold flex-grow-1"><i class="bi bi-save me-1"></i> Guardar</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                    <form id="formProducto" enctype="multipart/form-data">
                        <input type="hidden" id="prodId" name="id">
                        <input type="hidden" id="prodImagenUrl" name="imagen_url">
                        <div class="row">
                            <div class="col-md-4 mb-4 mb-md-0 d-flex flex-column align-items-center justify-content-start">
                                <label class="form-label text-secondary w-100 text-center fw-bold"><i class="bi bi-camera me-1"></i> Fotografía</label>
                                <div class="position-relative mt-2" style="width: 180px; height: 180px; border: 2px dashed #444; border-radius: 16px; overflow: hidden; background: #1a1a1a; cursor: pointer;" onclick="document.getElementById('prodImagen').click()">
                                    <img id="imgPreview" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <div id="imgPlaceholder" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="bi bi-cloud-arrow-up fs-1 mb-2 text-secondary"></i>
                                        <span style="font-size: 0.8rem; text-align: center; padding: 0 10px;">Clic para subir<br>imagen (JPG/PNG)</span>
                                    </div>
                                </div>
                                <input type="file" id="prodImagen" name="imagen" class="d-none" accept="image/*" onchange="previewImage(event)">
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label text-secondary">Nombre del Producto</label>
                                    <input type="text" id="prodNombre" name="nombre" class="form-control bg-dark text-white border-secondary" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Código</label>
                                        <input type="text" id="prodCodigo" name="codigo" class="form-control bg-dark text-white border-secondary">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Marca</label>
                                        <input type="text" id="prodMarca" name="marca" class="form-control bg-dark text-white border-secondary">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Precio Venta ($)</label>
                                        <input type="number" step="0.01" id="prodPrecio" name="precio" class="form-control bg-dark text-white border-secondary" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Precio Costo ($)</label>
                                        <input type="number" step="0.01" id="prodPrecioCosto" name="precio_costo" class="form-control bg-dark text-white border-secondary" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="form-label text-secondary">Stock Actual</label>
                                        <input type="number" id="prodStock" name="stock" class="form-control bg-dark text-white border-secondary" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-secondary">Stock Mínimo</label>
                                        <input type="number" id="prodStockMinimo" name="stock_minimo" class="form-control bg-dark text-white border-secondary" placeholder="5">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-secondary">Stock Máximo</label>
                                        <input type="number" id="prodStockMaximo" name="stock_maximo" class="form-control bg-dark text-white border-secondary" placeholder="—">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Unidad de Medida</label>
                                        <select id="prodUnidad" name="unidad_medida" class="form-select bg-dark text-white border-secondary">
                                            <option value="unidad">Unidad</option>
                                            <option value="kg">Kilogramo (kg)</option>
                                            <option value="g">Gramo (g)</option>
                                            <option value="l">Litro (L)</option>
                                            <option value="ml">Mililitro (ml)</option>
                                            <option value="caja">Caja</option>
                                            <option value="paquete">Paquete</option>
                                            <option value="tonelada">Tonelada</option>
                                            <option value="metro">Metro (m)</option>
                                            <option value="par">Par</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Categoría</label>
                                <input type="text" id="prodCategoria" name="categoria" class="form-control bg-dark text-white border-secondary">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Vencimiento (Opcional)</label>
                                <input type="date" id="prod-vencimiento" name="fecha_vencimiento" class="form-control bg-dark text-white border-secondary">
                            </div>
                        </div>
                        
                        <!-- NUEVO: SELECTOR DE PROVEEDOR -->
                        <div class="row mb-3">
                            <div class="col-12">
                                 <label class="form-label text-secondary"><i class="bi bi-buildings me-1"></i> Proveedor Asignado (Opcional)</label>
                                <select id="prod-proveedor" name="proveedor_id" class="form-select bg-dark text-white border-secondary">
                                    <option value="">-- Sin proveedor asignado --</option>
                                    @foreach(\App\Models\Proveedor::all() as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label text-secondary"><i class="bi bi-currency-dollar me-1"></i> Precios por Proveedor</label>
                                <div id="preciosProveedorContainer" style="max-height:200px;overflow-y:auto;">
                                    <div style="color:#555;font-size:0.8rem;padding:8px 0;">Selecciona un proveedor y guarda para registrar su precio.</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarProducto" class="btn btn-danger fw-bold"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitudes Pendientes -->
    <div class="modal fade" id="modalRequisiciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color: #141414; border: 1px solid #333; border-radius: 12px;">
                <div class="modal-header" style="border-bottom: 1px solid #2a2a2a;">
                    <h5 class="modal-title text-white"><i class="bi bi-inbox-fill text-warning me-2"></i> Solicitudes Pendientes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-white" id="modalRequisicionesBody">
                    <p class="text-muted text-center py-4">Cargando solicitudes...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- SCRIPTS VITALES DEL CATÁLOGO -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.Toast = (typeof Swal !== 'undefined') ? Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, 
                timerProgressBar: true, background: '#141414', color: '#fff',
                customClass: { popup: 'border border-secondary' }
            }) : null;

            let mensajeExito = null;

            const btnGuardarMobile = document.getElementById('btnGuardarMobile');
            if (btnGuardarMobile) {
                btnGuardarMobile.addEventListener('click', function() {
                    document.getElementById('btnGuardarProducto').click();
                });
            }

            const btnGuardar = document.getElementById('btnGuardarProducto');
            if(btnGuardar) {
                btnGuardar.addEventListener('click', function() {
                    const form = document.getElementById('formProducto');
                    const formData = new FormData(form);
                    const id = document.getElementById('prodId').value;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    let url = '{{ route("guardar.producto") }}';
                    if (id) {
                        url = `/productos/${id}/actualizar`;
                        formData.append('_method', 'PUT');
                    }

                    // Validar stock ≤ stock_maximo antes de enviar
                    const stockVal = parseInt(document.getElementById('prodStock')?.value);
                    const maxVal = parseInt(document.getElementById('prodStockMaximo')?.value);
                    if (maxVal > 0 && stockVal > maxVal) {
                        mostrarToast('El stock (' + stockVal + ') supera el máximo permitido (' + maxVal + ')', 'bi bi-exclamation-triangle-fill');
                        return;
                    }

                    // Loading state
                    const originalHtml = btnGuardar.innerHTML;
                    btnGuardar.disabled = true;
                    btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

                    fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        if(data.success) {
                            const nombre = document.getElementById('prodNombre')?.value || data.producto?.nombre || '';
                            const prodId = document.getElementById('prodId').value;
                            if (prodId) {
                                mostrarToast(nombre + ' actualizado correctamente', 'bi bi-check-circle-fill');
                                setTimeout(() => window.location.reload(), 800);
                            } else {
                                mostrarToast(nombre + ' registrado en el catálogo', 'bi bi-check-circle-fill');
                                setTimeout(() => window.location.reload(), 1200);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error al guardar:', error);
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML = originalHtml;
                        let msg = error.message || error.error || 'Revisa los campos e intenta de nuevo.';
                        if (error.errors) {
                            const list = Object.values(error.errors).flat();
                            msg = list.join('<br>');
                        }
                        mostrarToast(msg, 'bi bi-exclamation-triangle-fill');
                    });
                });
            }

            const modalReq = document.getElementById('modalRequisiciones');
            if(modalReq) modalReq.addEventListener('show.bs.modal', mostrarRequisicionesPendientes);

            // AUTO-FILL DESDE EL ESCÁNER
            const params = new URLSearchParams(window.location.search);
            const nuevoCodigo = params.get('nuevo_codigo');
            if (nuevoCodigo) {
                const nuevoNombre = params.get('nuevo_nombre') || '';
                const nuevaImagen = params.get('nueva_imagen') || '';
                setTimeout(() => abrirModalNuevo(), 100);
                setTimeout(() => {
                    document.getElementById('prodCodigo').value = nuevoCodigo;
                    document.getElementById('prodNombre').value = decodeURIComponent(nuevoNombre);
                    if (nuevaImagen) {
                        const img = document.getElementById('imgPreview');
                        img.src = decodeURIComponent(nuevaImagen);
                        img.style.display = 'block';
                        document.getElementById('imgPlaceholder').style.display = 'none';
                        // Guardar URL para enviar al backend
                        document.getElementById('prodImagenUrl').value = decodeURIComponent(nuevaImagen);
                    }
                    mostrarToast('Datos del producto cargados desde el escáner', 'bi bi-upc-scan');
                }, 300);
                // Limpiar URL sin recargar
                window.history.replaceState({}, '', window.location.pathname);
            }
        });

        function actualizarStockRapido(id, cambio) {
            const input = document.getElementById('stock-input-' + id);
            if (!input) return;
            let nuevoValor = parseInt(input.value) + cambio;
            if (nuevoValor < 0) return;
            const maximo = input.getAttribute('data-stock-maximo');
            if (maximo && parseInt(maximo) > 0 && nuevoValor > parseInt(maximo)) {
                mostrarToast('Stock máximo: ' + maximo + ' ' + (input.getAttribute('data-producto-nombre') || ''), 'bi bi-exclamation-triangle-fill');
                return;
            }
            input.value = nuevoValor;
            animarStock(input, cambio);
            mandarStockAlBackend(id, nuevoValor);
        }

        function guardarStockManual(id) {
            const input = document.getElementById('stock-input-' + id);
            if (!input) return;
            let nuevoValor = parseInt(input.value);
            if (isNaN(nuevoValor) || nuevoValor < 0) { input.value = 0; nuevoValor = 0; }
            const maximo = input.getAttribute('data-stock-maximo');
            if (maximo && parseInt(maximo) > 0 && nuevoValor > parseInt(maximo)) {
                mostrarToast('Stock máximo: ' + maximo + ' (' + (input.getAttribute('data-producto-nombre') || '') + ')', 'bi bi-exclamation-triangle-fill');
                input.value = parseInt(maximo);
                return;
            }
            mandarStockAlBackend(id, nuevoValor);
        }

        function animarStock(elemento, cambio) {
            elemento.classList.remove('anim-stock-sube', 'anim-stock-baja');
            void elemento.offsetWidth; 
            elemento.classList.add(cambio > 0 ? 'anim-stock-sube' : 'anim-stock-baja');
        }

        function mandarStockAlBackend(id, cantidad) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/productos/' + id + '/stock', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: JSON.stringify({ cantidad: cantidad })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                if (data.success) mostrarToast('Inventario actualizado', 'bi bi-check-circle-fill');
            })
            .catch(err => {
                mostrarToast(err.error || 'Error al actualizar stock', 'bi bi-exclamation-triangle-fill');
                // Revertir visual
                const input = document.getElementById('stock-input-' + id);
                if (input && err.stock_actual) input.value = err.stock_actual;
            });
        }

        function abrirModalNuevo() {
            document.getElementById('formProducto').reset();
            document.getElementById('prodId').value = '';
            document.getElementById('prodStockMinimo').value = 5;
            document.getElementById('prodUnidad').value = 'unidad';
            document.getElementById('modalProductoTitle').innerHTML = '<i class="bi bi-box-seam text-danger me-2"></i> Nuevo Producto';
            const container = document.getElementById('preciosProveedorContainer');
            if (container) container.innerHTML = '<div style="color:#555;font-size:0.8rem;padding:8px 0;">Guarda el producto primero, luego edítalo para asignar precios por proveedor.</div>';
            
            const imgPreview = document.getElementById('imgPreview');
            const imgPlaceholder = document.getElementById('imgPlaceholder');
            if(imgPreview && imgPlaceholder) {
                imgPreview.src = ''; imgPreview.style.display = 'none'; imgPlaceholder.style.display = 'flex';
            }
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
        }

        function editarProducto(producto) {
            document.getElementById('prodId').value = producto.id;
            document.getElementById('prodNombre').value = producto.nombre;
            document.getElementById('prodPrecio').value = producto.precio;
            document.getElementById('prodPrecioCosto').value = producto.precio_costo || '';
            document.getElementById('prodStock').value = producto.stock || producto.cantidad;
            document.getElementById('prodStockMinimo').value = producto.stock_minimo ?? 5;
            document.getElementById('prodStockMaximo').value = producto.stock_maximo || '';
            const selUnidad = document.getElementById('prodUnidad');
            if (selUnidad) selUnidad.value = producto.unidad_medida || 'unidad';
            document.getElementById('prodCategoria').value = producto.categoria || '';
            document.getElementById('prod-vencimiento').value = producto.fecha_vencimiento ? producto.fecha_vencimiento.split(' ')[0] : '';
            document.getElementById('prodCodigo').value = producto.codigo || producto.codigo_barras || '';
            document.getElementById('prodMarca').value = producto.marca || '';
            
            const prodProveedor = document.getElementById('prod-proveedor');
            if (prodProveedor) prodProveedor.value = producto.proveedor_id || '';

            const imgPreview = document.getElementById('imgPreview');
            const imgPlaceholder = document.getElementById('imgPlaceholder');
            if (imgPreview && imgPlaceholder) {
                if (producto.imagen) {
                    imgPreview.src = producto.imagen.startsWith('http') ? producto.imagen : '/storage/' + producto.imagen;
                    imgPreview.style.display = 'block'; imgPlaceholder.style.display = 'none';
                } else {
                    imgPreview.src = ''; imgPreview.style.display = 'none'; imgPlaceholder.style.display = 'flex';
                }
            }
            // Cargar precios por proveedor
            cargarPreciosProveedor(producto.id);

            document.getElementById('modalProductoTitle').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i> Editar Producto';
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
        }

        async function cargarPreciosProveedor(productoId) {
            const container = document.getElementById('preciosProveedorContainer');
            if (!container) return;
            container.innerHTML = '<div style="color:#666;font-size:0.8rem;padding:8px 0;"><i class="bi bi-hourglass-split me-1"></i>Cargando...</div>';
            try {
                const res = await fetch('/productos/' + productoId + '/proveedores-precio');
                const data = await res.json();
                if (!data.length) {
                    container.innerHTML = '<div style="color:#555;font-size:0.8rem;padding:8px 0;">Sin proveedores adicionales. Selecciona uno y guarda el producto para asignar precio.</div>';
                    return;
                }
                container.innerHTML = data.map(p => {
                    const costo = p.pivot.precio_costo ? '$' + parseFloat(p.pivot.precio_costo).toFixed(2) : '<span style="color:#555;">—</span>';
                    const codProv = p.pivot.codigo_proveedor || '';
                    return '<div style="display:flex;align-items:center;justify-content:space-between;padding:6px 8px;border-bottom:1px solid rgba(255,255,255,0.04);font-size:0.8rem;">' +
                        '<span><i class="bi bi-buildings me-1" style="color:#888;"></i>' + p.nombre + '</span>' +
                        '<span style="color:#00b894;font-weight:600;">' + costo + '</span>' +
                        (codProv ? '<span style="color:#666;font-size:0.7rem;">Código: ' + codProv + '</span>' : '') +
                    '</div>';
                }).join('');
            } catch(e) {
                container.innerHTML = '<div style="color:#E50914;font-size:0.8rem;padding:8px 0;">Error al cargar precios.</div>';
            }
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreview').style.display = 'block';
                document.getElementById('imgPlaceholder').style.display = 'none';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function confirmarEliminacion(id, nombre) {
            Swal.fire({
                title: '¿Eliminar ' + nombre + '?',
                text: 'No hay vuelta atrás',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No',
                background: '#141414',
                color: '#fff',
                position: 'top-end',
                toast: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-eliminar-' + id).submit();
                }
            });
        }

        let priceChartInstance = null;

        function verHistorialPrecios(productoId, nombre) {
            document.getElementById('priceHistoryTitle').textContent = nombre;
            document.getElementById('priceHistoryContent').innerHTML = '<div class="text-center py-4" style="color:#666;"><i class="bi bi-hourglass-split" style="font-size:2rem;"></i><p class="mt-2">Cargando métricas...</p></div>';
            document.getElementById('priceChartContainer').innerHTML = '';
            const modal = new bootstrap.Modal(document.getElementById('modalPriceHistory'));
            modal.show();

            fetch('/api/price-history/' + productoId)
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        document.getElementById('priceHistoryContent').innerHTML = '<div class="text-center py-4" style="color:#555;"><i class="bi bi-clock-history" style="font-size:2.5rem;"></i><p class="mt-3" style="font-size:0.9rem;">Sin cambios de precio registrados.</p></div>';
                        return;
                    }
                    data.reverse();
                    const labels = data.map(h => new Date(h.created_at).toLocaleDateString('es-ES', {day:'2-digit', month:'2-digit'}));
                    const usdPrices = data.map(h => parseFloat(h.precio_usd_nuevo));
                    const bsPrices = data.map(h => h.precio_bs_nuevo != null ? parseFloat(h.precio_bs_nuevo) : null);

                    document.getElementById('priceChartContainer').innerHTML = '<canvas id="priceChart" height="150"></canvas>';
                    if (priceChartInstance) priceChartInstance.destroy();
                    priceChartInstance = new Chart(document.getElementById('priceChart'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Precio USD',
                                data: usdPrices,
                                borderColor: '#00b894',
                                backgroundColor: 'rgba(0,184,148,0.1)',
                                fill: true, tension: 0.3,
                                pointBackgroundColor: '#00b894',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1,
                                pointRadius: 4,
                            }, bsPrices[0] != null ? {
                                label: 'Precio Bs.',
                                data: bsPrices,
                                borderColor: '#fdcb6e',
                                backgroundColor: 'rgba(253,203,110,0.1)',
                                fill: true, tension: 0.3,
                                pointBackgroundColor: '#fdcb6e',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1,
                                pointRadius: 4,
                                yAxisID: 'y1',
                            } : null].filter(Boolean),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { labels: { color: '#888', font: { size: 11 } } }
                            },
                            scales: {
                                x: { ticks: { color: '#666', maxTicksLimit: 10 }, grid: { color: 'rgba(255,255,255,0.03)' } },
                                y: { beginAtZero: false, ticks: { color: '#666', callback: v => '$' + v }, grid: { color: 'rgba(255,255,255,0.03)' } },
                                y1: bsPrices[0] != null ? {
                                    position: 'right',
                                    ticks: { color: '#fdcb6e', callback: v => 'Bs.' + v },
                                    grid: { display: false },
                                } : undefined,
                            }
                        }
                    });

                    let html = '<div style="max-height:300px;overflow-y:auto;margin-top:1rem;">';
                    data.slice().reverse().forEach(h => {
                        const time = new Date(h.created_at).toLocaleString('es-ES', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'});
                        const quien = h.user_name || 'Sistema';
                        const subio = parseFloat(h.precio_nuevo) >= parseFloat(h.precio_anterior);
                        const pctLabel = h.incremento_label && h.incremento_label !== '—' ? h.incremento_label : '';
                        const usdAnt = h.precio_usd_anterior != null ? parseFloat(h.precio_usd_anterior).toFixed(2) : null;
                        const usdNew = h.precio_usd_nuevo != null ? parseFloat(h.precio_usd_nuevo).toFixed(2) : null;
                        const bsAnt = h.precio_bs_anterior != null ? parseFloat(h.precio_bs_anterior).toFixed(2) : null;
                        const bsNew = h.precio_bs_nuevo != null ? parseFloat(h.precio_bs_nuevo).toFixed(2) : null;
                        const tasaLabel = h.tasa_dolar ? 'Bs.' + parseFloat(h.tasa_dolar).toFixed(2) : null;
                        const diffUsd = (usdAnt && usdNew) ? (parseFloat(usdNew) - parseFloat(usdAnt)).toFixed(2) : null;
                        html += `<div style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:#181818;border-radius:8px;margin-bottom:6px;border-left:3px solid ${subio ? '#00b894' : '#E50914'};">
                            <div style="flex:1;">
                                <div style="font-size:0.9rem;font-weight:700;color:#fff;">
                                    <span style="color:#00b894;">$</span>${usdAnt || '—'}
                                    <i class="bi bi-arrow-right" style="color:#666;margin:0 8px;"></i>
                                    <span style="color:${subio ? '#00b894' : '#E50914'};">$${usdNew || '—'}</span>
                                </div>
                                <div style="font-size:0.7rem;color:#666;margin-top:2px;">
                                    <span style="color:#888;">Bs.</span>${bsAnt || '—'} → <span style="color:#888;">Bs.</span>${bsNew || '—'}
                                </div>
                                <div style="font-size:0.7rem;color:#666;margin-top:1px;">
                                    <i class="bi bi-person me-1"></i>${quien} <i class="bi bi-clock ms-2 me-1"></i>${time}
                                    ${tasaLabel ? ` <span style="font-size:0.65rem;color:#555;"><i class="bi bi-currency-exchange ms-2 me-1"></i>${tasaLabel}</span>` : ''}
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:10px;background:${subio ? 'rgba(0,184,148,0.15)' : 'rgba(229,9,20,0.15)'};color:${subio ? '#00b894' : '#E50914'};">${subio ? '▲ +' : '▼ '}${diffUsd ? '$' + diffUsd : '—'}</div>
                                ${pctLabel ? `<div style="font-size:0.65rem;color:#888;margin-top:2px;">${pctLabel}</div>` : ''}
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    document.getElementById('priceHistoryContent').innerHTML = html;
                })
                .catch((err) => {
                    console.error('Price history error:', err);
                    document.getElementById('priceHistoryContent').innerHTML = '<div class="text-center py-4" style="color:#E50914;"><i class="bi bi-exclamation-triangle" style="font-size:2rem;"></i><p class="mt-2">Error al cargar historial</p></div>';
                });
        }

        let transferProductoId = null;
        let transferProductoNombre = '';

        function abrirTransferencia(id, nombre, stock) {
            transferProductoId = id;
            transferProductoNombre = nombre;
            document.getElementById('transferProductoNombre').textContent = nombre;
            document.getElementById('transferProductoStock').textContent = 'Stock: ' + stock + ' uds';
            document.getElementById('transferCantidad').value = 1;
            document.getElementById('transferCantidad').max = stock;
            document.getElementById('transferSucursal').value = '';
            document.getElementById('transferInfo').classList.add('d-none');
            document.getElementById('btnConfirmarTransferencia').disabled = true;
            new bootstrap.Modal(document.getElementById('modalTransferir')).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sel = document.getElementById('transferSucursal');
            const cant = document.getElementById('transferCantidad');
            const btn = document.getElementById('btnConfirmarTransferencia');
            const info = document.getElementById('transferInfo');
            const distEl = document.getElementById('transferDistancia');
            const fleteEl = document.getElementById('transferFlete');

            function actualizarInfo() {
                const opt = sel.options[sel.selectedIndex];
                if (opt && opt.value) {
                    const dist = parseFloat(opt.getAttribute('data-dist')) || 0;
                    distEl.textContent = dist.toLocaleString();
                    info.classList.remove('d-none');
                    btn.disabled = false;
                } else {
                    info.classList.add('d-none');
                    btn.disabled = true;
                }
            }

            sel.addEventListener('change', actualizarInfo);
            cant.addEventListener('input', actualizarInfo);
        });

        function confirmarTransferencia() {
            const cantidad = parseInt(document.getElementById('transferCantidad').value);
            const sucursal = document.getElementById('transferSucursal').value;
            const opt = document.querySelector('#transferSucursal option[value="' + sucursal + '"]');
            const dist = opt ? parseInt(opt.getAttribute('data-dist')) : 0;
            if (!cantidad || !sucursal) return;

            Swal.fire({
                title: '¿Transferir ' + cantidad + ' uds?',
                html:
                    '<div style="text-align:left;color:#ccc;">' +
                    '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Producto</span><span style="color:#fff;font-weight:600;">' + transferProductoNombre + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Destino</span><span style="color:#fff;font-weight:600;">' + sucursal + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Distancia</span><span style="color:#fff;">' + dist.toLocaleString() + ' km</span></div>' +
                    '<div style="display:flex;justify-content:space-between;padding:6px 0;"><span style="color:#888;">Cantidad</span><span style="color:#fff;">' + cantidad + ' uds</span></div>' +
                    '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00b894',
                cancelButtonColor: '#444',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Sí, transferir',
                cancelButtonText: 'Cancelar',
                background: '#1a1a1a',
                color: '#fff',
                position: 'top-end',
                toast: false,
                showConfirmButton: true,
                timer: undefined,
                customClass: { popup: 'border border-secondary shadow-lg' }
            }).then((result) => {
                if (!result.isConfirmed) return;

                const btn = document.getElementById('btnConfirmarTransferencia');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Transfiriendo...';

                fetch('/transferir-producto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        producto_id: transferProductoId,
                        cantidad: cantidad,
                        sucursal: sucursal
                    })
                })
                .then(r => r.json())
                .then(data => {
                    btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Transferir';
                    btn.disabled = false;
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalTransferir')).hide();
                        Swal.fire({
                            title: '✅ Transferencia Exitosa',
                            html:
                                '<div style="text-align:left;color:#ccc;">' +
                                '<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Producto</span><span style="color:#fff;font-weight:600;">' + transferProductoNombre + '</span></div>' +
                                '<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Destino</span><span style="color:#00b894;font-weight:600;">' + sucursal + '</span></div>' +
                                '<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #2a2a2a;"><span style="color:#888;">Distancia</span><span style="color:#fff;">' + dist.toLocaleString() + ' km</span></div>' +
                                '<div style="display:flex;justify-content:space-between;padding:8px 0;"><span style="color:#888;">Cantidad</span><span style="color:#fff;">' + cantidad + ' uds</span></div>' +
                                '<div style="display:flex;justify-content:space-between;padding:8px 0;"><span style="color:#888;">Fecha</span><span style="color:#888;">' + new Date(data.fecha).toLocaleString('es-ES') + '</span></div>' +
                                '</div>' +
                                '<div class="mt-3 text-center" style="font-size:0.8rem;color:#555;">El stock se descontó del inventario local</div>',
                            icon: 'success',
                            confirmButtonColor: '#00b894',
                            confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Listo',
                            background: '#1a1a1a',
                            color: '#fff',
                            customClass: { popup: 'border border-secondary shadow-lg' }
                        }).then(() => location.reload());
                    } else {
                        mostrarToast(data.message || 'Error al transferir', 'bi bi-exclamation-triangle-fill');
                    }
                })
                .catch(() => {
                    btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Transferir';
                    btn.disabled = false;
                    mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
                });
            });
        }

        function mostrarRequisicionesPendientes() {
            const reqs = @json($requisicionesPendientes ?? []);
            const body = document.getElementById('modalRequisicionesBody');
            
            if (!reqs || reqs.length === 0) {
                body.innerHTML = '<div class="text-center py-5"><i class="bi bi-inbox text-secondary" style="font-size:4rem;"></i><h5 class="mt-3 text-white">Bandeja vacía</h5><p class="text-secondary">No hay solicitudes pendientes en este momento.</p></div>';
                return;
            }
            
            let html = '<div style="max-height:450px; overflow-y:auto; padding-right: 10px;">';
            
            reqs.forEach(r => {
                let userName = r.user ? (r.user.display_name || r.user.name) : 'Usuario Desconocido';

                let timeBuster = r.user && r.user.updated_at ? new Date(r.user.updated_at).getTime() : Date.now();

                let rutaImagen = r.user ? (r.user.foto || r.user.avatar || r.user.imagen || r.user.profile_photo_path) : null;

                let userPhoto = rutaImagen
                    ? '/storage/' + rutaImagen + '?v=' + timeBuster
                    : `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=E50914&color=fff&bold=true`;

                let prodName = r.producto ? r.producto.nombre : 'Producto Eliminado';
                
                html += `
                <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-dark border border-secondary rounded-4 shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="d-flex align-items-center gap-3">
                        <img src="${userPhoto}" alt="${userName}" class="rounded-circle border border-secondary" style="width: 60px; height: 60px; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                        <div>
                            <h6 class="mb-0 text-white fw-bold">${userName} <span class="badge bg-danger bg-opacity-25 text-danger ms-2 border border-danger border-opacity-50" style="font-size:0.65rem; letter-spacing: 0.5px;">EMPLEADO</span></h6>
                            <small class="text-secondary d-block mb-1 mt-1" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> ${new Date(r.created_at).toLocaleString()}</small>
                            <span class="text-light" style="font-size: 0.95rem;">Solicita: <b class="text-warning">${prodName}</b></span>
                            <span class="badge bg-secondary ms-2 px-2 py-1 fs-6 text-white border border-secondary shadow-sm">× ${r.cantidad} uds</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <button onclick="procesarRequisicion(${r.id}, 'aprobar')" class="btn btn-sm btn-success fw-bold px-3"><i class="bi bi-check-lg me-1"></i> Aprobar</button>
                        <button onclick="procesarRequisicion(${r.id}, 'rechazar')" class="btn btn-sm btn-outline-danger fw-bold px-3"><i class="bi bi-x-lg me-1"></i> Rechazar</button>
                    </div>
                </div>`;
            });
            
            html += '</div>';
            body.innerHTML = html;
        }

        async function procesarRequisicion(id, accion) {
            try {
                const response = await fetch('/requisiciones/' + id + '/' + accion, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    mostrarToast(data.message, 'bi bi-check-circle-fill');
                    setTimeout(() => location.reload(), 800);
                } else { mostrarToast(data.message, 'bi bi-exclamation-triangle-fill'); }
            } catch (error) { mostrarToast('Problema de conexión.', 'bi bi-exclamation-triangle-fill'); }
        }

        function exportarTablaCSV(nombreArchivo) {
            const tabla = document.getElementById('tablaAuditoria');
            if (!tabla) { mostrarToast('No hay datos para exportar', 'bi bi-exclamation-triangle-fill'); return; }
            let csv = [];
            const filas = tabla.querySelectorAll('tr');
            filas.forEach(fila => {
                let row = [];
                fila.querySelectorAll('th, td').forEach(col => row.push('"' + col.textContent.trim().replace(/"/g, '""') + '"'));
                csv.push(row.join(','));
            });
            const blob = new Blob(['\ufeff' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = nombreArchivo;
            link.click();
        }

        // ==========================================
        // 6. LÓGICA DEL ESCÁNER (HTML5-QRCode)
        // ==========================================
        let html5QrcodeScanner;
        
        document.addEventListener('DOMContentLoaded', () => {
            const scannerModal = document.getElementById('scannerModal');
            if(scannerModal) {
                scannerModal.addEventListener('shown.bs.modal', function () {
                    // Iniciar cámara con un rectángulo perfecto para códigos de barras
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader", { 
                            fps: 10, 
                            qrbox: { width: 280, height: 100 },
                            aspectRatio: 1.5
                        }, 
                        /* verbose= */ false
                    );
                    html5QrcodeScanner.render(onScanSuccess);
                });

                scannerModal.addEventListener('hidden.bs.modal', function () {
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear().catch(error => console.error("Failed to clear scanner.", error));
                    }
                });
            }
        });

        async function onScanSuccess(decodedText, decodedResult) {
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
            
            let modalEl = document.getElementById('scannerModal');
            let modalObj = bootstrap.Modal.getInstance(modalEl);
            if(modalObj) modalObj.hide();
            
            const codigo = decodedText.trim();
            if (!codigo) return;

            document.getElementById('topbarSearchInput').value = codigo;

            let encontrado = null;
            document.querySelectorAll('.product-card').forEach(card => {
                const codeEl = card.querySelector('.product-card-code');
                if (codeEl && codeEl.textContent.includes(codigo)) {
                    encontrado = card;
                }
            });

            if (encontrado) {
                const dataAttr = encontrado.querySelector('[data-producto]');
                if (dataAttr) {
                    try {
                        const producto = JSON.parse(dataAttr.getAttribute('data-producto'));
                        setTimeout(() => editarProducto(producto), 400);
                        mostrarToast('Producto: ' + producto.nombre, 'bi bi-check-circle-fill');
                    } catch(e) {
                        mostrarToast('Error al abrir producto', 'bi bi-exclamation-triangle-fill');
                    }
                } else {
                    document.querySelectorAll('.product-card.scan-highlight').forEach(c => c.classList.remove('scan-highlight'));
                    encontrado.classList.add('scan-highlight');
                    encontrado.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => encontrado.classList.remove('scan-highlight'), 3000);
                }
            } else {
                let nombreApi = '';
                let imagenApi = '';
                try {
                    const apiRes = await fetch('https://world.openfoodfacts.org/api/v0/product/' + codigo + '.json');
                    const apiData = await apiRes.json();
                    if (apiData.status === 1 && apiData.product) {
                        nombreApi = apiData.product.product_name || apiData.product.generic_name || '';
                        imagenApi = apiData.product.image_front_url || apiData.product.image_url || '';
                    }
                } catch(e) { /* ignore api error */ }

                let htmlContent = 'Código: <strong style="color:#E50914;font-family:monospace;font-size:1.2rem;">' + codigo + '</strong>';
                if (nombreApi) {
                    htmlContent += '<br><br><div style="display:flex;align-items:center;gap:12px;justify-content:center;background:#2a2a2a;padding:10px;border-radius:8px"><img src="' + imagenApi + '" style="width:70px;height:70px;object-fit:contain;border-radius:6px;background:#333" onerror="this.style.display=\'none\'"> <span style="font-weight:500;font-size:0.95rem">' + nombreApi + '</span></div>';
                }
                htmlContent += '<br>¿Deseas registrarlo en el catálogo?';

                Swal.fire({
                    icon: 'question',
                    title: 'Producto no registrado',
                    html: htmlContent,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-plus-circle me-1"></i> Registrar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#E50914',
                    cancelButtonColor: '#444',
                    background: '#1c1c1c', color: '#fff',
                    reverseButtons: true,
                    focusConfirm: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        setTimeout(() => {
                            abrirModalNuevo();
                            setTimeout(() => {
                                document.getElementById('prodCodigo').value = codigo;
                                if (nombreApi) document.getElementById('prodNombre').value = nombreApi;
                                if (imagenApi) {
                                    const img = document.getElementById('imgPreview');
                                    img.src = imagenApi;
                                    img.style.display = 'block';
                                    document.getElementById('imgPlaceholder').style.display = 'none';
                                    document.getElementById('prodImagenUrl').value = imagenApi;
                                }
                            }, 300);
                        }, 400);
                    }
                });
            }
        }

        function procesarCodigoModal() {
            let code = document.getElementById('manualBarcodeInput').value.trim();
            if(!code) return;
            onScanSuccess(code, null);
        }

        function cerrarScannerYAbrirNuevo() {
            let manualCode = document.getElementById('manualBarcodeInput').value.trim();
            
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
            let mScanner = bootstrap.Modal.getInstance(document.getElementById('scannerModal'));
            if(mScanner) mScanner.hide();
            
            setTimeout(() => {
                abrirModalNuevo();
                if(manualCode) {
                    document.getElementById('prodCodigo').value = manualCode;
                }
            }, 400);
        }
    </script>

    <!-- BOTÓN FLOTANTE PARA ABRIR EL ESCÁNER -->
    <button class="scanner-fab" data-bs-toggle="modal" data-bs-target="#scannerModal" title="Abrir Escáner de Código de Barras">
        <i class="bi bi-upc-scan"></i>
    </button>

    <!-- MODAL: ESCÁNER DE CÓDIGOS -->
    <div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-upc-scan text-success me-2"></i> Escanear Código</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="overflow: hidden;">
                    <div class="scanner-laser-zone" style="width: 100%; height: 300px; background: #000; display: flex; align-items: center; justify-content: center;">
                        <div id="reader" style="width: 100%; position: relative; z-index: 5;"></div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 d-flex flex-column gap-3">
                    <div class="d-flex gap-2 w-100">
                        <input type="text" id="manualBarcodeInput" class="form-control bg-dark text-white border-secondary" placeholder="Ingresar código manual...">
                        <button onclick="procesarCodigoModal()" class="btn btn-outline-secondary" title="Buscar Código"><i class="bi bi-search"></i></button>
                    </div>
                    <button onclick="cerrarScannerYAbrirNuevo()" class="btn w-100 fw-bold" style="background: linear-gradient(135deg, #E50914, #B20710); color: white; border: none;">
                        <i class="bi bi-plus-lg me-1"></i> Registrar Nuevo Producto con este Código
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE LECTURA (EMPLEADO - SOLO VISUALIZACIÓN) -->
    <div class="modal fade" id="modalDetallesLectura" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-secondary shadow-lg" style="background-color: #1a1a1a;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-info-circle text-info me-2"></i>Ficha del Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <img id="lectura-imagen" src="" alt="Producto" style="width: 160px; height: 160px; object-fit: cover; border-radius: 12px; margin-bottom: 15px; border: 2px solid #333;">
                    <h4 id="lectura-nombre" class="text-white fw-bold mb-1"></h4>
                    <p id="lectura-categoria" class="text-secondary small mb-3"></p>

                    <div class="row g-2 text-start mt-2">
                        <div class="col-6">
                            <div class="p-3 bg-dark rounded border border-secondary h-100">
                                <small class="text-secondary d-block mb-1">Código de Barras</small>
                                <strong id="lectura-codigo" class="text-white fs-6"></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-dark rounded border border-secondary h-100">
                                <small class="text-secondary d-block mb-1">Stock Actual</small>
                                <strong id="lectura-stock" class="text-white fs-5"></strong> <span class="text-secondary small">uds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-dark w-100" data-bs-dismiss="modal">Cerrar Ficha</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL HISTORIAL DE PRECIOS -->
    <div class="modal fade" id="modalPriceHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-secondary shadow-lg" style="background: #1a1a1a;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-graph-up-arrow text-danger me-2"></i>Métrica de Subida de Precio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="text-white mb-3" id="priceHistoryTitle">Producto</h6>
                    <div id="priceChartContainer" style="margin-bottom:1rem;"></div>
                    <div id="priceHistoryContent">
                        <div class="text-center py-4" style="color:#666;">
                            <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                            <p class="mt-2">Cargando historial...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TRANSFERIR A SUCURSAL -->
    <div class="modal fade" id="modalTransferir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-secondary shadow-lg" style="background: #1a1a1a;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-send-fill text-success me-2"></i>Transferir a Sucursal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-white-50" style="font-size:0.8rem;">Producto</label>
                        <div class="text-white fw-bold" id="transferProductoNombre" style="font-size:1.1rem;">—</div>
                        <div style="color:#888;font-size:0.8rem;" id="transferProductoStock">Stock: —</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50" style="font-size:0.8rem;">Cantidad a transferir</label>
                        <input type="number" id="transferCantidad" class="form-control bg-dark text-white border-secondary" value="1" min="1" style="border-radius:8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50" style="font-size:0.8rem;">Sucursal destino</label>
                        <select id="transferSucursal" class="form-select bg-dark text-white border-secondary" style="border-radius:8px;">
                            <option value="">Seleccionar sucursal...</option>
                            @foreach(config('sucursales') as $nombre => $data)
                            <option value="{{ $nombre }}" data-dist="{{ $data['dist'] }}">{{ $nombre }} @if($data['dist'] > 0)({{ number_format($data['dist']) }} km)@else (Sede Principal)@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="transferInfo" class="p-3 rounded d-none" style="background:rgba(0,184,148,0.08);border:1px solid rgba(0,184,148,0.2);">
                        <div style="color:#00b894;font-size:0.85rem;"><i class="bi bi-geo-alt me-1"></i>Distancia: <strong id="transferDistancia">—</strong> km</div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn fw-bold" id="btnConfirmarTransferencia" onclick="confirmarTransferencia()" disabled style="background:linear-gradient(135deg,#00b894,#00a381);color:#fff;border:none;border-radius:8px;">
                        <i class="bi bi-send-fill me-1"></i> Transferir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SENSOR DE CONEXIÓN INTELIGENTE (OFFLINE/ONLINE) -->
    <script>
        function checkNetworkStatus() {
            const isOnline = navigator.onLine;
            const indicators = document.querySelectorAll('.status-indicator');
            
            // Botones clave a bloquear
            const btnProcesar = document.getElementById('btnProcesar');
            const btnGuardarProd = document.getElementById('btnGuardarProducto');

            // Actualizar interfaz visual (Navbar)
            indicators.forEach(ind => {
                const dot = ind.querySelector('.status-dot');
                const text = ind.querySelector('.status-text');
                
                if (isOnline) {
                    if (text) { text.innerText = 'En línea'; text.style.color = '#ccc'; }
                    if (dot) { dot.style.background = '#00b894'; dot.style.boxShadow = '0 0 8px rgba(0,184,148,0.7)'; }
                } else {
                    if (text) { text.innerText = 'Sin conexión'; text.style.color = '#e74c3c'; }
                    if (dot) { dot.style.background = '#e74c3c'; dot.style.boxShadow = '0 0 8px rgba(231,76,60,0.7)'; }
                }
            });

            // Lógica de protección de datos (el toast de red ya lo muestra el navbar)
            if (!isOnline) {
                if (btnProcesar) btnProcesar.disabled = true;
                if (btnGuardarProd) btnGuardarProd.disabled = true;
            } else {
                if (btnProcesar && typeof carrito !== 'undefined' && carrito.length > 0) btnProcesar.disabled = false;
                if (btnGuardarProd) btnGuardarProd.disabled = false;
            }
        }

        // Escuchar eventos del navegador en tiempo real
        window.addEventListener('online', checkNetworkStatus);
        window.addEventListener('offline', checkNetworkStatus);
        document.addEventListener('DOMContentLoaded', checkNetworkStatus);

        // Función de Rollback Criptográfico
        function revertirTransaccion(id) {
            Swal.fire({
                title: '¿Revertir Transacción?',
                text: "Esto NO borrará el registro. Generará un movimiento inverso automático para compensar el error manteniendo la integridad criptográfica.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#444',
                confirmButtonText: 'Sí, aplicar rollback',
                cancelButtonText: 'No',
                background: '#141414',
                color: '#fff',
                position: 'top-end',
                toast: true,
                showConfirmButton: true,
                timer: undefined,
                customClass: { popup: 'border border-secondary shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/auditoria/revertir/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarToast(data.message, 'bi bi-check-circle-fill');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            mostrarToast(data.message, 'bi bi-exclamation-triangle-fill');
                        }
                    });
                }
            });
        }
    </script>

    <script>
        // 1. FUNCIÓN PARA EL BOTÓN 'DETALLES'
        window.verDetallesProducto = function(nombre, categoria, codigo, stock, imagenUrl) {
            document.getElementById('lectura-nombre').innerText = nombre;
            document.getElementById('lectura-categoria').innerText = categoria;
            document.getElementById('lectura-codigo').innerText = codigo;
            document.getElementById('lectura-stock').innerText = stock;
            document.getElementById('lectura-imagen').src = imagenUrl;
            
            const modal = new bootstrap.Modal(document.getElementById('modalDetallesLectura'));
            modal.show();
        };

        // 2. FUNCIÓN PARA EL BOTÓN '+ PEDIR' (LOCALSTORAGE)
        let carritoRequisicion = JSON.parse(localStorage.getItem('oswa_carrito')) || [];

        document.addEventListener('DOMContentLoaded', () => {
            let contador = document.getElementById('contador-requisicion');
            if(contador) contador.innerText = carritoRequisicion.length;
        });

        // Persistir pestaña activa entre páginas
        const tabLinks = document.querySelectorAll('[data-bs-toggle="pill"]');
        tabLinks.forEach(tab => {
            tab.addEventListener('shown.bs.tab', () => {
                sessionStorage.setItem('catalogoActiveTab', tab.id);
            });
        });
        const savedTab = sessionStorage.getItem('catalogoActiveTab');
        if (savedTab) {
            const tabEl = document.getElementById(savedTab);
            if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }

        window.agregarARequisicion = function(id, nombre, stock) {
            let existe = carritoRequisicion.find(item => item.id === id);

            if (!existe) {
                carritoRequisicion.push({ id: id, nombre: nombre, cantidad: 1, stock: stock });
                localStorage.setItem('oswa_carrito', JSON.stringify(carritoRequisicion));

                let contador = document.getElementById('contador-requisicion');
                if(contador) {
                    contador.innerText = carritoRequisicion.length;
                    contador.style.transform = 'scale(1.4)';
                    setTimeout(() => contador.style.transform = 'scale(1)', 200);
                }

                mostrarToast(`¡Añadido: ${nombre}!`, 'bi bi-check-circle-fill');
            } else {
                mostrarToast('Ya está en tu lista', 'bi bi-check-circle-fill');
            }
        };
    </script>

</body>
</html>