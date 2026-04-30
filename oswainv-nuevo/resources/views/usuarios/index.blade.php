<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administrar Usuarios - OSWA Inv</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-body: #141414;
            --bg-card: #1a1a2e;
            --bg-input: #222;
            --border-color: #2a2a4a;
            --text-primary: #fff;
            --text-secondary: #888;
            --accent-primary: #E50914;
            --accent-success: #00b894;
            --accent-danger: #e74c3c;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg-body); color: var(--text-primary); font-family: 'Helvetica Neue', Arial, sans-serif; }

        .topbar { background: #000; padding: 0.6rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; }
        .topbar-logo { font-size: 1.4rem; font-weight: 800; color: var(--accent-primary); letter-spacing: 1px; display: flex; align-items: center; gap: 8px; }
        .topbar-nav { display: flex; gap: 1.5rem; margin-left: 3rem; }
        .topbar-nav a { color: var(--text-secondary); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .topbar-nav a:hover, .topbar-nav a.active { color: var(--text-primary); }
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .theme-toggle { background: var(--bg-input); border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #e5e5e5; }
        .theme-toggle i { color: #e5e5e5; }
        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; cursor: pointer; transition: box-shadow 0.2s; }
        .user-avatar:hover { box-shadow: 0 0 12px rgba(229,9,20,0.6); }
        .dropdown-menu-netflix { position: absolute; top: calc(100% + 12px); right: 0; width: 260px; background: #141414; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 12px 40px rgba(0,0,0,0.8); padding: 8px 0; z-index: 9999; display: none; animation: dropIn 0.2s ease; }
        .dropdown-menu-netflix::before { content: ''; position: absolute; top: -8px; right: 20px; width: 16px; height: 16px; background: #141414; border-left: 1px solid #2a2a2a; border-top: 1px solid #2a2a2a; transform: rotate(45deg); }
        @keyframes dropIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-menu-netflix .dropdown-header { padding: 14px 16px 10px; border-bottom: 1px solid #222; }
        .dropdown-menu-netflix .dd-name { font-weight: 700; font-size: 0.9rem; color: #fff; }
        .dropdown-menu-netflix .dd-email { font-size: 0.75rem; color: #888; margin-top: 2px; }
        .dropdown-menu-netflix .dd-role { font-size: 0.7rem; color: var(--accent-primary); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
        .dropdown-menu-netflix .dd-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; cursor: pointer; transition: background 0.15s; border: none; background: none; width: 100%; text-align: left; }
        .dropdown-menu-netflix .dd-item:hover { background: #1f1f1f; color: #fff; }
        .dropdown-menu-netflix .dd-item i { color: #888; font-size: 1rem; width: 20px; text-align: center; }
        .dropdown-menu-netflix .dd-divider { height: 1px; background: #222; margin: 6px 0; }
        .dropdown-menu-netflix .dd-item.dd-logout { color: var(--accent-danger); }
        .dropdown-menu-netflix .dd-item.dd-logout:hover { background: rgba(231,76,60,0.1); }
        .dropdown-menu-netflix a.dd-item { text-decoration: none; }

        .main-content { padding: 2rem; max-width: 1200px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.6rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .page-title i { color: var(--accent-primary); }
        .btn-nuevo { background: var(--accent-primary); color: #fff; border: none; padding: 10px 24px; border-radius: 4px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-nuevo:hover { background: #b8070f; box-shadow: 0 0 15px rgba(229,9,20,0.4); transform: translateY(-1px); }

        .card-netflix { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
        .card-netflix table { width: 100%; border-collapse: collapse; }
        .card-netflix thead th { background: #0d0d1a; color: var(--text-secondary); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 14px 20px; border-bottom: 1px solid var(--border-color); }
        .card-netflix tbody tr { border-bottom: 1px solid #222; transition: background 0.2s; }
        .card-netflix tbody tr:hover { background: rgba(229,9,20,0.05); }
        .card-netflix tbody td { padding: 14px 20px; color: #e5e5e5; font-size: 0.9rem; }
        .badge-rol { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-admin { background: rgba(229,9,20,0.15); color: var(--accent-primary); }
        .badge-empleado { background: rgba(0,184,148,0.15); color: var(--accent-success); }
        .btn-accion { background: none; border: 1px solid rgba(231,76,60,0.3); color: var(--accent-danger); padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; transition: all 0.2s; }
        .btn-accion.activo { border-color: rgba(0,184,148,0.3); color: var(--accent-success); }
        .btn-accion:hover { background: rgba(231,76,60,0.1); border-color: var(--accent-danger); }
        .btn-accion.activo:hover { background: rgba(0,184,148,0.1); border-color: var(--accent-success); }

        .logs-table { margin-top: 2rem; }
        .logs-table .table-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px; }
        .logs-table .table-title i { color: var(--accent-primary); }

        .modal-content { background: var(--bg-card); border: 1px solid var(--border-color); }
        .modal-header { border-bottom: 1px solid var(--border-color); }
        .modal-header .modal-title { color: var(--text-primary); font-weight: 700; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 1.5rem; }
        .form-control { background: var(--bg-input); border: none; color: var(--text-primary); border-radius: 4px; padding: 12px; }
        .form-control:focus { background: #2a2a3a; border: none; color: var(--text-primary); box-shadow: none; outline: 1px solid #666; }
        .form-label { color: var(--text-secondary); font-size: 0.9rem; }
    </style>
</head>
<body data-theme="dark">
    
    <nav class="topbar">
        <div style="display:flex;align-items:center;">
            <div class="topbar-logo"><i class="bi bi-box-seam"></i> <span>OSWA Inv</span></div>
            <div class="topbar-nav">
                <a href="{{ route('inventario') }}">Dashboard</a>
                <a href="{{ route('vencimientos') }}">Vencimientos</a>
                <a href="{{ route('auditoria') }}">Auditoría</a>
                <a href="{{ route('usuarios.index') }}" class="active">Usuarios</a>
            </div>
        </div>
        <div class="topbar-right">
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
                    <div class="dd-divider"></div>
                    <button class="dd-item dd-logout" onclick="document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="main-content">
        <div class="page-header">
            <div class="page-title">
                <i class="bi bi-people"></i> Administración de Personal
            </div>
            <button class="btn-nuevo" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                <i class="bi bi-plus-lg"></i> Nuevo Empleado
            </button>
        </div>
        
        <div class="card-netflix">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Registrado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight:600;">{{ $user->name }}</td>
                        <td style="color:#888;">{{ $user->email }}</td>
                        <td><span class="badge-rol {{ $user->rol === 'admin' ? 'badge-admin' : 'badge-empleado' }}">{{ ucfirst($user->rol) }}</span></td>
                        <td style="color:#666;font-size:0.8rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($user->id !== auth()->id())
                            <button class="btn-accion {{ $user->is_active ? 'activo' : '' }}" onclick="cambiarEstatus({{ $user->id }}, '{{ $user->name }}', {{ $user->is_active }})">
                                <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'check-circle' }}"></i> {{ $user->is_active ? 'Suspender' : 'Activar' }}
                            </button>
                            @else
                            <span style="color:#666;font-size:0.8rem;">Tú</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5" style="color:#666;"><i class="bi bi-people" style="font-size:2rem;"></i><p class="mt-2">No hay usuarios registrados</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="logs-table">
            <div class="table-title"><i class="bi bi-clock-history"></i> Últimos 20 Accesos al Sistema</div>
            <div class="card-netflix">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Dirección IP</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight:600;">{{ $log->user?->name ?? 'Desconocido' }}</td>
                            <td style="color:#888;">{{ $log->user?->email ?? 'N/A' }}</td>
                            <td style="color:#aaa;font-family:monospace;">{{ $log->ip_address }}</td>
                            <td style="color:#666;font-size:0.8rem;">{{ $log->login_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5" style="color:#666;"><i class="bi bi-shield-lock" style="font-size:2rem;"></i><p class="mt-2">No hay registros de acceso recientes</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="nuevoUsuarioForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-control" name="rol" required>
                                <option value="empleado">Empleado</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-nuevo"><i class="bi bi-save me-1"></i>Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        
        function toggleTheme() {
            const body = document.body;
            const current = body.getAttribute('data-theme');
            body.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
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
                </div>`,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#E50914',
                background: '#141414',
                color: '#fff'
            });
        }
        
        document.getElementById('nuevoUsuarioForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route('usuarios.guardar') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario Creado',
                        text: data.message || 'Empleado registrado correctamente',
                        confirmButtonColor: '#E50914',
                        background: '#141414',
                        color: '#fff',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo crear el usuario',
                        confirmButtonColor: '#E50914',
                        background: '#141414',
                        color: '#fff'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    confirmButtonColor: '#E50914',
                    background: '#141414',
                    color: '#fff'
                });
            }
        });
        
        function cambiarEstatus(id, nombre, is_active) {
            const accion = is_active ? 'suspender' : 'activar';
            Swal.fire({
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
                html: `Estás a punto de ${accion} a <strong style="color:#E50914;">${nombre}</strong>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#333',
                background: '#141414',
                color: '#fff'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('{{ route('usuarios.cambiarEstatus') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id: id })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Actualizado',
                                text: data.message,
                                confirmButtonColor: '#E50914',
                                background: '#141414',
                                color: '#fff',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo actualizar el estatus',
                                confirmButtonColor: '#E50914',
                                background: '#141414',
                                color: '#fff'
                            });
                        }
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión', confirmButtonColor: '#E50914', background: '#141414', color: '#fff' });
                    }
                }
            });
        }
    </script>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
