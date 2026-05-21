<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Misiones - OSWA Inv</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #141414; color: #fff; font-family: 'Inter', sans-serif; min-height: 100vh; }
        .netflix-bg { background: linear-gradient(to bottom, #1a1a1a 0%, #141414 100%); min-height: 100vh; }
        .netflix-header { padding: 2rem 0 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 2rem; }
        .netflix-header h1 { font-size: 2.2rem; font-weight: 700; letter-spacing: -0.5px; }
        .netflix-header h1 small { font-size: 1rem; font-weight: 400; color: #888; }
        .nf-card { background: #1c1c1c; border: none; border-radius: 12px; padding: 1.5rem; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .nf-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, #E50914, #E50914 50%, transparent 50%); opacity: 0; transition: opacity 0.3s ease; }
        .nf-card:hover::before { opacity: 1; }
        .nf-card:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .nf-card-title { font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 10px; }
        .nf-input { background: #2a2a2a; border: 1px solid #333; border-radius: 8px; color: #fff; padding: 0.7rem 1rem; font-size: 0.9rem; width: 100%; transition: border-color 0.3s; }
        .nf-input:focus { border-color: #E50914; outline: none; box-shadow: 0 0 0 2px rgba(229,9,20,0.2); }
        .nf-input::placeholder { color: #666; }
        .nf-select { background: #2a2a2a; border: 1px solid #333; border-radius: 8px; color: #fff; padding: 0.7rem 1rem; width: 100%; }
        .nf-select:focus { border-color: #E50914; outline: none; }
        .nf-btn { background: #E50914; border: none; border-radius: 8px; color: #fff; font-weight: 600; padding: 0.7rem 1.5rem; transition: all 0.3s; width: 100%; }
        .nf-btn:hover { background: #b20710; transform: scale(1.02); }
        .nf-btn-outline { background: transparent; border: 1px solid #333; border-radius: 8px; color: #aaa; padding: 0.5rem 1rem; transition: all 0.3s; }
        .nf-btn-outline:hover { border-color: #E50914; color: #E50914; }
        .mission-item { background: #181818; border-radius: 10px; padding: 1rem 1.2rem; margin-bottom: 0.7rem; border-left: 3px solid #ffc107; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center; }
        .mission-item:hover { background: #1f1f1f; }
        .mission-item.completed { border-left-color: #00b894; }
        .mission-item .badge-state { font-size: 0.7rem; font-weight: 600; padding: 0.3rem 0.8rem; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-pendiente { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
        .badge-completada { background: rgba(0, 184, 148, 0.15); color: #00b894; }
        .badge-fallida { background: rgba(229, 9, 20, 0.15); color: #E50914; }
        .employee-chip { display: flex; align-items: center; justify-content: space-between; background: #181818; padding: 0.6rem 1rem; border-radius: 8px; margin-bottom: 0.4rem; transition: all 0.3s; }
        .employee-chip:hover { background: #1f1f1f; }
        .employee-chip .count-badge { background: rgba(229,9,20,0.15); color: #E50914; font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 12px; }
        .presence-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; transition: all 0.3s; margin-right: 8px; }
        .presence-dot.online { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        .presence-dot.offline { background: #555; }
        .back-link { color: #666; text-decoration: none; font-size: 0.9rem; transition: color 0.3s; }
        .back-link:hover { color: #E50914; }
        .completion-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
        .completion-dot.done { background: #00b894; box-shadow: 0 0 8px rgba(0,184,148,0.5); }
        .completion-dot.pending { background: #ffc107; box-shadow: 0 0 8px rgba(255,193,7,0.3); }
        .stat-mini { background: #181818; border-radius: 10px; padding: 1rem; text-align: center; }
        .stat-mini .num { font-size: 1.8rem; font-weight: 800; }
        .stat-mini .lbl { font-size: 0.7rem; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #141414; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        label { font-size: 0.8rem; color: #888; font-weight: 500; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .nf-toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 12px; pointer-events: none; max-width: 420px; }
        .nf-toast { pointer-events: auto; background: rgba(28,28,28,0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); color: #fff; padding: 16px 20px; border-radius: 12px; border: 1px solid rgba(229,9,20,0.25); box-shadow: 0 8px 32px rgba(0,0,0,0.6), 0 0 0 1px rgba(229,9,20,0.1) inset; display: flex; align-items: center; gap: 14px; font-size: 0.9rem; font-weight: 500; min-width: 320px; animation: nfToastIn 0.5s cubic-bezier(0.16, 1, 0.3, 1); position: relative; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .nf-toast:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,0.7), 0 0 0 1px rgba(229,9,20,0.2) inset; }
        .nf-toast i { font-size: 1.4rem; color: #E50914; flex-shrink: 0; filter: drop-shadow(0 0 6px rgba(229,9,20,0.4)); }
        .nf-toast .toast-progress { position: absolute; bottom: 0; left: 0; height: 3px; background: linear-gradient(90deg, #E50914, #ff6b6b); animation: nfToastProgress 3s linear forwards; border-radius: 0 2px 0 0; }
        .nf-toast.removing { animation: nfToastOut 0.4s cubic-bezier(0.55, 0, 0.55, 0.4) forwards; }
        .nf-toast.removing .toast-progress { animation-play-state: paused; }
        @keyframes nfToastIn { from { transform: translateY(40px) scale(0.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
        @keyframes nfToastOut { from { transform: translateY(0) scale(1); opacity: 1; } to { transform: translateY(20px) scale(0.95); opacity: 0; } }
        @keyframes nfToastProgress { from { width: 100%; } to { width: 0%; } }
        @keyframes nfToastIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes nfToastOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(120%); opacity: 0; } }
    </style>
</head>
<body>
    <div class="netflix-bg">
        <div class="nf-toast-container" id="nf-toast-container"></div>
        <div class="container">
            <div class="netflix-header d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-flag-fill text-danger me-2"></i>Misiones <small>Gestión de objetivos</small></h1>
                </div>
                <a href="{{ route('inventario') }}" class="back-link"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="nf-card">
                        <div class="nf-card-title"><i class="bi bi-person-plus fs-5" style="color: #E50914;"></i> Asignar Misión</div>
                        <form id="form-mision" onsubmit="asignarMision(event)">
                            <div class="mb-3">
                                <label>Título</label>
                                <input type="text" name="titulo" class="nf-input" placeholder="Nombre de la misión" required>
                            </div>
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea name="descripcion" class="nf-input" rows="2" placeholder="Detalles de la misión"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Vence</label>
                                <input type="date" name="fecha_vencimiento" class="nf-input">
                            </div>
                            <div class="mb-3">
                                <label>Empleado</label>
                                <select name="user_id" class="nf-select" required>
                                    <option value="">Seleccionar</option>
                                    @foreach($usuarios as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="nf-btn">Asignar Misión</button>
                        </form>
                    </div>

                    <div class="nf-card">
                        <div class="nf-card-title"><i class="bi bi-people fs-5" style="color: #E50914;"></i> Empleados</div>
                        <div id="empleados-container">
                        @forelse($usuarios as $u)
                            <div class="employee-chip" data-user-id="{{ $u->id }}">
                                <span><span class="presence-dot offline" id="dot-{{ $u->id }}"></span><i class="bi bi-person-circle me-2" style="color: #555;"></i>{{ $u->name }}</span>
                                <span class="count-badge">{{ $u->misiones()->where('estado', 'pendiente')->count() }} pendiente(s)</span>
                            </div>
                        @empty
                            <p class="text-secondary text-center py-3" id="sin-empleados" style="font-size: 0.9rem;">No hay empleados registrados.</p>
                        @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="nf-card">
                        <div class="nf-card-title"><i class="bi bi-clock-history fs-5" style="color: #E50914;"></i> Historial</div>

                        <div class="row g-2 mb-4">
                            <div class="col-3">
                                <div class="stat-mini">
                                    <div class="num" style="color: #E50914;">{{ $misiones->count() }}</div>
                                    <div class="lbl">Totales</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="stat-mini">
                                    <div class="num" style="color: #00b894;">{{ $misiones->where('estado', 'completada')->count() }}</div>
                                    <div class="lbl">Aprobadas</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="stat-mini">
                                    <div class="num" style="color: #E50914;">{{ $misiones->where('estado', 'fallida')->count() }}</div>
                                    <div class="lbl">Rechazadas</div>
                                </div>
                            </div>
                            <div class="col-3">
                                @php $total = $misiones->count(); $completadas = $misiones->where('estado', 'completada')->count(); $tasa = $total > 0 ? round(($completadas / $total) * 100) : 0; @endphp
                                <div class="stat-mini">
                                    <div class="num" style="color: {{ $tasa >= 50 ? '#00b894' : '#ffc107' }};">{{ $tasa }}%</div>
                                    <div class="lbl">Tasa de Éxito</div>
                                </div>
                            </div>
                        </div>

                        @forelse($misiones as $m)
                            <div class="mission-item {{ $m->estado === 'completada' ? 'completed' : '' }}">
                                <div class="d-flex align-items-center gap-3" style="flex: 1;">
                                    <span class="completion-dot {{ $m->estado === 'completada' ? 'done' : ($m->estado === 'fallida' ? 'pending' : 'pending') }}" style="{{ $m->estado === 'fallida' ? 'background: #E50914; box-shadow: 0 0 8px rgba(229,9,20,0.3);' : '' }}"></span>
                                    <div style="flex: 1;">
                                        <h6 class="mb-1" style="font-weight: 600; font-size: 0.95rem;">{{ $m->titulo }}</h6>
                                        <small style="color: #666;">
                                            <i class="bi bi-person me-1"></i>{{ $m->user->name ?? '—' }}
                                            @if($m->fecha_vencimiento)
                                                <i class="bi bi-calendar ms-2 me-1"></i>{{ \Carbon\Carbon::parse($m->fecha_vencimiento)->format('d/m/Y') }}
                                            @endif
                                            @if($m->estado === 'completada' && $m->updated_at)
                                                <i class="bi bi-check-circle ms-2 me-1" style="color: #00b894;"></i>{{ $m->updated_at->format('d/m/Y H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge-state {{ $m->estado === 'completada' ? 'badge-completada' : ($m->estado === 'fallida' ? 'badge-fallida' : 'badge-pendiente') }}">
                                        {{ $m->estado === 'completada' ? 'Completada' : ($m->estado === 'fallida' ? 'Fallida' : 'Pendiente') }}
                                    </span>
                                    <button onclick="aprobarMision({{ $m->id }}, this)" class="btn btn-sm" title="Aprobar" style="background: rgba(0,184,148,0.15); color: #00b894; border: 1px solid rgba(0,184,148,0.3); border-radius: 6px; font-weight: 600; padding: 3px 10px; font-size: 0.7rem;">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button onclick="rechazarMision({{ $m->id }}, this)" class="btn btn-sm" title="Rechazar" style="background: rgba(229,9,20,0.15); color: #E50914; border: 1px solid rgba(229,9,20,0.3); border-radius: 6px; font-weight: 600; padding: 3px 10px; font-size: 0.7rem;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5" style="color: #444;">
                                <i class="bi bi-flag" style="font-size: 3rem;"></i>
                                <p class="mt-2" style="font-size: 0.9rem;">No hay misiones registradas.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        function mostrarToast(mensaje, icono = 'bi bi-check-circle-fill') {
            const container = document.getElementById('nf-toast-container');
            if (!container) return;
            const t = document.createElement('div');
            t.className = 'nf-toast';
            t.innerHTML = '<i class="bi ' + icono + '"></i><span style="flex:1;">' + mensaje + '</span><div class="toast-progress"></div>';
            container.appendChild(t);
            setTimeout(() => {
                t.classList.add('removing');
                setTimeout(() => t.remove(), 400);
            }, 3000);
        }

        async function asignarMision(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            try {
                const res = await fetch('{{ route("misiones.store") }}', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: data
                });
                const r = await res.json();
                if (r.success) { mostrarToast(r.message, 'bi bi-check-circle-fill'); setTimeout(() => window.location.reload(), 800); }
                else { mostrarToast(r.message || 'Error', 'bi bi-exclamation-triangle-fill'); }
            } catch (err) { mostrarToast('Error de conexión.', 'bi bi-exclamation-triangle-fill'); }
        }

        async function aprobarMision(id, btn) {
            btn.disabled = true;
            const parent = btn.closest('.d-flex.align-items-center.gap-2');
            try {
                const res = await fetch('/gestion/misiones/' + id + '/aprobar', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const r = await res.json();
                if (r.success) {
                    if (parent) {
                        const btns = parent.querySelectorAll('button');
                        btns.forEach(b => b.remove());
                        const badge = parent.querySelector('.badge-state');
                        if (badge) { badge.className = 'badge-state badge-completada'; badge.textContent = 'Completada'; }
                    }
                    mostrarToast(r.message, 'bi bi-check-circle-fill');
                } else {
                    btn.disabled = false;
                    mostrarToast(r.message || 'Error', 'bi bi-exclamation-triangle-fill');
                }
            } catch (err) { btn.disabled = false; mostrarToast('Error de conexión.', 'bi bi-exclamation-triangle-fill'); }
        }

        async function rechazarMision(id, btn) {
            btn.disabled = true;
            const parent = btn.closest('.d-flex.align-items-center.gap-2');
            try {
                const res = await fetch('/gestion/misiones/' + id + '/rechazar', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const r = await res.json();
                if (r.success) {
                    if (parent) {
                        const btns = parent.querySelectorAll('button');
                        btns.forEach(b => b.remove());
                        const badge = parent.querySelector('.badge-state');
                        if (badge) { badge.className = 'badge-state badge-fallida'; badge.textContent = 'Fallida'; }
                    }
                    mostrarToast(r.message, 'bi bi-check-circle-fill');
                } else {
                    btn.disabled = false;
                    mostrarToast(r.message || 'Error', 'bi bi-exclamation-triangle-fill');
                }
            } catch (err) { btn.disabled = false; mostrarToast('Error de conexión.', 'bi bi-exclamation-triangle-fill'); }
        }

        async function revertirMision(id) {
            try {
                const res = await fetch('/gestion/misiones/' + id + '/revertir', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const r = await res.json();
                if (r.success) { mostrarToast(r.message, 'bi bi-check-circle-fill'); setTimeout(() => window.location.reload(), 800); }
                else { mostrarToast(r.message || 'Error', 'bi bi-exclamation-triangle-fill'); }
            } catch (err) { mostrarToast('Error de conexión.', 'bi bi-exclamation-triangle-fill'); }
        }

        // ─── Presencia en Vivo ───
        async function actualizarPresencia() {
            try {
                const res = await fetch('/api/online-users');
                const data = await res.json();
                const onlineIds = data.online_ids || [];
                document.querySelectorAll('.employee-chip').forEach(chip => {
                    const uid = parseInt(chip.dataset.userId);
                    const dot = document.getElementById('dot-' + uid);
                    if (dot) {
                        dot.className = 'presence-dot ' + (onlineIds.includes(uid) ? 'online' : 'offline');
                    }
                });
            } catch(e) { /* silencio */ }
        }
        actualizarPresencia();
        setInterval(actualizarPresencia, 15000);
    </script>
</body>
</html>