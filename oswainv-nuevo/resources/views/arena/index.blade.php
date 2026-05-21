<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OSWA Arena - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #141414; color: #fff; font-family: 'Inter', sans-serif; min-height: 100vh; }

        @keyframes rgbArena { 0% { background-position: 0% 50%; } 100% { background-position: 300% 50%; } }

        .arena-container { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }
        .arena-header { text-align: center; margin-bottom: 2.5rem; position: relative; }
        .arena-header::after { content: ''; display: block; width: 80px; height: 3px; background: #E50914; margin: 1rem auto 0; border-radius: 2px; }
        .arena-logo-img { height: 48px; filter: drop-shadow(0 0 8px rgba(229,9,20,0.4)) brightness(0) invert(1); transform: perspective(400px) rotateY(-5deg); transition: transform .4s ease; }
        .arena-logo-img:hover { transform: perspective(400px) rotateY(0deg) scale(1.05); }
        .arena-title-text { font-size: 2rem; font-weight: 800; animation: rgbArena 3s linear infinite; background: linear-gradient(90deg,#E50914,#ff6b6b,#B20710,#E50914); background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 0 10px rgba(229,9,20,0.3)); letter-spacing: -1px; }
        .arena-subtitle { color: #999; margin-top: .3rem; font-size: .9rem; }

        .arena-card { background: linear-gradient(145deg, rgba(30,30,30,0.9), rgba(20,20,20,0.95)); border-radius: 16px; padding: 1.5rem; border: 1px solid rgba(255,255,255,0.06); backdrop-filter: blur(10px); transition: all .3s ease; position: relative; overflow: hidden; }
        .arena-card:hover { border-color: rgba(229,9,20,0.2); transform: translateY(-2px); box-shadow: 0 8px 30px rgba(229,9,20,0.08); }
        .arena-card .card-glow { position: absolute; top: -50%; right: -50%; width: 100%; height: 100%; background: radial-gradient(circle, rgba(229,9,20,0.03) 0%, transparent 70%); pointer-events: none; }

        .level-circle { width: 88px; height: 88px; border-radius: 50%; background: linear-gradient(135deg,#E50914,#ff6b6b); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: #fff; margin: 0 auto .8rem; box-shadow: 0 0 30px rgba(229,9,20,.3), inset 0 2px 4px rgba(255,255,255,.15); position: relative; }
        .level-circle::after { content: ''; position: absolute; inset: -3px; border-radius: 50%; border: 2px solid rgba(255,255,255,.1); }

        .xp-bar-track { height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden; margin: .8rem 0; }
        .xp-bar-fill { height: 100%; background: linear-gradient(90deg,#E50914,#ff6b6b); border-radius: 3px; transition: width .8s cubic-bezier(.4,0,.2,1); position: relative; }
        .xp-bar-fill::after { content: ''; position: absolute; right: 0; top: 0; width: 20px; height: 100%; background: rgba(255,255,255,.3); filter: blur(4px); }

        .stat-value { font-size: 2.2rem; font-weight: 700; line-height: 1.2; }
        .stat-label { font-size: .75rem; color: #888; text-transform: uppercase; letter-spacing: .8px; font-weight: 500; margin-top: 2px; }

        .rank-badge-netflix { display: inline-flex; align-items: center; gap: 6px; background: rgba(229,9,20,.12); color: #E50914; padding: .3rem 1rem; border-radius: 20px; font-weight: 700; font-size: .9rem; border: 1px solid rgba(229,9,20,.15); }

        .leader-item { display: flex; align-items: center; gap: 12px; padding: .7rem 1rem; border-radius: 8px; transition: background .2s; cursor: default; }
        .leader-item:hover { background: rgba(255,255,255,.04); }
        .leader-item .rank-num { font-size: 1.1rem; font-weight: 700; width: 28px; text-align: center; color: #555; font-variant-numeric: tabular-nums; }
        .leader-item .rank-num.r1 { color: #ffd700; } .leader-item .rank-num.r2 { color: #c0c0c0; } .leader-item .rank-num.r3 { color: #cd7f32; }
        .leader-item .l-avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg,#333,#444); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; color: #fff; overflow: hidden; flex-shrink: 0; border: 2px solid transparent; }
        .leader-item .l-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .leader-item .l-avatar.is-me { border-color: #E50914; }
        .leader-item .l-info { flex: 1; min-width: 0; }
        .leader-item .l-name { font-weight: 600; font-size: .9rem; color: #eee; }
        .leader-item .l-detail { font-size: .72rem; color: #777; margin-top: 1px; }
        .leader-item .l-badge { font-size: .6rem; background: #E50914; color: #fff; padding: 1px 6px; border-radius: 8px; font-weight: 600; margin-left: 6px; }
        .leader-list { max-height: 500px; overflow-y: auto; }
        .leader-list::-webkit-scrollbar { width: 4px; }
        .leader-list::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

        .ach-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
        .ach-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1rem .8rem; text-align: center; transition: all .3s; opacity: .35; filter: grayscale(.8); }
        .ach-card.unlocked { opacity: 1; filter: none; border-color: rgba(229,9,20,.2); background: rgba(229,9,20,.04); }
        .ach-card.unlocked:hover { border-color: rgba(229,9,20,.4); transform: translateY(-3px); box-shadow: 0 6px 20px rgba(229,9,20,.1); }
        .ach-card .ach-icon { font-size: 1.8rem; margin-bottom: .4rem; display: block; }
        .ach-card .ach-name { font-size: .78rem; font-weight: 600; color: #ccc; }
        .ach-card .ach-desc { font-size: .65rem; color: #666; margin-top: .2rem; }

        .xp-feed { max-height: 320px; overflow-y: auto; }
        .xp-feed::-webkit-scrollbar { width: 4px; }
        .xp-feed::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }
        .xp-item { display: flex; align-items: center; gap: 10px; padding: .45rem 0; border-bottom: 1px solid rgba(255,255,255,.04); font-size: .85rem; }
        .xp-item:last-child { border-bottom: none; }
        .xp-amount { color: #ffd700; font-weight: 700; white-space: nowrap; font-size: .8rem; background: rgba(255,215,0,.1); padding: 2px 8px; border-radius: 4px; }

        .section-title { font-size: 1.05rem; font-weight: 700; color: #eee; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }
        .section-title small { font-size: .7rem; color: #666; font-weight: 400; margin-left: auto; }

        .fire-icon { display: inline-block; animation: flicker 1.5s ease-in-out infinite; }
        @keyframes flicker { 0%,100% { transform: scale(1); } 50% { transform: scale(1.1); } }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }

        @media(max-width:768px){
            .arena-header { padding-top: 1rem; }
            .arena-title-text { font-size: 1.5rem; }
            .arena-card { padding: 1rem; }
            .ach-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="arena-container">
        <div class="arena-header">
            <div style="display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;">
                <img src="{{ asset('img/logo-unellez.png') }}" class="arena-logo-img" alt="UNELLEZ">
                <span class="arena-title-text">OSWA Arena</span>
            </div>
            <p class="arena-subtitle">Compite, sube de nivel y desbloquea todos los logros</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="arena-card text-center">
                    <div class="card-glow"></div>
                    <div class="level-circle">{{ $progress['level'] }}</div>
                    <div style="font-weight:600;color:#ddd;font-size:.95rem;">Nivel {{ $progress['level'] }}</div>
                    <div class="xp-bar-track"><div class="xp-bar-fill" style="width:{{ $progress['progress'] }}%"></div></div>
                    <div style="font-size:.8rem;color:#999;">{{ $progress['xp_in_level'] }} / {{ $progress['xp_for_next'] }} XP</div>
                    <div style="font-size:.7rem;color:#666;margin-top:.3rem;">Total: {{ $progress['xp'] }} XP</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="arena-card text-center h-100">
                    <div class="card-glow"></div>
                    <div class="rank-badge-netflix" style="margin:0 auto .6rem;">#{{ $userRank }}</div>
                    <div class="stat-value" style="color:#E50914;">{{ $stats['stock_entries'] + $stats['stock_exits'] }}</div>
                    <div class="stat-label">Movimientos</div>
                    <div style="display:flex;justify-content:center;gap:16px;margin-top:.6rem;">
                        <div><span style="color:#4ecdc4;font-weight:600;">{{ $stats['missions_completed'] }}</span><span style="color:#666;font-size:.7rem;display:block;">Misiones</span></div>
                        <div><span style="color:#ffd700;font-weight:600;">{{ $user->achievements()->count() }}</span><span style="color:#666;font-size:.7rem;display:block;">Logros</span></div>
                        <div><span style="color:#ff6b6b;font-weight:600;">{{ $stats['products_registered'] }}</span><span style="color:#666;font-size:.7rem;display:block;">Productos</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="arena-card text-center h-100">
                    <div class="card-glow"></div>
                    <div class="fire-icon" style="font-size:2.5rem;font-weight:700;color:#ffd700;line-height:1;">{{ $user->current_streak }}</div>
                    <div style="font-weight:600;color:#ddd;font-size:.9rem;margin-top:2px;">Días seguidos</div>
                    <div style="display:flex;justify-content:center;gap:20px;margin-top:.5rem;">
                        <div><span style="color:#ffd700;font-weight:600;">🔥 {{ $user->current_streak }}</span><span style="color:#666;font-size:.7rem;display:block;">Actual</span></div>
                        <div><span style="color:#aaa;font-weight:600;">🏆 {{ $user->longest_streak }}</span><span style="color:#666;font-size:.7rem;display:block;">Máxima</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="arena-card">
                    <div class="card-glow"></div>
                    <div class="section-title">
                        <span>🏆 Tabla de Líderes</span>
                        <small>{{ count($leaderboard) }} jugadores</small>
                    </div>
                    <div class="leader-list">
                        @forelse($leaderboard as $i => $u)
                        <div class="leader-item">
                            <div class="rank-num @if($i==0)r1 @elseif($i==1)r2 @elseif($i==2)r3 @endif">{{ $i+1 }}</div>
                            <div class="l-avatar {{ $u['id']===$user->id ? 'is-me' : '' }}">
                                @if($u['profile_photo_path'])
                                <img src="{{ asset('storage/'.$u['profile_photo_path']) }}" alt="" onerror="this.style.display='none';this.parentNode.textContent='{{ $u['name'][0] }}'">
                                @else
                                {{ $u['name'][0] }}
                                @endif
                            </div>
                            <div class="l-info">
                                <div class="l-name">
                                    {{ $u['name'] }}
                                    @if($u['id']===$user->id)<span class="l-badge">TÚ</span>@endif
                                </div>
                                <div class="l-detail">Nivel {{ $u['nivel'] }} · {{ $u['xp'] }} XP · {{ $u['achievements_count'] }} logros</div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="color:#ffd700;font-weight:600;font-size:.85rem;">🔥 {{ $u['current_streak'] }}</div>
                                <div style="font-size:.6rem;color:#555;">racha</div>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center;padding:2rem;color:#555;">No hay jugadores aún</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="arena-card h-100">
                    <div class="card-glow"></div>
                    <div class="section-title">
                        <span>📜 Actividad Reciente</span>
                        <small>XP ganado</small>
                    </div>
                    <div class="xp-feed">
                        @forelse($recentXp as $log)
                        <div class="xp-item">
                            <span class="xp-amount">+{{ $log->xp }}</span>
                            <span style="flex:1;color:#bbb;">{{ $log->description ?? Str::headline($log->action) }}</span>
                            <span style="font-size:.65rem;color:#555;white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        @empty
                        <div style="text-align:center;padding:2rem;color:#555;font-size:.85rem;">
                            <i class="bi bi-lightning-charge-fill d-block mb-2" style="font-size:2rem;color:#333;"></i>
                            Aún sin actividad. ¡Empieza a usar el sistema!
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="arena-card mb-4">
            <div class="card-glow"></div>
            <div class="section-title">
                <span>🎯 Logros</span>
                <small>{{ $user->achievements()->count() }} / {{ collect($achievements)->where('hidden', false)->count() }} desbloqueados</small>
            </div>
            <div class="ach-grid">
                @foreach($achievements as $ach)
                @if($ach['hidden'] && !$ach['unlocked']) @continue @endif
                <div class="ach-card {{ $ach['unlocked'] ? 'unlocked' : '' }}">
                    <span class="ach-icon"><i class="bi {{ $ach['icon'] }}"></i></span>
                    <div class="ach-name">{{ $ach['name'] }}</div>
                    <div class="ach-desc">{{ $ach['unlocked'] ? $ach['description'] : '🔒 Bloqueado' }}</div>
                    @if($ach['unlocked'])
                    <div style="font-size:.6rem;color:#ffd700;margin-top:.4rem;font-weight:600;">+{{ $ach['xp_reward'] }} XP</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('partials.perfiles')
    <div class="oswa-toast-container" id="oswa-toast-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
