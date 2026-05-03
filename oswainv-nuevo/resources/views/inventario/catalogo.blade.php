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
        [data-theme="light"] {
            --bg-dark: #121212; --bg-card: #1c1c1c; --bg-input: #2a2a2a;
            --border-color: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }
        
        .topbar { 
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%);
            backdrop-filter: blur(10px); border: none !important;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; overflow: visible !important;
        }
        .topbar::-webkit-scrollbar { display: none; }
        .topbar-left { display: flex; align-items: center; gap: 2rem; }
        .topbar-logo { white-space: nowrap; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .topbar-logo .logo-text { display: inline-block !important;
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
        }
        @keyframes rgbText { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        
        .logo-nav-unellez { height: 35px; filter: brightness(0) invert(1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; margin-right: 10px; }
        .logo-nav-unellez:hover { transform: scale(1.2); filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8)); }
        
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0; }
        .topbar-nav a:hover, .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: var(--accent-primary); border-radius: 1px; }
        
        .nav-dropdown { position: relative; }
        .nav-dropdown .dropdown-toggle { cursor: pointer; }
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #121212; border: 1px solid var(--n-border); border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.6); padding: 6px 0; z-index: 1000; display: none; }
        .nav-dropdown.show .dropdown-menu-custom { display: block; }
        .dropdown-item-custom { display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; }
        .dropdown-item-custom:hover { background: rgba(229,9,20,0.1); color: #fff; }
        
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .topbar-search { position: relative; }
        .topbar-search input { width: 220px; padding: 7px 14px 7px 34px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 6px; color: #ffffff; font-size: 0.85rem; transition: all 0.3s; }
        .topbar-search input::placeholder { color: rgba(255,255,255,0.6); }
        .topbar-search input:focus { outline: none; background: rgba(255,255,255,0.12); border-color: var(--accent-primary); width: 280px; box-shadow: 0 0 12px rgba(229,9,20,0.15); }
        .topbar-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.6); font-size: 0.85rem; }

        .status-indicator { display: flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); font-size: 0.72rem; font-weight: 500; transition: all 0.3s; white-space: nowrap; flex-shrink: 0; height: fit-content; }
        .status-indicator .status-dot { width: 7px; height: 7px; border-radius: 50%; transition: background 0.3s ease; }
        .status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 8px rgba(0,184,148,0.7); }
        .status-indicator.online .status-text { color: #ccc; }
        
        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }
        
        .btn-nuevo { background: linear-gradient(135deg, var(--accent-primary), #ff6b6b); color: white; padding: 10px 20px; border-radius: 4px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-nuevo:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(229,9,20,0.4); }

        .btn-requisicion-empleado { background-color: #E50914; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s; }
        .btn-requisicion-admin { background-color: transparent; color: #e5e5e5; border: 1px solid #444; border-radius: 8px; padding: 10px 20px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s; }
        .btn-requisicion-admin:hover { background-color: rgba(255,255,255,0.08); border-color: #E50914; color: #E50914; transform: translateY(-2px); }
        
        .theme-toggle { background: none; border: none; color: #b3b3b3; font-size: 1.1rem; cursor: pointer; padding: 6px; border-radius: 50%; transition: all 0.2s; }
        
        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 36px; height: 36px; border-radius: 4px; background: var(--accent-primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; }
        .dropdown-menu-netflix { position: absolute; top: 110%; right: 0; min-width: 260px; background: rgba(0,0,0,0.9); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 8px 40px rgba(0,0,0,0.7); padding: 8px 0; z-index: 1000; display: none; }
        .user-dropdown:hover .dropdown-menu-netflix, .dropdown-menu-netflix.show { display: block; }
        .dropdown-header { padding: 12px 16px; border-bottom: 1px solid #333; }
        .dropdown-header .dd-name { color: #fff; font-weight: 600; font-size: 0.95rem; }
        .dropdown-header .dd-email { color: #b3b3b3; font-size: 0.8rem; margin-top: 2px; }
        .dropdown-header .dd-role { color: var(--accent-primary); font-size: 0.75rem; font-weight: 600; margin-top: 2px; text-transform: uppercase; }
        .dd-item { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; border: none; background: none; color: #ccc; font-size: 0.9rem; text-align: left; cursor: pointer; transition: background 0.2s; }
        .dd-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .dd-divider { height: 1px; background: #333; margin: 6px 0; }
        .dd-logout { color: var(--accent-danger) !important; }
        
        .menu-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 767px) { .menu-toggle { display: block; } .topbar-nav { display: none; } }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .product-card { background: var(--bg-card) !important; border: 1px solid var(--n-border) !important; border-radius: 12px !important; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; position: relative; }
        .product-card:hover { transform: translateY(-5px) scale(1.02); border-color: var(--n-red) !important; box-shadow: 0 10px 20px rgba(0,0,0,0.5); z-index: 5; }
        .product-card.stock-critical { border-left: 4px solid var(--accent-danger); }
        .product-card.stock-low { border-left: 4px solid var(--accent-warning); }
        .product-card.stock-normal { border-left: 4px solid var(--accent-success); }
        
        .product-card-img { width: 100%; height: 180px; object-fit: cover; }
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
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; box-shadow: inset 0 0 5px rgba(0,0,0,0.5); }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #E50914, #ff6b6b); }

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
    </style>
</head>
<body data-theme="dark">
    
    <nav class="topbar" id="topbar">
        <div class="topbar-left d-flex align-items-center gap-3">
            <div class="topbar-logo d-flex align-items-center gap-2">
                <img src="{{ asset('img/logo-unellez.png') }}" class="logo-nav-unellez" alt="Logo"> 
                <span class="logo-text">OSWA Inv</span>
            </div>
            <div class="status-indicator online d-none d-md-flex ms-2 me-4">
                <span class="status-dot"></span>
                <span class="status-text text-white" style="font-size: 0.75rem;">En línea</span>
            </div>
        </div>

        <div class="topbar-nav" id="topbarNav">
            <a href="{{ route('inventario') }}" class="{{ request()->routeIs('inventario') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('catalogo') }}" class="{{ request()->routeIs('catalogo') ? 'active' : '' }}">Catálogo</a>
            <a href="{{ route('proveedores') }}" class="{{ request()->routeIs('proveedores') ? 'active' : '' }}">Proveedores</a>
            
            <div class="nav-dropdown">
                <a href="#" class="dropdown-toggle" onclick="event.preventDefault(); this.parentElement.classList.toggle('show')">Reportes</a>
                <div class="dropdown-menu-custom">
                    <a href="{{ route('exportar.pdf') }}" target="_blank" class="dropdown-item-custom">
                        <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Inventario (PDF)
                    </a>
                </div>
            </div>
        </div>

        <div class="topbar-right d-none d-md-flex align-items-center gap-3">
            <button class="theme-toggle" onclick="toggleTheme()" title="Modo claro/oscuro"><i class="bi bi-moon-fill"></i></button>
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="topbarSearchInput" placeholder="Buscar productos...">
            </div>
            <div class="user-dropdown" id="userDropdown">
                <div class="d-flex align-items-center gap-2" onclick="toggleUserDropdown()">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <i class="bi bi-caret-down-fill" id="dropdownArrow" style="color:#888;font-size:0.7rem;transition:transform 0.2s;"></i>
                </div>
                <div class="dropdown-menu-netflix" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dd-name">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                        <div class="dd-email">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        <div class="dd-role">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                    </div>
                    <button class="dd-item" onclick="mostrarMiCuenta()"><i class="bi bi-person-circle"></i> Mi Cuenta</button>
                    <button class="dd-item" onclick="mostrarAtajos()"><i class="bi bi-keyboard"></i> Atajos de Teclado</button>
                    @if(auth()->user()?->rol === 'admin')
                    <a href="{{ route('usuarios.index') }}" class="dd-item"><i class="bi bi-people"></i> Administrar Usuarios</a>
                    @endif
                    <div class="dd-divider"></div>
                    <button type="button" class="dd-item text-white" onclick="abrirSelectorPerfiles(event)" style="background: none; border: none; cursor: pointer; width: 100%; text-align: left;">
                        <i class="bi bi-arrow-left-right text-danger"></i> Cambiar de Cuenta
                    </button>
                    <div class="dd-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 w-100">
                        @csrf
                        <button type="submit" class="dd-item dd-logout w-100 text-start" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="bi bi-box-arrow-right"></i> Cambiar Usuario / Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <button class="menu-toggle d-md-none" onclick="toggleSidebar()" style="background: transparent; border: none; color: white; font-size: 2rem; padding: 0;"><i class="bi bi-list"></i></button>
    </nav>
    
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
                <a href="{{ route('requisiciones.crear') }}" class="btn-requisicion-empleado">
                    <i class="bi bi-cart-plus"></i> Hacer Requisición
                </a>
                @endif

                @if(isset($esAdmin) && $esAdmin || (Auth::check() && Auth::user()->rol === 'admin'))
                <button type="button" class="btn-requisicion-admin" data-bs-toggle="modal" data-bs-target="#modalRequisiciones">
                    <i class="bi bi-inbox"></i> Ver Solicitudes Pendientes
                </button>
                <button class="btn-nuevo" onclick="abrirModalNuevo()">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Producto
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
            @if(Auth::check() && Auth::user()->rol === 'admin')
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
                </div>

                <div class="products-grid" id="productsGrid">
                    @forelse($productos as $producto)
                    <div class="product-card {{ $producto->stock <= 2 ? 'stock-critical' : ($producto->stock <= 5 ? 'stock-low' : 'stock-normal') }}">
                        @if($producto->imagen)
                            @if(filter_var($producto->imagen, FILTER_VALIDATE_URL))
                                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="product-card-img">
                            @else
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="product-card-img">
                            @endif
                        @else
                            <div class="product-card-img-placeholder"><i class="bi bi-image"></i></div>
                        @endif
                        
                        <div class="product-card-info">
                            <div class="product-card-title">{{ $producto->nombre }}</div>
                            <div class="product-card-meta">{{ $producto->marca ?? 'Sin marca' }} • {{ $producto->categoria }}</div>
                            <div class="product-card-code"><i class="bi bi-upc-scan"></i> {{ $producto->codigo }}</div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                @if(auth()->user()->rol == 'admin')
                                    <div class="fw-bold" style="color: #00b894; font-size: 1.1rem;">
                                        ${{ number_format($producto->precio, 2) }}
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
                        
                        <div class="mt-3 pt-3 px-3 pb-3" style="border-top: 1px solid #2a2a2a;">
                            @if(Auth::user()->rol === 'admin')
                            <div class="d-flex justify-content-between align-items-center mb-3 px-3 py-1" style="background-color: #111; border-radius: 8px; border: 1px solid #333;">
                                <button type="button" class="btn btn-sm text-white fs-5 px-2 border-0" onclick="actualizarStockRapido({{ $producto->id }}, -1)">-</button>
                                <input type="number" id="stock-input-{{ $producto->id }}" value="{{ $producto->stock }}" class="fw-bold fs-5 text-white text-center border-0 bg-transparent oswa-stock-input" style="width: 60px; outline: none;" onchange="guardarStockManual({{ $producto->id }})">
                                <button type="button" class="btn btn-sm text-white fs-5 px-2 border-0" onclick="actualizarStockRapido({{ $producto->id }}, 1)">+</button>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm w-50 text-dark fw-bold" style="background-color: #ffc107; border: none; border-radius: 6px;" data-producto="{{ $producto->toJson() }}" onclick="editarProducto(JSON.parse(this.getAttribute('data-producto')))">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>
                                <button type="button" class="btn btn-sm w-50 text-white fw-bold" style="background-color: #E50914; border: none; border-radius: 6px;" onclick="confirmarEliminacion({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                                <form id="form-eliminar-{{ $producto->id }}" action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                            @else
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm w-50 btn-outline-info" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#modalDetalles{{ $producto->id }}">
                                        <i class="bi bi-eye"></i> Detalles
                                    </button>
                                    <a href="{{ route('requisiciones.crear') }}?producto_id={{ $producto->id }}" class="btn btn-sm w-50 btn-outline-success" style="border-radius: 6px;">
                                        <i class="bi bi-cart-plus"></i> Pedir
                                    </a>
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
            </div>

            @if(Auth::check() && Auth::user()->rol === 'admin')
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
                                                {{ $audit->usuario->name ?? 'Sistema' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-secondary" style="font-family: monospace; font-size: 0.85rem; letter-spacing: 0.5px;" title="{{ $audit->firma_hash }}">
                                                {{ $audit->firma_hash ? substr($audit->firma_hash, 0, 15) . '...' : 'SIN FIRMA' }}
                                            </span>
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

    <footer class="professional-footer">
        <div class="mb-1">&copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.</div>
        <div>Desarrollado con <i class="bi bi-code-slash text-primary"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span></div>
        <div class="mt-2 d-flex align-items-center justify-content-center" style="font-size: 0.75rem; opacity: 0.8;">
            <span>Ingeniería en Informática — V Semestre |</span>
            <strong style="letter-spacing: 0.5px; margin-left: 8px;">UNELLEZ</strong>
        </div>
    </footer>

    <!-- MODAL: NUEVO / EDITAR PRODUCTO -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold" id="modalProductoTitle"><i class="bi bi-box-seam text-danger me-2"></i> Gestión de Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formProducto" enctype="multipart/form-data">
                        <input type="hidden" id="prodId" name="id">
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
                                        <label class="form-label text-secondary">Precio ($)</label>
                                        <input type="number" step="0.01" id="prodPrecio" name="precio" class="form-control bg-dark text-white border-secondary" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Stock Inicial</label>
                                        <input type="number" id="prodStock" name="stock" class="form-control bg-dark text-white border-secondary" required>
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

            let mensajeExito = {!! json_encode(session('success')) !!};
            if (mensajeExito && window.Toast) window.Toast.fire({ icon: 'success', title: mensajeExito, iconColor: '#25D366' });

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

                    fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(async response => {
                        if (!response.ok) throw await response.json();
                        return response.json();
                    })
                    .then(data => {
                        if(data.success) {
                            Swal.fire('¡Éxito!', 'Producto guardado correctamente', 'success').then(() => window.location.reload());
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        let msg = error.message || 'Revisa los campos e intenta de nuevo.';
                        Swal.fire('Error', msg, 'error');
                    });
                });
            }

            const modalReq = document.getElementById('modalRequisiciones');
            if(modalReq) modalReq.addEventListener('show.bs.modal', mostrarRequisicionesPendientes);
        });

        function actualizarStockRapido(id, cambio) {
            const input = document.getElementById('stock-input-' + id);
            if (!input) return;
            let nuevoValor = parseInt(input.value) + cambio;
            if (nuevoValor < 0) return; 
            input.value = nuevoValor;
            animarStock(input, cambio);
            mandarStockAlBackend(id, nuevoValor);
        }

        function guardarStockManual(id) {
            const input = document.getElementById('stock-input-' + id);
            if (!input) return;
            let nuevoValor = parseInt(input.value);
            if (isNaN(nuevoValor) || nuevoValor < 0) { input.value = 0; nuevoValor = 0; }
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
            .then(res => res.json())
            .then(data => {
                if (data.success && window.Toast) window.Toast.fire({ icon: 'success', title: 'Inventario actualizado', iconColor: '#25D366' });
            });
        }

        function abrirModalNuevo() {
            document.getElementById('formProducto').reset();
            document.getElementById('prodId').value = '';
            document.getElementById('modalProductoTitle').innerHTML = '<i class="bi bi-box-seam text-danger me-2"></i> Nuevo Producto';
            
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
            document.getElementById('prodStock').value = producto.stock || producto.cantidad;
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
            document.getElementById('modalProductoTitle').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i> Editar Producto';
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
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
                title: '¿Eliminar Producto?', html: 'Vas a borrar <b>' + nombre + '</b>.<br>¡No hay vuelta atrás!',
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#E50914', cancelButtonColor: '#444',
                confirmButtonText: 'Sí, eliminar', background: '#141414', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('form-eliminar-' + id).submit();
            });
        }

        function mostrarRequisicionesPendientes() {
            const reqs = @json($requisicionesPendientes ?? []);
            const body = document.getElementById('modalRequisicionesBody');
            if (!reqs || reqs.length === 0) {
                body.innerHTML = '<div class="text-center py-4"><i class="bi bi-inbox" style="font-size:3rem;color:#555;"></i><p class="mt-3 text-muted">No hay solicitudes pendientes en este momento.</p></div>';
                return;
            }
            let html = '<div style="max-height:450px; overflow-y:auto;">';
            reqs.forEach(r => {
                html += `<div style="padding:14px; border-bottom:1px solid #2a2a2a; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <div><strong class="text-white">${r.user ? r.user.name : 'Usuario'}</strong> solicita 
                    <b class="text-warning">${r.producto ? r.producto.nombre : 'Producto'}</b> × <span class="fw-bold">${r.cantidad}</span>
                    <br><small style="color:#888;">${r.created_at}</small></div>
                    <div style="display:flex; gap:8px; flex-shrink:0;">
                    <button onclick="procesarRequisicion(${r.id}, 'aprobar')" class="btn btn-sm btn-success fw-bold"><i class="bi bi-check-lg"></i> Aprobar</button>
                    <button onclick="procesarRequisicion(${r.id}, 'rechazar')" class="btn btn-sm btn-danger fw-bold"><i class="bi bi-x-lg"></i> Rechazar</button>
                    </div></div>`;
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
                    Swal.fire('¡Listo!', data.message, 'success').then(() => location.reload());
                } else { Swal.fire('Error', data.message, 'error'); }
            } catch (error) { Swal.fire('Error', 'Problema de conexión.', 'error'); }
        }

        function exportarTablaCSV(nombreArchivo) {
            const tabla = document.getElementById('tablaAuditoria');
            if (!tabla) { Swal.fire('Error', 'No hay datos para exportar', 'warning'); return; }
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

        function onScanSuccess(decodedText, decodedResult) {
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
            
            let modalEl = document.getElementById('scannerModal');
            let modalObj = bootstrap.Modal.getInstance(modalEl);
            if(modalObj) modalObj.hide();
            
            const searchInput = document.getElementById('topbarSearchInput');
            if(searchInput) {
                searchInput.value = decodedText;
                searchInput.focus();
                searchInput.dispatchEvent(new Event('input', { bubbles: true })); 
            }
            
            if (window.Toast) {
                window.Toast.fire({ icon: 'success', title: 'Código detectado: ' + decodedText });
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

    @include('partials.perfiles')
</body>
</html>