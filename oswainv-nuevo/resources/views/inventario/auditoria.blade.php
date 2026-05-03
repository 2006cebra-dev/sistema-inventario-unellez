<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Auditoría Kardex - OSWA Inv</title>
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
        
        .audit-table { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
        .audit-table thead { background: #222; }
        .audit-table th { padding: 1rem 1.5rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.8rem; }
        .audit-table td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
        .audit-table tbody tr { background: var(--bg-card); transition: background 0.2s ease; }
        .audit-table tbody tr:hover { background: #222; }
        
        body[data-theme="dark"] .audit-table,
        body[data-theme="dark"] .audit-table th,
        body[data-theme="dark"] .audit-table td,
        body[data-theme="dark"] .audit-table thead th,
        body[data-theme="dark"] .audit-table tbody tr {
            background-color: #181818 !important;
            color: #ffffff !important;
            border-color: #2b2b2b !important;
        }
        body[data-theme="dark"] .audit-table thead th {
            background-color: #222 !important;
            color: #b3b3b3 !important;
        }
        body[data-theme="dark"] .audit-table tbody tr:hover {
            background-color: #222 !important;
        }

        .type-entrada { background: rgba(0,184,148,0.15); color: var(--accent-success); padding: 4px 10px; border-radius: 4px; font-weight: 600; }
        .type-salida { background: rgba(231,76,60,0.15); color: var(--accent-danger); padding: 4px 10px; border-radius: 4px; font-weight: 600; }

        .firma-valid { 
            background: rgba(0,184,148,0.2); color: #10b981; padding: 6px 14px; border-radius: 4px; font-weight: 600; font-size: 0.85rem;
            border: 1px solid #10b981;
            box-shadow: 0 0 12px rgba(16,185,129,0.3);
            display: inline-flex; align-items: center; gap: 6px;
        }
        .firma-invalid { 
            background: rgba(239,68,68,0.25); color: #ef4444; padding: 6px 14px; border-radius: 4px; font-weight: 700; font-size: 0.85rem;
            border: 1px solid #ef4444;
            animation: pulseAlert 1s infinite;
            box-shadow: 0 0 15px rgba(239,68,68,0.5);
            display: inline-flex; align-items: center; gap: 6px;
        }
        @keyframes pulseAlert {
            0%, 100% { box-shadow: 0 0 10px rgba(239,68,68,0.4); transform: scale(1); }
            50% { box-shadow: 0 0 25px rgba(239,68,68,0.8); transform: scale(1.02); }
        }
        
        .firma-hash { font-family: monospace; font-size: 0.75rem; color: var(--text-secondary); background: var(--bg-input); padding: 4px 8px; border-radius: 4px; }
        
        .user-badge { background: var(--bg-input); padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; }
        
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

            @if(auth()->check() && auth()->user()->rol === 'admin')
                <a href="#" class="text-info"><i class="bi bi-people"></i> Administrar Usuarios</a>
            @endif

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
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-50">
            <div class="d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-file-earmark-text fs-4"></i>
                </div>
                <h2 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">Auditoría de Movimiento</h2>
            </div>
            
            <button onclick="Swal.fire('Próximamente', 'Estamos preparando el código del controlador para exportar a Excel', 'info')" class="btn btn-success d-flex align-items-center gap-2" style="font-weight: 600; padding: 8px 16px;">
                <i class="bi bi-file-earmark-excel fs-5"></i> Exportar CSV
            </button>
        </div>
        
        <div class="table-responsive">
            <div class="audit-table">
                <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                        <th>Firma SHA-256</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimientos as $mov)
                        @php
                            // 1. RECALCULAR HASH EN TIEMPO REAL CON NOMBRES EXACTOS
                            $cadenaActual = $mov->id . $mov->codigo_producto . $mov->tipo . $mov->cantidad . $mov->motivo . $mov->usuario_accion;
                            $hashEnTiempoReal = hash('sha256', $cadenaActual);

                            // 2. SISTEMA DE SEMÁFORO DE INTEGRIDAD (Usando firma_hash)
                            if (empty($mov->firma_hash)) {
                                $estado = 'Antiguo';
                                $claseBadge = 'border-secondary text-secondary';
                                $icono = 'bi-clock-history';
                                $textoFirma = 'SIN FIRMA';
                            } elseif ($mov->firma_hash === $hashEnTiempoReal) {
                                $estado = 'Seguro';
                                $claseBadge = 'border-success text-success';
                                $icono = 'bi-shield-check';
                                $textoFirma = substr($mov->firma_hash, 0, 15) . '...';
                            } else {
                                $estado = 'Alterado';
                                $claseBadge = 'border-danger text-danger bg-danger bg-opacity-10 fw-bold';
                                $icono = 'bi-shield-exclamation';
                                $textoFirma = 'HASH INVÁLIDO';
                            }
                        @endphp

                        <tr>
                            <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div>{{ $mov->codigo_producto }}</div>
                                <small style="color: var(--text-secondary);">{{ $mov->producto?->nombre ?? 'Sin producto' }}</small>
                            </td>
                            <td>
                                <span class="{{ $mov->tipo === 'Entrada' ? 'type-entrada' : 'type-salida' }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td><strong>{{ $mov->cantidad }}</strong></td>
                            <td>{{ $mov->motivo }}</td>
                            <td><span class="user-badge">{{ $mov->usuario_accion }}</span></td>
                            <td class="text-secondary align-middle" style="font-family: monospace; font-size: 0.85rem; letter-spacing: 1px;">
                                @if($estado == 'Alterado')
                                    <span class="text-danger fw-bold">{{ $textoFirma }}</span>
                                @else
                                    {{ $textoFirma }}
                                @endif
                            </td>
                            <td class="align-middle">
                                <button class="btn btn-sm {{ $claseBadge }} d-flex align-items-center bg-transparent" style="cursor: default; border-radius: 6px;">
                                    <i class="bi {{ $icono }} me-2"></i> {{ $estado }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 p-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px;">
            <h6><i class="bi bi-shield-lock me-2"></i>Seguridad de Datos</h6>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Cada movimiento genera una firma digital SHA-256 que incluye: código del producto + tipo + cantidad + fecha/hora.
                Si alguien intenta modificar el historial, la firma ya no coincidirá y el sistema mostrará "Alterada".
            </p>
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
            
            // Antifraud: Check for altered records
            const alteredRows = document.querySelectorAll('.firma-invalid');
            if (alteredRows.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ ALERTA CRÍTICA - POSIBLE FRAUDE',
                    html: `<p style="color:#ff6b6b;font-size:1.1rem;">Se detectaron <strong>${alteredRows.length} registro(s)</strong> alterado(s) en la Base de Datos.</p><p style="color:var(--text-secondary);">La firma SHA-256 no coincide. Existe posibilidad de manipulación directa en la BD.</p>`,
                    background: 'var(--bg-card)',
                    color: 'var(--text-primary)',
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'Investigar',
                    allowOutsideClick: false,
                    backdrop: `
                        rgba(231,76,60,0.3)
                        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Ctext y='50' x='50' font-size='50' text-anchor='middle'%3E⚠️%3C/text%3E%3C/svg%3E")
                        center no-repeat
                    `
                });
            }
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