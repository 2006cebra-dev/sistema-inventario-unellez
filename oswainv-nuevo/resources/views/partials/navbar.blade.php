<!-- CSS DEL NAVBAR GLOBAL Y SCROLLBAR -->
<style>
    :root { --topbar-height: 68px; }
    
    /* FIX: Eliminar el estiramiento y scroll horizontal */
    body, html { 
        overflow-x: hidden !important; 
        width: 100%; 
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    /* --- SCROLLBAR PREMIUM --- */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
    ::-webkit-scrollbar-thumb { background: #B20710; border-radius: 10px; border: 2px solid #0a0a0a; }
    ::-webkit-scrollbar-thumb:hover { background: #E50914; }

    /* --- ESTILOS DEL NAVBAR --- */
    .topbar { 
        position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
        background: rgba(18,18,18,0.95); backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 4%; z-index: 1050; border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .topbar-logo { font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }
    .logo-text {
        font-weight: 800;
        animation: rgbAnimation 3s linear infinite;
        background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
        background-size: 300% 100%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 8px rgba(229,9,20,0.3));
    }
    @keyframes rgbAnimation {
        0% { background-position: 0% 50%; }
        100% { background-position: 300% 50%; }
    }
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
    
    /* Menús */
    .topbar-nav { display: flex; align-items: center; gap: 1rem; height: 100%; }
    .topbar-nav-item { position: relative; height: 100%; display: flex; align-items: center; padding-bottom: 15px; margin-bottom: -15px; cursor: pointer; z-index: 1050; }
    .nav-link-custom { color: #a3a3a3; text-decoration: none; font-size: 0.95rem; font-weight: 600; padding: 8px 16px; border-radius: 8px; transition: all 0.3s ease; }
    .nav-link-custom:hover, .nav-link-custom.active { color: #ffffff; background: rgba(255, 255, 255, 0.08); }
    .nav-link-custom.active { border-bottom: 2px solid #E50914; border-radius: 8px 8px 0 0; }

    /* Dropdowns */
    .dropdown-menu-custom {
        visibility: hidden; opacity: 0; position: absolute; top: 100%; left: 0; 
        background: #141414; border: 1px solid #333; min-width: 260px; 
        border-radius: 0 0 12px 12px; padding: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.8);
        z-index: 1100; transform: translateY(10px); transition: all 0.2s ease;
    }
    @media (min-width: 768px) { .topbar-nav-item:hover .dropdown-menu-custom { visibility: visible; opacity: 1; transform: translateY(0); } }
    .dropdown-menu-custom.active { visibility: visible; opacity: 1; transform: translateY(0); }
    .dropdown-header-custom { padding: 8px 12px; font-size: 0.7rem; text-transform: uppercase; color: #666; font-weight: 700; border-bottom: 1px solid #333; margin-bottom: 8px; }
    .dropdown-item-custom { display: flex; align-items: center; gap: 12px; padding: 10px 15px; color: #ccc; text-decoration: none; border-radius: 8px; font-size: 0.9rem; transition: 0.2s; }
    .dropdown-item-custom:hover { background: rgba(255,255,255,0.08); color: #fff; transform: translateX(5px); }
    .item-cierre { color: #0984e3 !important; }
    .item-cierre:hover { background: rgba(9, 132, 227, 0.15) !important; }

    /* --- INDICADOR DE RED --- */
    .status-indicator { display: flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); font-size: 0.72rem; font-weight: 500; transition: all 0.3s; white-space: nowrap; }
    .status-indicator .status-dot { width: 8px; height: 8px; border-radius: 50%; transition: background 0.3s ease; }
    .status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 8px rgba(0,184,148,0.7); }
    .status-indicator.online .status-text { color: #ccc; }
    .status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 8px rgba(231,76,60,0.7); }
    .status-indicator.offline .status-text { color: #e74c3c; }

    /* --- MENÚ DERECHO NETFLIX --- */
    .topbar-right { display: flex; align-items: center; gap: 1rem; }
    .topbar-search { position: relative; }
    .topbar-search input { width: 220px; padding: 7px 14px 7px 34px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 6px; color: #ffffff; font-size: 0.85rem; transition: all 0.3s; }
    .topbar-search input:focus { outline: none; background: rgba(255,255,255,0.12); border-color: #E50914; width: 280px; box-shadow: 0 0 12px rgba(229,9,20,0.15); }
    .topbar-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.6); font-size: 0.85rem; }
    .user-dropdown { position: relative; cursor: pointer; padding-bottom: 15px; margin-bottom: -15px; }
    .user-avatar { width: 32px; height: 32px; border-radius: 4px; background: #E50914; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; }
    .dropdown-menu-netflix { position: absolute; top: 100%; right: 0; min-width: 260px; background: rgba(15,15,15,0.95); border: 1px solid #333; border-radius: 8px; box-shadow: 0 8px 40px rgba(0,0,0,0.7); padding: 8px 0; z-index: 1200; display: none; }
    .user-dropdown:hover .dropdown-menu-netflix, .dropdown-menu-netflix.show { display: block; }
    .dropdown-header { padding: 12px 16px; border-bottom: 1px solid #333; }
    .dropdown-header .dd-name { color: #fff; font-weight: 600; font-size: 0.95rem; }
    .dropdown-header .dd-email { color: #b3b3b3; font-size: 0.8rem; margin-top: 2px; }
    .dropdown-header .dd-role { color: #E50914; font-size: 0.75rem; font-weight: 600; margin-top: 2px; text-transform: uppercase; }
    .dd-item { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; border: none; background: none; color: #ccc; font-size: 0.9rem; text-align: left; cursor: pointer; transition: background 0.2s; }
    .dd-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .dd-divider { height: 1px; background: #333; margin: 6px 0; }
    .dd-logout { color: #e74c3c !important; }

    /* Ajuste para el contenido (Restaurando márgenes laterales) */
    .main-content { 
        padding-top: calc(var(--topbar-height) + 2rem); 
        padding-left: 4%; 
        padding-right: 4%; 
        padding-bottom: 2rem; 
    }

    /* ─── TOAST NOTIFICACIONES PREMIUM (BOTTOM-RIGHT) ─── */
    .oswa-toast-container {
        position: fixed; bottom: 24px; right: 24px; z-index: 999999;
        display: flex; flex-direction: column; gap: 12px; pointer-events: none;
        max-width: 420px;
    }
    .oswa-toast {
        pointer-events: auto;
        background: rgba(28, 28, 28, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #fff;
        padding: 16px 20px;
        border-radius: 12px;
        border: 1px solid rgba(229, 9, 20, 0.25);
        box-shadow: 0 8px 32px rgba(0,0,0,0.6), 0 0 0 1px rgba(229, 9, 20, 0.1) inset;
        display: flex; align-items: center; gap: 14px;
        font-size: 0.9rem; font-weight: 500; line-height: 1.4;
        min-width: 320px;
        animation: oswaToastIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .oswa-toast:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.7), 0 0 0 1px rgba(229, 9, 20, 0.2) inset;
    }
    .oswa-toast i {
        font-size: 1.4rem;
        color: #E50914;
        flex-shrink: 0;
        filter: drop-shadow(0 0 6px rgba(229, 9, 20, 0.4));
    }
    .oswa-toast .toast-progress {
        position: absolute; bottom: 0; left: 0; height: 3px;
        background: linear-gradient(90deg, #E50914, #ff6b6b);
        animation: oswaToastProgress 3s linear forwards;
        border-radius: 0 2px 0 0;
    }
    .oswa-toast.removing {
        animation: oswaToastOut 0.4s cubic-bezier(0.55, 0, 0.55, 0.4) forwards;
    }
    .oswa-toast.removing .toast-progress {
        animation-play-state: paused;
    }
    @keyframes oswaToastIn {
        from { transform: translateY(40px) scale(0.95); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    @keyframes oswaToastOut {
        from { transform: translateY(0) scale(1); opacity: 1; }
        to { transform: translateY(20px) scale(0.95); opacity: 0; }
    }
    @keyframes oswaToastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* ─── TOAST DE CONFIRMACIÓN PREMIUM (SIDEBAR) ─── */
    .oswa-confirm-toast {
        background: #1a1a1a !important;
        border: 1px solid #E50914 !important;
        border-radius: 14px !important;
        box-shadow: 0 0 30px rgba(229,9,20,0.15), 0 10px 40px rgba(0,0,0,0.6) !important;
        padding: 20px 24px !important;
        animation: oswaToastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
        max-width: 380px !important;
    }
    .oswa-confirm-toast .swal2-title {
        color: #fff !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        margin-bottom: 6px !important;
    }
    .oswa-confirm-toast .swal2-html-container {
        color: #a3a3a3 !important;
        font-size: 0.85rem !important;
        line-height: 1.5 !important;
    }
    .oswa-confirm-toast .swal2-icon {
        margin: 0 0 12px 0 !important;
        border-color: #E50914 !important;
        color: #E50914 !important;
        animation: oswaPulse 1.5s ease-in-out infinite !important;
    }
    .oswa-confirm-toast .swal2-icon.swal2-warning {
        border-color: #E50914 !important;
        color: #E50914 !important;
    }
    .oswa-confirm-toast .swal2-actions {
        margin-top: 16px !important;
        gap: 10px !important;
    }
    .oswa-confirm-toast .swal2-confirm {
        background: #E50914 !important;
        border: none !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        padding: 10px 20px !important;
        transition: all 0.3s !important;
    }
    .oswa-confirm-toast .swal2-confirm:hover {
        background: #ff0f1b !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(229,9,20,0.4) !important;
    }
    .oswa-confirm-toast .swal2-cancel {
        background: transparent !important;
        border: 1px solid #444 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        padding: 10px 20px !important;
        color: #a3a3a3 !important;
        transition: all 0.3s !important;
    }
    .oswa-confirm-toast .swal2-cancel:hover {
        border-color: #E50914 !important;
        color: #fff !important;
        background: rgba(229,9,20,0.1) !important;
    }
    @keyframes oswaToastSlideIn {
        from { transform: translateX(120%) scale(0.9); opacity: 0; }
        to { transform: translateX(0) scale(1); opacity: 1; }
    }
    @keyframes oswaPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.08); opacity: 0.8; }
    }
</style>

<!-- HTML DEL NAVBAR GLOBAL -->
<nav class="topbar" id="topbar">
    <div class="topbar-left d-flex align-items-center gap-3">
        <div class="topbar-logo d-flex align-items-center gap-2">
            <img src="{{ asset('img/logo-unellez.png') }}" class="logo-nav-unellez" alt="Logo">
            <span class="logo-text">OSWA Inv</span>
        </div>
        <!-- INDICADOR DE CONEXIÓN -->
        <div class="status-indicator online d-none d-md-flex ms-2" id="networkIndicator">
            <span class="status-dot"></span>
            <span class="status-text ms-2" id="networkText">En línea</span>
        </div>
    </div>

    <!-- MENÚ CENTRAL -->
    <div class="topbar-nav" id="topbarNav">
        <div class="topbar-nav-item">
            <a href="{{ route('inventario') }}" class="nav-link-custom {{ request()->routeIs('inventario') ? 'active' : '' }}">Inicio</a>
        </div>
        <div class="topbar-nav-item">
            <a href="javascript:void(0)" class="nav-link-custom {{ request()->routeIs('catalogo') || request()->routeIs('despacho.*') ? 'active' : '' }}" onclick="toggleDropdown('inventarioDropdown')">
                Inventario <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem;"></i>
            </a>
            <div class="dropdown-menu-custom" id="inventarioDropdown">
                <div class="dropdown-header-custom">Catálogo y Movimientos</div>
                <a href="{{ route('catalogo') }}" class="dropdown-item-custom"><i class="bi bi-box-seam text-primary"></i> Catálogo de Productos</a>
                <a href="{{ route('despacho.vista') ?? '#' }}" class="dropdown-item-custom"><i class="bi bi-upc-scan text-danger"></i> Despacho de Productos</a>
            </div>
        </div>
        <div class="topbar-nav-item">
            <a href="{{ route('proveedores') }}" class="nav-link-custom {{ request()->routeIs('proveedores') ? 'active' : '' }}">Proveedores</a>
        </div>
        @if(auth()->check() && auth()->user()->rol === 'admin')
        <div class="topbar-nav-item">
            <a href="javascript:void(0)" class="nav-link-custom" onclick="toggleDropdown('gestionDropdown')">
                Gestión <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem;"></i>
            </a>
            <div class="dropdown-menu-custom" id="gestionDropdown">
                <div class="dropdown-header-custom">Administración</div>
                <a href="{{ route('usuarios.index') ?? '#' }}" class="dropdown-item-custom"><i class="bi bi-people-fill text-info"></i> Usuarios y Roles</a>
                <a href="{{ url('/gestion/misiones') }}" class="dropdown-item-custom"><i class="bi bi-flag-fill text-danger"></i> Misiones</a>
                <a href="{{ route('catalogo') }}#tab-auditoria" class="dropdown-item-custom"><i class="bi bi-shield-lock text-success"></i> Reportes de Auditoría</a>
                <div style="height: 1px; background: rgba(255,255,255,0.05); margin: 8px 0;"></div>
                <a href="{{ route('reporte.cierre') ?? '#' }}" class="dropdown-item-custom item-cierre"><i class="bi bi-file-earmark-check-fill"></i> Generar Cierre Diario</a>
                <a href="{{ route('respaldo.db') ?? '#' }}" class="dropdown-item-custom"><i class="bi bi-database-fill-down text-warning"></i> Respaldar Base de Datos</a>
            </div>
        </div>
        @endif
    </div>

    <!-- MENÚ DERECHO -->
    <div class="topbar-right d-none d-md-flex align-items-center gap-3">
        <div class="topbar-search">
            <i class="bi bi-search"></i>
            <input type="text" id="topbarSearchInput" placeholder="Buscar en el sistema...">
        </div>
        <a href="{{ route('chat.index') }}" class="position-relative text-decoration-none" title="Chat Interno" style="color:#a3a3a3;font-size:1.3rem;transition:color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#a3a3a3'">
            <i class="bi bi-chat-dots-fill"></i>
            <span id="chatUnreadBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:#E50914;font-size:0.55rem;padding:2px 4px;min-width:16px;display:none;">0</span>
        </a>

        <!-- OSWA Pulse: Campana de notificaciones -->
        <div class="position-relative" id="notifContainer" style="cursor:pointer;">
            <a class="text-decoration-none position-relative" onclick="toggleNotifDropdown()" title="Notificaciones" style="color:#a3a3a3;font-size:1.3rem;">
                <i class="bi bi-bell-fill"></i>
                <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:#E50914;font-size:0.55rem;padding:2px 4px;min-width:16px;display:none;">0</span>
            </a>
            <div id="notifDropdown" class="dropdown-menu-netflix" style="display:none;position:absolute;right:0;top:100%;width:360px;max-height:420px;overflow:hidden;z-index:9999;margin-top:8px;">
                <div class="dropdown-header" style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 1rem;">
                    <span style="font-weight:600;font-size:0.9rem;">Notificaciones</span>
                    <span onclick="markAllNotifRead()" style="font-size:0.7rem;color:#E50914;cursor:pointer;">Marcar todo leído</span>
                </div>
                <div id="notifList" style="max-height:320px;overflow-y:auto;padding:0;"></div>
                <div style="padding:0.5rem 1rem;text-align:center;border-top:1px solid rgba(255,255,255,0.06);">
                    <a href="{{ route('arena.index') }}" style="font-size:0.75rem;color:#666;text-decoration:none;">Ver todas en Arena 🏟️</a>
                </div>
            </div>
        </div>

        <!-- OSWA Arena link -->
        <a href="{{ route('arena.index') }}" class="text-decoration-none" title="OSWA Arena" style="color:#a3a3a3;font-size:1.3rem;transition:color 0.3s;" onmouseover="this.style.color='#ffd700'" onmouseout="this.style.color='#a3a3a3'">
            <i class="bi bi-trophy-fill"></i>
        </a>

        <div class="user-dropdown" id="userDropdown">
            <div class="d-flex align-items-center gap-2" onclick="toggleUserDropdown()">
                @if(auth()->user()?->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                @else
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                @endif
                <i class="bi bi-caret-down-fill" id="dropdownArrow" style="color:#888;font-size:0.7rem;"></i>
            </div>
            <div class="dropdown-menu-netflix" id="userDropdownMenu">
                <div class="dropdown-header">
                    <div class="dd-name">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                    <div class="dd-email">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                    <div class="dd-role">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                </div>
                <button class="dd-item" onclick="mostrarMiCuenta()"><i class="bi bi-person-circle"></i> Mi Cuenta</button>
                <button class="dd-item" onclick="mostrarAtajos()"><i class="bi bi-keyboard"></i> Atajos de Teclado</button>
                <button type="button" class="dd-item text-white" onclick="abrirSelectorPerfiles(event)"><i class="bi bi-arrow-left-right text-danger"></i> Cambiar de Cuenta</button>
                <div class="dd-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 w-100">
                    @csrf
                    <button type="submit" class="dd-item dd-logout w-100 text-start"><i class="bi bi-box-arrow-right"></i> Salir del Sistema</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="oswa-toast-container" id="oswa-toast-container"></div>

<!-- SCRIPTS GLOBALES -->
<script>
    // Control de Dropdowns
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if(dropdown) dropdown.classList.toggle('active');
    }
    function toggleUserDropdown() {
        const userMenu = document.getElementById('userDropdownMenu');
        if(userMenu) userMenu.classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.topbar-nav-item')) {
            document.querySelectorAll('.dropdown-menu-custom').forEach(menu => menu.classList.remove('active'));
        }
        if (!e.target.closest('.user-dropdown')) {
            const userMenu = document.getElementById('userDropdownMenu');
            if(userMenu) userMenu.classList.remove('show');
        }
    });

    // Sensor de Red (Online/Offline)
    function checkNetworkStatus() {
        const indicator = document.getElementById('networkIndicator');
        const text = document.getElementById('networkText');
        
        if (navigator.onLine) {
            if(indicator) { indicator.classList.remove('offline'); indicator.classList.add('online'); }
            if(text) text.innerText = 'En línea';
        } else {
            if(indicator) { indicator.classList.remove('online'); indicator.classList.add('offline'); }
            if(text) text.innerText = 'Sin conexión';
            
            if (typeof mostrarToast !== 'undefined') {
                mostrarToast('Se perdió la conexión. Operaciones bloqueadas.', 'bi bi-wifi-off');
            }
        }
    }
    window.addEventListener('online', checkNetworkStatus);
    window.addEventListener('offline', checkNetworkStatus);
    document.addEventListener('DOMContentLoaded', checkNetworkStatus);

    // CONFIGURACIÓN DEL TOAST ESTILO NETFLIX
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#141414',
        color: '#ffffff',
        iconColor: '#E50914',
        customClass: {
            popup: 'border border-secondary shadow-lg'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // TOAST NOTIFICACIÓN PREMIUM
    function mostrarToast(mensaje, icono = 'bi bi-check-circle-fill') {
        const container = document.getElementById('oswa-toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'oswa-toast';
        toast.innerHTML = `<i class="${icono}"></i><span style="flex:1;">${mensaje}</span><div class="toast-progress"></div>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
    window.mostrarToast = mostrarToast;

    // Heartbeat de presencia en vivo (cada 30s)
    (function() {
        const hbCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        async function enviarHeartbeat() {
            try {
                await fetch('/api/heartbeat', { method: 'POST', headers: { 'X-CSRF-TOKEN': hbCsrf }, body: '' });
            } catch(e) { /* silencio */ }
        }
        enviarHeartbeat();
        setInterval(enviarHeartbeat, 30000);
    })();

    // Chat: badge de mensajes no leídos (cada 10s)
    (function() {
        async function actualizarBadgeChat() {
            try {
                const res = await fetch('/api/chat/unread');
                const data = await res.json();
                const badge = document.getElementById('chatUnreadBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            } catch(e) {}
        }
        actualizarBadgeChat();
        setInterval(actualizarBadgeChat, 10000);
    })();

    // OSWA Pulse: Notificaciones
    (function() {
        let lastNotifId = 0;
        let notifOpen = false;

        window.toggleNotifDropdown = function() {
            const dd = document.getElementById('notifDropdown');
            if (!dd) return;
            notifOpen = !dd.style.display || dd.style.display === 'none';
            dd.style.display = notifOpen ? 'block' : 'none';
            if (notifOpen) cargarNotificaciones();
        };

        async function cargarNotificaciones() {
            try {
                const res = await fetch('/api/notifications?unread_only=1');
                const data = await res.json();
                const list = document.getElementById('notifList');
                if (!list) return;
                if (data.length === 0) {
                    list.innerHTML = '<div style="padding:1.5rem;text-align:center;color:#555;font-size:0.85rem;"><i class="bi bi-check2-circle d-block mb-2" style="font-size:2rem;"></i>Todo al día</div>';
                    return;
                }
                list.innerHTML = data.map(n => `
                    <div class="notif-item" onclick="marcarNotifLeida(${n.id})" style="padding:0.6rem 1rem;border-bottom:1px solid rgba(255,255,255,0.03);cursor:pointer;transition:background 0.2s;display:flex;gap:10px;align-items:flex-start;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                        <div style="font-size:1.1rem;flex-shrink:0;margin-top:2px;"><i class="bi ${n.icon || 'bi-bell-fill'}"></i></div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.85rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${n.title}</div>
                            <div style="font-size:0.7rem;color:#666;margin-top:2px;">${n.message || ''}</div>
                        </div>
                        <div style="font-size:0.6rem;color:#444;white-space:nowrap;">${new Date(n.created_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}</div>
                    </div>
                `).join('');
                if (data.length > 0) lastNotifId = data[0].id;
            } catch(e) {}
        }

        window.marcarNotifLeida = async function(id) {
            try {
                await fetch('/api/notifications/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' } });
                cargarNotificaciones();
                actualizarBadgeNotif();
            } catch(e) {}
        };

        window.markAllNotifRead = async function() {
            try {
                await fetch('/api/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' } });
                cargarNotificaciones();
                actualizarBadgeNotif();
            } catch(e) {}
        };

        async function actualizarBadgeNotif() {
            try {
                const res = await fetch('/api/notifications/unread-count');
                const data = await res.json();
                const badge = document.getElementById('notifBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            } catch(e) {}
        }

        actualizarBadgeNotif();
        setInterval(actualizarBadgeNotif, 12000);

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            const container = document.getElementById('notifContainer');
            const dd = document.getElementById('notifDropdown');
            if (container && dd && !container.contains(e.target)) {
                dd.style.display = 'none';
                notifOpen = false;
            }
        });
    })();

    // Mostrar toasts desde session flash al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            mostrarToast('{{ session('success') }}');
        @endif
        @if(session('error'))
            mostrarToast('{{ session('error') }}', 'bi bi-exclamation-triangle-fill');
        @endif
    });
</script>

<script>
    function mostrarMiCuenta() {
        const menu = document.getElementById('userDropdownMenu');
        if(menu) menu.classList.remove('show');
        Swal.fire({
            title: '',
            html: `<div style="text-align:center;">
                <div style="position:relative;display:inline-block;margin-bottom:16px;">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:3px solid #E50914;box-shadow:0 0 20px rgba(229,9,20,0.3);">
                    @else
                        <div style="width:80px;height:80px;background:linear-gradient(135deg,#E50914,#b20710);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;border:3px solid #E50914;box-shadow:0 0 20px rgba(229,9,20,0.3);margin:0 auto;">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div style="position:absolute;bottom:0;right:0;width:24px;height:24px;background:#00b894;border-radius:50%;border:3px solid #1c1c1c;display:flex;align-items:center;justify-content:center;"><i class="bi bi-check" style="color:#fff;font-size:0.7rem;"></i></div>
                </div>
                <div style="font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:4px;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                <div style="color:#888;font-size:0.85rem;margin-bottom:16px;">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;text-align:center;">
                    <div style="background:#141414;border-radius:8px;padding:10px;">
                        <div style="color:#E50914;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->rol === 'admin' ? 'Admin' : 'Empleado' }}</div>
                        <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Rol</div>
                    </div>
                    <div style="background:#141414;border-radius:8px;padding:10px;">
                        <div style="color:#ffc107;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->xp ?? 0 }} XP</div>
                        <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Experiencia</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;text-align:center;">
                    <div style="background:#141414;border-radius:8px;padding:10px;">
                        <div style="color:#00b894;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->misiones()->where('estado', 'completada')->count() ?? 0 }}</div>
                        <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Misiones</div>
                    </div>
                    <div style="background:#141414;border-radius:8px;padding:10px;">
                        <div style="color:#0984e3;font-size:1.1rem;font-weight:700;">{{ auth()->user()?->nivel ?? 1 }}</div>
                        <div style="color:#666;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;">Nivel</div>
                    </div>
                </div>
            </div>`,
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#E50914',
            background: '#121212',
            color: '#fff',
            width: 380
        });
    }

    function mostrarAtajos() {
        const menu = document.getElementById('userDropdownMenu');
        if(menu) menu.classList.remove('show');
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

    // Fallback: definiciones de perfiles si no fueron incluidas vía partials.perfiles
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
            if (sel) sel.classList.remove('oswa-hidden');
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
                    mostrarToast('Error al cambiar de cuenta', 'bi bi-exclamation-triangle-fill');
                    return;
                }
                const data = await res.json();
                sessionStorage.removeItem('oswa_temp_password');
                if (data.success) window.location.reload();
                else {
                    document.body.style.cursor = 'default';
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Contraseña incorrecta', confirmButtonColor: '#E50914', background: '#121212', color: '#fff' });
                }
            } catch(e) {
                document.body.style.cursor = 'default';
                sessionStorage.removeItem('oswa_temp_password');
                console.error(e);
                mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
            }
        };
    }
</script>

<script>
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

@include('partials.perfiles')
