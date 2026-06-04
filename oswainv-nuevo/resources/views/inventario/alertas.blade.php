<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Alertas de Inventario - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('partials.navbar')
    <style>
        :root { --bg: #0f0f0f; --card: #1a1a1a; --n-red: #E50914; --n-border: #2a2a2a; --text: #e5e5e5; }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; }
        .main-content { padding-top: 80px; }
        .alerts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .alert-stat { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1.5rem; position: relative; overflow: hidden; animation: fadeUp 0.5s ease; transition: all 0.3s; }
        .alert-stat:hover { transform: translateY(-4px); }
        .alert-stat:nth-child(2) { animation-delay: 0.1s; }
        .alert-stat:nth-child(3) { animation-delay: 0.2s; }
        .alert-stat:nth-child(4) { animation-delay: 0.3s; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .alert-stat-icon { font-size: 2rem; margin-bottom: 8px; }
        .alert-stat-value { font-size: 2rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .alert-stat-label { font-size: 0.7rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .tabs { display: flex; gap: 0; background: var(--card); border: 1px solid var(--n-border); border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; }
        .tab { flex: 1; padding: 0.8rem 1rem; text-align: center; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #666; transition: all 0.3s; border-bottom: 2px solid transparent; }
        .tab:hover { color: #fff; background: rgba(255,255,255,0.03); }
        .tab.active { color: var(--n-red); border-bottom-color: var(--n-red); background: rgba(229,9,20,0.06); }
        .tab-pane { display: none; animation: fadeUp 0.4s ease; }
        .tab-pane.active { display: block; }
        .alert-item { display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1rem; background: var(--card); border: 1px solid var(--n-border); border-radius: 12px; margin-bottom: 6px; transition: all 0.3s; }
        .alert-item:hover { border-color: var(--n-red); transform: translateX(4px); }
        .alert-item.critical { border-left: 3px solid var(--n-red); }
        .alert-item.warning { border-left: 3px solid #ffc107; }
        .alert-item.ok { border-left: 3px solid #00b894; }
        .alert-icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .alert-icon-box.danger { background: rgba(229,9,20,0.12); color: var(--n-red); }
        .alert-icon-box.warning { background: rgba(253,203,110,0.12); color: #ffc107; }
        .alert-icon-box.success { background: rgba(0,184,148,0.12); color: #00b894; }
        .alert-info { flex: 1; min-width: 0; }
        .alert-name { font-weight: 600; color: #fff; font-size: 0.9rem; }
        .alert-meta { font-size: 0.75rem; color: #666; }
        .alert-badge { padding: 3px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; flex-shrink: 0; }
        .alert-badge.danger { background: rgba(229,9,20,0.15); color: var(--n-red); }
        .alert-badge.warning { background: rgba(253,203,110,0.15); color: #ffc107; }
        .alert-badge.success { background: rgba(0,184,148,0.15); color: #00b894; }
        .prod-bar { height: 4px; border-radius: 2px; margin-top: 4px; background: #2a2a2a; overflow: hidden; }
        .prod-bar-fill { height: 100%; border-radius: 2px; transition: width 0.8s ease; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--card); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container-fluid px-4 py-4" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="text-white fw-bold mb-0"><i class="bi bi-bell-fill me-2" style="color:var(--n-red);"></i>Alertas de Inventario</h4>
                <div style="color:#666;font-size:0.8rem;margin-top:4px;">Monitor de stock y vencimientos en tiempo real</div>
            </div>
            <div style="font-size:0.75rem;color:#555;">
                <i class="bi bi-arrow-repeat me-1"></i>Actualizado {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="alerts-grid">
            <div class="alert-stat">
                <div class="alert-stat-icon" style="color:var(--n-red);"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="alert-stat-value" style="color:var(--n-red);">{{ $bajoStock->count() }}</div>
                <div class="alert-stat-label">Stock Bajo</div>
            </div>
            <div class="alert-stat">
                <div class="alert-stat-icon" style="color:#ffc107;"><i class="bi bi-clock-fill"></i></div>
                <div class="alert-stat-value" style="color:#ffc107;">{{ $porVencer->count() }}</div>
                <div class="alert-stat-label">Por Vencer</div>
            </div>
            <div class="alert-stat">
                <div class="alert-stat-icon" style="color:var(--n-red);"><i class="bi bi-box-seam"></i></div>
                <div class="alert-stat-value" style="color:var(--n-red);">{{ $sinStock->count() }}</div>
                <div class="alert-stat-label">Sin Stock</div>
            </div>
            <div class="alert-stat">
                <div class="alert-stat-icon" style="color:#888;"><i class="bi bi-check-circle-fill"></i></div>
                <div class="alert-stat-value" style="color:#00b894;">{{ $saludable }}</div>
                <div class="alert-stat-label">Saludables</div>
            </div>
        </div>

        <div class="tabs">
            <div class="tab active" data-tab="bajo-stock" onclick="cambiarTab('bajo-stock')">
                <i class="bi bi-exclamation-triangle me-1"></i> Bajo Stock ({{ $bajoStock->count() }})
            </div>
            <div class="tab" data-tab="por-vencer" onclick="cambiarTab('por-vencer')">
                <i class="bi bi-calendar-x me-1"></i> Por Vencer ({{ $porVencer->count() }})
            </div>
            <div class="tab" data-tab="sin-stock" onclick="cambiarTab('sin-stock')">
                <i class="bi bi-box-seam me-1"></i> Sin Stock ({{ $sinStock->count() }})
            </div>
            <div class="tab" data-tab="todo" onclick="cambiarTab('todo')">
                <i class="bi bi-list me-1"></i> Todo
            </div>
        </div>

        <div class="tab-pane active" id="tab-bajo-stock">
            @forelse($bajoStock as $p)
            <div class="alert-item critical">
                <div class="alert-icon-box danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="alert-info">
                    <div class="alert-name">{{ $p->nombre }}</div>
                    <div class="alert-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · Mín: {{ $p->stock_minimo }} {{ $p->unidad_medida ?? 'uds' }}</div>
                    <div class="prod-bar"><div class="prod-bar-fill" style="width:{{ min(100, ($p->stock / max($p->stock_minimo, 1)) * 100) }}%;background:{{ $p->stock == 0 ? 'var(--n-red)' : '#ffc107' }};"></div></div>
                </div>
                <div class="alert-badge danger">{{ $p->stock }} / {{ $p->stock_minimo }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:3rem;color:#555;"><i class="bi bi-check-circle-fill" style="font-size:3rem;color:#00b894;"></i><p class="mt-2">No hay productos con stock bajo</p></div>
            @endforelse
        </div>

        <div class="tab-pane" id="tab-por-vencer">
            @forelse($porVencer as $p)
            @php
                $dias = now()->diffInDays(\Carbon\Carbon::parse($p->fecha_vencimiento), false);
                $critico = $dias <= 7;
            @endphp
            <div class="alert-item {{ $critico ? 'critical' : 'warning' }}">
                <div class="alert-icon-box {{ $critico ? 'danger' : 'warning' }}"><i class="bi bi-calendar-x"></i></div>
                <div class="alert-info">
                    <div class="alert-name">{{ $p->nombre }}</div>
                    <div class="alert-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · Stock: {{ $p->stock }} {{ $p->unidad_medida ?? 'uds' }}</div>
                </div>
                <div class="alert-badge {{ $critico ? 'danger' : 'warning' }}">
                    @if($dias < 0) VENCIDO @else {{ floor($dias) }} días @endif
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:3rem;color:#555;"><i class="bi bi-calendar-check-fill" style="font-size:3rem;color:#00b894;"></i><p class="mt-2">No hay productos por vencer</p></div>
            @endforelse
        </div>

        <div class="tab-pane" id="tab-sin-stock">
            @forelse($sinStock as $p)
            <div class="alert-item critical">
                <div class="alert-icon-box danger"><i class="bi bi-box-seam"></i></div>
                <div class="alert-info">
                    <div class="alert-name">{{ $p->nombre }}</div>
                    <div class="alert-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · Categoría: {{ $p->categoria }}</div>
                </div>
                <div class="alert-badge danger">0 uds</div>
            </div>
            @empty
            <div style="text-align:center;padding:3rem;color:#555;"><i class="bi bi-check-circle-fill" style="font-size:3rem;color:#00b894;"></i><p class="mt-2">No hay productos agotados</p></div>
            @endforelse
        </div>

        <div class="tab-pane" id="tab-todo">
            <div style="display:flex;flex-direction:column;gap:6px;">
                @forelse($todasAlertas as $p)
                @php
                    $esBajo = $p->stock_bajo;
                    $esVencer = $p->fecha_vencimiento && now()->diffInDays(\Carbon\Carbon::parse($p->fecha_vencimiento), false) <= 30;
                    $esSin = $p->stock == 0;
                    $tipo = $esSin ? 'danger' : ($esBajo ? 'warning' : 'warning');
                    $icono = $esSin ? 'bi-box-seam' : ($esBajo ? 'bi-exclamation-triangle' : 'bi-calendar-x');
                    $label = $esSin ? '0 uds' : ($esBajo ? $p->stock.'/'.$p->stock_minimo : (\Carbon\Carbon::parse($p->fecha_vencimiento)->format('d/m/Y')));
                @endphp
                <div class="alert-item {{ $esSin || ($esBajo && $p->stock == 0) ? 'critical' : 'warning' }}">
                    <div class="alert-icon-box {{ $tipo }}"><i class="bi {{ $icono }}"></i></div>
                    <div class="alert-info">
                        <div class="alert-name">{{ $p->nombre }}</div>
                        <div class="alert-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · {{ $p->stock }} {{ $p->unidad_medida ?? 'uds' }} @if($esVencer)· Vence {{ \Carbon\Carbon::parse($p->fecha_vencimiento)->format('d/m') }}@endif</div>
                    </div>
                    <div class="alert-badge {{ $tipo }}">{{ $label }}</div>
                </div>
                @empty
                <div style="text-align:center;padding:3rem;color:#555;"><i class="bi bi-emoji-smile-fill" style="font-size:3rem;color:#00b894;"></i><p class="mt-2">Todo en orden, no hay alertas</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@include('partials.mobile-bottom-nav')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function cambiarTab(id) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    document.querySelector('.tab[data-tab="' + id + '"]').classList.add('active');
}
</script>
</body>
</html>
