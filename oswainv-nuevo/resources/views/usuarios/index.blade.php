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
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg-main) !important; color: var(--text-primary); }

        /* Glassmorphism Navbar */
        .topbar {
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%) !important;
            backdrop-filter: blur(10px);
            border: none !important;
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; overflow: visible !important;
        }
        .topbar-left { display: flex; align-items: center; gap: 2rem; }
        .topbar-logo { white-space: nowrap; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .topbar-logo .logo-text {
            display: inline-block !important;
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
        }
        @keyframes rgbText { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .logo-nav-unellez {
            height: 35px; filter: brightness(0) invert(1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; margin-right: 10px;
        }
        .logo-nav-unellez:hover {
            transform: scale(1.2);
            filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
        }
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0; }
        .topbar-nav a:hover, .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: var(--accent-primary); border-radius: 1px; }

        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .topbar-search { position: relative; }
        .topbar-search input { width: 220px; padding: 7px 14px 7px 34px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 6px; color: #ffffff; font-size: 0.85rem; transition: all 0.3s; }
        .topbar-search input::placeholder { color: rgba(255,255,255,0.6); }
        .topbar-search input:focus { outline: none; background: rgba(255,255,255,0.12); border-color: var(--accent-primary); width: 280px; box-shadow: 0 0 12px rgba(229,9,20,0.15); }
        .topbar-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.6); font-size: 0.85rem; }

        .status-indicator {
            display: flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 20px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
            font-size: 0.72rem; font-weight: 500; transition: all 0.3s;
            white-space: nowrap; flex-shrink: 0; height: fit-content;
        }
        .status-indicator .status-dot { width: 7px; height: 7px; border-radius: 50%; transition: background 0.3s ease; }
        .status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 8px rgba(0,184,148,0.7); }
        .status-indicator.online .status-text { color: #ccc; }

        .theme-toggle { background: transparent; border: none; font-size: 1.2rem; cursor: pointer; color: #e5e5e5; }

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
        .dropdown-menu-netflix .dd-item i { color: #888; font-size: 1rem; width: 20px; text-align: center; }
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
            .topbar-nav.show {
                display: flex; flex-direction: column; position: absolute;
                top: var(--topbar-height); left: 0; right: 0;
                background: #000000; padding: 1.5rem 5%; gap: 1rem;
                border-bottom: 1px solid var(--border-color); height: calc(100vh - var(--topbar-height));
                overflow-y: auto; z-index: 1000;
            }
            .mobile-user-section {
                display: flex; flex-direction: column; gap: 10px;
                padding-top: 15px; margin-top: auto; border-top: 1px solid var(--border-color);
            }
        }
        @media (min-width: 769px) {
            .menu-toggle { display: none !important; }
            .mobile-user-section { display: none !important; }
        }

        /* Main Content */
        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; max-width: 1200px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 10px; }
        .page-title { font-size: 1.6rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .page-title i { color: var(--accent-primary); }
        .btn-netflix-red {
            background: var(--n-red) !important; color: #fff !important; border: none !important;
            font-weight: 600; padding: 10px 24px; border-radius: 4px;
            box-shadow: 0 4px 15px rgba(229,9,20,0.4); transition: all 0.3s ease;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
        }
        .btn-netflix-red:hover { background: #b8070f !important; transform: scale(1.05); box-shadow: 0 8px 25px rgba(229,9,20,0.6); }

        /* User Cards */
        .user-card {
            background: var(--bg-card) !important;
            border-radius: 15px !important;
            padding: 20px;
            border: 1px solid var(--n-border) !important;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease, border-color 0.4s ease;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .user-card:hover {
            transform: translateY(-8px) scale(1.05);
            border-color: var(--n-red) !important;
            box-shadow: 0 15px 30px rgba(0,0,0,0.6);
            z-index: 5;
        }
        .user-avatar-card {
            width: 60px; height: 60px;
            background: var(--n-red);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem; color: #fff;
            font-weight: 700;
        }
        .user-card .user-name {
            font-weight: 700; font-size: 1rem; margin-bottom: 4px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .status-dot-indicator { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .status-dot-indicator.online { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        .status-dot-indicator.offline { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        .user-card .user-email {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            color: var(--text-secondary); font-size: 0.78rem; margin-bottom: 10px;
        }
        .badge-rol { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; margin-bottom: 16px; }
        .badge-admin { background: rgba(229,9,20,0.15); color: var(--accent-primary); border: 1px solid rgba(229,9,20,0.3); }
        .badge-empleado { background: rgba(148,163,184,0.15); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); }
        .user-card .user-date {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            color: #555; font-size: 0.7rem; margin-bottom: 12px;
        }
        .user-card-actions { display: flex; gap: 8px; justify-content: center; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--n-border); }
        .btn-status {
            background: none;
            border: 1px solid rgba(231,76,60,0.3);
            color: var(--accent-danger);
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-status.activo { border-color: rgba(0,184,148,0.3); color: var(--accent-success); }
        .btn-status:hover { background: rgba(231,76,60,0.15); border-color: var(--accent-danger); }
        .btn-status.activo:hover { background: rgba(0,184,148,0.15); border-color: var(--accent-success); }

        /* Activity Feed */
        .activity-feed {
            margin-top: 3rem;
            background: #0f0f0f;
            border-radius: 15px;
            border: 1px solid var(--n-border);
            padding: 24px;
        }
        .feed-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; color: var(--text-primary); }
        .feed-title i { color: var(--accent-primary); }
        .feed-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #1a1a1a;
        }
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

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #E50914, #ff6b6b); }
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
            <a href="{{ route('inventario') }}">Dashboard</a>
            <a href="{{ route('proveedores') }}">Proveedores</a>
            <a href="{{ route('auditoria') }}">Auditoría</a>
            <a href="{{ route('usuarios.index') }}" class="active">Usuarios</a>

            <div class="mobile-user-section d-md-none mt-auto pt-4 border-top border-secondary">
                <div class="status-indicator online mb-3" style="width: fit-content;">
                    <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                    <span class="status-text text-white" style="font-size: 0.8rem;">En línea</span>
                </div>
                <div class="user-info mb-3 d-flex align-items-center gap-2">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
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
            <button class="theme-toggle" onclick="toggleTheme()" title="Modo claro/oscuro"><i class="bi bi-moon-fill"></i></button>
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="topbarSearchInput" placeholder="Buscar usuarios...">
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
                    <a href="{{ route('usuarios.index') }}" class="dd-item"><i class="bi bi-people"></i> Administrar Usuarios</a>
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
                <div class="user-card">
                    <div class="user-avatar-card">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                    <div class="user-name">
                        <span class="status-dot-indicator {{ $user->is_active ? 'online' : 'offline' }}"></span>
                        {{ $user->name }}
                    </div>
                    <div class="user-email">{{ $user->email }}</div>
                    <span class="badge-rol {{ $user->rol === 'admin' ? 'badge-admin' : 'badge-empleado' }}">
                        <i class="bi bi-{{ $user->rol === 'admin' ? 'shield-fill-check' : 'person' }} me-1"></i>{{ ucfirst($user->rol) }}
                    </span>
                    <div class="user-date">
                        <i class="bi bi-calendar3 me-1"></i>{{ $user->created_at->format('d/m/Y') }}
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
            @forelse($logs as $log)
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
            @empty
            <div class="text-center py-5" style="color:#555;">
                <i class="bi bi-shield-lock" style="font-size:3rem;"></i>
                <p class="mt-2">No hay registros de acceso recientes</p>
            </div>
            @endforelse
        </div>
    </main>

    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="nuevoUsuarioForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-control" name="rol" required>
                                <option value="empleado">Empleado</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-netflix-red"><i class="bi bi-save me-1"></i>Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function toggleTheme() {
            const body = document.body;
            const current = body.getAttribute('data-theme');
            body.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
        }

        function toggleSidebar() {
            document.getElementById('topbarNav').classList.toggle('show');
        }

        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            arrow.style.color = isOpen ? '#888' : '#E50914';
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (!dropdown.contains(e.target)) {
                document.getElementById('userDropdownMenu').style.display = 'none';
            }
        });

        function mostrarMiCuenta() {
            document.getElementById('userDropdownMenu').style.display = 'none';
            Swal.fire({
                title: 'Mi Cuenta',
                html: `<div style="text-align:left;background:#1c1c1c;border-radius:12px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div style="width:48px;height:48px;background:#E50914;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div style="font-size:1.1rem;font-weight:700;color:#fff;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                            <div style="color:#888;font-size:0.85rem;">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        </div>
                    </div>
                </div>`,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#E50914',
                background: '#121212',
                color: '#fff'
            });
        }

        function mostrarAtajos() {
            document.getElementById('userDropdownMenu').style.display = 'none';
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

        function cambiarCuenta(e) {
            e.preventDefault();
            document.getElementById('userDropdownMenu').style.display = 'none';
            Swal.fire({
                title: 'Cambiar de Cuenta',
                text: 'Serás redirigido al login para seleccionar otra cuenta.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#333',
                background: '#121212',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        document.getElementById('nuevoUsuarioForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route('usuarios.guardar') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario Creado',
                        text: data.message || 'Empleado registrado correctamente',
                        confirmButtonColor: '#E50914',
                        background: '#121212',
                        color: '#fff',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo crear el usuario',
                        confirmButtonColor: '#E50914',
                        background: '#121212',
                        color: '#fff'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    confirmButtonColor: '#E50914',
                    background: '#121212',
                    color: '#fff'
                });
            }
        });

        function cambiarEstatus(id, nombre, is_active) {
            const accion = is_active ? 'suspender' : 'activar';
            Swal.fire({
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
                html: `Estás a punto de ${accion} a <strong style="color:#E50914;">${nombre}</strong>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#333',
                background: '#121212',
                color: '#fff'
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Actualizado',
                                text: data.message,
                                confirmButtonColor: '#E50914',
                                background: '#121212',
                                color: '#fff',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo actualizar el estatus',
                                confirmButtonColor: '#E50914',
                                background: '#121212',
                                color: '#fff'
                            });
                        }
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión', confirmButtonColor: '#E50914', background: '#121212', color: '#fff' });
                    }
                }
            });
        }

        document.getElementById('topbarSearchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const name = card.querySelector('.user-name').textContent.toLowerCase();
                const email = card.querySelector('.user-email').textContent.toLowerCase();
                card.closest('.col-md-6').style.display = (name.includes(query) || email.includes(query)) ? '' : 'none';
            });
        });
    </script>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
