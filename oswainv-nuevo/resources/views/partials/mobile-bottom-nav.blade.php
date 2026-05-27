<nav class="mobile-bottom-nav d-md-none" id="mobileBottomNav">
    <a href="{{ route('inventario') }}" class="mb-item {{ request()->routeIs('inventario') ? 'active' : '' }}">
        <i class="bi bi-house-fill"></i><span>Inicio</span>
    </a>
    <button class="mb-item mb-btn" onclick="toggleInvMenu(event)" id="mbInvBtn">
        <i class="bi bi-grid-fill"></i><span>Inventario</span>
    </button>
    <a href="{{ route('chat.index') }}" class="mb-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
        <i class="bi bi-chat-dots-fill"></i><span>Chat</span>
        <span class="mb-badge" id="mbChatBadge" style="display:none;">0</span>
    </a>
    @if(auth()->check() && auth()->user()->rol === 'admin')
    <button class="mb-item mb-btn" onclick="toggleGestionMenu(event)" id="mbGestionBtn">
        <i class="bi bi-gear-fill"></i><span>Gestión</span>
    </button>
    @endif
    <a href="{{ route('proveedores') }}" class="mb-item {{ request()->routeIs('proveedores') ? 'active' : '' }}">
        <i class="bi bi-truck"></i><span>Proveedores</span>
    </a>
</nav>

<!-- POPUP GESTIÓN (solo admin) -->
@if(auth()->check() && auth()->user()->rol === 'admin')
<div class="inv-popup" id="gestionPopup" style="display:none;">
    <div class="inv-popup-overlay" onclick="toggleGestionMenu()"></div>
    <div class="inv-popup-menu">
        <div class="inv-popup-header">Gestión</div>
        <a href="{{ route('usuarios.index') ?? '#' }}" class="inv-popup-item"><i class="bi bi-people-fill text-info"></i> Usuarios y Roles</a>
        <a href="{{ url('/gestion/misiones') }}" class="inv-popup-item"><i class="bi bi-flag-fill text-danger"></i> Misiones</a>
        <a href="{{ route('catalogo') }}#tab-auditoria" class="inv-popup-item"><i class="bi bi-shield-lock text-success"></i> Reportes de Auditoría</a>
        <div style="height:1px;background:rgba(255,255,255,0.05);margin:8px 0;"></div>
        <a href="{{ route('reporte.cierre') ?? '#' }}" class="inv-popup-item"><i class="bi bi-file-earmark-check-fill"></i> Generar Cierre Diario</a>
        <a href="{{ route('respaldo.db') ?? '#' }}" class="inv-popup-item"><i class="bi bi-database-fill-down text-warning"></i> Respaldar Base de Datos</a>
    </div>
</div>
@endif

<!-- POPUP INVENTARIO (Catálogo / Despacho) -->
<div class="inv-popup" id="invPopup" style="display:none;">
    <div class="inv-popup-overlay" onclick="toggleInvMenu()"></div>
    <div class="inv-popup-menu">
        <div class="inv-popup-header">Inventario</div>
        <a href="{{ route('catalogo') }}" class="inv-popup-item"><i class="bi bi-box-seam text-primary"></i> Catálogo de Productos</a>
        <a href="{{ route('despacho.vista') ?? '#' }}" class="inv-popup-item"><i class="bi bi-upc-scan text-danger"></i> Despacho de Productos</a>
    </div>
</div>

<!-- DRAWER NOTIFICACIONES MÓVIL (slide-in derecha) -->
<div class="notif-drawer-overlay" id="notifDrawerOverlay" style="display:none;" onclick="toggleMbDrawer()"></div>
<div class="notif-drawer" id="notifDrawer" style="display:none;">
    <div class="notif-drawer-header">
        <span style="font-weight:700;font-size:1rem;"><i class="bi bi-bell-fill text-danger me-2"></i>Notificaciones</span>
        <div>
            <span onclick="markAllNotifRead()" style="font-size:0.75rem;color:#E50914;cursor:pointer;margin-right:12px;">Todo leído</span>
            <button onclick="toggleMbDrawer()" class="btn-close btn-close-white" style="font-size:0.8rem;"></button>
        </div>
    </div>
    <div class="notif-drawer-body" id="notifDrawerBody">
        <div style="padding:2rem;text-align:center;color:#444;"><i class="bi bi-check2-circle d-block mb-2" style="font-size:2rem;"></i>Cargando...</div>
    </div>
