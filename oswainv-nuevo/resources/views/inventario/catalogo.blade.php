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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root {
            --bg-dark: #141414; --bg-card: #181818; --bg-input: #333333;
            --border-color: #2b2b2b; --text-primary: #ffffff; --text-secondary: #b3b3b3;
            --accent-primary: #E50914; --accent-success: #00b894;
            --accent-danger: #e74c3c; --accent-warning: #fdcb6e; --accent-info: #0984e3;
            --topbar-height: 68px;
        }
        [data-theme="light"] {
            --bg-dark: #f5f6f8; --bg-card: #ffffff; --bg-input: #e9ecef;
            --border-color: #dee2e6; --text-primary: #212529; --text-secondary: #6c757d;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-primary); margin: 0; }
        
        .topbar { 
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
            background: linear-gradient(180deg, rgba(0,0,0,0.95) 0%, rgba(20,20,20,0.98) 100%);
            border-bottom: 1px solid var(--border-color); 
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; backdrop-filter: blur(10px);
            overflow: visible !important;
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
        
        .logo-nav-unellez {
            height: 35px;
            filter: brightness(0) invert(1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            margin-right: 10px;
        }
        .logo-nav-unellez:hover {
            transform: scale(1.2);
            filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
        }
        
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0; }
        .topbar-nav a:hover, .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: var(--accent-primary); border-radius: 1px; }
        
        .nav-dropdown { position: relative; }
        .nav-dropdown .dropdown-toggle { cursor: pointer; }
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #1a1a1a; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.6); padding: 6px 0; z-index: 1000; display: none; }
        .nav-dropdown.show .dropdown-menu-custom { display: block; }
        .dropdown-item-custom { display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; }
        .dropdown-item-custom:hover { background: rgba(229,9,20,0.1); color: #fff; }
        .dropdown-item-custom.text-muted { color: #666; cursor: default; }
        .dropdown-item-custom.text-muted:hover { background: transparent; color: #666; }
        
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
        .status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 8px rgba(231,76,60,0.7); }
        .status-indicator.offline .status-text { color: #e74c3c; }
        
        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }
        
        .btn-nuevo { background: linear-gradient(135deg, var(--accent-primary), #ff6b6b); color: white; padding: 10px 20px; border-radius: 4px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-nuevo:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(229,9,20,0.4); }
        
        .theme-toggle { background: none; border: none; color: #b3b3b3; font-size: 1.1rem; cursor: pointer; padding: 6px; border-radius: 50%; transition: all 0.2s; }
        .theme-toggle:hover { background: rgba(255,255,255,0.1); color: #fff; }
        
        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 36px; height: 36px; border-radius: 4px; background: var(--accent-primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; }
        .dropdown-menu-netflix { position: absolute; top: 110%; right: 0; min-width: 260px; background: rgba(0,0,0,0.9); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 8px 40px rgba(0,0,0,0.7); padding: 8px 0; z-index: 1000; display: none; }
        .user-dropdown:hover .dropdown-menu-netflix, .dropdown-menu-netflix.show { display: block; }
        .dropdown-header { padding: 12px 16px; border-bottom: 1px solid #333; }
        .dropdown-header .dd-name { color: #fff; font-weight: 600; font-size: 0.95rem; }
        .dropdown-header .dd-email { color: #b3b3b3; font-size: 0.8rem; margin-top: 2px; }
        .dropdown-header .dd-role { color: var(--accent-primary); font-size: 0.75rem; font-weight: 600; margin-top: 2px; text-transform: uppercase; letter-spacing: 1px; }
        .dd-item { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; border: none; background: none; color: #ccc; font-size: 0.9rem; text-align: left; cursor: pointer; transition: background 0.2s; }
        .dd-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .dd-divider { height: 1px; background: #333; margin: 6px 0; }
        .dd-logout { color: var(--accent-danger) !important; }
        .dd-logout:hover { background: rgba(229,9,20,0.1) !important; }
        
        @media (max-width: 767px) {
            .topbar-nav { display: none; flex-direction: column; position: absolute; top: var(--topbar-height); left: 0; right: 0; background: rgba(0,0,0,0.98); padding: 1rem 4%; border-bottom: 1px solid var(--border-color); }
            .topbar-nav.show { display: flex; }
            .topbar-search { display: none; }
        }
        
        .menu-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 767px) { .menu-toggle { display: block; } }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        
        .product-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .product-card.stock-critical { border-left: 4px solid var(--accent-danger); }
        .product-card.stock-low { border-left: 4px solid var(--accent-warning); }
        .product-card.stock-normal { border-left: 4px solid var(--accent-success); }
        
        .product-card-img { width: 100%; height: 180px; object-fit: cover; }
        .product-card-img-placeholder { height: 180px; background: #222; display: flex; align-items: center; justify-content: center; color: #555; font-size: 3rem; }
        
        .product-card-info { padding: 1rem 1rem 0.5rem; }
        .product-card-title { font-weight: 600; font-size: 1.05rem; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-card-meta { color: var(--text-secondary); font-size: 0.8rem; }
        .product-card-code { color: #777; font-size: 0.75rem; font-family: monospace; margin-top: 4px; }
        
        .product-card-controls { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem 1rem; }
        
        .stock-pill { display: flex; align-items: center; background: #222; border-radius: 4px; overflow: hidden; }
        .stock-pill-btn { background: none; border: none; color: #fff; padding: 6px 10px; cursor: pointer; font-size: 0.8rem; transition: background 0.2s; }
        .stock-pill-btn:hover { background: rgba(229,9,20,0.2); color: var(--accent-primary); }
        .stock-pill-value { width: 40px; text-align: center; background: transparent; border: none; color: #fff; font-weight: 600; font-size: 0.9rem; }
        .stock-pill-value:focus { outline: none; }
        
        .product-card-actions { display: flex; gap: 6px; }
        .card-action-btn { width: 32px; height: 32px; border-radius: 4px; border: none; background: rgba(255,255,255,0.08); color: #b3b3b3; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.2s; }
        .card-action-btn:hover { background: rgba(229,9,20,0.2); color: var(--accent-primary); }
        
        .professional-footer { text-align: center; padding: 1.5rem 4%; margin-top: 2rem; border-top: 1px solid var(--border-color); color: var(--text-secondary); font-size: 0.85rem; }
        .professional-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .professional-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        .scanner-fab { position: fixed; bottom: 30px; right: 30px; width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--accent-primary), #B20710); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; text-decoration: none; box-shadow: 0 4px 20px rgba(229,9,20,0.4); transition: all 0.3s; z-index: 900; }
        .scanner-fab:hover { transform: scale(1.1); box-shadow: 0 6px 25px rgba(229,9,20,0.6); }

        .modal-content { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); }
        .modal-header { border-bottom: 1px solid var(--border-color); }
        .modal-footer { border-top: 1px solid var(--border-color); }
        .form-control { background: var(--bg-input); border: 1px solid var(--border-color); color: var(--text-primary); }
        .form-control:focus { background: #444; border-color: var(--accent-primary); color: #fff; box-shadow: 0 0 0 0.25rem rgba(229,9,20,0.25); }
        .form-label { color: var(--text-secondary); }
        .form-select { background-color: var(--bg-input); border: 1px solid var(--border-color); color: var(--text-primary); }
        .form-select:focus { background-color: #444; border-color: var(--accent-primary); color: #fff; }
        
        .bot-fab { position: fixed; bottom: 100px; right: 30px; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #0984e3, #00b894); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: none; cursor: pointer; box-shadow: 0 4px 20px rgba(9,132,227,0.4); transition: all 0.3s; z-index: 900; }
        .bot-fab:hover { transform: scale(1.1); }
        .floating-bot-window { position: fixed; bottom: 170px; right: 30px; width: 350px; height: 450px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); z-index: 901; display: none; flex-direction: column; overflow: hidden; }
        .floating-bot-window.show { display: flex; }
        .bot-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: linear-gradient(90deg, #0984e3, #00b894); color: white; font-weight: 600; }
        .bot-header button { background: none; border: none; color: white; cursor: pointer; }
        .bot-chat-history { flex: 1; padding: 12px; overflow-y: auto; }
        .chat-bubble { padding: 8px 12px; border-radius: 12px; margin-bottom: 8px; font-size: 0.9rem; max-width: 80%; }
        .bot-bubble { background: #222; color: #ddd; }
        .user-bubble { background: var(--accent-primary); color: white; margin-left: auto; }
        .bot-input-area { display: flex; padding: 8px; border-top: 1px solid var(--border-color); }
        .bot-input-area input { flex: 1; background: #222; border: none; padding: 8px 12px; border-radius: 4px; color: white; }
        .bot-input-area button { background: var(--accent-primary); border: none; padding: 0 12px; color: white; border-radius: 4px; margin-left: 6px; cursor: pointer; }

        @media (max-width: 767px) { .bot-fab, .floating-bot-window { display: none !important; } }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; box-shadow: inset 0 0 5px rgba(0,0,0,0.5); }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #E50914, #ff6b6b); }

        /* Estilos para las pestañas del Catálogo */
        .nav-pills .nav-link { color: #a0a0a0; background-color: transparent; border: 1px solid #333; transition: all 0.3s ease; }
        .nav-pills .nav-link:hover { color: #fff; background-color: rgba(255, 255, 255, 0.05); }
        .nav-pills .nav-link.active { color: #fff; background-color: #E50914 !important; border-color: #E50914; box-shadow: 0 0 15px rgba(229, 9, 20, 0.4); }

        /* Animación flotante para botones de Stock */
        .floating-number {
            position: absolute;
            font-weight: bold;
            font-size: 1.2rem;
            pointer-events: none;
            animation: floatUp 0.8s ease-out forwards;
            z-index: 1000;
        }
        .float-plus { color: #00b894; }
        .float-minus { color: #d63031; }
        @keyframes floatUp {
            0% { opacity: 1; transform: translateY(0) scale(1); }
            100% { opacity: 0; transform: translateY(-30px) scale(1.5); }
        }
        @keyframes pulseRed { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }
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
                <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
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

            <div class="mobile-user-section d-md-none mt-auto pt-4 border-top border-secondary">
                <div class="status-indicator online mb-3" style="width: fit-content;">
                    <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                    <span class="status-text text-white" style="font-size: 0.8rem;">En línea</span>
                </div>
                <div class="user-info mb-3 d-flex align-items-center gap-2">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    <div>
                        <div class="text-white fw-bold" style="font-size: 0.9rem;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm w-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Salir del Sistema
                </a>
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
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
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
                    <button class="dd-item" onclick="cambiarCuenta(event)"><i class="bi bi-arrow-left-right"></i> Cambiar de Cuenta</button>
                    <div class="dd-divider"></div>
                    <button class="dd-item dd-logout" onclick="document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
                </div>
            </div>
        </div>

        <button class="menu-toggle d-md-none" onclick="toggleSidebar()" style="background: transparent; border: none; color: white; font-size: 2rem; padding: 0;">
            <i class="bi bi-list"></i>
        </button>
    </nav>
    
    <main class="main-content">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-50">
            <div class="d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-grid-fill fs-4"></i>
                </div>
                <h2 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">Catálogo General</h2>
            </div>
            
            @if($esAdmin)
            <button class="btn-nuevo" data-bs-toggle="modal" data-bs-target="#modalProducto" onclick="document.getElementById('prodId').value='';document.getElementById('prodNombre').value='';document.getElementById('prodPrecio').value='';document.getElementById('prodStock').value='';document.getElementById('prodCategoria').value='';document.getElementById('prod-vencimiento').value='';document.getElementById('prod-proveedor').value='';document.getElementById('modalProductoTitle').innerHTML='<i class=\'bi bi-box-seam text-danger me-2\'></i> Nuevo Producto'">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Producto
            </button>
            @endif
        </div>

        <!-- SISTEMA DE PESTAÑAS (TABS) -->
        <ul class="nav nav-pills mb-4" id="catalogoTabs" role="tablist" style="gap: 10px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-2" id="productos-tab" data-bs-toggle="pill" data-bs-target="#tab-productos" type="button" role="tab" style="border-radius: 8px;">
                    <i class="bi bi-grid-fill me-2"></i> Productos
                </button>
            </li>
            @if(auth()->user()->rol == 'admin')
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-2" id="auditoria-tab" data-bs-toggle="pill" data-bs-target="#tab-auditoria" type="button" role="tab" style="border-radius: 8px;">
                    <i class="bi bi-clock-history me-2"></i> Historial de Movimientos
                </button>
            </li>
            @endif
        </ul>

        <!-- CONTENIDO DE LAS PESTAÑAS -->
        <div class="tab-content" id="catalogoTabsContent">
            
            <!-- PESTAÑA 1: CATÁLOGO DE PRODUCTOS -->
            <div class="tab-pane fade show active" id="tab-productos" role="tabpanel" tabindex="0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span style="color:var(--text-secondary);font-weight:500;">{{ $productos->count() }} productos</span>
                </div>

                <div class="products-grid" id="productsGrid">
                    @forelse($productos as $producto)
                    <div class="product-card {{ $producto->stock <= 2 ? 'stock-critical' : ($producto->stock <= 5 ? 'stock-low' : 'stock-normal') }}" data-producto-id="{{ $producto->id }}" data-stock="{{ $producto->stock }}" data-nombre="{{ $producto->nombre }}" data-codigo="{{ $producto->codigo }}">
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
                                        
                                        $color = '';
                                        $texto = '';
                                        $icono = '';

                                        if ($dias < 0) {
                                            $color = '#ff4757'; 
                                            $texto = '¡Vencido!';
                                            $icono = 'bi-exclamation-octagon-fill';
                                        } elseif ($dias <= 30) {
                                            $color = '#ffa502'; 
                                            $texto = 'Vence en ' . floor($dias) . ' d';
                                            $icono = 'bi-clock-history';
                                        } else {
                                            $color = '#7bed9f'; 
                                            $texto = $fechaVenc->format('d/m/y');
                                            $icono = 'bi-calendar2-check';
                                        }
                                    @endphp
                                    
                                    <div class="fw-semibold px-2 py-1 rounded" style="color: {{ $color }}; background: rgba(0,0,0,0.2); font-size: 0.75rem; letter-spacing: 0.5px; border: 1px solid {{ $color }}40;">
                                        <i class="bi {{ $icono }} me-1"></i> {{ $texto }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="product-card-controls">
                            <div class="stock-pill">
                                <button class="stock-pill-btn stock-pill-minus" onclick="actualizarStock({{ $producto->id }}, 'restar', this)"><i class="bi bi-dash"></i></button>
                                <span id="stock-{{ $producto->id }}" class="mx-2 fw-bold text-white">{{ $producto->stock }}</span>
                                <button class="stock-pill-btn stock-pill-plus" onclick="actualizarStock({{ $producto->id }}, 'sumar', this)"><i class="bi bi-plus"></i></button>
                            </div>
                            @if($esAdmin)
                            <div class="product-card-actions">
                                <button class="card-action-btn card-action-btn-transfer" title="Transferir" onclick="abrirTransferencia({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->stock }})"><i class="bi bi-truck"></i></button>
                                <button class="card-action-btn card-action-btn-order" title="Orden de Compra" onclick="generarOrden({{ $producto->id }}, '{{ $producto->nombre }}')"><i class="bi bi-cart"></i></button>
                                <button class="card-action-btn card-action-btn-edit" title="Editar" onclick="editarProducto({{ json_encode($producto) }})"><i class="bi bi-pencil"></i></button>
                                <button class="card-action-btn card-action-btn-delete" title="Eliminar" onclick="eliminarProducto({{ $producto->id }})"><i class="bi bi-trash"></i></button>
                            </div>
                            @else
                            <div class="product-card-actions">
                                <button class="card-action-btn" style="background: rgba(0,184,148,0.15); color: #00b894;" title="Pedir Material" onclick="abrirRequisicion({{ $producto->id }}, '{{ $producto->nombre }}')"><i class="bi bi-hand-index"></i></button>
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

            @if(auth()->user()->rol == 'admin')
            <!-- PESTAÑA 2: AUDITORÍA -->
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
                                            <span class="badge border border-secondary text-secondary px-3 py-2 d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.05); border-radius: 6px;">
                                                <i class="bi bi-clock-history"></i> Antiguo
                                            </span>
                                        @elseif($audit->esValida())
                                            <span class="badge border border-success text-success px-3 py-2 d-inline-flex align-items-center gap-1" style="background: rgba(25, 135, 84, 0.05); border-radius: 6px; box-shadow: 0 0 10px rgba(25,135,84,0.2);">
                                                <i class="bi bi-shield-check"></i> Válida
                                            </span>
                                        @else
                                            <span class="badge border border-danger text-danger px-3 py-2 d-inline-flex align-items-center gap-1" style="background: rgba(220, 53, 69, 0.1); border-radius: 6px; box-shadow: 0 0 10px rgba(220,53,69,0.4); animation: pulseRed 2s infinite;">
                                                <i class="bi bi-shield-x"></i> ALTERADA
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
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
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 18px; margin-left: 8px; margin-right: 4px; filter: brightness(0) invert(1);">
            <strong style="letter-spacing: 0.5px;">UNELLEZ</strong>
        </div>
    </footer>

    <a href="{{ route('escaner') }}" class="scanner-fab" title="Escáner (Alt+E)"><i class="bi bi-upc-scan"></i></a>

    <!-- MODAL: NUEVO / EDITAR PRODUCTO -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold" id="modalProductoTitle"><i class="bi bi-box-seam text-danger me-2"></i> Gestión de Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProducto">
                        <input type="hidden" id="prodId">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Nombre del Producto</label>
                            <input type="text" id="prodNombre" name="nombre" class="form-control bg-dark text-white border-secondary" placeholder="Ej. Margarina Mavesa 500g" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label text-secondary">Precio ($)</label>
                                <input type="number" step="0.01" id="prodPrecio" name="precio" class="form-control bg-dark text-white border-secondary" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-secondary">Stock</label>
                                <input type="number" id="prodStock" name="stock" class="form-control bg-dark text-white border-secondary" placeholder="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary">Categoría</label>
                            <input type="text" id="prodCategoria" name="categoria" class="form-control bg-dark text-white border-secondary" placeholder="General">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary">Fecha de Vencimiento (Opcional)</label>
                            <input type="date" id="prod-vencimiento" name="fecha_vencimiento" class="form-control bg-dark text-white border-secondary">
                        </div>
                        <div class="mb-3 border-top border-secondary border-opacity-25 pt-3 mt-3">
                            <label class="form-label text-secondary"><i class="bi bi-truck text-warning me-1"></i> Proveedor Asociado</label>
                            <select class="form-select bg-dark text-white border-secondary shadow-none" id="prod-proveedor" name="proveedor_id">
                                <option value="">Ninguno (Producto Independiente)</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->nombre }} ({{ $prov->rif }})</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarProducto" class="btn btn-danger">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: TRANSFERENCIA Y RUTAS -->
    <div class="modal fade" id="modalTransferir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-truck text-warning me-2"></i> Logística de Transferencia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary mb-2"><i class="bi bi-geo-alt-fill text-danger"></i> Ruta de transferencia (Origen: <strong>Barinas</strong>)</p>
                    <div id="mapaTransferencia" style="height: 250px; width: 100%; background: #1a1a1a; border-radius: 8px; border: 1px solid #333;" class="mb-3 d-flex justify-content-center align-items-center text-muted">
                        Cargando coordenadas del sistema...
                    </div>
                    <input type="hidden" id="transProductoId">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Cantidad a transferir</label>
                            <input type="number" id="transCantidad" class="form-control bg-dark text-white border-secondary" placeholder="Ej. 50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Destino</label>
                            <select id="transDestino" class="form-select bg-dark text-white border-secondary">
                                <option value="">Seleccionar...</option>
                                <option value="Caracas">Caracas</option>
                                <option value="Maracaibo">Maracaibo</option>
                                <option value="Valencia">Valencia</option>
                                <option value="Barquisimeto">Barquisimeto</option>
                                <option value="San Cristóbal">San Cristóbal</option>
                                <option value="Mérida">Mérida</option>
                                <option value="Guanare">Guanare</option>
                                <option value="Trujillo">Trujillo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnProcesarTransferencia" class="btn btn-warning text-dark fw-bold"><i class="bi bi-send-check"></i> Procesar Transferencia</button>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function toggleSidebar() { document.getElementById('topbarNav').classList.toggle('show'); }
        function toggleUserDropdown() { document.getElementById('userDropdownMenu').classList.toggle('show'); }
        function toggleTheme() {
            const nuevo = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', nuevo);
            localStorage.setItem('theme', nuevo);
            const icon = document.querySelector('.theme-toggle i');
            if (icon) icon.className = nuevo === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', saved);
            const icon = document.querySelector('.theme-toggle i');
            if (icon) icon.className = saved === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';

            document.getElementById('topbarSearchInput')?.addEventListener('input', function() {
                const texto = this.value.toLowerCase();
                document.querySelectorAll('.product-card').forEach(card => {
                    const nombre = (card.dataset.nombre || '').toLowerCase();
                    const codigo = (card.dataset.codigo || '').toLowerCase();
                    card.style.display = (nombre.includes(texto) || codigo.includes(texto)) ? '' : 'none';
                });
            });

            // MAPA DE TRANSFERENCIA
            const modalTransferir = document.getElementById('modalTransferir');
            let mapaInicializado = false;
            let leafletMap = null;
            
            if(modalTransferir) {
                modalTransferir.addEventListener('shown.bs.modal', function () {
                    const mapContainer = document.getElementById('mapaTransferencia');
                    if (!mapaInicializado) {
                        mapContainer.classList.remove('d-flex', 'justify-content-center', 'align-items-center', 'text-muted');
                        mapContainer.innerHTML = '';
                        leafletMap = L.map(mapContainer).setView([8.6224, -70.2075], 7);
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19
                        }).addTo(leafletMap);
                        
                        const origenIcon = L.divIcon({ html: '<i class="bi bi-geo-alt-fill text-danger" style="font-size:2rem;"></i>', className: '', iconSize: [30, 30] });
                        L.marker([8.6224, -70.2075], { icon: origenIcon }).addTo(leafletMap).bindPopup('Origen: Barinas');
                        mapaInicializado = true;
                    } else {
                        setTimeout(() => leafletMap.invalidateSize(), 100);
                    }
                });
            }

            // 3. GUARDAR PRODUCTO (Crear/Editar)
            document.getElementById('btnGuardarProducto').addEventListener('click', function() {
                const form = document.getElementById('formProducto');
                const formData = new FormData(form);
                const id = document.getElementById('prodId').value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                let url = '{{ route('guardar.producto') }}';
                if (id) {
                    url = `/productos/${id}/actualizar`;
                    formData.append('_method', 'PUT');
                }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw errorData;
                    }
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        Swal.fire('¡Éxito!', 'Producto guardado correctamente', 'success')
                            .then(() => window.location.reload());
                    }
                })
                .catch(error => {
                    console.error("Error capturado:", error);
                    let errorMsg = 'Error interno del servidor.';
                    if (error.errors) {
                        errorMsg = Object.values(error.errors)[0][0];
                    } else if (error.message) {
                        errorMsg = error.message;
                    }
                    Swal.fire('Error', errorMsg, 'error');
                });
            });

            // 4. PROCESAR TRANSFERENCIA
            document.getElementById('btnProcesarTransferencia').addEventListener('click', async function() {
                const id = document.getElementById('transProductoId').value;
                const cantidad = document.getElementById('transCantidad').value;
                const destino = document.getElementById('transDestino').value;
                if(!cantidad || !destino) { Swal.fire('Campos incompletos', 'Ingresa cantidad y destino', 'warning'); return; }

                try {
                    const response = await fetch('{{ route('transferir.producto') }}', {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ producto_id: id, cantidad, sucursal: destino })
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({ title: 'Transferencia procesada', icon: 'success', html: `<a href="${data.pdf_url}" target="_blank" class="btn btn-danger mt-3">Descargar PDF</a>` });
                        bootstrap.Modal.getInstance(document.getElementById('modalTransferir')).hide();
                        setTimeout(() => location.reload(), 2000);
                    } else { Swal.fire('Error', data.message, 'error'); }
                } catch (e) { Swal.fire('Error', 'Error de conexión', 'error'); }
            });
        });

        function editarProducto(producto) {
            document.getElementById('prodId').value = producto.id;
            document.getElementById('prodNombre').value = producto.nombre;
            document.getElementById('prodPrecio').value = producto.precio;
            document.getElementById('prodStock').value = producto.stock;
            document.getElementById('prodCategoria').value = producto.categoria || 'General';
            document.getElementById('prod-vencimiento').value = producto.fecha_vencimiento || '';
            document.getElementById('prod-proveedor').value = producto.proveedor_id || '';
            document.getElementById('modalProductoTitle').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i> Editar Producto';
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
        }

        function abrirTransferencia(id, nombre, stockActual) {
            document.getElementById('transProductoId').value = id;
            document.getElementById('transCantidad').max = stockActual;
            document.getElementById('transCantidad').value = 1;
            document.getElementById('transDestino').value = '';
            new bootstrap.Modal(document.getElementById('modalTransferir')).show();
        }

        function generarOrden(id) { window.open('{{ url('orden-compra') }}/' + id, '_blank'); }

        function abrirRequisicion(id, nombre) {
            Swal.fire({ title: 'Solicitar Material', text: 'Producto: ' + nombre, input: 'number', inputLabel: 'Cantidad', inputAttributes: { min: 1 }, showCancelButton: true, confirmButtonText: 'Enviar Solicitud', confirmButtonColor: '#fdcb6e' }).then((result) => {
                if (result.value) {
                    fetch('{{ route('requisiciones.solicitar') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ producto_id: id, cantidad: result.value }) })
                    .then(res => res.json()).then(data => {
                        if (data.success) { Swal.fire('Solicitud Enviada', 'El administrador revisará tu pedido.', 'success'); }
                        else { Swal.fire('Error', data.message, 'error'); }
                    });
                }
            });
        }

// FUNCIÓN ROBUSTA PARA ACTUALIZAR STOCK CON ANIMACIONES
function actualizarStock(idProducto, operacion, btnElement) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('{{ route('ajustar.stock') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: idProducto, accion: operacion })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error de conexión con el servidor');
        return response.json();
    })
    .then(data => {
        if(data.success) {
            const stockElement = document.getElementById(`stock-${idProducto}`);
            if(stockElement) {
                stockElement.innerText = data.nuevo_stock;
            }

            const rect = btnElement.getBoundingClientRect();
            const floatEl = document.createElement('div');
            const isPlus = operacion === 'sumar';
            floatEl.className = `floating-number ${isPlus ? 'float-plus' : 'float-minus'}`;
            floatEl.textContent = isPlus ? '+1' : '-1';
            
            floatEl.style.left = `${rect.left + window.scrollX + (rect.width/2) - 10}px`;
            floatEl.style.top = `${rect.top + window.scrollY - 10}px`;
            document.body.appendChild(floatEl);
            setTimeout(() => floatEl.remove(), 800);

            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: isPlus ? 'success' : 'info',
                title: isPlus ? 'Stock incrementado' : 'Stock reducido',
                showConfirmButton: false,
                timer: 1500,
                background: 'var(--bg-card)',
                color: '#ffffff'
            });
        } else {
            Swal.fire('Error', data.message || 'No se pudo actualizar', 'error');
        }
    })
    .catch(error => {
        console.error("Error capturado:", error);
        Swal.fire('Error de Conexión', 'No se pudo actualizar el stock en la base de datos.', 'error');
    });
}

        function eliminarProducto(id) {
            Swal.fire({ title: '¿Eliminar producto?', text: 'Esta acción no se puede deshacer.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#E50914', cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar' }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('eliminar.producto') }}', { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id }) })
                    .then(res => res.json()).then(data => {
                        if (data.success) { Swal.fire('Eliminado', data.message, 'success'); setTimeout(() => location.reload(), 1000); }
                        else { Swal.fire('Error', data.message, 'error'); }
                    });
                }
            });
        }

        function mostrarMiCuenta() { Swal.fire('Mi Cuenta', 'Función en desarrollo.', 'info'); }
        function mostrarAtajos() { Swal.fire('Atajos', '<ul style="text-align:left;"><li><b>Alt+E</b>: Escáner</li><li><b>Alt+T</b>: Cambiar Tema</li></ul>', 'info'); }
        function cambiarCuenta(e) { e.preventDefault(); document.getElementById('logout-form').submit(); }

        function exportarTablaCSV(nombreArchivo) {
            const tabla = document.getElementById('tablaAuditoria');
            if (!tabla) { Swal.fire('Error', 'No hay datos para exportar', 'warning'); return; }
            let csv = [];
            const filas = tabla.querySelectorAll('tr');
            filas.forEach(fila => {
                let row = [];
                fila.querySelectorAll('th, td').forEach(col => {
                    row.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
                });
                csv.push(row.join(','));
            });
            const blob = new Blob(['\ufeff' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = nombreArchivo;
            link.click();
        }
    </script>
</body>
</html>
