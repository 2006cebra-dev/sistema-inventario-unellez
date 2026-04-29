<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSWA Inv - Gestión de Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
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
        
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; }
        
        .logout-btn { background: none; border: none; color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: color 0.2s ease; }
        .logout-btn:hover { color: var(--accent-primary); }
        
        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }
        
        .btn-nuevo { background: linear-gradient(135deg, var(--accent-primary), #ff6b6b); color: white; padding: 10px 20px; border-radius: 4px; border: none; font-weight: 600; cursor: pointer; }
        .btn-nuevo:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(229,9,20,0.4); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2rem; }
        @media (max-width: 1199px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 767px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 575px) { .stats-grid { grid-template-columns: 1fr; } }
        
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease; animation: fadeInUp 0.5s ease forwards; opacity: 0; }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 50px rgba(0,0,0,0.3); }
        .stat-card.highlight { border-color: var(--accent-success); box-shadow: 0 0 30px rgba(0,184,148,0.3); }
        .stat-icon { width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-value { font-size: 1.75rem; font-weight: 700; }
        .stat-label { color: var(--text-secondary); font-size: 0.9rem; }
        
        .search-box { position: relative; max-width: 400px; margin-bottom: 1rem; }
        .search-box input { width: 100%; padding: 12px 16px 12px 45px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); }
        .search-box input:focus { outline: none; border-color: var(--accent-primary); }
        .search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
        
        .search-commands { display: flex; gap: 8px; margin-bottom: 1rem; flex-wrap: wrap; }
        .search-command { background: var(--bg-input); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; color: var(--text-secondary); }
        .search-command:hover { background: var(--accent-primary); color: white; }
        
        body[data-theme="dark"] .inventory-table,
        body[data-theme="dark"] .table,
        body[data-theme="dark"] .inventory-table th,
        body[data-theme="dark"] .inventory-table td,
        body[data-theme="dark"] .inventory-table thead th,
        body[data-theme="dark"] .inventory-table tbody tr,
        body[data-theme="dark"] .inventory-table tbody td {
            background-color: #181818 !important;
            color: #ffffff !important;
            border-color: #2b2b2b !important;
        }
        body[data-theme="dark"] .inventory-table thead th {
            background-color: #222 !important;
            color: #b3b3b3 !important;
        }
        body[data-theme="dark"] .inventory-table tbody tr:hover {
            background-color: #2b2b2b !important;
        }
        
        .inventory-table { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; margin-bottom: 2rem; }
        .inventory-table thead { background: var(--bg-input); }
        .inventory-table th { padding: 1rem 1.5rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.8rem; }
        .inventory-table td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .inventory-table tbody tr { transition: all 0.3s ease; background: var(--bg-card); color: var(--text-primary); }
        .inventory-table tbody tr:hover { background: rgba(229,9,20,0.08); }

        .inventory-table tbody tr.row-glow { animation: pulseGlow 2s infinite; }
        @keyframes pulseGlow {
            0%, 100% { background: var(--bg-card); box-shadow: 0 0 0 rgba(253,203,110,0); }
            50% { background: rgba(253,203,110,0.1); box-shadow: 0 0 15px rgba(253,203,110,0.3); }
        }
        .inventory-table tbody tr.row-critical { animation: pulseCritical 1s infinite; }
        @keyframes pulseCritical {
            0%, 100% { background: var(--bg-card); box-shadow: 0 0 5px rgba(239,68,68,0.2); }
            50% { background: rgba(239,68,68,0.15); box-shadow: 0 0 20px rgba(239,68,68,0.5); }
        }
        .inventory-table tbody tr.row-normal { transition: all 0.3s ease; }
        .inventory-table tbody tr.row-normal:hover { box-shadow: 0 0 10px rgba(16,185,129,0.2); }
        
        .product-name { font-weight: 600; }
        .product-code { font-size: 0.8rem; color: var(--text-secondary); }
        
        .category-badge { background: var(--bg-input); color: var(--accent-info); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; }
        .price-tag { font-weight: 700; color: var(--accent-success); }
        
        .stock-control { display: flex; align-items: center; gap: 8px; }
        .stock-btn { width: 32px; height: 32px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; font-size: 1rem; cursor: pointer; }
        .stock-btn-minus { background: var(--accent-danger); color: white; }
        .stock-btn-plus { background: var(--accent-success); color: white; }
        
        .action-btn { border: none; border-radius: 8px; padding: 6px 10px; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; }
        .action-btn-edit { background: rgba(13, 110, 253, 0.15); color: #0d6efd; }
        .action-btn-edit:hover { background: rgba(13, 110, 253, 0.35); box-shadow: 0 0 12px rgba(13, 110, 253, 0.3); }
        .action-btn-transfer { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
        .action-btn-transfer:hover { background: rgba(255, 193, 7, 0.35); box-shadow: 0 0 12px rgba(255, 193, 7, 0.3); }
        .action-btn-order { background: rgba(13, 202, 240, 0.15); color: #0dc0f0; }
        .action-btn-order:hover { background: rgba(13, 202, 240, 0.35); box-shadow: 0 0 12px rgba(13, 202, 240, 0.3); }
        .action-btn-delete { background: rgba(229, 9, 20, 0.15); color: var(--accent-primary); }
        .action-btn-delete:hover { background: rgba(229, 9, 20, 0.35); box-shadow: 0 0 12px rgba(229, 9, 20, 0.3); }
        
        .float-plus, .float-minus {
            animation: floatUp 0.8s ease-out forwards;
        }
        .float-plus { color: var(--accent-success); }
        .float-minus { color: var(--accent-danger); }
        @keyframes floatUp {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(-30px); opacity: 0; }
        }
        
        .flash-green { animation: flashGreen 0.4s ease-out; }
        .flash-red { animation: flashRed 0.4s ease-out; }
        @keyframes flashGreen {
            0%, 100% { background: var(--bg-input); }
            50% { background: rgba(16,185,129,0.5); }
        }
        @keyframes flashRed {
            0%, 100% { background: var(--bg-input); }
            50% { background: rgba(239,68,68,0.5); }
        }
        
        .stock-input { width: 50px; height: 32px; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); text-align: center; font-weight: 700; }
        
        .stock-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .stock-normal { background: rgba(0,184,148,0.15); color: var(--accent-success); }
        .stock-low { background: rgba(253,203,110,0.15); color: var(--accent-warning); }
        .stock-critical { background: rgba(231,76,60,0.15); color: var(--accent-danger); }
        
        .table-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        
        /* Gráficos */
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem; }
        @media (max-width: 991.98px) { .charts-grid { grid-template-columns: 1fr; } }
        
        .chart-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; }
        .chart-card h5 { margin-bottom: 1rem; }
        .chart-container { position: relative; height: 280px; width: 100%; }
        
        .oswa-bot-card { background: var(--bg-card); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-primary); border-radius: 4px; padding: 1.5rem; margin-bottom: 2rem; }
        .bot-input { flex: 1; padding: 12px 16px; background: var(--bg-input); border: none; border-radius: 4px; color: var(--text-primary); }
        .bot-response { margin-top: 1rem; padding: 1rem; border-radius: 4px; background: var(--bg-input); display: none; }
        .bot-response.show { display: block; }
        
        .scanner-fab { position: fixed; bottom: 2rem; right: 2rem; width: 60px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, var(--accent-primary), #ff6b6b); border: none; color: white; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 30px rgba(229,9,20,0.4); z-index: 100; }
        
        .modal-content { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 1rem 1.5rem; }
        .modal-title { color: var(--text-primary); }
        .btn-close { filter: invert(1); }
        [data-theme="light"] .btn-close { filter: invert(0); }
        .modal-body { padding: 1.5rem; }
        
        .form-control { background: var(--bg-input); border: none; color: var(--text-primary); border-radius: 4px; padding: 12px; }
        .form-control:focus { background: #444; border: none; color: var(--text-primary); box-shadow: none; outline: 1px solid #666; }
        .form-label { color: var(--text-secondary); font-size: 0.9rem; }
        
        .theme-toggle { background: var(--bg-input); border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #e5e5e5; }
        .theme-toggle i { color: #e5e5e5; }
        
        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; cursor: pointer; transition: box-shadow 0.2s; }
        .user-avatar:hover { box-shadow: 0 0 12px rgba(229,9,20,0.6); }
        
        .dropdown-menu-netflix { position: absolute; top: calc(100% + 12px); right: 0; width: 260px; background: #141414; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 12px 40px rgba(0,0,0,0.8); padding: 8px 0; z-index: 9999; display: none; animation: dropIn 0.2s ease; }
        .dropdown-menu-netflix::before { content: ''; position: absolute; top: -8px; right: 20px; width: 16px; height: 16px; background: #141414; border-left: 1px solid #2a2a2a; border-top: 1px solid #2a2a2a; transform: rotate(45deg); }
        @keyframes dropIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        
        .dropdown-menu-netflix .dropdown-header { padding: 14px 16px 10px; border-bottom: 1px solid #222; }
        .dropdown-menu-netflix .dropdown-header .dd-name { font-weight: 700; font-size: 0.9rem; color: #fff; }
        .dropdown-menu-netflix .dropdown-header .dd-email { font-size: 0.75rem; color: #888; margin-top: 2px; }
        .dropdown-menu-netflix .dropdown-header .dd-role { font-size: 0.7rem; color: var(--accent-primary); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .dropdown-menu-netflix .dd-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; cursor: pointer; transition: background 0.15s; border: none; background: none; width: 100%; text-align: left; }
        .dropdown-menu-netflix .dd-item:hover { background: #1f1f1f; color: #fff; }
        .dropdown-menu-netflix .dd-item i { color: #888; font-size: 1rem; width: 20px; text-align: center; }
        
        .dropdown-menu-netflix .dd-divider { height: 1px; background: #222; margin: 6px 0; }
        
        .dropdown-menu-netflix .dd-item.dd-logout { color: var(--accent-danger); }
        .dropdown-menu-netflix .dd-item.dd-logout:hover { background: rgba(231,76,60,0.1); }
        .dropdown-menu-netflix a.dd-item { text-decoration: none; }
        
        .dd-shortcut { margin-left: auto; font-size: 0.65rem; color: #555; background: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        
        .search-box input { width: 100%; padding: 12px 16px 12px 45px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); }
        .search-box input::placeholder { color: #e5e5e5; }
        .search-box input:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 10px rgba(229,9,20,0.2); }
        
        #status-indicator { display: flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; background: #222; border: 1px solid #2b2b2b; font-size: 0.75rem; font-weight: 500; }
        #status-indicator .status-dot { width: 8px; height: 8px; border-radius: 50%; transition: background 0.3s ease; }
        #status-indicator .status-text { color: var(--text-secondary); transition: color 0.3s ease; }
        #status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        #status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        #status-indicator.offline .status-text { color: #e74c3c; }
        
        #map { height: 300px; width: 100%; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
        .map-label { font-size: 0.75rem; color: var(--text-secondary); display: flex; align-items: center; gap: 4px; margin-bottom: 8px; }
        .route-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 12px; }
        .stat-item { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 10px 12px; text-align: center; }
        .stat-item .stat-label { font-size: 0.7rem; color: #666; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-item .stat-value { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .stat-item .stat-cost { color: #00b894; }
    </style>
</head>
<body data-theme="dark">
    
    <nav class="topbar" id="topbar">
        <div class="topbar-left">
            <div class="topbar-logo"><i class="bi bi-box-seam"></i> <span class="logo-text">OSWA Inv</span></div>
            <div class="topbar-nav" id="topbarNav">
                <a href="{{ route('inventario') }}" class="active">Dashboard</a>
                <a href="{{ route('vencimientos') }}">Vencimientos</a>
                <a href="{{ route('auditoria') }}">Auditoría</a>
            </div>
        </div>
        <div class="topbar-right">
            <div id="status-indicator" class="online">
                <span class="status-dot"></span>
                <span class="status-text" id="statusText">En línea</span>
            </div>
            <button class="theme-toggle" onclick="toggleTheme()"><i class="bi bi-moon"></i></button>
            
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
                    <button class="dd-item" onclick="mostrarMiCuenta()">
                        <i class="bi bi-person-circle"></i> Mi Cuenta
                    </button>
                    <button class="dd-item" onclick="mostrarAtajos()">
                        <i class="bi bi-keyboard"></i> Atajos de Teclado
                        <span class="dd-shortcut">Alt+?</span>
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="dd-item" style="text-decoration:none;">
                        <i class="bi bi-people"></i> Administrar Usuarios
                    </a>
                    <button class="dd-item" onclick="cambiarCuenta(event)">
                        <i class="bi bi-arrow-left-right"></i> Cambiar de Cuenta
                    </button>
                    <div class="dd-divider"></div>
                    <button class="dd-item dd-logout" onclick="document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Panel de Control</h4>
            <button class="btn-nuevo" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Producto
            </button>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(229,9,20,0.15); color: var(--accent-primary);"><i class="bi bi-box-seam"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="totalProductos">{{ $totalProductos }}</div>
                        <div class="stat-label">Total Productos</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(9,132,227,0.15); color: var(--accent-info);"><i class="bi bi-stack"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="stockTotal">{{ number_format($stockTotal) }}</div>
                        <div class="stat-label">Unidades en Almacén</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" id="cardAlertas">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(253,203,110,0.15); color: var(--accent-warning);"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="alertasStock">{{ $alertasStock }}</div>
                        <div class="stat-label">Alertas de Bajo Stock</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" id="cardCapital">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(0,184,148,0.15); color: var(--accent-success);"><i class="bi bi-currency-dollar"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="capitalInvertido">${{ number_format($capitalInvertido, 2) }}</div>
                        <div class="stat-label">Capital Invertido</div>
                        <div class="stat-sub" style="font-size:0.75rem;color:#888;margin-top:2px;">Eqv: Bs. {{ number_format($capitalInvertidoBs, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" id="cardTasa">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(229,9,20,0.15); color: var(--accent-primary);"><i class="bi bi-bank"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="tasaBcv">{{ number_format($tasaBcv, 2) }}</div>
                        <div class="stat-label">Tasa BCV (Bs/USD)</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="oswa-bot-card">
            <h5><i class="bi bi-robot me-2"></i>Pregunta a OSWA-Bot</h5>
            <div class="d-flex gap-2">
                <input type="text" class="bot-input" id="botInput" placeholder="¿Último movimiento? ¿Qué se vence pronto?" onkeypress="if(event.key==='Enter') enviarBot()">
                <button class="btn btn-primary" style="background: var(--accent-primary); border: none; padding: 0 20px;" onclick="enviarBot()"><i class="bi bi-send"></i></button>
            </div>
            <div class="bot-response" id="botResponse"></div>
        </div>
        
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Busca productos..." oninput="filtrarTabla(this.value)">
        </div>
        <div class="search-commands">
            <span class="search-command" onclick="ejecutarComando('bajo stock')">"bajo stock"</span>
            <span class="search-command" onclick="ejecutarComando('total')">"total"</span>
            <span class="search-command" onclick="location.reload()">"reset"</span>
        </div>
        
        <div class="inventory-table">
            <div class="table-header">
                <div><i class="bi bi-table me-2"></i>Lista de Productos</div>
                <span style="color:#e5e5e5;font-weight:500;">{{ $productos->count() }} productos</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="productTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">Foto</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th class="text-center">Stock</th>
                            @if($esAdmin)<th class="text-center" style="color:#e5e5e5;text-transform:uppercase;letter-spacing:1px;font-size:0.75rem;">Acciones</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr data-producto-id="{{ $producto->id }}" data-stock="{{ $producto->stock }}" class="{{ $producto->stock <= 2 ? 'row-critical' : ($producto->stock <= 5 ? 'row-glow' : 'row-normal') }}">
                            <td class="text-center">
                                @if($producto->imagen)
                                    {{-- LÓGICA MÁGICA: VERIFICAR SI ES URL O ARCHIVO LOCAL --}}
                                    @if(filter_var($producto->imagen, FILTER_VALIDATE_URL))
                                        <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                @else
                                    <div style="width: 45px; height: 45px; background: #222; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-image text-muted"></i></div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="product-name">{{ $producto->nombre }}</div>
                                    <div class="product-code">{{ $producto->codigo }}</div>
                                </div>
                            </td>
                            <td><span class="category-badge">{{ $producto->categoria }}</span></td>
                            <td><span class="price-tag">${{ number_format($producto->precio, 2) }}</span></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button class="stock-btn stock-btn-minus" onclick="ajustarStock({{ $producto->id }}, 'restar')"><i class="bi bi-dash"></i></button>
                                    <input type="number" class="stock-input" value="{{ $producto->stock }}" min="0" id="stock-{{ $producto->id }}" onchange="ajustarStockDirecto({{ $producto->id }}, this.value)">
                                    <button class="stock-btn stock-btn-plus" onclick="ajustarStock({{ $producto->id }}, 'sumar')"><i class="bi bi-plus"></i></button>
                                    <span class="stock-badge {{ $producto->stock <= 5 ? ($producto->stock <= 2 ? 'stock-critical' : 'stock-low') : 'stock-normal' }}" id="stock-badge-{{ $producto->id }}">{{ $producto->stock <= 5 ? ($producto->stock <= 2 ? 'Crítico' : 'Bajo') : 'Normal' }}</span>
                                </div>
                            </td>
                            @if($esAdmin)
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <button class="action-btn action-btn-edit" title="Editar" onclick="editarProducto({{ $producto->id }})"><i class="bi bi-pencil"></i></button>
                                    <button class="action-btn action-btn-transfer" title="Transferir" onclick="abrirTransferencia({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->stock }})"><i class="bi bi-truck"></i></button>
                                    <button class="action-btn action-btn-order" title="Orden de Compra" onclick="generarOrden({{ $producto->id }}, '{{ $producto->nombre }}')"><i class="bi bi-cart"></i></button>
                                    <button class="action-btn action-btn-delete" title="Eliminar" onclick="eliminarProducto({{ $producto->id }})"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ $esAdmin ? 6 : 5 }}" class="text-center py-5"><i class="bi bi-inbox" style="font-size: 2rem;"></i><p class="mt-2">Sin productos</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="charts-grid">
            <div class="chart-card">
                <h5><i class="bi bi-pie-chart me-2"></i>Distribución por Categorías</h5>
                <div class="chart-container">
                    <canvas id="categoriaChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h5><i class="bi bi-pie-chart me-2"></i>Estado del Inventario</h5>
                <div class="chart-container">
                    <canvas id="stockChart"></canvas>
                </div>
                <div class="d-flex justify-content-around mt-3 text-center">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-success);" id="chartSaludable">{{ $stockSaludable }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Saludable</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-danger);" id="chartCritico">{{ $stockCritico }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Crítico</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <a href="{{ route('escaner') }}" class="scanner-fab" title="Escáner"><i class="bi bi-upc-scan"></i></a>
    
    <div class="modal fade" id="nuevoProductoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo Producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="nuevoProductoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="imagen_url" id="imagenUrlInput">

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 text-center" id="cajaPreviewInternet" style="display: none;">
                                <p class="form-label text-success fw-bold mb-2"><i class="bi bi-cloud-check me-1"></i>¡Foto detectada por el escáner!</p>
                                <img id="previewFotoInternet" src="" style="width: 120px; height: 120px; object-fit: cover; border-radius: 4px; border: 3px solid var(--accent-primary); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Código de Barras</label>
                                <input type="text" class="form-control" name="codigo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <input type="text" class="form-control" name="marca">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría</label>
                                <input type="text" class="form-control" name="categoria" value="General">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio ($)</label>
                                <input type="number" class="form-control" name="precio" step="0.01" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Inicial</label>
                                <input type="number" class="form-control" name="stock" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" name="fecha_vencimiento">
                            </div>
                            
                            <div class="col-md-6" id="cajaFotoPc">
                                <label class="form-label">Foto del Producto (Desde tu PC)</label>
                                <input type="file" class="form-control" name="imagen" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--accent-primary); border: none;">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editarProductoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="editarProductoForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" id="editCodigo" name="codigo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marca</label>
                                <input type="text" class="form-control" id="editMarca" name="marca">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="editCategoria" name="categoria">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" id="editPrecio" name="precio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Vencimiento</label>
                                <input type="date" class="form-control" id="editVencimiento" name="fecha_vencimiento">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="transferenciaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-truck me-2"></i>Transferir a Sucursal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="transferenciaForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <input type="text" class="form-control" id="transferProducto" readonly>
                            <input type="hidden" id="transferProductoId" name="producto_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Actual</label>
                            <input type="text" class="form-control" id="transferStock" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sucursal Destino</label>
                            <select class="form-select" id="sucursalDestino" name="sucursal" required>
                                <option value="">Seleccionar...</option>
                                <option value="Caracas">Caracas</option>
                                <option value="Maracaibo">Maracaibo</option>
                                <option value="Valencia">Valencia</option>
                                <option value="Barquisimeto">Barquisimeto</option>
                                <option value="San Cristóbal">San Cristóbal</option>
                                <option value="Mérida">Mérida</option>
                                <option value="Puerto La Cruz">Puerto La Cruz</option>
                                <option value="Maturín">Maturín</option>
                                <option value="Ciudad Guayana">Ciudad Guayana</option>
                                <option value="Coro">Coro</option>
                                <option value="Cumaná">Cumaná</option>
                                <option value="Guanare">Guanare</option>
                                <option value="San Juan de los Morros">San Juan de los Morros</option>
                                <option value="Trujillo">Trujillo</option>
                                <option value="San Felipe">San Felipe</option>
                                <option value="Barcelona">Barcelona</option>
                                <option value="Porlamar">Porlamar</option>
                                <option value="La Guaira">La Guaira</option>
                                <option value="San Fernando de Apure">San Fernando de Apure</option>
                                <option value="Puerto Ayacucho">Puerto Ayacucho</option>
                                <option value="Tucupita">Tucupita</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad a Enviar</label>
                            <input type="number" class="form-control" id="transferCantidad" name="cantidad" min="1" required>
                        </div>
                        <div class="mb-3">
                            <div class="map-label"><i class="bi bi-geo-alt"></i>Ruta de transferencia (Origen: Barinas)</div>
                            <div id="map"></div>
                            <div id="route-stats" class="route-stats" style="display:none;">
                                <div class="stat-item">
                                    <span class="stat-label">Distancia</span>
                                    <span class="stat-value" id="stat-distancia">0 km</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Costo Flete</span>
                                    <span class="stat-value stat-cost" id="stat-flete">$0.00</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Tiempo Est.</span>
                                    <span class="stat-value" id="stat-tiempo">0 h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-truck me-1"></i>Procesar Transferencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="ordenCompraModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cart me-2"></i>Orden de Compra</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-center">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 4rem; color: var(--accent-info);"></i>
                    <p class="mt-3">Generando PDF de reorder...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-info" onclick="descargarOrden()"><i class="bi bi-download me-1"></i>Descargar PDF</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        
        function toggleSidebar() {
            const nav = document.getElementById('topbarNav');
            nav.classList.toggle('show');
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
                html: `<div style="text-align:left;background:#1a1a2e;border-radius:8px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div style="width:48px;height:48px;background:#E50914;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div style="font-size:1.1rem;font-weight:700;color:#fff;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                            <div style="color:#888;font-size:0.85rem;">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        </div>
                    </div>
                    <div style="border-top:1px solid #2a2a2a;padding-top:12px;">
                        <p style="margin:6px 0;color:#888;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;">ROL</p>
                        <p style="margin:0 0 12px;color:#E50914;font-weight:700;font-size:1rem;">{{ auth()->user()?->rol ?? 'empleado' }}</p>
                        <p style="margin:6px 0;color:#888;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;">MIEMBRO DESDE</p>
                        <p style="margin:0;color:#fff;font-size:0.95rem;">{{ auth()->user()?->created_at ? auth()->user()->created_at->format('d/m/Y') : 'No disponible' }}</p>
                    </div>
                </div>`,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#E50914',
                background: '#141414',
                color: '#fff'
            });
        }
        
        function mostrarAtajos() {
            document.getElementById('userDropdownMenu').style.display = 'none';
            Swal.fire({
                title: 'Atajos de Teclado',
                html: `<div style="text-align:left;background:#1a1a2e;border-radius:8px;padding:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#222;border-radius:6px;margin-bottom:8px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="bi bi-search" style="color:#E50914;font-size:1.1rem;"></i>
                            <span style="color:#e5e5e5;font-size:0.9rem;">Buscar productos</span>
                        </div>
                        <kbd style="background:#333;color:#fff;padding:4px 10px;border-radius:4px;font-size:0.8rem;font-family:monospace;">Alt + B</kbd>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#222;border-radius:6px;margin-bottom:8px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="bi bi-upc-scan" style="color:#E50914;font-size:1.1rem;"></i>
                            <span style="color:#e5e5e5;font-size:0.9rem;">Abrir Escáner</span>
                        </div>
                        <kbd style="background:#333;color:#fff;padding:4px 10px;border-radius:4px;font-size:0.8rem;font-family:monospace;">Alt + E</kbd>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#222;border-radius:6px;margin-bottom:8px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="bi bi-truck" style="color:#E50914;font-size:1.1rem;"></i>
                            <span style="color:#e5e5e5;font-size:0.9rem;">Transferencias</span>
                        </div>
                        <kbd style="background:#333;color:#fff;padding:4px 10px;border-radius:4px;font-size:0.8rem;font-family:monospace;">Alt + T</kbd>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#222;border-radius:6px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="bi bi-plus-circle" style="color:#E50914;font-size:1.1rem;"></i>
                            <span style="color:#e5e5e5;font-size:0.9rem;">Nuevo Producto</span>
                        </div>
                        <kbd style="background:#333;color:#fff;padding:4px 10px;border-radius:4px;font-size:0.8rem;font-family:monospace;">Alt + N</kbd>
                    </div>
                </div>`,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#E50914',
                background: '#141414',
                color: '#fff'
            });
        }
        
        function cambiarCuenta(e) {
            e.preventDefault();
            document.getElementById('userDropdownMenu').style.display = 'none';
            Swal.fire({
                title: 'Cambiando de cuenta...',
                text: 'Cerrando sesión actual de forma segura',
                icon: 'info',
                timer: 1500,
                background: '#181818',
                color: '#fff',
                showConfirmButton: false
            }).then(() => {
                document.getElementById('logout-form').submit();
            });
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                switch(e.key.toLowerCase()) {
                    case 'b':
                        e.preventDefault();
                        document.getElementById('searchInput').focus();
                        break;
                    case 'e':
                        e.preventDefault();
                        document.querySelector('.scanner-fab').click();
                        break;
                    case 't':
                        e.preventDefault();
                        showNotification('info', 'Selecciona un producto y haz clic en el botón de transferencia');
                        break;
                    case 'n':
                        e.preventDefault();
                        new bootstrap.Modal(document.getElementById('nuevoProductoModal')).show();
                        break;
                }
            }
        });
        
        const categoriaData = @json($categorias);
        const categoriaChart = new Chart(document.getElementById('categoriaChart'), {
            type: 'doughnut',
            data: { 
                labels: Object.keys(categoriaData).length ? Object.keys(categoriaData) : ['Sin datos'], 
                datasets: [{ data: Object.keys(categoriaData).length ? Object.values(categoriaData) : [1], backgroundColor: ['#E50914','#00b894','#fdcb6e','#0984e3','#e74c3c'], borderWidth: 0 }] 
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#b3b3b3' } } } }
        });
        
        const stockChart = new Chart(document.getElementById('stockChart'), {
            type: 'pie',
            data: { labels: ['Stock Saludable', 'Stock Crítico'], datasets: [{ data: [{{ $stockSaludable }}, {{ $stockCritico }}], backgroundColor: ['#00b894','#e74c3c'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        
        function initTheme() {
            const saved = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', saved);
        }
        
        // ✨ LA INTELIGENCIA ARTIFICIAL DEL FORMULARIO ✨
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const codigo = urlParams.get('nuevo_codigo');
            const nombre = urlParams.get('nuevo_nombre');
            const imagenUrl = urlParams.get('nueva_imagen');
            
            if (codigo) {
                document.querySelector('#nuevoProductoModal input[name="codigo"]').value = codigo;
                if (nombre) {
                    document.querySelector('#nuevoProductoModal input[name="nombre"]').value = nombre;
                }
                
                // Si el escáner nos mandó una foto de internet:
                if (imagenUrl) {
                    // 1. Guardamos la URL oculta para el controlador
                    document.getElementById('imagenUrlInput').value = imagenUrl;
                    // 2. Ponemos la foto en el cuadro de preview
                    document.getElementById('previewFotoInternet').src = imagenUrl;
                    // 3. Mostramos el cuadro de preview
                    document.getElementById('cajaPreviewInternet').style.display = 'block';
                    // 4. Ocultamos el botón de subir desde la PC
                    document.getElementById('cajaFotoPc').style.display = 'none';
                } else {
                    // Si no hay foto de internet, nos aseguramos que se vea el botón de PC
                    document.getElementById('cajaPreviewInternet').style.display = 'none';
                    document.getElementById('cajaFotoPc').style.display = 'block';
                }
                
                document.querySelector('#nuevoProductoModal input[name="stock"]').value = 1;
                
                new bootstrap.Modal(document.getElementById('nuevoProductoModal')).show();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        initTheme();
        
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
        
        function toggleTheme() {
            const nuevo = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', nuevo);
            localStorage.setItem('theme', nuevo);
        }
        
        function showNotification(icon, title, isError = false) {
            Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 2000, background: isError ? '#dc3545' : '#00b894', color: '#fff' });
        }
        
        async function ajustarStock(productoId, accion) {
            const stockInput = document.getElementById('stock-' + productoId);
            const isIncrement = accion === 'sumar';
            try {
                const response = await fetch('{{ route('ajustar.stock') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id: productoId, accion: accion }) });
                const data = await response.json();
                if (data.success) {
                    stockInput.value = data.nuevo_stock;
                    
                    const floatEl = document.createElement('span');
                    floatEl.innerText = isIncrement ? '+1' : (data.nuevo_stock < parseInt(stockInput.value) - 1 ? '-1' : '-1');
                    floatEl.style.cssText = 'position:absolute; z-index:100; font-size:1rem; font-weight:bold; pointer-events:none;';
                    floatEl.className = isIncrement ? 'float-plus' : 'float-minus';
                    floatEl.innerText = isIncrement ? '+1' : '-1';
                    stockInput.parentElement.appendChild(floatEl);
                    setTimeout(() => floatEl.remove(), 800);
                    
                    stockInput.classList.add(isIncrement ? 'flash-green' : 'flash-red');
                    setTimeout(() => stockInput.classList.remove('flash-green', 'flash-red'), 400);
                    
                    actualizarTodo(data);
                    const row = document.querySelector('tr[data-producto-id="' + productoId + '"]');
                    row.className = data.nuevo_stock <= 2 ? 'row-critical' : (data.nuevo_stock <= 5 ? 'row-glow' : 'row-normal');
                    actualizarBadge(document.getElementById('stock-badge-' + productoId), data.nuevo_stock);
                    showNotification('success', data.nuevo_stock === 0 ? '¡Stock agotado!' : 'Stock actualizado');
                }
            } catch (e) { showNotification('error', 'Error de conexión', true); }
        }
        
        async function ajustarStockDirecto(productoId, valor) {
            const nuevoValor = Math.max(0, parseInt(valor) || 0);
            try {
                const response = await fetch('{{ route('ajustar.stock') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id: productoId, accion: 'set', valor: nuevoValor }) });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('stock-' + productoId).value = data.nuevo_stock;
                    actualizarTodo(data);
                    actualizarBadge(document.getElementById('stock-badge-' + productoId), data.nuevo_stock);
                    showNotification('success', 'Stock actualizado');
                }
            } catch (e) { showNotification('error', 'Error', true); }
        }
        
        function actualizarTodo(data) {
            document.getElementById('totalProductos').textContent = data.total_productos || {{ $totalProductos }};
            document.getElementById('stockTotal').textContent = Number(data.stock_total).toLocaleString();
            document.getElementById('alertasStock').textContent = data.alertas_stock;
            document.getElementById('capitalInvertido').textContent = '$' + Number(data.capital_invertido).toLocaleString('en', { minimumFractionDigits: 2 });
            document.getElementById('chartSaludable').textContent = data.stock_saludable;
            document.getElementById('chartCritico').textContent = data.stock_critico;
            stockChart.data.datasets[0].data = [data.stock_saludable, data.stock_critico];
            stockChart.update();
        }
        
        function actualizarBadge(badge, stock) {
            badge.className = 'stock-badge';
            if (stock <= 2) { badge.classList.add('stock-critical'); badge.textContent = 'Crítico'; }
            else if (stock <= 5) { badge.classList.add('stock-low'); badge.textContent = 'Bajo'; }
            else { badge.classList.add('stock-normal'); badge.textContent = 'Normal'; }
        }
        
        function filtrarTabla(texto) {
            document.querySelectorAll('#productTable tbody tr').forEach(row => {
                const nombre = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
                const codigo = row.querySelector('.product-code')?.textContent.toLowerCase() || '';
                row.style.display = (nombre.includes(texto.toLowerCase()) || codigo.includes(texto.toLowerCase())) ? '' : 'none';
            });
        }
        
        function ejecutarComando(cmd) {
            if (cmd === 'bajo stock') {
                document.querySelectorAll('#productTable tbody tr').forEach(row => { row.style.display = parseInt(row.dataset.stock) <= 5 ? '' : 'none'; });
                showNotification('info', 'Mostrando bajo stock');
            } else if (cmd === 'total') {
                const card = document.getElementById('cardCapital');
                card.classList.add('highlight');
                showNotification('success', 'Capital resaltado');
                setTimeout(() => card.classList.remove('highlight'), 3000);
            }
        }
        
        async function enviarBot() {
            const pregunta = document.getElementById('botInput').value.trim();
            if (!pregunta) return;
            const responseDiv = document.getElementById('botResponse');
            responseDiv.className = 'bot-response show';
            responseDiv.innerHTML = '<i class="bi bi-robot me-2"></i>Buscando...';
            try {
                const response = await fetch('{{ route('oswa.bot') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ pregunta: pregunta }) });
                const data = await response.json();
                if (data.success) responseDiv.innerHTML = '<i class="bi bi-robot me-2"></i>' + data.respuesta;
                else responseDiv.innerHTML = '<i class="bi bi-emoji-frown me-2"></i>No entendí';
            } catch (e) { responseDiv.innerHTML = '<i class="bi bi-emoji-frown me-2"></i>Error'; }
        }
        
        async function eliminarProducto(id) {
            const confirm = await Swal.fire({ title: '¿Eliminar?', text: 'No se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí', cancelButtonText: 'Cancelar' });
            if (confirm.isConfirmed) {
                try {
                    const response = await fetch('{{ route('eliminar.producto') }}', { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id: id }) });
                    const data = await response.json();
                    if (data.success) { showNotification('success', 'Eliminado'); setTimeout(() => location.reload(), 1500); }
                    else showNotification('error', data.message, true);
                } catch (e) { showNotification('error', 'Error', true); }
            }
        }
        
        function editarProducto(id) {
            const producto = {!! $productos->toJson() !!};
            const prod = producto.find(p => p.id === id);
            if (!prod) return;
            
            document.getElementById('editId').value = prod.id;
            document.getElementById('editNombre').value = prod.nombre;
            document.getElementById('editCodigo').value = prod.codigo;
            document.getElementById('editMarca').value = prod.marca || '';
            document.getElementById('editCategoria').value = prod.categoria || '';
            document.getElementById('editPrecio').value = prod.precio;
            document.getElementById('editVencimiento').value = prod.fecha_vencimiento || '';
            
            new bootstrap.Modal(document.getElementById('editarProductoModal')).show();
        }
        
        function abrirTransferencia(id, nombre, stock) {
            document.getElementById('transferProducto').value = nombre;
            document.getElementById('transferProductoId').value = id;
            document.getElementById('transferStock').value = stock;
            document.getElementById('transferCantidad').max = stock;
            document.getElementById('transferCantidad').value = 1;
            document.getElementById('sucursalDestino').value = '';
            new bootstrap.Modal(document.getElementById('transferenciaModal')).show();
            
            if (typeof google !== 'undefined' && google.maps) {
                initTransferMap();
            }
        }
        
        function generarOrden(id, nombre) {
            window.location.href = '/orden-compra/' + id;
        }
        
        function descargarOrden() {
            showNotification('success', 'PDF descargado');
            bootstrap.Modal.getInstance(document.getElementById('ordenCompraModal')).hide();
        }
        
        document.getElementById('transferenciaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const productoId = document.getElementById('transferProductoId').value;
            const sucursal = document.getElementById('sucursalDestino').value;
            const cantidad = parseInt(document.getElementById('transferCantidad').value);
            const stockActual = parseInt(document.getElementById('transferStock').value);
            
            if (cantidad > stockActual) {
                showNotification('error', 'Cantidad mayor al stock disponible', true);
                return;
            }
            
            try {
                const response = await fetch('{{ route('transferir.producto') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ producto_id: productoId, cantidad: cantidad, sucursal: sucursal })
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('transferenciaModal')).hide();
                    const distanciaKm = data.distancia || 0;
                    const costoFlete = data.costo_flete || 0;
                    const pdfUrl = `{{ route('transferencia.pdf') }}?producto=${encodeURIComponent(data.producto)}&cantidad=${cantidad}&sucursal=${encodeURIComponent(sucursal)}&distancia=${distanciaKm}&costo=${costoFlete}&fecha=${encodeURIComponent(data.fecha)}`;
                    Swal.fire({
                        icon: 'success',
                        title: 'Transferencia Procesada',
                        html: `<div style="text-align:left;font-size:0.95rem;">
                            <p style="margin:4px 0;"><strong style="color:#888;">Producto:</strong> ${data.producto}</p>
                            <p style="margin:4px 0;"><strong style="color:#888;">Cantidad:</strong> ${cantidad} unidades</p>
                            <p style="margin:4px 0;"><strong style="color:#888;">Destino:</strong> ${sucursal}</p>
                            <p style="margin:4px 0;"><strong style="color:#888;">Distancia:</strong> ${distanciaKm} km</p>
                            <p style="margin:4px 0;"><strong style="color:#00b894;">Costo Flete:</strong> $${costoFlete.toFixed(2)}</p>
                            <hr style="border-color:#333;margin:12px 0;">
                            <a href="${pdfUrl}" target="_blank" id="btn-pdf-transfer" style="display:inline-block;padding:10px 24px;background:#3a3a5c;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:0.9rem;transition:all 0.3s;">📄 Descargar Guía de Despacho (PDF)</a>
                        </div>`,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#E50914',
                        background: '#1a1a2e',
                        color: '#fff',
                        showClass: { popup: 'animate__animated animate__fadeInDown' },
                        hideClass: { popup: 'animate__animated animate__fadeOutUp' }
                    });
                    setTimeout(() => location.reload(), 3000);
                } else {
                    showNotification('error', data.message, true);
                }
            } catch (e) {
                showNotification('error', 'Error al procesar transferencia', true);
            }
        });
        
        document.getElementById('editarProductoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route('actualizar.producto') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editarProductoModal')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Producto guardado correctamente',
                        background: 'var(--bg-card)',
                        color: 'var(--text-primary)',
                        confirmButtonColor: 'var(--accent-primary)',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', data.message, true);
                }
            } catch (e) {
                showNotification('error', 'Error al actualizar', true);
            }
        });
        
        document.getElementById('nuevoProductoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route('guardar.producto') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData });
                const data = await response.json();
                if (data.success) {
                    showNotification('success', 'Producto guardado');
                    bootstrap.Modal.getInstance(document.getElementById('nuevoProductoModal')).hide();
                    this.reset();
                    setTimeout(() => location.reload(), 1500);
                } else { showNotification('error', data.message, true); }
            } catch (e) { showNotification('error', 'Error al guardar', true); }
        });
        
    </script>
    
    <script>
        // GOOGLE MAPS: Coordenadas de sucursales
        const BARINAS = { lat: 8.6226, lng: -70.2039 };
        const sucursalesCoords = {
            'Caracas': { lat: 10.4806, lng: -66.8983, dist: 500 },
            'Maracaibo': { lat: 10.6427, lng: -71.6125, dist: 450 },
            'Valencia': { lat: 10.1620, lng: -68.0077, dist: 350 },
            'Barquisimeto': { lat: 10.0678, lng: -69.3474, dist: 280 },
            'San Cristóbal': { lat: 7.7669, lng: -72.2250, dist: 320 },
            'Mérida': { lat: 8.5912, lng: -71.1434, dist: 170 },
            'Puerto La Cruz': { lat: 10.2167, lng: -64.6333, dist: 820 },
            'Maturín': { lat: 9.7458, lng: -63.1767, dist: 950 },
            'Ciudad Guayana': { lat: 8.2913, lng: -62.7092, dist: 1100 },
            'Coro': { lat: 11.4045, lng: -69.6734, dist: 480 },
            'Cumaná': { lat: 10.4530, lng: -64.1753, dist: 870 },
            'Guanare': { lat: 9.0417, lng: -69.7422, dist: 120 },
            'San Juan de los Morros': { lat: 9.9089, lng: -67.3556, dist: 270 },
            'Trujillo': { lat: 9.3694, lng: -70.4403, dist: 150 },
            'San Felipe': { lat: 10.3367, lng: -68.7428, dist: 300 },
            'Barcelona': { lat: 10.1344, lng: -64.7011, dist: 830 },
            'Porlamar': { lat: 10.9578, lng: -63.8500, dist: 950 },
            'La Guaira': { lat: 10.5950, lng: -66.9467, dist: 510 },
            'San Fernando de Apure': { lat: 7.8939, lng: -67.4742, dist: 380 },
            'Puerto Ayacucho': { lat: 5.6639, lng: -67.6236, dist: 720 },
            'Tucupita': { lat: 9.0647, lng: -62.0517, dist: 1050 },
        };
        
        let transferMap = null;
        let originMarker = null;
        let destMarker = null;
        let routeLine = null;
        let animInterval = null;
        let allMarkers = [];
        let isAnimating = false;
        
        function initTransferMap() {
            const mapContainer = document.getElementById('map');
            if (!mapContainer || !navigator.onLine) return;
            
            if (transferMap) {
                transferMap.setCenter(BARINAS);
                transferMap.setZoom(6);
                if (destMarker) destMarker.setMap(null);
                if (routeLine) routeLine.setMap(null);
                document.getElementById('route-stats').style.display = 'none';
                return;
            }
            
            transferMap = new google.maps.Map(mapContainer, {
                center: BARINAS,
                zoom: 6,
                styles: [
                    { elementType: "geometry", stylers: [{ color: "#1d2c4d" }] },
                    { elementType: "labels.text.fill", stylers: [{ color: "#8ec3b9" }] },
                    { elementType: "labels.text.stroke", stylers: [{ color: "#1a3646" }] },
                    { featureType: "administrative.country", elementType: "geometry.stroke", stylers: [{ color: "#4b6878" }] },
                    { featureType: "water", elementType: "geometry.fill", stylers: [{ color: "#0e1626" }] },
                    { featureType: "poi", elementType: "geometry", stylers: [{ color: "#283d6a" }] },
                    { featureType: "road", elementType: "geometry", stylers: [{ color: "#304a7d" }] },
                    { featureType: "transit", elementType: "geometry", stylers: [{ color: "#2f3948" }] },
                    { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#0e1626" }] },
                ],
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: false,
            });
            
            originMarker = new google.maps.Marker({
                position: BARINAS,
                map: transferMap,
                title: 'Barinas (Origen)',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 14,
                    fillColor: '#00b894',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                }
            });
            
            const allBounds = new google.maps.LatLngBounds();
            allBounds.extend(BARINAS);
            
            for (const [nombre, coords] of Object.entries(sucursalesCoords)) {
                const marker = new google.maps.Marker({
                    position: { lat: coords.lat, lng: coords.lng },
                    map: transferMap,
                    title: nombre,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 5,
                        fillColor: '#E50914',
                        fillOpacity: 0.6,
                        strokeColor: '#ffffff',
                        strokeWeight: 1,
                    }
                });
                allMarkers.push(marker);
                allBounds.extend({ lat: coords.lat, lng: coords.lng });
            }
            
            transferMap.fitBounds(allBounds, { top: 30, bottom: 30, left: 30, right: 30 });
            
            document.getElementById('sucursalDestino').addEventListener('change', function() {
                const ciudad = this.value;
                if (!ciudad) {
                    if (destMarker) destMarker.setMap(null);
                    if (routeLine) routeLine.setMap(null);
                    document.getElementById('route-stats').style.display = 'none';
                    allMarkers.forEach(m => m.setMap(transferMap));
                    if (originMarker) originMarker.setMap(transferMap);
                    transferMap.fitBounds(allBounds, { top: 30, bottom: 30, left: 30, right: 30 });
                    return;
                }
                const destino = sucursalesCoords[ciudad];
                if (!destino || !transferMap) return;
                
                allMarkers.forEach(m => m.setMap(null));
                
                if (destMarker) destMarker.setMap(null);
                if (routeLine) routeLine.setMap(null);
                
                destMarker = new google.maps.Marker({
                    position: { lat: destino.lat, lng: destino.lng },
                    map: transferMap,
                    title: ciudad,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 16,
                        fillColor: '#E50914',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 3,
                    }
                });
                
                routeLine = new google.maps.Polyline({
                    path: [
                        { lat: BARINAS.lat, lng: BARINAS.lng },
                        { lat: destino.lat, lng: destino.lng }
                    ],
                    geodesic: true,
                    strokeColor: '#E50914',
                    strokeOpacity: 1,
                    strokeWeight: 3,
                    icons: [{
                        icon: { path: 'M 0,-1 0,1', strokeColor: '#ff3d47', strokeWeight: 2 },
                        offset: '0',
                        repeat: '20px'
                    }]
                });
                routeLine.setMap(transferMap);
                
                const routeBounds = new google.maps.LatLngBounds();
                routeBounds.extend(BARINAS);
                routeBounds.extend({ lat: destino.lat, lng: destino.lng });
                transferMap.fitBounds(routeBounds, { top: 50, bottom: 50, left: 50, right: 50 });
                
                animateStats(destino.dist);
            });
        }
        
        function animateStats(distancia) {
            const costoFlete = distancia * 0.25;
            const tiempoEst = distancia / 80;
            
            const statsPanel = document.getElementById('route-stats');
            statsPanel.style.display = 'grid';
            
            const distEl = document.getElementById('stat-distancia');
            const fleteEl = document.getElementById('stat-flete');
            const tiempoEl = document.getElementById('stat-tiempo');
            
            if (animInterval) clearInterval(animInterval);
            
            let current = 0;
            const step = distancia / 40;
            const duration = 800;
            const intervalTime = duration / 40;
            
            animInterval = setInterval(() => {
                current += step;
                if (current >= distancia) {
                    current = distancia;
                    clearInterval(animInterval);
                }
                distEl.textContent = Math.round(current) + ' km';
                fleteEl.textContent = '$' + (current * 0.25).toFixed(2);
                tiempoEl.textContent = (current / 80).toFixed(1) + ' h';
            }, intervalTime);
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnxMWZA56z9F_4RsHWVEnx2wWnvilMA0Q"></script>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>