</div>

<script>
window.toggleInvMenu = function(e) {
    if (e) e.stopPropagation();
    const popup = document.getElementById('invPopup');
    if (!popup) return;
    const show = popup.style.display === 'none';
    popup.style.display = show ? 'block' : 'none';
    const gestion = document.getElementById('gestionPopup');
    if (gestion) gestion.style.display = 'none';
};

window.toggleGestionMenu = function(e) {
    if (e) e.stopPropagation();
    const popup = document.getElementById('gestionPopup');
    if (!popup) return;
    const show = popup.style.display === 'none';
    popup.style.display = show ? 'block' : 'none';
    const inv = document.getElementById('invPopup');
    if (inv) inv.style.display = 'none';
};

window.toggleMbDrawer = function() {
    const drawer = document.getElementById('notifDrawer');
    const overlay = document.getElementById('notifDrawerOverlay');
    if (!drawer) return;
    const show = drawer.style.display === 'none';
    drawer.style.display = show ? 'flex' : 'none';
    if (overlay) overlay.style.display = show ? 'block' : 'none';
    if (show) cargarNotifDrawer();
    document.body.style.overflow = show ? 'hidden' : '';
};

async function cargarNotifDrawer() {
    try {
        const res = await fetch('/api/notifications');
        const data = await res.json();
        const body = document.getElementById('notifDrawerBody');
        if (!body) return;
        if (!data.length) {
            body.innerHTML = '<div style="padding:2rem;text-align:center;color:#444;"><i class="bi bi-check2-circle d-block mb-2" style="font-size:2rem;"></i>Todo al día</div>';
            return;
        }
        body.innerHTML = data.map(n => {
            const canApprove = n.type === 'requisition' && n.status === 'pendiente';
            return `<div class="notif-drawer-item">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.85rem;font-weight:500;">${n.title}</div>
                    <div style="font-size:0.7rem;color:#888;margin-top:2px;">${n.message || ''}</div>
                    <div style="font-size:0.6rem;color:#555;margin-top:4px;">${new Date(n.created_at).toLocaleString()}</div>
                    ${canApprove ? `<div style="display:flex;gap:6px;margin-top:8px;">
                        <button class="mb-notif-btn approbe" onclick="accionNotif(${n.id},'aprobar')"><i class="bi bi-check-lg"></i> Aprobar</button>
                        <button class="mb-notif-btn reject" onclick="accionNotif(${n.id},'rechazar')"><i class="bi bi-x-lg"></i> Rechazar</button>
                    </div>` : ''}
                </div>
                <button class="mb-notif-dismiss" onclick="marcarNotifLeida(${n.id})" title="Descartar"><i class="bi bi-check2"></i></button>
            </div>`;
        }).join('');
    } catch(e) {}
}

window.accionNotif = async function(id, accion) {
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('/api/notifications/' + id + '/read', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }
        });
        cargarNotifDrawer();
        actualizarBadgeNotif();
        if (typeof mostrarToast !== 'undefined') {
            mostrarToast('Notificación ' + (accion === 'aprobar' ? 'aprobada' : 'rechazada'), 'bi bi-check-circle-fill');
        }
    } catch(e) {}
};

document.addEventListener('click', function(e) {
    if (!e.target.closest('#invPopup') && !e.target.closest('#mbInvBtn')) {
        const popup = document.getElementById('invPopup');
        if (popup) popup.style.display = 'none';
    }
    if (!e.target.closest('#gestionPopup') && !e.target.closest('#mbGestionBtn')) {
        const popup = document.getElementById('gestionPopup');
        if (popup) popup.style.display = 'none';
    }
});
</script>
