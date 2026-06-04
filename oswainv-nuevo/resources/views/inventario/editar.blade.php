<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Producto - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b;
            --bg-dark: #121212; --bg-input: #2a2a2a; --border-color: #2b2b2b;
            --text-primary: #e5e5e5; --text-secondary: #a3a3a3;
            --accent-primary: #E50914; --accent-success: #00b894; --accent-danger: #e74c3c;
            --accent-warning: #fdcb6e; --accent-info: #0984e3; --topbar-height: 68px;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }

        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height);
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%);
            backdrop-filter: blur(10px); border: none !important;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999;
        }
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
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #121212; border: 1px solid var(--n-border); border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.6); padding: 6px 0; z-index: 1000; display: none; }
        .nav-dropdown.show .dropdown-menu-custom { display: block; }
        .dropdown-item-custom { display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; }
        .dropdown-item-custom:hover { background: rgba(229,9,20,0.1); color: #fff; }

        .topbar-right { display: flex; align-items: center; gap: 1rem; }
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

        .status-indicator { display: flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); font-size: 0.72rem; font-weight: 500; transition: all 0.3s; white-space: nowrap; flex-shrink: 0; }
        .status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 8px rgba(0,184,148,0.7); }
        .status-indicator.online .status-text { color: #ccc; }
        .status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 8px rgba(231,76,60,0.7); }
        .status-indicator.offline .status-text { color: #e74c3c; }

        .menu-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 767px) {
            .menu-toggle { display: block; }
            .topbar-nav { display: none; flex-direction: column; position: absolute; top: var(--topbar-height); left: 0; right: 0; background: rgba(18,18,18,0.98); padding: 1rem 4%; border-bottom: 1px solid var(--n-border); }
            .topbar-nav.show { display: flex; }
            .topbar-search { display: none; }
        }

        html, body { overflow-x: hidden; overflow-y: auto; height: auto; min-height: 100vh; }
        .main-content { padding-top: calc(var(--topbar-height) + 1rem); padding-left: 4%; padding-right: 4%; padding-bottom: 6rem; min-height: auto; height: auto; }

        .edit-card { background: #1a1a1a; border: 1px solid #333; border-radius: 12px; overflow: visible; }
        .edit-card .form-control { background-color: #222 !important; border: 1px solid #444 !important; color: #e5e5e5 !important; }
        .edit-card .form-control:focus { border-color: var(--accent-primary) !important; box-shadow: 0 0 0 0.2rem rgba(229,9,20,0.25) !important; }
        .edit-card .form-control::placeholder { color: #666 !important; }
        @media (max-width: 767px) {
            .main-content { padding-left: 0; padding-right: 0; padding-top: calc(var(--topbar-height) + 0.5rem); padding-bottom: 80px; }
            .row.justify-content-center { margin: 0; }
            .col-md-8 { padding: 0; }
            .edit-card { border-radius: 0; border-left: none; border-right: none; }
            .edit-card .card-body { padding: 12px 12px 0 !important; }
            .edit-card .form-control { font-size: 0.85rem; padding: 8px 10px; }
            .edit-card label { font-size: 0.75rem; margin-bottom: 2px !important; }
            .row.mb-3 { margin-bottom: 0.5rem !important; }
            .row.mb-3 > div { margin-bottom: 0.4rem; }
            .d-flex.align-items-center.mb-4 { margin-bottom: 0.5rem !important; }
            .d-flex.align-items-center.mb-4 h4 { font-size: 0.95rem; }
            textarea.form-control { min-height: 60px; }
            .edit-form-btns { display: none !important; }
            .edit-mobile-bar { display: flex; position: fixed; bottom: 0; left: 0; right: 0; background: #1a1a1a; padding: 10px 16px; z-index: 10000; gap: 10px; border-top: 1px solid #333; box-shadow: 0 -4px 12px rgba(0,0,0,0.5); }
            .edit-mobile-bar .btn { flex: 1; font-size: 0.85rem; padding: 10px; border-radius: 6px; }
        }

        .edit-mobile-bar { display: none; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-page-enter { animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
    </style>
</head>
<body>

    <nav class="topbar" id="topbar">
        <div class="topbar-left d-flex align-items-center gap-3">
            <div class="topbar-logo d-flex align-items-center gap-2">
                <img src="{{ asset('img/logo-unellez.png') }}" class="logo-nav-unellez" alt="Logo">
                <span class="logo-text">OSWA Inv</span>
            </div>
            <div class="status-indicator online d-none d-md-flex">
                <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                <span class="status-text text-white" style="font-size: 0.75rem;">En línea</span>
            </div>
        </div>

        <div class="topbar-nav" id="topbarNav">
            <a href="{{ route('inventario') }}">Dashboard</a>
            <a href="{{ route('catalogo') }}" class="active">Catálogo</a>
            <a href="{{ route('proveedores') }}">Proveedores</a>
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
            <div class="user-dropdown" id="userDropdown">
                <div class="d-flex align-items-center gap-2" onclick="toggleUserDropdown()">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->display_name }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->display_name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <i class="bi bi-caret-down-fill" id="dropdownArrow" style="color:#888;font-size:0.7rem;"></i>
                </div>
                <div class="dropdown-menu-netflix" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dd-name">{{ auth()->user()?->display_name ?? 'Usuario' }}</div>
                        <div class="dd-email">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        <div class="dd-role">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                    </div>
                    <button type="button" class="dd-item" onclick="abrirSelectorPerfiles(event)" style="background: none; border: none; cursor: pointer; padding: 0; color: #fff;">
                        <i class="bi bi-arrow-left-right text-danger"></i> Cambiar de Cuenta
                    </button>
                    <div class="dd-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 w-100">
                        @csrf
                        <button type="submit" class="dd-item dd-logout w-100 text-start" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <button class="menu-toggle d-md-none" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
    </nav>

    <main class="main-content">
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                mostrarToast('{{ session('success') }}');
            });
        </script>
        @endif

        <div class="row justify-content-center animate-page-enter">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ url('/catalogo') }}" class="btn btn-outline-light btn-sm me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="text-white mb-0"><i class="bi bi-pencil-square text-warning me-2"></i> Editar Producto</h4>
                </div>

                <div class="edit-card">
                    <div class="card-body p-4">
                        <form id="editProductForm" action="{{ route('productos.update', $producto->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-white mb-1">Nombre del Producto</label>
                                    <input type="text" name="nombre" class="form-control" value="{{ $producto->nombre }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-white mb-1">Código de Barras</label>
                                    <input type="text" name="codigo" class="form-control" value="{{ $producto->codigo }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="text-white mb-1">Precio ($)</label>
                                    <input type="number" step="0.01" name="precio" class="form-control" value="{{ $producto->precio }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-white mb-1">Stock Actual</label>
                                    <input type="number" name="stock" class="form-control" value="{{ $producto->stock }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-white mb-1">Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento" class="form-control" value="{{ $producto->fecha_vencimiento?->format('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-white mb-1">Marca</label>
                                    <input type="text" name="marca" class="form-control" value="{{ $producto->marca }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-white mb-1">Categoría</label>
                                    <input type="text" name="categoria" class="form-control" value="{{ $producto->categoria }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-white mb-1">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ $producto->descripcion }}</textarea>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="edit-mobile-bar">
        <a href="{{ url('/catalogo') }}" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i> Volver</a>
        <button type="submit" form="editProductForm" class="btn fw-bold" style="background-color: #ffc107; color:#000;">Guardar Cambios</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleUserDropdown() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                const menu = document.getElementById('userDropdownMenu');
                if (menu) menu.classList.remove('show');
            }
        });
        function toggleSidebar() {
            document.getElementById('topbarNav').classList.toggle('show');
        }
        document.querySelectorAll('.nav-dropdown .dropdown-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('show');
            });
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-dropdown')) {
                document.querySelectorAll('.nav-dropdown.show').forEach(function(dd) { dd.classList.remove('show'); });
            }
        });
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

@include('partials.perfiles')

</body>
</html>
