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
        body { background-color: var(--main-bg); font-family: 'Inter', 'Segoe UI', sans-serif; }
        
        /* Sidebar Pro */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; color: white; transition: 0.3s; z-index: 1000; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .main-content { margin-left: 260px; padding: 30px; transition: 0.3s; }
        
        .nav-link { color: #94a3b8; padding: 14px 20px; margin: 4px 10px; border-radius: 12px; transition: 0.2s; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: white; background: var(--accent); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        .nav-link i { font-size: 1.2rem; }

        /* Diseño Móvil */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; border-radius: 0 0 20px 20px; }
            .sidebar ul { display: flex; flex-direction: row; overflow-x: auto; padding: 10px; }
            .nav-link { padding: 10px; margin: 2px; font-size: 0.85em; white-space: nowrap; }
            .main-content { margin-left: 0; padding: 20px; }
            .hide-mobile { display: none; }
        }

        /* Botones Modernos */
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
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-2 me-2">
                    <i class="bi bi-person-fill text-white"></i>
                </div>
                <div>
                    <p class="mb-0 small fw-bold">Carlos Braca</p>
                    <p class="mb-0 text-muted" style="font-size: 0.7rem;">UNELLEZ - Barinas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        @yield('contenido')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Notificaciones de Éxito
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Excelente!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 3000
            });
        @endif

        // Notificaciones de Error
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Algo salió mal',
                html: '<div class="text-start"><ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>',
                confirmButtonColor: '#ef4444'
            });
        @endif
    </script>
</body>
</html>