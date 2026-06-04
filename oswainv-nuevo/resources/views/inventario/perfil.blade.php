<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @include('partials.navbar')
    <style>
        :root { --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3; }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--bg-main); color: var(--text-primary); min-height: 100vh; }
        .profile-header { background: linear-gradient(135deg, #1c1c1c 0%, #2a1515 100%); border: 1px solid var(--n-border); border-radius: 20px; padding: 2rem; margin-top: 2rem; position: relative; overflow: hidden; }
        .profile-header::before { content: ''; position: absolute; top: -50%; right: -20%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(229,9,20,0.08) 0%, transparent 70%); pointer-events: none; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--n-red); }
        .profile-avatar-placeholder { width: 100px; height: 100px; border-radius: 50%; background: var(--n-red); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: #fff; border: 3px solid var(--n-red); }
        .xp-bar { height: 12px; background: #2a2a2a; border-radius: 6px; overflow: hidden; }
        .xp-bar-fill { height: 100%; background: linear-gradient(90deg, #E50914, #ff6b6b); border-radius: 6px; transition: width 1s ease; }
        .section-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 10px; }
        .achievement-card { background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 14px; padding: 1.2rem; text-align: center; transition: all 0.3s ease; opacity: 1; }
        .achievement-card:hover { transform: translateY(-4px); border-color: var(--n-red); }
        .achievement-card.locked { opacity: 0.4; filter: grayscale(0.8); }
        .achievement-card.locked:hover { opacity: 0.6; border-color: #555; transform: none; }
        .achievement-icon { font-size: 2.2rem; margin-bottom: 8px; }
        .achievement-name { font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; }
        .achievement-desc { font-size: 0.7rem; color: var(--text-secondary); }
        .achievement-xp { font-size: 0.65rem; color: #ffd700; margin-top: 6px; }
        .stat-label-sm { font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value-lg { font-size: 1.8rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .badge-role { background: rgba(229,9,20,0.15); color: var(--n-red); border: 1px solid rgba(229,9,20,0.3); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .streak-fire { color: #ff6b6b; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
        .chart-container { background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 14px; padding: 1.5rem; margin-top: 1.5rem; }
    </style>
</head>
<body>
<div class="container py-4" style="max-width: 1100px;">
    <a href="{{ route('inventario') }}" class="btn btn-sm mb-3" style="background: rgba(255,255,255,0.05); color: #888; border: 1px solid var(--n-border); border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Volver al Dashboard
    </a>

    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-auto">
                @if($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="" class="profile-avatar">
                @else
                    <div class="profile-avatar-placeholder">{{ strtoupper(substr($user->display_name, 0, 1)) }}</div>
                @endif
            </div>
            <div class="col">
                <h2 class="mb-1 fw-bold">{{ $user->display_name }}</h2>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge-role">
                        @switch($user->rol)
                            @case('admin') <i class="bi bi-shield-fill-check me-1"></i>Jefe @break
                            @case('desarrollador') <i class="bi bi-code-slash me-1"></i>Desarrollador @break
                            @default <i class="bi bi-person me-1"></i>{{ ucfirst($user->rol) }}
                        @endswitch
                    </span>
                    @if($user->nick)
                        <span style="color: var(--text-secondary); font-size: 0.8rem;"><i class="bi bi-at me-1"></i>{{ $user->nick }}</span>
                    @endif
                    <span style="color: var(--text-secondary); font-size: 0.8rem;"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-2">
                    <div>
                        <div class="stat-label-sm">Nivel</div>
                        <div class="stat-value-lg" style="color: var(--n-red);">{{ $nivel }}</div>
                    </div>
                    <div>
                        <div class="stat-label-sm">Experiencia</div>
                        <div class="stat-value-lg" style="color: #ffd700;">{{ $xpActual }} <span style="font-size: 0.9rem; color: var(--text-secondary);">/ {{ $xpSiguiente }}</span></div>
                    </div>
                    <div>
                        <div class="stat-label-sm">Racha</div>
                        <div class="stat-value-lg"><span class="streak-fire"><i class="bi bi-fire"></i></span> {{ $user->current_streak ?? 0 }} <span style="font-size: 0.7rem; color: var(--text-secondary);">días</span></div>
                    </div>
                    <div>
                        <div class="stat-label-sm">Logros</div>
                        <div class="stat-value-lg" style="color: #00b894;">{{ $desbloqueados }} <span style="font-size: 0.7rem; color: var(--text-secondary);">/ {{ $totalLogros }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size: 0.75rem; color: var(--text-secondary);">Progreso al Nivel {{ $nivel + 1 }}</span>
                <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ $xpBar }}%</span>
            </div>
            <div class="xp-bar"><div class="xp-bar-fill" style="width: {{ $xpBar }}%;"></div></div>
        </div>
    </div>

    <div class="mt-4">
        <h3 class="section-title"><i class="bi bi-trophy-fill" style="color: #ffd700;"></i> Logros</h3>
        <div class="row g-3">
            @foreach($achievements as $ach)
                @php $unlocked = $user->achievements->contains($ach->id); @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="achievement-card {{ !$unlocked ? 'locked' : '' }}" title="{{ $ach->description }}">
                        <div class="achievement-icon">{{ $unlocked ? "<i class=\"bi {$ach->icon}\" style=\"color: #ffd700;\"></i>" : "<i class=\"bi bi-lock-fill\" style=\"color: #555;\"></i>" }}</div>
                        <div class="achievement-name">{{ $ach->name }}</div>
                        <div class="achievement-desc">{{ $ach->description }}</div>
                        @if($unlocked)
                            <div class="achievement-xp"><i class="bi bi-star-fill me-1"></i>+{{ $ach->xp_reward }} XP</div>
                            <div style="font-size: 0.6rem; color: #00b894; margin-top: 4px;"><i class="bi bi-check-circle-fill me-1"></i>{{ $ach->pivot->unlocked_at ? \Carbon\Carbon::parse($ach->pivot->unlocked_at)->format('d/m/Y') : '' }}</div>
                        @else
                            <div class="achievement-xp" style="color: #666;"><i class="bi bi-star me-1"></i>+{{ $ach->xp_reward }} XP</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="chart-container mt-4">
        <h4 style="font-weight: 600; margin-bottom: 1rem;"><i class="bi bi-activity me-2" style="color: var(--n-red);"></i>Mi Actividad Reciente</h4>
        <canvas id="activityChart" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($activityLabels) !!},
            datasets: [{
                label: 'Movimientos',
                data: {!! json_encode($activityData) !!},
                borderColor: '#E50914',
                backgroundColor: 'rgba(229,9,20,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#E50914',
                pointBorderColor: '#fff',
                pointBorderWidth: 1,
                pointRadius: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#666', maxTicksLimit: 10 }, grid: { color: 'rgba(255,255,255,0.03)' } },
                y: { beginAtZero: true, ticks: { color: '#666', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.03)' } }
            }
        }
    });
});
</script>
</body>
</html>
