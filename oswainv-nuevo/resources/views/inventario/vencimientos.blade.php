<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        
        .menu-toggle { display: none; background: none; border: none; color: var(--text-primary); font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 768px) {
            .topbar-nav { display: none; }
            .topbar-nav.show { display: flex; flex-direction: column; position: absolute; top: var(--topbar-height); left: 0; right: 0; background: rgba(0,0,0,0.97); padding: 1rem 4%; gap: 1rem; border-bottom: 1px solid var(--border-color); }
            .menu-toggle { display: block; }
        }
        
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; }
        
        .logout-btn { background: none; border: none; color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: color 0.2s ease; }
        .logout-btn:hover { color: var(--accent-primary); }
        
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
        
        #status-indicator { display: flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; background: #222; border: 1px solid #2b2b2b; font-size: 0.75rem; font-weight: 500; }
        #status-indicator .status-dot { width: 8px; height: 8px; border-radius: 50%; transition: background 0.3s ease; }
        #status-indicator .status-text { color: var(--text-secondary); transition: color 0.3s ease; }
        #status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        #status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        #status-indicator.offline .status-text { color: #e74c3c; }
    </style>
</head>
<body data-theme="dark">
    
    <nav class="topbar" id="topbar">
        <div class="topbar-left">
            <div class="topbar-logo"><i class="bi bi-box-seam"></i> <span class="logo-text">OSWA Inv</span></div>
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            <div class="topbar-nav" id="topbarNav">
                <a href="{{ route('inventario') }}">Dashboard</a>
                <a href="{{ route('vencimientos') }}" class="active">Vencimientos</a>
                <a href="{{ route('auditoria') }}">Auditoría</a>
            </div>
        </div>
        <div class="topbar-right">
            <div id="status-indicator" class="online">
                <span class="status-dot"></span>
                <span class="status-text" id="statusText">En línea</span>
            </div>
            <button class="theme-toggle" onclick="toggleTheme()"><i class="bi bi-moon"></i></button>
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div style="font-weight: 600; font-size: 0.85rem;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-secondary);">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i> <span style="font-size: 0.8rem;">Salir</span>
                </button>
            </form>
        </div>
    </nav>
    
    <main class="main-content">
        <h4 class="mb-4" style="font-weight: 700; font-size: 1.5rem;"><i class="bi bi-calendar-x me-2" style="color: var(--accent-danger);"></i>Control de Vencimientos</h4>
        
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
                const indicator = document.getElementById('status-indicator');
                const statusText = document.getElementById('statusText');
                if (navigator.onLine) {
                    indicator.className = 'online';
                    statusText.textContent = 'En línea';
                } else {
                    indicator.className = 'offline';
                    statusText.textContent = 'Sin conexión';
                }
            }
            window.addEventListener('online', updateStatusIndicator);
            window.addEventListener('offline', updateStatusIndicator);
            updateStatusIndicator();
        });
    </script>
</body>
</html>
