<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-dark">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle fw-bold text-white" href="#" role="button" data-bs-toggle="modal" data-bs-target="#profileModal">
                                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end bg-dark border-secondary shadow" aria-labelledby="navbarDropdown">
                                    <!-- BOTÓN REAL: Cambiar Cuenta (Abre el Modal sin redirigir) -->
                                    <button class="dropdown-item text-light py-2" onclick="abrirSelectorPerfiles(event)" style="background: none; border: none; cursor: pointer; width: 100%; text-align: left;">
                                        <i class="bi bi-arrow-left-right text-danger"></i> Cambiar de Cuenta
                                    </button>
                                    
                                    <div class="dropdown-divider border-secondary"></div>

                                    <a class="dropdown-item text-danger py-2" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

<style>
    .mobile-bottom-nav {
        position: fixed; bottom: 0; left: 0; right: 0; z-index: 1060;
        background: rgba(14,14,14,0.97);
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255,255,255,0.06);
        display: none;
        justify-content: space-around;
        align-items: center;
        padding: 0 env(safe-area-inset-bottom, 0);
        height: 64px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.6);
    }
    @media (max-width: 767px) {
        .mobile-bottom-nav { display: flex; }
        body { padding-bottom: 64px; }
    }
    .mobile-bottom-nav .mb-item {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 2px; text-decoration: none; color: #666; font-size: 0.6rem; font-weight: 500;
        padding: 6px 12px; border-radius: 8px; transition: all 0.2s ease; position: relative;
        min-width: 56px; -webkit-tap-highlight-color: transparent;
    }
    .mobile-bottom-nav .mb-item i { font-size: 1.3rem; transition: all 0.2s ease; }
    .mobile-bottom-nav .mb-item span { margin-top: 1px; }
    .mobile-bottom-nav .mb-item:active { transform: scale(0.92); }
    .mobile-bottom-nav .mb-item.active,
    .mobile-bottom-nav .mb-item:active i { color: #E50914; }
    .mobile-bottom-nav .mb-item.active span { color: #E50914; }
    .mb-badge {
        position: absolute; top: 2px; right: 6px;
        background: #E50914; color: #fff; font-size: 0.5rem; font-weight: 700;
        padding: 1px 5px; border-radius: 10px; min-width: 16px; text-align: center; line-height: 1.3;
    }
    .mb-item.mb-btn { background: none; border: none; font-family: inherit; cursor: pointer; -webkit-tap-highlight-color: transparent; }
    .mb-item.mb-btn:focus-visible { outline: none; }
    .inv-popup { position: fixed; z-index: 1070; top: 0; left: 0; right: 0; bottom: 0; }
    .inv-popup-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); }
    .inv-popup-menu { position: absolute; bottom: calc(64px + 8px); left: 50%; transform: translateX(-50%); background: #1a1a1a; border: 1px solid #333; border-radius: 16px; padding: 8px; min-width: 240px; box-shadow: 0 -8px 30px rgba(0,0,0,0.8); }
    .inv-popup-header { padding: 10px 14px 6px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #666; font-weight: 700; }
    .inv-popup-item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; color: #ddd; text-decoration: none; border-radius: 10px; font-size: 0.9rem; transition: background 0.2s; }
    .inv-popup-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .notif-drawer-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1080; }
    .notif-drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 85%; max-width: 340px; background: #141414; z-index: 1090; display: flex; flex-direction: column; box-shadow: -8px 0 30px rgba(0,0,0,0.7); animation: slideInRight 0.3s ease; }
    @keyframes slideInRight { from { transform: translateX(100%); } to { transform: translateX(0); } }
    .notif-drawer-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
    .notif-drawer-body { flex: 1; overflow-y: auto; }
    .notif-drawer-item { display: flex; align-items: flex-start; gap: 8px; padding: 0.8rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.03); }
    .mb-notif-dismiss { background: none; border: none; color: #555; font-size: 1rem; padding: 4px; cursor: pointer; flex-shrink: 0; margin-top: 2px; }
    .mb-notif-btn { border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.7rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .mb-notif-btn.approbe { background: rgba(0,184,148,0.15); color: #00b894; }
    .mb-notif-btn.reject { background: rgba(229,9,20,0.15); color: #E50914; }
</style>

@include('partials.mobile-bottom-nav')

<!-- Toast container -->
<div class="oswa-toast-container" id="oswa-toast-container"></div>

<!-- Fallback de perfiles para páginas sin navbar que usan layouts.app -->
<script>
    if (!window.abrirSelectorPerfiles) {
        window.abrirSelectorPerfiles = async function(e) {
            if (e) e.preventDefault();
            const { value: password } = await Swal.fire({
                title: 'Verifica tu identidad',
                text: 'Ingresa tu contraseña para cambiar de cuenta',
                input: 'password',
                inputPlaceholder: 'Contraseña',
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#333',
                background: '#121212',
                color: '#fff',
                inputAttributes: { autocapitalize: 'off', autocomplete: 'off' }
            });
            if (!password) return;
            sessionStorage.setItem('oswa_temp_password', password);
            const menu = document.getElementById('userDropdownMenu');
            if(menu) { menu.classList.remove('show'); menu.style.display = ''; }
            const sel = document.getElementById('oswa-profile-selector');
            if (sel) { sel.style.display = ''; sel.classList.remove('oswa-hidden'); }
        };
    }
    if (!window.seleccionarPerfilConCarga) {
        window.seleccionarPerfilConCarga = async function(userId) {
            if(document.body.classList.contains('manage-mode')) return;
            const password = sessionStorage.getItem('oswa_temp_password');
            if (!password) {
                const { value: pwd } = await Swal.fire({
                    title: 'Ingresa tu contraseña',
                    input: 'password',
                    inputPlaceholder: 'Contraseña',
                    showCancelButton: true,
                    confirmButtonText: 'Cambiar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#E50914',
                    cancelButtonColor: '#333',
                    background: '#121212',
                    color: '#fff',
                    inputAttributes: { autocapitalize: 'off', autocomplete: 'off' }
                });
                if (!pwd) return;
                sessionStorage.setItem('oswa_temp_password', pwd);
                return seleccionarPerfilConCarga(userId);
            }
            document.body.style.cursor = 'wait';
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/cambiar-perfil-netflix', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ user_id: userId, password })
                });
                if (!res.ok) {
                    sessionStorage.removeItem('oswa_temp_password');
                    document.body.style.cursor = 'default';
                    if (typeof mostrarToast !== 'undefined') mostrarToast('Error al cambiar de cuenta', 'bi bi-exclamation-triangle-fill');
                    return;
                }
                const data = await res.json();
                sessionStorage.removeItem('oswa_temp_password');
                if (data.success) {
                    localStorage.setItem('oswa_switched_to', data.user_name);
                    window.location.href = data.redirect || '/dashboard';
                }
                else {
                    document.body.style.cursor = 'default';
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Contraseña incorrecta', confirmButtonColor: '#E50914', background: '#121212', color: '#fff' });
                }
            } catch(e) {
                document.body.style.cursor = 'default';
                sessionStorage.removeItem('oswa_temp_password');
                console.error(e);
                if (typeof mostrarToast !== 'undefined') mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
            }
        };
    }

    if (!window.toggleMbDrawer) {
        window.toggleMbDrawer = function() {
            window.location.href = '{{ route('inventario') }}';
        };
    }

    (function() {
        async function actualizarBadgeChat() {
            try {
                const res = await fetch('/api/chat/unread');
                const data = await res.json();
                const mbBadge = document.getElementById('mbChatBadge');
                if (mbBadge) {
                    if (data.count > 0) { mbBadge.textContent = data.count; mbBadge.style.display = 'inline'; }
                    else { mbBadge.style.display = 'none'; }
                }
            } catch(e) {}
        }
        actualizarBadgeChat();
        setInterval(actualizarBadgeChat, 10000);
    })();

    (function() {
        async function actualizarBadgeNotif() {
            try {
                const res = await fetch('/api/notifications/unread-count');
                const data = await res.json();
                const mbBadge = document.getElementById('mbNotifBadge');
                if (mbBadge) {
                    if (data.count > 0) { mbBadge.textContent = data.count > 99 ? '99+' : data.count; mbBadge.style.display = 'inline'; }
                    else { mbBadge.style.display = 'none'; }
                }
            } catch(e) {}
        }
        actualizarBadgeNotif();
        setInterval(actualizarBadgeNotif, 12000);
    })();

    window.addEventListener("pageshow", function(event) {
        if (event.persisted) window.location.reload();
    });

    (function() {
        const switchedTo = localStorage.getItem('oswa_switched_to');
        if (switchedTo) {
            localStorage.removeItem('oswa_switched_to');
            if (typeof mostrarToast === 'function') {
                setTimeout(() => mostrarToast('Has cambiado a ' + switchedTo, 'bi bi-person-fill'), 300);
            }
        }
    })();
</script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
