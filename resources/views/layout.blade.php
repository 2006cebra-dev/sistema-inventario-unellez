<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAV - UNELLEZ | OSWA-INV</title>
    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { 
            --main-bg-light: #f8fafc; 
            --main-bg-dark: #0f172a;
            --accent: #3b82f6; 
        }
        
        body { 
            background-color: var(--main-bg-light); 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            transition: background-color 0.4s ease, color 0.4s ease; 
            overflow-x: hidden;
        }

        /* 📜 SCROLLBAR CUSTOMIZADO */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.6); }

        /* Logo Animado Brillante */
        .logo-animado {
            background: linear-gradient(to right, #3b82f6, #8b5cf6, #06b6d4, #3b82f6);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: shine 3s linear infinite;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        @keyframes shine { to { background-position: 200% center; } }

        /* Icono del Logo Brillando */
        .sidebar h4 i {
            filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.8));
            animation: pulse-glow 2s infinite alternate;
        }
        @keyframes pulse-glow {
            0% { filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.5)); }
            100% { filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.9)); }
        }

        /* 🪟 SIDEBAR DE CRISTAL */
        .sidebar { 
            width: 260px; height: 100vh; 
            background: rgba(30, 41, 59, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: fixed; color: white; transition: 0.3s; z-index: 1000; 
            box-shadow: 4px 0 25px rgba(0,0,0,0.1); 
            border-right: 1px solid rgba(255,255,255,0.05); 
        }
        
        .main-content { margin-left: 260px; padding: 30px; transition: 0.3s; position: relative; z-index: 1; }
        
        .nav-link { 
            color: #94a3b8; padding: 12px 20px; margin: 6px 15px; 
            border-radius: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            position: relative; font-weight: 500;
        }
        
        .nav-link:hover { color: white; background: rgba(255,255,255,0.08); transform: translateX(6px); }
        
        .nav-link.active { 
            color: white; background: rgba(59, 130, 246, 0.15); 
            box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.4), 0 0 15px rgba(59, 130, 246, 0.2); 
            overflow: hidden; font-weight: 700;
        }
        .nav-link.active::before {
            content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
            background: var(--accent); border-radius: 4px 0 0 4px;
            box-shadow: 0 0 10px var(--accent);
        }

        .nav-link i { font-size: 1.2rem; transition: 0.3s; margin-right: 12px; }
        .nav-link:hover i { text-shadow: 0 0 10px rgba(255,255,255,0.5); transform: scale(1.1); } 

        /* ========================================== */
        /* 🌙 MODO OSCURO GLOBAL */
        /* ========================================== */
        body.dark-mode { 
            background-color: var(--main-bg-dark); 
            color: #f1f5f9; 
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.08), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.05), transparent 25%);
            background-attachment: fixed;
        }
        body.dark-mode .sidebar { background: rgba(15, 23, 42, 0.7); }
        
        /* 🚨 REGLAS BLINDADAS PARA EL HISTORIAL Y TABLAS EN MODO OSCURO */
        body.dark-mode .card, 
        body.dark-mode .bg-white { background-color: rgba(30, 41, 59, 0.6) !important; border-color: rgba(255,255,255,0.05) !important; }
        body.dark-mode .text-dark, body.dark-mode .text-muted { color: #cbd5e1 !important; }
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
        body.dark-mode p, body.dark-mode th, body.dark-mode td { color: #f8fafc !important; }
        
        body.dark-mode .table { --bs-table-bg: transparent; --bs-table-color: #f1f5f9; border-color: rgba(255,255,255,0.05); }
        body.dark-mode .bg-light, body.dark-mode thead.bg-light { background-color: rgba(15, 23, 42, 0.5) !important; color: #f1f5f9 !important; }
        body.dark-mode tbody tr { background-color: transparent !important; }
        body.dark-mode .table-hover tbody tr:hover { background-color: rgba(255, 255, 255, 0.05) !important; }

        /* ========================================== */
        /* ☀️ MODO CLARO GLOBAL */
        /* ========================================== */
        body:not(.dark-mode) .sidebar {
            background: rgba(255, 255, 255, 0.95); border-right: 1px solid #e2e8f0; color: #1e293b;
        }
        body:not(.dark-mode) .sidebar .text-white { color: #1e293b !important; }
        body:not(.dark-mode) .sidebar .text-muted { color: #64748b !important; }
        body:not(.dark-mode) .sidebar .logo-animado { 
            background: linear-gradient(to right, #1e40af, #3b82f6, #1e40af); -webkit-background-clip: text;
        }
        body:not(.dark-mode) .nav-link { color: #475569; }
        body:not(.dark-mode) .nav-link:hover { background: rgba(0, 0, 0, 0.05); color: #0f172a; text-shadow: none; }
        body:not(.dark-mode) .nav-link.active { color: white !important; text-shadow: none; background: #3b82f6; }
        body:not(.dark-mode) .sidebar .border-secondary { border-color: #e2e8f0 !important; }
        body:not(.dark-mode) .sidebar > div.mt-auto { background: rgba(0,0,0,0.03) !important; }

        /* Diseño Móvil */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; border-radius: 0 0 20px 20px; }
            .sidebar ul { display: flex; flex-direction: row; overflow-x: auto; padding: 10px; }
            .nav-link { padding: 10px; margin: 2px; font-size: 0.85em; white-space: nowrap; }
            .main-content { margin-left: 0; padding: 20px; }
            .hide-mobile { display: none; }
        }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="p-4 mb-2 text-center">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-box-seam-fill text-primary"></i> 
                <span class="logo-animado">OSWA-INV</span>
            </h4>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 mt-2 hide-mobile" style="font-size: 0.65rem; letter-spacing: 1px;">SISTEMA INTELIGENTE</span>
        </div>
        
        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <a href="/" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Panel de control
                </a>
            </li>
            <li>
                <a href="/escaner" class="nav-link {{ Request::is('escaner') ? 'active' : '' }}">
                    <i class="bi bi-upc-scan"></i> Escáner Móvil
                </a>
            </li>
            <li>
                <a href="/historial" class="nav-link {{ Request::is('historial') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Historial (Kardex)
                </a>
            </li>
            <li>
                <a href="/productos/crear" class="nav-link {{ Request::is('productos/crear') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i> Nuevo Producto
                </a>
            </li>
            <li class="mt-4 ms-3 hide-mobile mb-2">
                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Exportación</small>
            </li>
            <li>
                <a href="/productos/pdf" target="_blank" class="nav-link">
                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Descargar PDF
                </a>
            </li>
        </ul>

        <div class="p-4 mt-auto border-top border-secondary hide-mobile" style="border-color: rgba(255,255,255,0.08) !important; background: rgba(0,0,0,0.1);">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-2 shadow" style="box-shadow: 0 0 10px rgba(59,130,246,0.5) !important;">
                        <i class="bi bi-person-fill text-white"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-bold text-white">{{ Auth::check() ? Auth::user()->name : 'Invitado' }}</p>
                        <p class="mb-0" style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ Auth::check() ? Auth::user()->role : 'UNELLEZ' }}
                        </p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-1">
                    <button id="btnDarkMode" class="btn btn-sm text-warning border-0" title="Alternar Tema">
                        <i class="bi bi-moon-stars-fill fs-5" style="filter: drop-shadow(0 0 5px rgba(245, 158, 11, 0.5)); transition: 0.3s;"></i>
                    </button>

                    @auth
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm text-danger border-0" title="Cerrar Sesión">
                            <i class="bi bi-box-arrow-right fs-5" style="filter: drop-shadow(0 0 5px rgba(239, 68, 68, 0.5)); transition: 0.3s;"></i>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        @yield('contenido')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const btnDarkMode = document.getElementById('btnDarkMode');
        const iconDarkMode = btnDarkMode.querySelector('i');
        
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            iconDarkMode.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            btnDarkMode.classList.replace('text-warning', 'text-light');
            iconDarkMode.style.filter = "drop-shadow(0 0 5px rgba(255, 255, 255, 0.5))";
        }

        btnDarkMode.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            
            if (isDark) {
                localStorage.setItem('theme', 'dark');
                iconDarkMode.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
                btnDarkMode.classList.replace('text-warning', 'text-light');
                iconDarkMode.style.filter = "drop-shadow(0 0 5px rgba(255, 255, 255, 0.5))";
            } else {
                localStorage.setItem('theme', 'light');
                iconDarkMode.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
                btnDarkMode.classList.replace('text-light', 'text-warning');
                iconDarkMode.style.filter = "drop-shadow(0 0 5px rgba(245, 158, 11, 0.5))";
            }

            // 🪄 ¡MAGIA! Actualizamos el color de las gráficas en vivo SIN recargar la página
            if (typeof Chart !== 'undefined') {
                Chart.defaults.color = isDark ? '#9ca3af' : '#475569';
                for (let id in Chart.instances) {
                    Chart.instances[id].update();
                }
            }
        });

        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
                background: document.body.classList.contains('dark-mode') ? '#1e293b' : '#fff',
                color: document.body.classList.contains('dark-mode') ? '#fff' : '#000'
            });
            Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error', title: 'Algo salió mal',
                html: '<div class="text-start"><ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>',
                confirmButtonColor: '#ef4444',
                background: document.body.classList.contains('dark-mode') ? '#1e293b' : '#fff',
                color: document.body.classList.contains('dark-mode') ? '#fff' : '#000'
            });
        @endif
    </script>
</body>
</html>