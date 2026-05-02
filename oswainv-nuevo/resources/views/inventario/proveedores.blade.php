<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Proveedores - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-main: #121212;
            --bg-card: #1c1c1c;
            --n-red: #E50914;
            --n-border: #2b2b2b;
            --bg-dark: #121212;
            --bg-input: #2a2a2a;
            --border-color: #2b2b2b;
            --text-primary: #e5e5e5;
            --text-secondary: #a3a3a3;
            --accent-primary: #E50914;
            --accent-success: #00b894;
            --accent-danger: #e74c3c;
            --accent-warning: #fdcb6e;
            --accent-info: #0984e3;
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
            backdrop-filter: blur(10px);
            border: none !important;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999;
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
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #121212; border: 1px solid var(--n-border); border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.6); padding: 6px 0; z-index: 1000; display: none; }
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
            .topbar-nav { display: none; flex-direction: column; position: absolute; top: var(--topbar-height); left: 0; right: 0; background: rgba(18,18,18,0.98); padding: 1rem 4%; border-bottom: 1px solid var(--n-border); }
            .topbar-nav.show { display: flex; }
            .topbar-search { display: none; }
        }
        
        .menu-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 767px) { .menu-toggle { display: block; } }
        
        .professional-footer { text-align: center; padding: 1.5rem 4%; margin-top: 2rem; border-top: 1px solid var(--n-border); color: var(--text-secondary); font-size: 0.85rem; }
        .professional-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .professional-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        .card, .card-custom, .provider-card, .product-card {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--n-border) !important;
            border-radius: 12px !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .card:hover, .provider-card:hover, .product-card:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: var(--n-red) !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            z-index: 5;
        }

        .scanner-fab { position: fixed; bottom: 30px; right: 30px; width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--accent-primary), #B20710); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; text-decoration: none; box-shadow: 0 4px 20px rgba(229,9,20,0.4); transition: all 0.3s; z-index: 900; }
        .scanner-fab:hover { transform: scale(1.1); box-shadow: 0 6px 25px rgba(229,9,20,0.6); }

        .modal-content { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--n-border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--n-border); }
        .modal-footer { border-top: 1px solid var(--n-border); }
        .form-control { background: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; }
        .form-control:focus { background: #333; border-color: var(--accent-primary); color: #e5e5e5; box-shadow: 0 0 0 0.25rem rgba(229,9,20,0.25); }
        .form-label { color: var(--text-secondary); }
        .form-select { background-color: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; }
        .form-select:focus { background-color: #333; border-color: var(--accent-primary); color: #e5e5e5; }

        @keyframes pulseRed { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }

        /* Page Transition: Fade In + Slide Up */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-page-enter {
            animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.25s; }
        .delay-3 { animation-delay: 0.4s; }
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
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
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
                <input type="text" id="topbarSearchInput" placeholder="Buscar proveedores...">
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
                    <button class="dd-item" onclick="cambiarCuenta(event)"><i class="bi bi-arrow-left-right"></i> Cambiar de Cuenta</button>
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

        <button class="menu-toggle d-md-none" onclick="toggleSidebar()" style="background: transparent; border: none; color: white; font-size: 2rem; padding: 0;">
            <i class="bi bi-list"></i>
        </button>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    
    <main class="main-content">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-50 animate-page-enter">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3 text-warning d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-buildings-fill fs-4"></i>
                </div>
                <h2 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">Directorio de Proveedores</h2>
            </div>
            
            @if(auth()->user()?->rol === 'admin')
                <button class="btn-nuevo" data-bs-toggle="modal" data-bs-target="#modalNuevoProveedor" onclick="limpiarModalProveedor()" style="background: linear-gradient(135deg, #fdcb6e, #e17055);">
                    <i class="bi bi-plus-lg me-1"></i> Registrar Proveedor
                </button>
            @endif
        </div>

        <div class="row animate-page-enter delay-1" id="contenedor-proveedores">
            @forelse($proveedores as $proveedor)
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card h-100" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.5); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title text-white fw-bold mb-0"><i class="bi bi-building text-warning me-2"></i> {{ $proveedor->nombre }}</h5>
                                <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 px-2 py-1" title="Documento de Identidad Fiscal">
                                    <strong>RIF:</strong> {{ $proveedor->rif }}
                                </span>
                            </div>
                            
                            <div class="mt-3">
                                <p class="text-secondary mb-2" style="font-size: 0.95rem;">
                                    <i class="bi bi-person-badge text-info me-2"></i> {{ $proveedor->contacto ?? 'Sin contacto registrado' }}
                                </p>
                                <p class="text-secondary mb-2" style="font-size: 0.95rem;">
                                    <i class="bi bi-telephone text-success me-2"></i> {{ $proveedor->telefono ?? 'Sin teléfono' }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                                <button class="btn btn-sm btn-outline-light w-100 d-flex justify-content-between align-items-center" onclick="verProductosProveedor({{ json_encode($proveedor) }})">
                                    <span><i class="bi bi-box-seam me-2"></i> Catálogo del Proveedor</span>
                                    <span class="badge bg-primary rounded-pill">{{ $proveedor->productos ? $proveedor->productos->count() : 0 }}</span>
                                </button>
                            </div>
                        </div>
                        
                        @if(auth()->user()->rol == 'admin')
                        <div class="card-footer bg-transparent border-top border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center">
                            <button class="btn btn-success btn-sm fw-bold px-3 d-flex align-items-center gap-2 shadow-sm" onclick="abastecerProveedor({{ json_encode($proveedor) }})">
                                <i class="bi bi-cart-plus"></i> Abastecer
                            </button>
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm border-secondary border-opacity-25" title="Editar" onclick="editarProveedor({{ json_encode($proveedor) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm border-secondary border-opacity-25" title="Eliminar" onclick="eliminarProveedor({{ $proveedor->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-dark rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; border: 1px solid #333;">
                        <i class="bi bi-buildings text-secondary" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                    <h5 class="text-white mt-2">Directorio Vacío</h5>
                    <p class="text-muted">Aún no hay distribuidores registrados en el sistema.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- MODAL: REGISTRAR NUEVO PROVEEDOR -->
    <div class="modal fade" id="modalNuevoProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-building-add text-warning me-2"></i> Registrar Proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProveedor">
                        <input type="hidden" name="id" id="prov-id">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label text-secondary">Nombre de la Empresa</label>
                                <input type="text" name="nombre" id="provNombre" class="form-control" placeholder="Ej. Alimentos Polar C.A." required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-secondary">RIF</label>
                                <input type="text" name="rif" id="provRif" class="form-control" placeholder="Ej. J-12345678-9" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Persona de Contacto (Vendedor)</label>
                                <input type="text" name="contacto" id="provContacto" class="form-control" placeholder="Nombre y Apellido del agente">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Teléfono</label>
                                <input type="text" name="telefono" id="provTelefono" class="form-control" placeholder="Ej. 0414-1234567">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary">Dirección Física</label>
                            <textarea name="direccion" id="provDireccion" class="form-control" rows="2" placeholder="Ubicación principal del distribuidor (Ej. Zona Industrial, Barinas)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarProveedor" class="btn btn-warning text-dark fw-bold"><i class="bi bi-save me-1"></i> Guardar Registro</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: CATÁLOGO DEL PROVEEDOR -->
    <div class="modal fade" id="modalCatalogoProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold" id="tituloCatalogoProv"><i class="bi bi-box-seam text-info me-2"></i> Catálogo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-secondary" style="font-size: 0.85rem;">
                                    <th>CÓDIGO</th>
                                    <th>PRODUCTO</th>
                                    <th>STOCK ACTUAL</th>
                                    <th>PRECIO VENTA</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductosProv">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ABASTECER (COMPRA) -->
    <div class="modal fade" id="modalAbastecerProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-cart-plus text-success me-2"></i> Orden de Abastecimiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAbastecer">
                        <input type="hidden" id="abastecer-prov-id">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Seleccionar Producto</label>
                            <select class="form-select bg-dark text-white border-secondary shadow-none" id="abastecer-producto-id" required>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary">Cantidad a Ingresar</label>
                            <input type="number" min="1" class="form-control bg-dark text-white border-secondary shadow-none" id="abastecer-cantidad" placeholder="Ej. 50" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success fw-bold" onclick="procesarAbastecimiento()"><i class="bi bi-check-lg"></i> Procesar Entrada</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="professional-footer">
        <div class="mb-1">&copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.</div>
        <div>Desarrollado con <i class="bi bi-code-slash text-primary"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span></div>
        <div class="mt-2 d-flex align-items-center justify-content-center" style="font-size: 0.75rem; opacity: 0.8;">
            <span>Ingeniería en Informática — V Semestre |</span>
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 18px; margin-left: 8px; margin-right: 4px; filter: brightness(0) invert(1);">
            <strong style="letter-spacing: 0.5px;">UNELLEZ</strong>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.querySelector('.theme-toggle i');
            if (body.getAttribute('data-theme') === 'dark') {
                body.setAttribute('data-theme', 'light');
                if(themeIcon) themeIcon.className = 'bi bi-sun-fill';
            } else {
                body.setAttribute('data-theme', 'dark');
                if(themeIcon) themeIcon.className = 'bi bi-moon-fill';
            }
        }

        function toggleSidebar() {
            document.getElementById('topbarNav').classList.toggle('show');
        }

        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            menu.classList.toggle('show');
            if(arrow) {
                arrow.style.transform = menu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if(dropdown && !dropdown.contains(e.target)) {
                document.getElementById('userDropdownMenu')?.classList.remove('show');
                document.getElementById('dropdownArrow').style.transform = 'rotate(0deg)';
            }
            const navDropdown = e.target.closest('.nav-dropdown');
            if (!navDropdown) {
                document.querySelectorAll('.nav-dropdown').forEach(dd => dd.classList.remove('show'));
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const btnGuardar = document.getElementById('btnGuardarProveedor');
            if(btnGuardar) {
                btnGuardar.addEventListener('click', function() {
                    const form = document.getElementById('formProveedor');
                    const formData = new FormData(form);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const id = document.getElementById('prov-id').value;
                    const url = id ? `/proveedores/${id}/actualizar` : "{{ route('proveedores.store') }}";

                    fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const modalEl = document.getElementById('modalNuevoProveedor');
                            const modalObj = bootstrap.Modal.getInstance(modalEl);
                            modalObj.hide();
                            form.reset();
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: id ? 'Proveedor actualizado' : 'Proveedor registrado exitosamente',
                                showConfirmButton: false, timer: 2000,
                                background: 'var(--bg-card)', color: '#ffffff'
                            }).then(() => { window.location.reload(); });
                        } else {
                            Swal.fire('Error', data.message || 'Revisa los datos e intenta de nuevo', 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire('Error de Conexión', 'No se pudo contactar al servidor', 'error');
                    });
                });
            }
        });

        function limpiarModalProveedor() {
            document.getElementById('formProveedor').reset();
            document.getElementById('prov-id').value = '';
            document.querySelector('#modalNuevoProveedor .modal-title').innerHTML = '<i class="bi bi-building-add text-warning me-2"></i> Registrar Proveedor';
        }

        function editarProveedor(proveedor) {
            document.getElementById('prov-id').value = proveedor.id;
            document.querySelector('#formProveedor input[name="nombre"]').value = proveedor.nombre;
            document.querySelector('#formProveedor input[name="rif"]').value = proveedor.rif;
            document.querySelector('#formProveedor input[name="contacto"]').value = proveedor.contacto || '';
            document.querySelector('#formProveedor input[name="telefono"]').value = proveedor.telefono || '';
            document.querySelector('#formProveedor textarea[name="direccion"]').value = proveedor.direccion || '';
            
            document.querySelector('#modalNuevoProveedor .modal-title').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i> Editar Proveedor';
            
            const modal = new bootstrap.Modal(document.getElementById('modalNuevoProveedor'));
            modal.show();
        }

        function eliminarProveedor(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto. Se eliminará el proveedor del directorio.",
                icon: 'warning',
                showCancelButton: true,
                background: 'var(--bg-card)',
                color: '#ffffff',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/proveedores/${id}/eliminar`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'El proveedor ha sido borrado.',
                                icon: 'success',
                                background: 'var(--bg-card)',
                                color: '#ffffff'
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire('Error de Conexión', 'No se pudo contactar al servidor', 'error');
                    });
                }
            });
        }

        function mostrarMiCuenta() { Swal.fire('Mi Cuenta', 'Función en desarrollo.', 'info'); }
        function mostrarAtajos() { Swal.fire('Atajos', '<ul style="text-align:left;"><li><b>Alt+T</b>: Cambiar Tema</li></ul>', 'info'); }
        function cambiarCuenta(e) { e.preventDefault(); document.getElementById('logout-form').submit(); }

        function verProductosProveedor(proveedor) {
            document.getElementById('tituloCatalogoProv').innerHTML = `<i class="bi bi-box-seam text-info me-2"></i> Catálogo de ${proveedor.nombre}`;
            const tbody = document.getElementById('tablaProductosProv');
            tbody.innerHTML = '';

            if (!proveedor.productos || proveedor.productos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary py-4">No hay productos asignados a este proveedor.</td></tr>';
            } else {
                proveedor.productos.forEach(prod => {
                    tbody.innerHTML += `
                        <tr style="border-bottom: 1px solid #222;">
                            <td class="text-secondary">${prod.codigo || 'N/A'}</td>
                            <td class="text-white fw-bold">${prod.nombre}</td>
                            <td><span class="badge ${prod.stock > 10 ? 'bg-success' : 'bg-danger'} bg-opacity-25 text-light border border-secondary px-2 py-1">${prod.stock}</span></td>
                            <td class="text-success">$${parseFloat(prod.precio).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }
            new bootstrap.Modal(document.getElementById('modalCatalogoProveedor')).show();
        }

        function abastecerProveedor(proveedor) {
            if (!proveedor.productos || proveedor.productos.length === 0) {
                Swal.fire('Catálogo Vacío', 'Este proveedor no tiene productos asignados. Asígnale productos desde el Catálogo principal primero.', 'warning');
                return;
            }

            document.getElementById('abastecer-prov-id').value = proveedor.id;
            document.getElementById('abastecer-cantidad').value = '';
            
            const select = document.getElementById('abastecer-producto-id');
            select.innerHTML = '<option value="" selected disabled>Seleccione un producto...</option>';
            proveedor.productos.forEach(prod => {
                select.innerHTML += `<option value="${prod.id}">${prod.nombre} (Stock: ${prod.stock})</option>`;
            });

            new bootstrap.Modal(document.getElementById('modalAbastecerProveedor')).show();
        }

        function procesarAbastecimiento() {
            const prodId = document.getElementById('abastecer-producto-id').value;
            const cant = document.getElementById('abastecer-cantidad').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if(!prodId || !cant || cant <= 0) {
                Swal.fire('Atención', 'Selecciona un producto y una cantidad válida.', 'warning');
                return;
            }

            fetch('{{ route("proveedores.abastecer") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ producto_id: prodId, cantidad: cant })
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Éxito', 'Stock actualizado y registrado en auditoría.', 'success')
                    .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'No se pudo procesar', 'error');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire('Error de Conexión', 'No se pudo contactar al servidor', 'error');
            });
        }
    </script>
</body>
</html>
