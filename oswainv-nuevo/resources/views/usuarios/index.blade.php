<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administrar Usuarios - OSWA Inv</title>
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
            --accent-info: #0984e3; --topbar-height: 68px;
        }
        [data-theme="light"] {
            --bg-dark: #121212; --bg-card: #1c1c1c; --bg-input: #2a2a2a;
            --border-color: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3;
        }
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg-main) !important; color: var(--text-primary); }

        /* Glassmorphism Navbar (ESTANDARIZADO) */
        .topbar {
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%) !important;
            backdrop-filter: blur(10px); border: none !important;
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; overflow: visible !important;
        }
        .topbar::-webkit-scrollbar { display: none; }
        .topbar-left { display: flex; align-items: center; gap: 2rem; }
        .topbar-logo { white-space: nowrap; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .topbar-logo .logo-text {
            display: inline-block !important;
            font-weight: 800;
            animation: rgbText 3s linear infinite;
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            filter: drop-shadow(0 0 8px rgba(229,9,20,0.3));
        }
        @keyframes rgbText { 0% { background-position: 0% 50%; } 100% { background-position: 300% 50%; } }
        .logo-nav-unellez {
            height: 40px;
            filter: drop-shadow(0 0 6px rgba(229,9,20,0.4)) brightness(0) invert(1);
            transform: perspective(400px) rotateY(-5deg);
            transition: transform 0.4s ease, filter 0.4s ease;
        }
        .logo-nav-unellez:hover {
            transform: perspective(400px) rotateY(0deg) scale(1.05);
            filter: drop-shadow(0 0 12px rgba(229,9,20,0.7)) brightness(0) invert(1);
        }
        
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0; }
        .topbar-nav a:hover, .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: var(--accent-primary); border-radius: 1px; }

        .nav-dropdown { position: relative; }
        .nav-dropdown .dropdown-toggle { cursor: pointer; }
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #0d0d0d; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.8); padding: 6px 0; z-index: 1000; display: none; }
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
        .status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 8px rgba(231,76,60,0.7); }
        .status-indicator.offline .status-text { color: #e74c3c; }

        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; cursor: pointer; transition: box-shadow 0.2s; }
        .user-avatar:hover { box-shadow: 0 0 12px rgba(229,9,20,0.6); }
        .dropdown-menu-netflix { position: absolute; top: calc(100% + 12px); right: 0; width: 260px; background: #141414; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 12px 40px rgba(0,0,0,0.8); padding: 8px 0; z-index: 9999; display: none; animation: dropIn 0.2s ease; }
        .dropdown-menu-netflix::before { content: ''; position: absolute; top: -8px; right: 20px; width: 16px; height: 16px; background: #141414; border-left: 1px solid #2a2a2a; border-top: 1px solid #2a2a2a; transform: rotate(45deg); }
        @keyframes dropIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-menu-netflix .dropdown-header { padding: 14px 16px 10px; border-bottom: 1px solid #222; }
        .dropdown-menu-netflix .dd-name { font-weight: 700; font-size: 0.9rem; color: #fff; }
        .dropdown-menu-netflix .dd-email { font-size: 0.75rem; color: #888; margin-top: 2px; }
        .dropdown-menu-netflix .dd-role { font-size: 0.7rem; color: var(--accent-primary); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
        .dropdown-menu-netflix .dd-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; cursor: pointer; transition: background 0.15s; border: none; background: none; width: 100%; text-align: left; text-decoration: none; }
        .dropdown-menu-netflix .dd-item:hover { background: #1f1f1f; color: #fff; }
        .dropdown-menu-netflix .dd-divider { height: 1px; background: #222; margin: 6px 0; }
        .dropdown-menu-netflix .dd-item.dd-logout { color: var(--accent-danger); }
        .dropdown-menu-netflix .dd-item.dd-logout:hover { background: rgba(231,76,60,0.1); }

        .menu-toggle { display: none; }
        @media (max-width: 768px) {
            .topbar { padding: 0 5%; }
            .topbar-left { width: 100%; justify-content: space-between; }
            .topbar-logo .logo-text { display: none; }
            .menu-toggle { display: block !important; background: transparent !important; border: none; color: white; font-size: 2rem; padding: 0; }
            .topbar-right { display: none !important; }
            .topbar-nav { display: none; }
            .topbar-nav.show { display: flex; flex-direction: column; position: absolute; top: var(--topbar-height); left: 0; right: 0; background: #000000; padding: 1.5rem 5%; gap: 1rem; border-bottom: 1px solid var(--border-color); height: calc(100vh - var(--topbar-height)); overflow-y: auto; z-index: 1000; }
            .mobile-user-section { display: flex; flex-direction: column; gap: 10px; padding-top: 15px; margin-top: auto; border-top: 1px solid var(--border-color); }
        }
        @media (min-width: 769px) { .menu-toggle { display: none !important; } .mobile-user-section { display: none !important; } }

        /* Main Content */
        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; max-width: 1200px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 10px; }
        .page-title { font-size: 1.6rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .page-title i { color: var(--accent-primary); }
        .btn-netflix-red { background: var(--n-red) !important; color: #fff !important; border: none !important; font-weight: 600; padding: 10px 24px; border-radius: 4px; box-shadow: 0 4px 15px rgba(229,9,20,0.4); transition: all 0.3s ease; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .btn-netflix-red:hover { background: #b8070f !important; transform: scale(1.05); box-shadow: 0 8px 25px rgba(229,9,20,0.6); }

        /* User Cards */
        .user-card { background: var(--bg-card) !important; border-radius: 15px !important; padding: 24px 20px; border: 1px solid var(--n-border) !important; transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease, border-color 0.4s ease; margin-bottom: 20px; position: relative; overflow: hidden; }
        .user-card:hover { transform: translateY(-8px) scale(1.05); border-color: var(--n-red) !important; box-shadow: 0 15px 30px rgba(0,0,0,0.6), 0 0 40px rgba(229,9,20,0.15); z-index: 5; }
        .user-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--n-red), transparent); opacity: 0; transition: opacity 0.4s; }
        .user-card:hover::after { opacity: 1; }
        .user-card .user-photo-wrapper { position: relative; display: inline-block; }
        .card-profile-photo { width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin: 0 auto 12px; display: block; border: 3px solid var(--n-border); box-shadow: 0 0 20px rgba(0,0,0,0.4); transition: all 0.4s; }
        .user-card:hover .card-profile-photo { border-color: var(--n-red); box-shadow: 0 0 30px rgba(229,9,20,0.4); }
        .user-avatar-card { width: 70px; height: 70px; background: linear-gradient(135deg, var(--n-red), #b20710); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 1.6rem; color: #fff; font-weight: 700; box-shadow: 0 0 20px rgba(229,9,20,0.3); transition: transform 0.3s; }
        .user-card:hover .user-avatar-card { transform: scale(1.1); }
        .user-card .user-name { font-weight: 700; font-size: 1rem; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .status-dot-indicator { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .status-dot-indicator.online { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        .status-dot-indicator.offline { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        .user-card .user-email { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; color: var(--text-secondary); font-size: 0.78rem; margin-bottom: 10px; }
        .badge-rol { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; margin-bottom: 16px; }
        .badge-admin { background: rgba(229,9,20,0.15); color: var(--accent-primary); border: 1px solid rgba(229,9,20,0.3); }
        .badge-empleado { background: rgba(148,163,184,0.15); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); }
        .user-card .user-date { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; color: #555; font-size: 0.7rem; margin-bottom: 12px; }
        .user-card-actions { display: flex; gap: 8px; justify-content: center; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--n-border); }
        .btn-status { background: none; border: 1px solid rgba(231,76,60,0.3); color: var(--accent-danger); padding: 6px 16px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 500; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .btn-status.activo { border-color: rgba(0,184,148,0.3); color: var(--accent-success); }
        .btn-status:hover { background: rgba(231,76,60,0.15); border-color: var(--accent-danger); }
        .btn-status.activo:hover { background: rgba(0,184,148,0.15); border-color: var(--accent-success); }

        /* Activity Feed */
        .activity-feed { margin-top: 3rem; background: #0f0f0f; border-radius: 15px; border: 1px solid var(--n-border); padding: 24px; }
        .feed-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; color: var(--text-primary); }
        .feed-title i { color: var(--accent-primary); }
        .feed-item { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #1a1a1a; }
        .feed-item:last-child { border-bottom: none; }
        .feed-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--accent-primary); flex-shrink: 0; }
        .feed-info { flex: 1; }
        .feed-user { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
        .feed-detail { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.78rem; color: var(--text-secondary); }
        .feed-meta { text-align: right; }
        .feed-ip { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.8rem; color: #64748b; }
        .feed-date { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.7rem; color: #475569; margin-top: 2px; }

        /* Modals */
        .modal-content { background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--n-border); }
        .modal-header .modal-title { color: var(--text-primary); font-weight: 700; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 1.5rem; }
        .form-control { background: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; padding: 12px; }
        .form-control:focus { background: #333; border-color: var(--accent-primary); color: var(--text-primary); box-shadow: none; outline: 1px solid #666; }
        .form-label { color: var(--text-secondary); font-size: 0.9rem; }

        /* Photo Upload Netflix */
        .photo-upload-wrapper { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 20px 0; }
        .photo-upload-preview { width: 100px; height: 100px; border-radius: 50%; background: #2a2a2a; border: 3px dashed #444; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; transition: all 0.3s; position: relative; }
        .photo-upload-preview:hover { border-color: var(--accent-primary); background: #333; }
        .photo-upload-preview.has-image { border-style: solid; border-color: var(--accent-primary); }
        .photo-upload-preview img { width: 100%; height: 100%; object-fit: cover; }
        .photo-upload-preview .upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 4px; color: #666; font-size: 0.7rem; text-align: center; }
        .photo-upload-preview .upload-placeholder i { font-size: 1.8rem; }
        .photo-upload-preview .upload-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; border-radius: 50%; }
        .photo-upload-preview:hover .upload-overlay { opacity: 1; }
        .photo-upload-preview .upload-overlay i { color: #fff; font-size: 1.5rem; }
        .photo-upload-hint { color: #555; font-size: 0.75rem; text-align: center; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #E50914, #ff6b6b); }
    </style>
</head>
<body data-theme="dark">

    <!-- NAVBAR GLOBAL ESTANDARIZADO -->
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
            <a href="{{ route('inventario') }}">Dashboard</a>
            <a href="{{ route('catalogo') }}">Catálogo</a>
            <a href="{{ route('proveedores') }}">Proveedores</a>
            
            <!-- PANEL DE CONTROL (EXCLUSIVO ADMIN) -->
            @if(auth()->user()?->rol === 'admin')
            <div class="nav-dropdown">
                <a href="#" class="dropdown-toggle {{ request()->routeIs('usuarios.*', 'auditoria') ? 'active' : '' }}" onclick="event.preventDefault(); this.parentElement.classList.toggle('show')">
                    Panel de Control
                </a>
                <div class="dropdown-menu-custom">
                    <a href="{{ route('exportar.pdf') }}" target="_blank" class="dropdown-item-custom">
                        <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Reporte Inventario
                    </a>
                    <a href="{{ route('catalogo') }}#tab-auditoria" class="dropdown-item-custom">
                        <i class="bi bi-clock-history text-success"></i> Auditoría
                    </a>
                    <a href="{{ route('usuarios.index') }}" class="dropdown-item-custom" style="background: rgba(255,255,255,0.05);">
                        <i class="bi bi-people-fill text-info"></i> Usuarios
                    </a>
                    <div class="dd-divider" style="height: 1px; background: #2a2a2a; margin: 4px 0;"></div>
                    <a href="{{ route('respaldo.db') }}" class="dropdown-item-custom">
                        <i class="bi bi-database-down text-warning"></i> Respaldar BD
                    </a>
                </div>
            </div>
            @endif

            <div class="mobile-user-section d-md-none mt-auto pt-4 border-top border-secondary">
                <div class="status-indicator online mb-3" style="width: fit-content;">
                    <span class="status-dot"></span>
                    <span class="status-text text-white" style="font-size: 0.8rem;">En línea</span>
                </div>
                <div class="user-info mb-3 d-flex align-items-center gap-2">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div>
                        <div class="text-white fw-bold" style="font-size: 0.9rem;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                        <div class="text-secondary" style="font-size: 0.8rem;">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right"></i> Salir del Sistema
                    </button>
                </form>
            </div>
        </div>

        <div class="topbar-right d-none d-md-flex align-items-center gap-3">
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="topbarSearchInput" placeholder="Buscar usuarios...">
            </div>
            <div class="user-dropdown" id="userDropdown">
                <div class="d-flex align-items-center gap-2" onclick="toggleUserDropdown()">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
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
                    
                    <button type="button" class="dd-item text-white" onclick="abrirSelectorPerfiles(event)" style="background: none; border: none; cursor: pointer; width: 100%; text-align: left;">
                        <i class="bi bi-arrow-left-right text-danger"></i> Cambiar de Cuenta
                    </button>
                    
                    <div class="dd-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 w-100">
                        @csrf
                        <button type="submit" class="dd-item dd-logout w-100 text-start" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="bi bi-box-arrow-right"></i> Salir del Sistema
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <button class="menu-toggle d-md-none" onclick="toggleSidebar()" style="background: transparent; border: none; color: white; font-size: 2rem; padding: 0;">
            <i class="bi bi-list"></i>
        </button>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <div class="page-header">
            <div class="page-title">
                <i class="bi bi-people"></i> Administración de Personal
            </div>
            <button class="btn-netflix-red" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                <i class="bi bi-plus-lg"></i> Nuevo Empleado
            </button>
        </div>

        <div class="row">
            @forelse($usuarios as $user)
            <div class="col-md-6 col-lg-4">
                <div class="user-card text-center">
                    @if($user->profile_photo_path)
                        <div class="user-photo-wrapper">
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="card-profile-photo">
                        </div>
                    @else
                        <div class="user-avatar-card">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    
                    <div class="user-name">
                        <span class="status-dot-indicator {{ $user->is_active ? 'online' : 'offline' }}"></span>
                        {{ $user->name }}
                    </div>
                    <div class="user-email">{{ $user->email }}</div>
                    @if($user->cedula)
                        <div style="color:#666;font-size:0.75rem;font-family:'Consolas','Monaco','Courier New',monospace;margin-bottom:4px;">
                            <i class="bi bi-person-vcard me-1"></i>{{ $user->cedula }}
                        </div>
                    @endif
                    @if($user->telefono)
                        <div style="color:#666;font-size:0.75rem;font-family:'Consolas','Monaco','Courier New',monospace;margin-bottom:6px;">
                            <i class="bi bi-telephone me-1"></i>{{ $user->telefono }}
                        </div>
                    @endif
                    <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
                        <span class="badge-rol {{ $user->rol === 'admin' ? 'badge-admin' : 'badge-empleado' }}">
                            <i class="bi bi-{{ $user->rol === 'admin' ? 'shield-fill-check' : 'person' }} me-1"></i>{{ ucfirst($user->rol) }}
                        </span>
                        <span style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);color:#ffc107;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;">
                            <i class="bi bi-star-fill me-1" style="font-size:0.6rem;"></i>{{ $user->xp ?? 0 }} XP
                        </span>
                        <span style="background:rgba(9,132,227,0.1);border:1px solid rgba(9,132,227,0.2);color:#0984e3;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;">
                            <i class="bi bi-trophy-fill me-1" style="font-size:0.6rem;"></i>Nv. {{ $user->nivel ?? 1 }}
                        </span>
                    </div>
                    <div class="user-date">
                        <i class="bi bi-calendar3 me-1"></i>Registrado: {{ $user->created_at->format('d/m/Y') }}
                    </div>
                    
                    @if($user->id !== auth()->id())
                    <div class="user-card-actions">
                        <button class="btn-status {{ $user->is_active ? 'activo' : '' }}" onclick="cambiarEstatus({{ $user->id }}, '{{ $user->name }}', {{ $user->is_active }})">
                            <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'check-circle' }}"></i>
                            {{ $user->is_active ? 'Suspender' : 'Activar' }}
                        </button>
                    </div>
                    @else
                    <div class="user-card-actions">
                        <span style="color:#555;font-size:0.8rem;"><i class="bi bi-person-check me-1"></i> Tú (sesión actual)</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5" style="color:#555;">
                <i class="bi bi-people" style="font-size:3rem;"></i>
                <p class="mt-2">No hay usuarios registrados</p>
            </div>
            @endforelse
        </div>

        <div class="activity-feed">
            <div class="feed-title"><i class="bi bi-clock-history"></i> Últimos 20 Accesos al Sistema</div>
            @if(isset($logs) && count($logs) > 0)
                @foreach($logs as $log)
                <div class="feed-item">
                    <div class="feed-dot"></div>
                    <div class="feed-info">
                        <div class="feed-user">{{ $log->user?->name ?? 'Desconocido' }}</div>
                        <div class="feed-detail">{{ $log->user?->email ?? 'N/A' }}</div>
                    </div>
                    <div class="feed-meta">
                        <div class="feed-ip">{{ $log->ip_address }}</div>
                        <div class="feed-date">{{ $log->login_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5" style="color:#555;">
                    <i class="bi bi-shield-lock" style="font-size:3rem;"></i>
                    <p class="mt-2">No hay registros de acceso recientes</p>
                </div>
            @endif
        </div>
    </main>

    <!-- MODAL NUEVO USUARIO (NETFLIX REDESIGN) -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header" style="border:none;padding:1.5rem 1.5rem 0;">
                    <h5 class="modal-title" style="font-weight:800;font-size:1.3rem;">
                        <i class="bi bi-person-plus-fill me-2" style="color:var(--accent-primary);"></i>Nuevo Empleado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="nuevoUsuarioForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body" style="padding:1.5rem;">
                        <div class="row g-4">
                            <!-- Columna izquierda: Foto -->
                            <div class="col-md-4 d-flex flex-column align-items-center justify-content-center border-end" style="border-color:var(--n-border)!important;">
                                <div class="photo-upload-wrapper">
                                    <div class="photo-upload-preview" id="photoPreview" onclick="document.getElementById('photoInput').click()">
                                        <div class="upload-placeholder">
                                            <i class="bi bi-camera-fill"></i>
                                            <span>Foto</span>
                                        </div>
                                        <div class="upload-overlay"><i class="bi bi-camera-fill"></i></div>
                                    </div>
                                    <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display:none;">
                                    <button type="button" class="btn btn-sm" style="background:rgba(229,9,20,0.1);color:var(--accent-primary);border:1px solid rgba(229,9,20,0.2);border-radius:6px;padding:4px 16px;font-size:0.75rem;" onclick="document.getElementById('photoInput').click()">Subir foto</button>
                                    <div class="photo-upload-hint">PNG, JPG. Máx 5 MB</div>
                                </div>
                            </div>
                            <!-- Columna derecha: Campos -->
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" name="name" placeholder="Ej: Juan Pérez" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" name="email" placeholder="correo@ejemplo.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Cédula / Documento</label>
                                        <input type="text" class="form-control" name="cedula" placeholder="V-12345678">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" name="telefono" placeholder="0412-1234567">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rol</label>
                                        <select class="form-control" name="rol" required>
                                            <option value="empleado">Empleado</option>
                                            <option value="admin">Administrador</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="password" placeholder="Mínimo 6 caracteres" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" id="confirmPassword" placeholder="Repite la contraseña" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--n-border);padding:1rem 1.5rem;">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="background:transparent;color:#888;border:1px solid #333;border-radius:8px;padding:10px 24px;">Cancelar</button>
                        <button type="submit" class="btn btn-netflix-red" style="padding:10px 32px;"><i class="bi bi-save me-1"></i>Guardar Empleado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function toggleSidebar() {
            document.getElementById('topbarNav').classList.toggle('show');
        }

        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            if(arrow) arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            if(arrow) arrow.style.color = isOpen ? '#888' : '#E50914';
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                const menu = document.getElementById('userDropdownMenu');
                if(menu) menu.style.display = 'none';
            }
        });

        function mostrarMiCuenta() {
            const menu = document.getElementById('userDropdownMenu');
            if(menu) menu.style.display = 'none';
            Swal.fire({
                title: '',
                html: `<div style="text-align:center;">
                    <div style="position:relative;display:inline-block;margin-bottom:16px;">
                        @if(auth()->user()?->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:3px solid #E50914;box-shadow:0 0 20px rgba(229,9,20,0.3);">
                        @else
                            <div style="width:80px;height:80px;background:linear-gradient(135deg,#E50914,#b20710);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;border:3px solid #E50914;box-shadow:0 0 20px rgba(229,9,20,0.3);margin:0 auto;">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                        @endif
                        <div style="position:absolute;bottom:0;right:0;width:24px;height:24px;background:#00b894;border-radius:50%;border:3px solid #1c1c1c;display:flex;align-items:center;justify-content:center;"><i class="bi bi-check" style="color:#fff;font-size:0.7rem;"></i></div>
                    </div>
                    <div style="font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:4px;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                    <div style="color:#888;font-size:0.85rem;margin-bottom:16px;">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;text-align:center;">
                        <div style="background:#141414;border-radius:8px;padding:10px;">
                            <div style="color:#E50914;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->rol === 'admin' ? 'Admin' : 'Empleado' }}</div>
                            <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Rol</div>
                        </div>
                        <div style="background:#141414;border-radius:8px;padding:10px;">
                            <div style="color:#ffc107;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->xp ?? 0 }} XP</div>
                            <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Experiencia</div>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;text-align:center;">
                        <div style="background:#141414;border-radius:8px;padding:10px;">
                            <div style="color:#00b894;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->misiones()->where('estado', 'completada')->count() ?? 0 }}</div>
                            <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Misiones</div>
                        </div>
                        <div style="background:#141414;border-radius:8px;padding:10px;">
                            <div style="color:#0984e3;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->nivel ?? 1 }}</div>
                            <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Nivel</div>
                        </div>
                    </div>
                </div>`,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#E50914',
                background: '#121212',
                color: '#fff',
                width: 380
            });
        }

        function mostrarAtajos() {
            const menu = document.getElementById('userDropdownMenu');
            if(menu) menu.style.display = 'none';
            Swal.fire({
                title: 'Atajos de Teclado',
                html: `<div style="text-align:left;background:#1c1c1c;border-radius:12px;padding:16px;color:#ccc;font-size:0.85rem;">
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #333;"><span>Buscar</span><kbd style="background:#333;padding:2px 8px;border-radius:4px;">Ctrl + K</kbd></div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #333;"><span>Nuevo Usuario</span><kbd style="background:#333;padding:2px 8px;border-radius:4px;">Ctrl + N</kbd></div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;"><span>Cerrar Sesión</span><kbd style="background:#333;padding:2px 8px;border-radius:4px;">Ctrl + Q</kbd></div>
                </div>`,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#E50914',
                background: '#121212',
                color: '#fff'
            });
        }

        // --- Photo preview ---
        document.getElementById('photoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('photoPreview');
                preview.innerHTML = `<img src="${ev.target.result}" alt="Foto">`;
                preview.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });

        // --- Password confirmation + submit ---
        const frmUsuario = document.getElementById('nuevoUsuarioForm');
        if(frmUsuario) {
            frmUsuario.addEventListener('submit', async function(e) {
                e.preventDefault();
                const pass = this.querySelector('[name="password"]').value;
                const confirm = document.getElementById('confirmPassword').value;
                if (pass !== confirm) {
                    mostrarToast('Las contraseñas no coinciden', 'bi bi-exclamation-triangle-fill');
                    return;
                }
                const formData = new FormData(this);
                try {
                    const response = await fetch('{{ route('usuarios.guardar') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        const mdl = bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal'));
                        if(mdl) mdl.hide();
                        mostrarToast(data.message || 'Usuario creado correctamente', 'bi bi-person-plus-fill');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        mostrarToast(data.message || 'No se pudo crear el usuario', 'bi bi-exclamation-triangle-fill');
                    }
                } catch (err) {
                    mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
                }
            });
        }

        function cambiarEstatus(id, nombre, is_active) {
            const accion = is_active ? 'suspender' : 'activar';
            Swal.fire({
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
                html: `Estás a punto de ${accion} a <strong style="color:#E50914;">${nombre}</strong>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No',
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#333',
                background: '#121212',
                color: '#fff',
                position: 'top-end',
                toast: true,
                showConfirmButton: true,
                timer: undefined,
                customClass: { popup: 'oswa-confirm-toast' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('{{ route('usuarios.cambiarEstatus') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id: id })
                        });
                        const data = await response.json();
                        if (data.success) {
                            mostrarToast(data.message, 'bi bi-check-circle-fill');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            mostrarToast(data.message || 'No se pudo actualizar el estatus', 'bi bi-exclamation-triangle-fill');
                        }
                    } catch (err) {
                        mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
                    }
                }
            });
        }

        const inputSearch = document.getElementById('topbarSearchInput');
        if(inputSearch) {
            inputSearch.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                document.querySelectorAll('.user-card').forEach(card => {
                    const name = card.querySelector('.user-name').textContent.toLowerCase();
                    const email = card.querySelector('.user-email').textContent.toLowerCase();
                    card.closest('.col-md-6').style.display = (name.includes(query) || email.includes(query)) ? '' : 'none';
                });
            });
        }

        function checkNetworkStatus() {
            const isOnline = navigator.onLine;
            document.querySelectorAll('.status-indicator').forEach(ind => {
                const dot = ind.querySelector('.status-dot');
                const text = ind.querySelector('.status-text');
                if (isOnline) {
                    ind.classList.replace('offline', 'online');
                    if (text) text.textContent = 'En línea';
                } else {
                    ind.classList.replace('online', 'offline');
                    if (text) text.textContent = 'Sin conexión';
                }
            });
        }
        window.addEventListener('online', checkNetworkStatus);
        window.addEventListener('offline', checkNetworkStatus);
        document.addEventListener('DOMContentLoaded', checkNetworkStatus);
    </script>

    <!-- INYECTAMOS EL MOTOR DE PERFILES -->
    @include('partials.perfiles')

</body>
</html>