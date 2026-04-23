<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAV - UNELLEZ</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { --sidebar-bg: #1e293b; --main-bg: #f8fafc; --accent: #3b82f6; }
        body { background-color: var(--main-bg); font-family: 'Inter', 'Segoe UI', sans-serif; transition: background-color 0.3s, color 0.3s; }
        
        /* Sidebar Pro */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; color: white; transition: 0.3s; z-index: 1000; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .main-content { margin-left: 260px; padding: 30px; transition: 0.3s; }
        
        .nav-link { color: #94a3b8; padding: 14px 20px; margin: 4px 10px; border-radius: 12px; transition: 0.2s; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: white; background: var(--accent); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        .nav-link i { font-size: 1.2rem; }

        /* ========================================== */
        /* 🌙 MODO OSCURO UNIVERSAL (BLINDADO) */
        /* ========================================== */
        body.dark-mode { background-color: #0f172a; color: #f1f5f9; }
        
        body.dark-mode .card, 
        body.dark-mode .bg-white, 
        body.dark-mode .mi-fondo { background-color: #1e293b !important; border-color: #334155 !important; }
        
        body.dark-mode .text-dark, body.dark-mode .text-muted { color: #f1f5f9 !important; }
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
        body.dark-mode p, body.dark-mode th, body.dark-mode td { color: #f1f5f9; }
        
        /* 📋 FORMULARIOS Y PRECIO ($) */
        body.dark-mode input.form-control, 
        body.dark-mode select.form-select, 
        body.dark-mode textarea.form-control { 
            background-color: #0f172a !important; 
            color: #f1f5f9 !important; 
            border-color: #334155 !important; 
        }

        /* Arreglo del cuadro verde del dólar en el Input Group */
        body.dark-mode .input-group-text:not(.bg-success) {
            background-color: #0f172a !important; 
            color: #f1f5f9 !important; 
            border-color: #334155 !important; 
        }

        body.dark-mode .input-group-text.bg-success {
            background-color: #198754 !important; /* Mantiene el verde */
            color: white !important; 
            border-color: #198754 !important;
        }

        /* Borde del input de precio para que combine con el verde */
        body.dark-mode .input-group input.border-success {
            border-color: #198754 !important;
        }
        
        body.dark-mode .form-control:focus { 
            background-color: #1e293b !important; 
            color: #ffffff !important; 
        }

        /* 📊 TABLA E HISTORIAL */
        body.dark-mode .table { --bs-table-bg: transparent; --bs-table-color: #f1f5f9; border-color: #334155; }
        body.dark-mode .bg-light, body.dark-mode thead.bg-light { background-color: #0f172a !important; color: #f1f5f9 !important; }
        body.dark-mode tbody tr { background-color: transparent !important; }
        body.dark-mode .table-hover tbody tr:hover { background-color: rgba(255, 255, 255, 0.05) !important; color: white; }
        
        /* 🎨 ETIQUETAS Y COLORES */
        body.dark-mode .text-danger { color: #ef4444 !important; }
        body.dark-mode .text-success { color: #10b981 !important; }
        body.dark-mode .text-warning { color: #f59e0b !important; }

        body.dark-mode td .badge.bg-success-subtle { color: #34d399 !important; background-color: rgba(16, 185, 129, 0.15) !important; border: 1px solid #34d399 !important; }
        body.dark-mode td .badge.bg-danger-subtle { color: #f87171 !important; background-color: rgba(239, 68, 68, 0.15) !important; border: 1px solid #f87171 !important; }
        
        /* DataTables */
        body.dark-mode .dataTables_wrapper { color: #f1f5f9 !important; }
        body.dark-mode .page-link { background-color: #1e293b; border-color: #334155; color: #f1f5f9; }
        body.dark-mode .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; }
        body.dark-mode .btn-light, body.dark-mode .mi-boton { background-color: #334155 !important; border-color: #334155 !important; color: #f1f5f9 !important; }

        /* SweetAlert Oscuro */
        body.dark-mode .swal2-popup { background-color: #1e293b !important; border: 1px solid #334155; color: #f1f5f9 !important; }
        body.dark-mode .swal2-title, body.dark-mode .swal2-html-container { color: #f1f5f9 !important; }

        /* Diseño Móvil */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; border-radius: 0 0 20px 20px; }
            .sidebar ul { display: flex; flex-direction: row; overflow-x: auto; padding: 10px; }
            .nav-link { padding: 10px; margin: 2px; font-size: 0.85em; white-space: nowrap; }
            .main-content { margin-left: 0; padding: 20px; }
            .hide-mobile { display: none; }
        }

        .btn-modern { border-radius: 12px; padding: 10px 20px; font-weight: 600; transition: 0.3s; }
        .btn-modern:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="p-4">
            <h4 class="fw-bold mb-0 text-white"><i class="bi bi-box-seam-fill text-primary"></i> OSWA-INV</h4>
            <small class="text-muted hide-mobile">Sistema de Inventario</small>
        </div>
        
        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <a href="/" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill me-3"></i> Panel de control
                </a>
            </li>
            <li>
                <a href="/escaner" class="nav-link {{ Request::is('escaner') ? 'active' : '' }}">
                    <i class="bi bi-upc-scan me-3"></i> Escáner Móvil
                </a>
            </li>
            <li>
                <a href="/historial" class="nav-link {{ Request::is('historial') ? 'active' : '' }}">
                    <i class="bi bi-clock-history me-3"></i> Historial (Kardex)
                </a>
            </li>
            <li>
                <a href="/productos/crear" class="nav-link {{ Request::is('productos/crear') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill me-3"></i> Nuevo Producto
                </a>
            </li>
            <li class="mt-4 ms-3 hide-mobile">
                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Reportes</small>
            </li>
            <li>
                <a href="/productos/pdf" target="_blank" class="nav-link">
                    <i class="bi bi-file-earmark-pdf-fill me-3"></i> Descargar PDF
                </a>
            </li>
        </ul>

        <div class="p-4 mt-auto border-top border-secondary hide-mobile">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-2">
                        <i class="bi bi-person-fill text-white"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-bold">{{ Auth::check() ? Auth::user()->name : 'Invitado' }}</p>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem;">
                            {{ Auth::check() ? ucfirst(Auth::user()->role) : 'UNELLEZ' }}
                        </p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <button id="btnDarkMode" class="btn btn-sm text-warning border-0 me-1" title="Modo Oscuro">
                        <i class="bi bi-moon-stars-fill fs-5"></i>
                    </button>

                    @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm text-danger border-0" title="Cerrar Sesión">
                            <i class="bi bi-box-arrow-right fs-5"></i>
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
        }

        btnDarkMode.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                iconDarkMode.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
                btnDarkMode.classList.replace('text-warning', 'text-light');
            } else {
                localStorage.setItem('theme', 'light');
                iconDarkMode.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
                btnDarkMode.classList.replace('text-light', 'text-warning');
            }
        });

        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
            });
            Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error', title: 'Algo salió mal',
                html: '<div class="text-start"><ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>',
                confirmButtonColor: '#ef4444'
            });
        @endif
    </script>
</body>
</html>