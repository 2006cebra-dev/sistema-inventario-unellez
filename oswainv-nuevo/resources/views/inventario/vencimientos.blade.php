<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vencimientos - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        body { background-color: var(--bg-dark); color: var(--text-primary); margin: 0; transition: all 0.3s ease; }
        
        .topbar { 
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
            background: linear-gradient(180deg, rgba(0,0,0,0.95) 0%, rgba(20,20,20,0.98) 100%);
            border-bottom: 1px solid var(--border-color); 
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; backdrop-filter: blur(10px);
        }
        
        .topbar-left { display: flex; align-items: center; gap: 3rem; }
        
        .topbar-logo { font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .topbar-logo .logo-text {
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
        }
        @keyframes rgbText {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a {
            color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;
            font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0;
        }
        .topbar-nav a:hover { color: #ffffff; }
        .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after {
            content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
            height: 2px; background: var(--accent-primary); border-radius: 1px;
        }
        
        .nav-dropdown { position: relative; }
        .nav-dropdown .dropdown-toggle { cursor: pointer; }
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #1a1a1a; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.6); padding: 6px 0; z-index: 1000; display: none; }
        .nav-dropdown.show .dropdown-menu-custom { display: block; }
        .dropdown-item-custom { display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; }
        .dropdown-item-custom:hover { background: rgba(229,9,20,0.1); color: #fff; }
        .dropdown-item-custom.text-muted { color: #666; cursor: default; }
        .dropdown-item-custom.text-muted:hover { background: transparent; color: #666; }
        
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        
        @media (max-width: 768px) {
            .topbar { padding: 0 5%; }
            .topbar-left { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 0; }
            .topbar-logo { font-size: 1.3rem; }
            .topbar-logo .logo-text { display: none; }
            .menu-toggle { display: block !important; margin-left: auto; font-size: 2rem; padding: 0; }
            .topbar-right { display: none !important; }

            .topbar-nav { display: none; }
            .topbar-nav.show { 
                display: flex; flex-direction: column; position: absolute; 
                top: var(--topbar-height); left: 0; right: 0; 
                background: #141414; padding: 1.5rem 5%; gap: 1rem; 
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

        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }
        
        .vencimiento-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; }
        .vencimiento-card.danger { border-left: 4px solid var(--accent-danger); }
        .vencimiento-card.warning { border-left: 4px solid var(--accent-warning); }
        .vencimiento-card.success { border-left: 4px solid var(--accent-success); }
        
        .vencimiento-title { display: flex; align-items: center; gap: 10px; margin-bottom: 1rem; }
        .vencimiento-count { font-size: 2rem; font-weight: 700; }
        
        .product-list { list-style: none; padding: 0; margin: 0; }
        .product-list li { padding: 12px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .product-list li:last-child { border-bottom: none; }
        .product-name { font-weight: 600; }
        .product-date { font-size: 0.85rem; color: var(--text-secondary); }
        
        .badge-vencido { background: rgba(231,76,60,0.15); color: var(--accent-danger); padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; }
        .badge-por-vencer { background: rgba(253,203,110,0.15); color: var(--accent-warning); padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; }
        .badge-saludable { background: rgba(0,184,148,0.15); color: var(--accent-success); padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; }
        
        .empty-state { text-align: center; padding: 3rem; color: var(--text-secondary); }
        
        .theme-toggle { background: var(--bg-input); border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-primary); }
        
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; }
        
        .status-indicator { 
    display: flex; align-items: center; gap: 6px; 
    padding: 4px 12px; border-radius: 20px; 
    background: #222; border: 1px solid #2b2b2b; 
    white-space: nowrap; flex-shrink: 0; height: fit-content;
}
        .status-indicator .status-dot { width: 8px; height: 8px; border-radius: 50%; transition: background 0.3s ease; }
        .status-indicator .status-text { color: var(--text-secondary); transition: color 0.3s ease; }
        .status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        .status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        .status-indicator.offline .status-text { color: #e74c3c; }
        
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
        .professional-footer {
            text-align: center;
            padding: 1.5rem 4%;
            margin-top: 2rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-dark);
            color: var(--text-secondary);
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .professional-footer span.highlight {
            color: var(--text-primary);
            font-weight: 600;
        }
        .professional-footer .heart-icon {
            color: var(--accent-danger);
            animation: heartbeat 1.5s infinite;
            display: inline-block;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
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
            <button class="theme-toggle" onclick="toggleTheme()"><i class="bi bi-moon"></i></button>
            <div class="user-info d-flex align-items-center gap-2">
                @if(auth()->user()?->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                @else
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                @endif
                <div>
                    <div style="font-weight: 600; font-size: 0.85rem; color: white;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-secondary);">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="logout-btn d-flex align-items-center gap-1" style="background: none; border: none; color: var(--text-secondary);">
                    <i class="bi bi-box-arrow-right"></i> <span style="font-size: 0.85rem;">Salir</span>
                </button>
            </form>
        </div>

        <button class="menu-toggle d-md-none" onclick="toggleSidebar()" style="background: transparent; border: none; color: white; font-size: 2rem; padding: 0;">
            <i class="bi bi-list"></i>
        </button>
    </nav>
    
    <main class="main-content">
        
        <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-50">
            <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="bi bi-calendar-x fs-4"></i>
            </div>
            <h2 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">Control de Vencimientos</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="vencimiento-card danger">
                    <div class="vencimiento-title">
                        <i class="bi bi-exclamation-triangle" style="color: var(--accent-danger);"></i>
                        <h5 class="mb-0">Vencidos</h5>
                    </div>
                    <div class="vencimiento-count" style="color: var(--accent-danger);">{{ $vencidos->count() }}</div>
                    @if($vencidos->count() > 0)
                    <ul class="product-list mt-3">
                        @foreach($vencidos->take(5) as $p)
                        <li>
                            <div>
                                <div class="product-name">{{ $p->nombre }}</div>
                                <div class="product-date">{{ $p->codigo }}</div>
                            </div>
                            <span class="badge-vencido">{{ $p->fecha_vencimiento->format('d/m/Y') }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="empty-state"><i class="bi bi-check-circle" style="font-size: 2rem;"></i><p class="mt-2">Sin productos vencidos</p></div>
                    @endif
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="vencimiento-card warning">
                    <div class="vencimiento-title">
                        <i class="bi bi-clock" style="color: var(--accent-warning);"></i>
                        <h5 class="mb-0">Por Vencer (30 días)</h5>
                    </div>
                    <div class="vencimiento-count" style="color: var(--accent-warning);">{{ $porVencer->count() }}</div>
                    @if($porVencer->count() > 0)
                    <ul class="product-list mt-3">
                        @foreach($porVencer->take(5) as $p)
                        <li>
                            <div>
                                <div class="product-name">{{ $p->nombre }}</div>
                                <div class="product-date">{{ $p->codigo }}</div>
                            </div>
                            <span class="badge-por-vencer">{{ $p->fecha_vencimiento->format('d/m/Y') }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="empty-state"><i class="bi bi-check-circle" style="font-size: 2rem;"></i><p class="mt-2">Sin productos por vencer</p></div>
                    @endif
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="vencimiento-card success">
                    <div class="vencimiento-title">
                        <i class="bi bi-check-circle" style="color: var(--accent-success);"></i>
                        <h5 class="mb-0">Saludables</h5>
                    </div>
                    <div class="vencimiento-count" style="color: var(--accent-success);">{{ $saludables->count() }}</div>
                    @if($saludables->count() > 0)
                    <ul class="product-list mt-3">
                        @foreach($saludables->take(5) as $p)
                        <li>
                            <div>
                                <div class="product-name">{{ $p->nombre }}</div>
                                <div class="product-date">{{ $p->codigo }}</div>
                            </div>
                            <span class="badge-saludable">{{ $p->fecha_vencimiento->format('d/m/Y') }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="empty-state"><i class="bi bi-inbox" style="font-size: 2rem;"></i><p class="mt-2">Sin productos registrados</p></div>
                    @endif
                </div>
            </div>
        </div>
    </main>
    
    <script>
        function toggleSidebar() {
            const nav = document.getElementById('topbarNav');
            nav.classList.toggle('show');
        }
        function toggleTheme() {
            const body = document.body;
            const nuevo = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-theme', nuevo);
            localStorage.setItem('theme', nuevo);
        }
        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', saved);
            
            function updateStatusIndicator() {
                const indicators = document.querySelectorAll('.status-indicator');
                indicators.forEach(indicator => {
                    const dot = indicator.querySelector('.status-dot');
                    const text = indicator.querySelector('.status-text');

                    if (navigator.onLine) {
                        indicator.classList.replace('offline', 'online');
                        dot.style.background = '#00b894';
                        dot.style.boxShadow = '0 0 6px rgba(0,184,148,0.6)';
                        if(text) text.textContent = 'En línea';
                    } else {
                        indicator.classList.replace('online', 'offline');
                        dot.style.background = '#e74c3c';
                        dot.style.boxShadow = '0 0 6px rgba(231,76,60,0.6)';
                        if(text) text.textContent = 'Sin red';
                    }
                });
            }
            window.addEventListener('online', updateStatusIndicator);
            window.addEventListener('offline', updateStatusIndicator);
            updateStatusIndicator();
        });
    </script>

    <footer class="professional-footer">
        <div class="mb-1">
            &copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.
        </div>
        <div>
            Desarrollado con <i class="bi bi-code-slash text-primary"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span>
        </div>
        <div class="mt-2 d-flex align-items-center justify-content-center" style="font-size: 0.75rem; opacity: 0.8;">
            <span>Ingeniería en Informática — V Semestre |</span>
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 18px; margin-left: 8px; margin-right: 4px; filter: brightness(0) invert(1);">
            <strong style="letter-spacing: 0.5px;">UNELLEZ</strong>
        </div>
    </footer>
</body>
</html>