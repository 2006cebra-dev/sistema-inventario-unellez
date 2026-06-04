<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Usuarios y Roles - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b; --bg-dark: #121212; --bg-input: #2a2a2a; --border-color: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3; --accent-primary: #E50914; --accent-success: #00b894; --accent-danger: #e74c3c; --accent-warning: #fdcb6e; --accent-info: #0984e3; --topbar-height: 68px; }
        * { font-family: 'Inter', sans-serif; }
        body, html { overflow-x: hidden !important; max-width: 100vw; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }

        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2rem; }
        @media (max-width: 1199px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 767px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        .stat-card { background: #1c1c1c !important; border: 1px solid #2b2b2b !important; border-radius: 15px !important; padding: 1.5rem; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); animation: fadeInUp 0.5s ease forwards; opacity: 0; overflow: hidden; position: relative; }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }
        .stat-card:nth-child(5) { animation-delay: 0.25s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card:hover { transform: translateY(-8px) scale(1.05); border-color: #E50914 !important; z-index: 100; box-shadow: 0 15px 30px rgba(0,0,0,0.6); }
        .stat-icon { font-size: 2rem; opacity: 0.8; margin-bottom: 10px; }
        .stat-value { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 1.8rem; font-weight: 800; }
        .stat-label { text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem; color: #888; }

        .oswa-3d-wrapper { perspective: 1000px; }
        .oswa-3d-card { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease; transform-style: preserve-3d; background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px; }
        .oswa-3d-card:hover { transform: translateY(-10px) rotateX(2deg) rotateY(2deg); box-shadow: -10px 20px 30px rgba(0, 0, 0, 0.8), inset 0 0 15px rgba(255, 255, 255, 0.02); border-color: #444; }
        .oswa-3d-icon { display: inline-block; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), text-shadow 0.4s; transform: translateZ(0) scale(1); backface-visibility: hidden; will-change: transform; }
        .oswa-3d-card:hover .oswa-3d-icon { transform: translateZ(60px) scale(1.2); text-shadow: 0 15px 10px rgba(0,0,0,0.5); }
        .stat-card.oswa-3d-card { animation-fill-mode: forwards; transform-style: preserve-3d; }
        .stat-card.oswa-3d-card:hover { transform: translateY(-10px) rotateX(2deg) rotateY(2deg) !important; box-shadow: -10px 20px 30px rgba(0, 0, 0, 0.8), inset 0 0 15px rgba(255, 255, 255, 0.02) !important; border-color: #444 !important; }
        .stat-card .stat-icon.oswa-3d-icon { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), text-shadow 0.4s; backface-visibility: hidden; will-change: transform; }
        .stat-card.oswa-3d-card:hover .stat-icon.oswa-3d-icon { transform: translateZ(60px) scale(1.2); text-shadow: 0 15px 10px rgba(0,0,0,0.5); }

        .oswa-loader-wrapper { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: #000000; z-index: 999999; display: flex; align-items: center; justify-content: center; flex-direction: column; transition: opacity 0.5s ease, visibility 0.5s ease; }
        .loader-content { text-align: center; width: 350px; max-width: 90%; }
        .loader-logo { height: 120px; filter: brightness(0) invert(1); margin-bottom: 25px; }
        .loader-bar-container { width: 100%; height: 4px; background-color: #222; margin-bottom: 15px; position: relative; }
        .loader-bar { width: 0%; height: 100%; background-color: #E50914; transition: width 0.1s linear; }
        .loader-text { font-family: 'Courier New', Courier, monospace; color: #fff; font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 10px; text-transform: uppercase; }
        .loader-percentage { font-family: 'Courier New', Courier, monospace; color: #fff; font-size: 0.95rem; font-weight: bold; }

        .cinematic-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: #000000; z-index: 99999; display: flex; justify-content: center; align-items: center; transition: opacity 1.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .cinematic-content { text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .intro-logo { width: 180px; filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255,255,255,0.5)); opacity: 0; animation: pulseGlow 3s forwards; }
        .intro-quote { opacity: 0; transform: scale(0.9); }
        .intro-quote.show { animation: textExplosion 3.5s forwards; }
        .quote-text { color: var(--text-secondary); font-size: 1.5rem; font-weight: 300; margin-bottom: 10px; letter-spacing: 2px; }
        .quote-highlight { color: #ffffff; font-size: 3rem; font-weight: 800; text-transform: uppercase; letter-spacing: 4px; text-shadow: 0 0 20px rgba(229, 9, 20, 0.8), 0 0 40px rgba(229, 9, 20, 0.4); }
        @keyframes pulseGlow { 0% { opacity: 0; transform: scale(0.8); } 30% { opacity: 1; transform: scale(1.05); filter: brightness(0) invert(1) drop-shadow(0 0 30px rgba(255,255,255,1)); } 80% { opacity: 1; transform: scale(1); filter: brightness(0) invert(1) drop-shadow(0 0 10px rgba(255,255,255,0.3)); } 100% { opacity: 0; transform: scale(1.1); } }
        @keyframes textExplosion { 0% { opacity: 0; transform: scale(0.8); filter: blur(10px); } 20% { opacity: 1; transform: scale(1.1); filter: blur(0); } 80% { opacity: 1; transform: scale(1); filter: blur(0); } 100% { opacity: 0; transform: scale(1.2); filter: blur(10px); } }
        .cinematic-overlay.fade-out { opacity: 0; pointer-events: none; }

        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 10px; animation: fadeInUp 0.5s ease forwards; opacity: 0; }
        .page-title { font-size: 1.6rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .page-title i { color: var(--accent-primary); }

        .btn-netflix-red { background: var(--n-red) !important; color: #fff !important; border: none !important; font-weight: 600; padding: 10px 24px; border-radius: 4px; box-shadow: 0 4px 15px rgba(229,9,20,0.4); transition: all 0.3s ease; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .btn-netflix-red:hover { background: #b8070f !important; transform: scale(1.05); box-shadow: 0 8px 25px rgba(229,9,20,0.6); }

        .filters-bar { display: flex; gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; animation: fadeInUp 0.5s ease 0.1s forwards; opacity: 0; }
        .filter-select { background: #2a2a2a; border: 1px solid #333; color: #e5e5e5; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; }
        .filter-select:focus { outline: none; border-color: var(--accent-primary); }
        .filter-search { position: relative; flex: 1; min-width: 200px; }
        .filter-search input { width: 100%; padding: 8px 14px 8px 36px; background: #2a2a2a; border: 1px solid #333; border-radius: 8px; color: #e5e5e5; font-size: 0.85rem; }
        .filter-search input:focus { outline: none; border-color: var(--accent-primary); }
        .filter-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666; font-size: 0.9rem; }

        .user-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem; }
        .user-card { position: relative; overflow: hidden; }
        .user-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--n-red), transparent); opacity: 0; transition: opacity 0.4s; }
        .user-card:hover::after { opacity: 1; }
        .user-photo-wrapper { position: relative; display: inline-block; }
        .card-profile-photo { width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin: 0 auto 12px; display: block; border: 3px solid var(--n-border); box-shadow: 0 0 20px rgba(0,0,0,0.4); transition: all 0.4s; }
        .user-card:hover .card-profile-photo { border-color: var(--n-red); box-shadow: 0 0 30px rgba(229,9,20,0.4); }
        .user-avatar-card { width: 70px; height: 70px; background: linear-gradient(135deg, var(--n-red), #b20710); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 1.6rem; color: #fff; font-weight: 700; box-shadow: 0 0 20px rgba(229,9,20,0.3); transition: transform 0.3s; }
        .user-card:hover .user-avatar-card { transform: scale(1.1); }
        .user-name { font-weight: 700; font-size: 1rem; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .status-dot-indicator { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .status-dot-indicator.online { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        .status-dot-indicator.offline { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        .user-email { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; color: var(--text-secondary); font-size: 0.78rem; margin-bottom: 10px; }
        .badge-rol { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; margin-bottom: 16px; }
        .badge-admin { background: rgba(229,9,20,0.15); color: var(--accent-primary); border: 1px solid rgba(229,9,20,0.3); }
        .badge-empleado { background: rgba(148,163,184,0.15); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); }
        .badge-dev { background: rgba(156,39,176,0.15); color: #ce93d8; border: 1px solid rgba(156,39,176,0.3); }
        .user-date { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; color: #555; font-size: 0.7rem; margin-bottom: 12px; }
        .user-card-actions { display: flex; gap: 8px; justify-content: center; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--n-border); }
        .btn-action { background: none; border: 1px solid rgba(148,163,184,0.2); color: #94a3b8; padding: 6px 16px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 500; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .btn-action:hover { background: rgba(148,163,184,0.1); border-color: #94a3b8; }
        .btn-action.edit:hover { background: rgba(9,132,227,0.15); border-color: var(--accent-info); color: var(--accent-info); }
        .btn-action.delete:hover { background: rgba(231,76,60,0.15); border-color: var(--accent-danger); color: var(--accent-danger); }
        .btn-action.suspend { border-color: rgba(231,76,60,0.3); color: var(--accent-danger); }
        .btn-action.suspend:hover { background: rgba(231,76,60,0.15); border-color: var(--accent-danger); }
        .btn-action.activate { border-color: rgba(0,184,148,0.3); color: var(--accent-success); }
        .btn-action.activate:hover { background: rgba(0,184,148,0.15); border-color: var(--accent-success); }

        .activity-feed { margin-top: 3rem; background: #0f0f0f; border-radius: 15px; border: 1px solid var(--n-border); padding: 24px; animation: fadeInUp 0.5s ease 0.2s forwards; opacity: 0; }
        .feed-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; color: var(--text-primary); }
        .feed-title i { color: var(--accent-primary); }
        .feed-item { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #1a1a1a; }
        .feed-item:last-child { border-bottom: none; }
        .feed-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--accent-primary); flex-shrink: 0; }
        .feed-info { flex: 1; }
        .feed-user { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
        .feed-detail { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.78rem; color: var(--text-secondary); }
        .feed-meta { text-align: right; }
        .feed-ip { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.8rem; color: #64748b; }
        .feed-date { font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.7rem; color: #475569; margin-top: 2px; }

        .roles-section { margin-top: 3rem; animation: fadeInUp 0.5s ease 0.3s forwards; opacity: 0; }
        .roles-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px; }
        .roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .role-card { background: #0f0f0f; border: 1px solid var(--n-border); border-radius: 12px; padding: 1.25rem; transition: all 0.3s ease; }
        .role-card:hover { border-color: var(--accent-primary); transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.4); }
        .role-name { font-size: 1.1rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .role-count { font-family: 'Consolas', monospace; font-size: 0.85rem; color: var(--text-secondary); }
        .role-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }

        .modal-content { background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 16px; overflow: hidden; }
        .modal-header { border: none; padding: 1.5rem 1.5rem 0; }
        .modal-header .modal-title { color: var(--text-primary); font-weight: 800; font-size: 1.3rem; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 1.5rem; }
        .modal-footer { border-top: 1px solid var(--n-border); padding: 1rem 1.5rem; }
        .form-control, .form-select { background: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; padding: 12px; }
        .form-control:focus, .form-select:focus { background: #333; border-color: var(--accent-primary); color: var(--text-primary); box-shadow: none; }
        .form-label { color: var(--text-secondary); font-size: 0.9rem; }
        .form-select option { background: #1c1c1c; color: #fff; }

        .photo-upload-wrapper { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 20px 0; }
        .photo-upload-preview { width: 100px; height: 100px; border-radius: 50%; background: #2a2a2a; border: 3px dashed #444; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; transition: all 0.3s; position: relative; }
        .photo-upload-preview:hover { border-color: var(--accent-primary); background: #333; }
        .photo-upload-preview.has-image { border-style: solid; border-color: var(--accent-primary); }
        .photo-upload-preview img { width: 100%; height: 100%; object-fit: cover; }
        .photo-upload-preview .upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 4px; color: #666; font-size: 0.7rem; text-align: center; }
        .photo-upload-preview .upload-placeholder i { font-size: 1.8rem; }
        .photo-upload-preview .upload-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; border-radius: 50%; }
        .photo-upload-preview:hover .upload-overlay { opacity: 1; }
        .photo-upload-preview .upload-overlay i { color: #fff; font-size: 1.5rem; }
        .photo-upload-hint { color: #555; font-size: 0.75rem; text-align: center; }

        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 6rem; }

        @media (max-width: 768px) { .user-grid { grid-template-columns: 1fr; } .roles-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body data-theme="dark">

    <div id="oswa-loader" class="oswa-loader-wrapper">
        <div class="loader-content">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="loader-logo">
            <div class="loader-bar-container"><div id="loader-bar" class="loader-bar"></div></div>
            <div id="loader-text" class="loader-text">CARGANDO GESTIÓN DE USUARIOS...</div>
            <div id="loader-percentage" class="loader-percentage">0%</div>
        </div>
    </div>

    <div id="cinematic-intro" class="cinematic-overlay">
        <div class="cinematic-content">
            <img src="{{ asset('img/logo-unellez.png') }}" id="intro-logo" class="intro-logo" alt="UNELLEZ">
            <div id="intro-quote" class="intro-quote d-none">
                <h2 class="quote-text">El talento es el activo más valioso.</h2>
                <h1 class="quote-highlight">Gestiona tu equipo.</h1>
            </div>
        </div>
    </div>

    @include('partials.navbar')

    <main class="main-content">

        <div class="page-header">
            <div class="page-title">
                <i class="bi bi-people-fill"></i> Administración de Personal
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm fw-bold d-flex align-items-center gap-2" style="background:rgba(0,184,148,0.15);color:#00b894;border:1px solid rgba(0,184,148,0.3);border-radius:8px;padding:8px 16px;transition:all 0.2s;" onclick="exportarUsuarios()">
                    <i class="bi bi-download"></i> Exportar
                </button>
                <button class="btn-netflix-red" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                    <i class="bi bi-plus-lg"></i> Nuevo Empleado
                </button>
            </div>
        </div>

        <div class="row mb-5 oswa-3d-wrapper">
            <div class="col-12">
                <div class="stats-grid">
                    <div class="stat-card oswa-3d-card" style="background-color:#1a1a1a;border:1px solid #333;border-radius:12px;">
                        <div class="stat-icon oswa-3d-icon" style="color:var(--accent-primary);"><i class="bi bi-people-fill"></i></div>
                        <div class="stat-value">{{ $usuarios->count() ?? 0 }}</div>
                        <div class="stat-label">Total Usuarios</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color:#1a1a1a;border:1px solid #333;border-radius:12px;">
                        <div class="stat-icon oswa-3d-icon" style="color:var(--accent-danger);"><i class="bi bi-shield-fill-check"></i></div>
                        <div class="stat-value">{{ $usuarios->where('rol','admin')->count() ?? 0 }}</div>
                        <div class="stat-label">Administradores</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color:#1a1a1a;border:1px solid #333;border-radius:12px;">
                        <div class="stat-icon oswa-3d-icon" style="color:var(--accent-info);"><i class="bi bi-person-badge-fill"></i></div>
                        <div class="stat-value">{{ $usuarios->where('rol','empleado')->count() ?? 0 }}</div>
                        <div class="stat-label">Empleados</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color:#1a1a1a;border:1px solid #333;border-radius:12px;">
                        <div class="stat-icon oswa-3d-icon" style="color:var(--accent-success);"><i class="bi bi-check-circle-fill"></i></div>
                        <div class="stat-value" id="activeCount">{{ $usuarios->where('is_active',true)->count() ?? 0 }}</div>
                        <div class="stat-label">Activos</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color:#1a1a1a;border:1px solid #333;border-radius:12px;">
                        <div class="stat-icon oswa-3d-icon" style="color:var(--accent-warning);"><i class="bi bi-pause-circle-fill"></i></div>
                        <div class="stat-value" id="inactiveCount">{{ $usuarios->where('is_active',false)->count() ?? 0 }}</div>
                        <div class="stat-label">Suspendidos</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filters-bar">
            <div class="filter-search">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar por nombre, email, nick..." oninput="filtrarUsuarios()">
            </div>
            <select class="filter-select" id="filterRol" onchange="filtrarUsuarios()">
                <option value="">Todos los roles</option>
                <option value="admin">Administradores</option>
                <option value="empleado">Empleados</option>
            </select>
            <select class="filter-select" id="filterStatus" onchange="filtrarUsuarios()">
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Suspendidos</option>
            </select>
        </div>

        <div class="user-grid" id="userGrid">
            @forelse($usuarios as $user)
            <div class="oswa-3d-wrapper user-card-wrapper" data-rol="{{ $user->rol }}" data-active="{{ $user->is_active ? 'active' : 'inactive' }}">
                <div class="user-card oswa-3d-card text-center" style="background:#1a1a1a;border:1px solid #333;border-radius:12px;padding:24px 20px;">
                    @if($user->profile_photo_path)
                        <div class="user-photo-wrapper">
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->display_name }}" class="card-profile-photo">
                        </div>
                    @else
                        <div class="user-avatar-card">{{ strtoupper(substr($user->display_name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div class="user-name">
                        <span class="status-dot-indicator {{ $user->is_active ? 'online' : 'offline' }}"></span>
                        {{ $user->display_name }}
                    </div>
                    <div class="user-email">{{ $user->email }}</div>
                    @if($user->nick)
                        <div style="color:#666;font-size:0.75rem;margin-bottom:6px;"><i class="bi bi-at me-1"></i>{{ $user->nick }}</div>
                    @endif
                    <div style="color:#666;font-size:0.75rem;font-family:'Consolas','Monaco','Courier New',monospace;margin-bottom:4px;">
                        <i class="bi bi-person-vcard me-1"></i>{{ $user->cedula ?? 'Sin cédula' }}
                    </div>
                    <div style="color:#666;font-size:0.75rem;font-family:'Consolas','Monaco','Courier New',monospace;margin-bottom:8px;">
                        <i class="bi bi-telephone me-1"></i>{{ $user->telefono ?? 'Sin teléfono' }}
                    </div>
                    <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
                        <span class="badge-rol {{ $user->rol === 'admin' ? 'badge-admin' : ($user->rol === 'desarrollador' ? 'badge-dev' : 'badge-empleado') }}">
                            <i class="bi bi-{{ $user->rol === 'admin' ? 'shield-fill-check' : ($user->rol === 'desarrollador' ? 'code-slash' : 'person') }} me-1"></i>{{ $user->rol === 'admin' ? 'Jefe' : ucfirst($user->rol) }}
                        </span>
                        <span style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);color:#ffc107;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;">
                            <i class="bi bi-star-fill me-1" style="font-size:0.6rem;"></i>{{ $user->xp ?? 0 }} XP
                        </span>
                        <span style="background:rgba(9,132,227,0.1);border:1px solid rgba(9,132,227,0.2);color:#0984e3;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;">
                            <i class="bi bi-trophy-fill me-1" style="font-size:0.6rem;"></i>Nv. {{ $user->nivel ?? 1 }}
                        </span>
                    </div>
                    <div class="user-date">
                        <i class="bi bi-calendar3 me-1"></i>Registrado: {{ $user->created_at->format('d/m/Y') }}
                    </div>
                    <div class="user-card-actions">
                        <button class="btn-action edit" onclick="editarUsuario({{ $user->id }})"><i class="bi bi-pencil"></i> Editar</button>
                        @if($user->id !== auth()->id())
                            <button class="btn-action {{ $user->is_active ? 'suspend' : 'activate' }}" onclick="cambiarEstatus({{ $user->id }}, '{{ $user->display_name }}', {{ $user->is_active }})">
                                <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'check-circle' }}"></i>
                                {{ $user->is_active ? 'Suspender' : 'Activar' }}
                            </button>
                            <button class="btn-action delete" onclick="eliminarUsuario({{ $user->id }}, '{{ $user->display_name }}')"><i class="bi bi-trash"></i></button>
                        @else
                            <span style="color:#555;font-size:0.8rem;"><i class="bi bi-person-check me-1"></i> Tú</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5" style="color:#555;">
                <i class="bi bi-people" style="font-size:3rem;"></i>
                <p class="mt-2">No hay usuarios registrados</p>
            </div>
            @endforelse
        </div>

        <div class="roles-section">
            <div class="roles-header">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-shield-fill me-2" style="color:var(--accent-primary);"></i>Roles del Sistema</h5>
                <button class="btn-netflix-red" style="padding:8px 18px;font-size:0.85rem;" onclick="mostrarNuevoRol()"><i class="bi bi-plus-lg"></i> Nuevo Rol</button>
            </div>
            <div class="roles-grid" id="rolesGrid">
                @php
                    $rolesAgrupados = $usuarios->groupBy('rol');
                    $rolesPermisos = \Illuminate\Support\Facades\Cache::get('roles_permisos', []);
                    $rolesExtraMostrar = collect(['desarrollador'])->filter(fn($r) => !isset($rolesAgrupados[$r]));
                    foreach ($rolesExtraMostrar as $re) { $rolesAgrupados[$re] = collect([]); }
                @endphp
                @foreach($rolesAgrupados as $rol => $users)
                @php
                    $permisos = $rolesPermisos[$rol] ?? [];
                    $isCustom = !in_array($rol, ['admin','empleado','desarrollador']);
                    $rolDisplay = $rol === 'admin' ? 'Jefe' : ($rol === 'desarrollador' ? 'Desarrollador' : ucfirst($rol));
                    $rolIcon = $rol === 'admin' ? 'shield-fill-check' : ($rol === 'desarrollador' ? 'code-slash' : ($isCustom ? 'person-gear' : 'person-badge'));
                    $rolColor = $rol === 'admin' ? 'var(--accent-primary)' : ($rol === 'desarrollador' ? '#ce93d8' : ($isCustom ? 'var(--accent-warning)' : 'var(--accent-info)'));
                    $rolBg = $rol === 'admin' ? 'rgba(229,9,20,0.15)' : ($rol === 'desarrollador' ? 'rgba(156,39,176,0.15)' : 'rgba(9,132,227,0.15)');
                    $rolBorder = $rol === 'admin' ? 'rgba(229,9,20,0.3)' : ($rol === 'desarrollador' ? 'rgba(156,39,176,0.3)' : 'rgba(9,132,227,0.3)');
                @endphp
                <div class="role-card" style="cursor:pointer;" onclick="editarRol('{{ $rol }}')" title="Click para editar permisos">
                    <div class="role-name">
                        <i class="bi bi-{{ $rolIcon }}" style="color:{{ $rolColor }}"></i>
                        {{ $rolDisplay }}
                        <span class="role-badge" style="background:{{ $rolBg }};color:{{ $rolColor }};border:1px solid {{ $rolBorder }}">{{ $users->count() }} usuario(s)</span>
                    </div>
                    @if(count($permisos) > 0)
                    <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px;">
                        @foreach($permisos as $p)
                            <span class="badge" style="background:rgba(0,184,148,0.12);color:#00b894;border:1px solid rgba(0,184,148,0.2);font-weight:400;font-size:0.65rem;padding:3px 8px;">{{ $p }}</span>
                        @endforeach
                    </div>
                    @else
                    <div style="color:#555;font-size:0.7rem;margin-bottom:8px;"><i class="bi bi-sliders me-1"></i>Sin permisos asignados</div>
                    @endif
                    <div class="role-count">
                        @foreach($users->take(5) as $u)
                            <span class="badge bg-dark me-1 mb-1" style="font-weight:400;font-size:0.7rem;padding:3px 8px;">{{ $u->display_name }}</span>
                        @endforeach
                        @if($users->count() > 5)
                            <span class="text-muted" style="font-size:0.7rem;">+{{ $users->count() - 5 }} más</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="activity-feed">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="feed-title mb-0"><i class="bi bi-clock-history"></i> Últimos 20 Accesos al Sistema</div>
                <span style="font-size:0.75rem;color:#555;"><i class="bi bi-shield-check me-1"></i>Bitácora de seguridad</span>
            </div>
            @if(isset($logs) && count($logs) > 0)
                @foreach($logs as $log)
                <div class="feed-item">
                    <div class="feed-dot"></div>
                    <div class="feed-info">
                        <div class="feed-user">{{ $log->user?->display_name ?? 'Desconocido' }}</div>
                        <div class="feed-detail">{{ $log->user?->email ?? 'N/A' }} · {{ $log->user?->rol ?? '—' }}</div>
                    </div>
                    <div class="feed-meta">
                        <div class="feed-ip">{{ $log->ip_address }}</div>
                        <div class="feed-date">{{ $log->login_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5" style="color:#555;">
                    <i class="bi bi-shield-lock" style="font-size:3rem;"></i>
                    <p class="mt-2">No hay registros de acceso recientes</p>
                </div>
            @endif
        </div>

    </main>

    @include('partials.mobile-bottom-nav')

    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2" style="color:var(--accent-primary);"></i>Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="nuevoUsuarioForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-4 d-flex flex-column align-items-center justify-content-center border-end" style="border-color:var(--n-border)!important;">
                                <div class="photo-upload-wrapper">
                                    <div class="photo-upload-preview" id="photoPreview" onclick="document.getElementById('photoInput').click()">
                                        <div class="upload-placeholder">
                                            <i class="bi bi-camera-fill"></i>
                                            <span>Foto</span>
                                        </div>
                                        <div class="upload-overlay"><i class="bi bi-camera-fill"></i></div>
                                    </div>
                                    <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display:none;">
                                    <button type="button" class="btn btn-sm" style="background:rgba(229,9,20,0.1);color:var(--accent-primary);border:1px solid rgba(229,9,20,0.2);border-radius:6px;padding:4px 16px;font-size:0.75rem;" onclick="document.getElementById('photoInput').click()">Subir foto</button>
                                    <div class="photo-upload-hint">PNG, JPG. Máx 5 MB</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" name="name" placeholder="Ej: Juan Pérez" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apodo / Nick</label>
                                        <input type="text" class="form-control" name="nick" placeholder="Ej: juanperez">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" name="email" placeholder="correo@ejemplo.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Cédula / Documento</label>
                                        <input type="text" class="form-control" name="cedula" placeholder="V-12345678">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" name="telefono" placeholder="0412-1234567">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rol</label>
                                        <select class="form-control" name="rol" required>
                                            <option value="empleado">Empleado</option>
                                            <option value="admin">Administrador</option>
                                            @if(isset($rolesExtra))
                                                @foreach($rolesExtra as $re)
                                                    <option value="{{ $re }}">{{ ucfirst($re) }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="password" placeholder="Mínimo 6 caracteres" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" id="confirmPassword" placeholder="Repite la contraseña" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="background:transparent;color:#888;border:1px solid #333;border-radius:8px;padding:10px 24px;">Cancelar</button>
                        <button type="submit" class="btn btn-netflix-red" style="padding:10px 32px;"><i class="bi bi-save me-1"></i>Guardar Empleado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarUsuarioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color:var(--accent-primary);"></i>Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editarUsuarioForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-4 d-flex flex-column align-items-center justify-content-center border-end" style="border-color:var(--n-border)!important;">
                                <div class="photo-upload-wrapper">
                                    <div class="photo-upload-preview" id="editPhotoPreview" onclick="document.getElementById('editPhotoInput').click()">
                                        <div class="upload-placeholder">
                                            <i class="bi bi-camera-fill"></i>
                                            <span>Foto</span>
                                        </div>
                                        <div class="upload-overlay"><i class="bi bi-camera-fill"></i></div>
                                    </div>
                                    <input type="file" id="editPhotoInput" name="profile_photo" accept="image/*" style="display:none;">
                                    <button type="button" class="btn btn-sm" style="background:rgba(229,9,20,0.1);color:var(--accent-primary);border:1px solid rgba(229,9,20,0.2);border-radius:6px;padding:4px 16px;font-size:0.75rem;" onclick="document.getElementById('editPhotoInput').click()">Cambiar foto</button>
                                    <div class="photo-upload-hint">PNG, JPG. Máx 5 MB</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="editName" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apodo / Nick</label>
                                        <input type="text" class="form-control" id="editNick" name="nick">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="editEmail" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Cédula / Documento</label>
                                        <input type="text" class="form-control" id="editCedula" name="cedula">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="editTelefono" name="telefono">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rol</label>
                                        <select class="form-control" id="editRol" name="rol" required>
                                            <option value="empleado">Empleado</option>
                                            <option value="admin">Administrador</option>
                                            @if(isset($rolesExtra))
                                                @foreach($rolesExtra as $re)
                                                    <option value="{{ $re }}">{{ ucfirst($re) }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva Contraseña <small style="color:#555;">(dejar vacío para mantener)</small></label>
                                        <input type="password" class="form-control" id="editPassword" name="password" placeholder="Nueva contraseña">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="background:transparent;color:#888;border:1px solid #333;border-radius:8px;padding:10px 24px;">Cancelar</button>
                        <button type="submit" class="btn btn-netflix-red" style="padding:10px 32px;"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nuevoRolModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shield-plus me-2" style="color:var(--accent-primary);"></i>Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Rol</label>
                        <input type="text" class="form-control" id="nuevoRolNombre" placeholder="Ej: supervisor, gerente..." style="text-transform:lowercase;font-size:1rem;padding:12px 16px;">
                        <div style="color:#555;font-size:0.75rem;margin-top:6px;">Solo letras, números y guión bajo.</div>
                    </div>
                    <div>
                        <label class="form-label fw-bold mb-3 d-block">
                            <i class="bi bi-shield-check me-1" style="color:var(--accent-primary);"></i>Permisos del Rol
                            <small style="color:#555;font-weight:400;margin-left:8px;">Marca los permisos que tendrá este rol</small>
                        </label>
                        <div class="row g-2" id="permisosGrid">
                            @php
                                $todosPermisos = [
                                    'ver_dashboard' => 'Ver Dashboard',
                                    'ver_catalogo' => 'Ver Catálogo',
                                    'ver_proveedores' => 'Ver Proveedores',
                                    'gestionar_productos' => 'Gestionar Productos',
                                    'gestionar_proveedores' => 'Gestionar Proveedores',
                                    'aprobar_requisiciones' => 'Aprobar Requisiciones',
                                    'gestionar_usuarios' => 'Gestionar Usuarios',
                                    'ver_auditoria' => 'Ver Auditoría',
                                    'gestionar_misiones' => 'Gestionar Misiones',
                                    'gestionar_precios' => 'Gestionar Precios',
                                    'exportar_pdf' => 'Exportar PDF',
                                    'respaldar_bd' => 'Respaldar BD',
                                    'chat' => 'Chat Interno',
                                ];
                            @endphp
                            @foreach($todosPermisos as $key => $label)
                            <div class="col-md-6">
                                <label class="d-flex align-items-center gap-2 p-2 rounded" style="cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" class="permiso-check" value="{{ $key }}" style="width:18px;height:18px;accent-color:var(--accent-primary);cursor:pointer;">
                                    <span style="color:#ccc;font-size:0.9rem;">{{ $label }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content:space-between;">
                    <div style="color:#555;font-size:0.75rem;"><i class="bi bi-info-circle me-1"></i>Los roles admin y empleado tienen permisos fijos.</div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="background:transparent;color:#888;border:1px solid #333;border-radius:8px;padding:10px 24px;">Cancelar</button>
                        <button type="button" class="btn btn-netflix-red" onclick="guardarNuevoRol(this)" style="padding:10px 32px;"><i class="bi bi-check-lg me-1"></i>Crear Rol</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarRolModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color:var(--accent-primary);"></i>Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editRolNombre">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Rol</label>
                        <input type="text" class="form-control" id="editRolNombreInput" style="text-transform:lowercase;font-size:1rem;padding:12px 16px;" readonly>
                    </div>
                    <div>
                        <label class="form-label fw-bold mb-3 d-block">
                            <i class="bi bi-shield-check me-1" style="color:var(--accent-primary);"></i>Permisos del Rol
                        </label>
                        <div class="row g-2" id="editPermisosGrid">
                            @foreach($todosPermisos as $key => $label)
                            <div class="col-md-6">
                                <label class="d-flex align-items-center gap-2 p-2 rounded" style="cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" class="edit-permiso-check" value="{{ $key }}" style="width:18px;height:18px;accent-color:var(--accent-primary);cursor:pointer;">
                                    <span style="color:#ccc;font-size:0.9rem;">{{ $label }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal" style="background:transparent;color:#888;border:1px solid #333;border-radius:8px;padding:10px 24px;">Cancelar</button>
                    <button type="button" class="btn btn-netflix-red" onclick="guardarEditarRol(this)" style="padding:10px 32px;"><i class="bi bi-save me-1"></i>Guardar Permisos</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';

        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('cinematic-intro');
            if (!overlay) return;
            if (sessionStorage.getItem('oswaIntroPlayed') === 'true') {
                overlay.remove();
                return;
            }
            const logo = document.getElementById('intro-logo');
            const quote = document.getElementById('intro-quote');
            setTimeout(() => {
                if (logo) logo.classList.add('d-none');
                if (quote) { quote.classList.remove('d-none'); quote.classList.add('show'); }
            }, 1000);
            setTimeout(() => {
                overlay.classList.add('fade-out');
                sessionStorage.setItem('oswaIntroPlayed', 'true');
            }, 2500);
            setTimeout(() => overlay.remove(), 3500);
        });

        (function() {
            const loader = document.getElementById('oswa-loader');
            if (!loader) return;
            const bar = document.getElementById('loader-bar');
            const pct = document.getElementById('loader-percentage');
            const txt = document.getElementById('loader-text');
            let p = 0;
            const msgs = ['CARGANDO GESTIÓN DE USUARIOS...', 'CARGANDO PERFILES...', 'SINCRONIZANDO DATOS...', 'LISTO'];
            const iv = setInterval(() => {
                p += Math.floor(Math.random() * 15) + 3;
                if (p > 100) p = 100;
                if (bar) bar.style.width = p + '%';
                if (pct) pct.textContent = p + '%';
                if (p >= 30 && p < 60) { if (txt) txt.textContent = msgs[1]; }
                else if (p >= 60 && p < 90) { if (txt) txt.textContent = msgs[2]; }
                else if (p >= 100) {
                    if (txt) txt.textContent = msgs[3];
                    clearInterval(iv);
                    setTimeout(() => { loader.style.opacity = '0'; setTimeout(() => loader.remove(), 500); }, 300);
                }
            }, 120);
        })();

        function filtrarUsuarios() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const rol = document.getElementById('filterRol').value;
            const status = document.getElementById('filterStatus').value;
            document.querySelectorAll('.user-card-wrapper').forEach(w => {
                const card = w.querySelector('.user-card');
                const name = card.querySelector('.user-name').textContent.toLowerCase();
                const email = card.querySelector('.user-email').textContent.toLowerCase();
                const nick = card.querySelector('[class*="bi-at"]') ? card.querySelector('[class*="bi-at"]').parentElement.textContent.toLowerCase() : '';
                const matchSearch = !q || name.includes(q) || email.includes(q) || nick.includes(q);
                const matchRol = !rol || w.dataset.rol === rol;
                const matchStatus = !status || w.dataset.active === status;
                w.style.display = (matchSearch && matchRol && matchStatus) ? '' : 'none';
            });
        }

        document.getElementById('photoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('photoPreview');
                preview.innerHTML = `<img src="${ev.target.result}" alt="Foto">`;
                preview.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('editPhotoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('editPhotoPreview');
                preview.innerHTML = `<img src="${ev.target.result}" alt="Foto">`;
                preview.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('nuevoUsuarioForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const pass = this.querySelector('[name="password"]').value;
            const confirm = document.getElementById('confirmPassword').value;
            if (pass !== confirm) {
                mostrarToast('Las contraseñas no coinciden', 'bi bi-exclamation-triangle-fill');
                return;
            }
                const formData = new FormData(this);
                try {
                    const response = await fetch('{{ route('usuarios.guardar') }}', {
                        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData
                    });
                    if (!response.ok) {
                        const text = await response.text();
                        console.error('Error HTTP', response.status, text.substring(0, 200));
                        mostrarToast('Error del servidor (' + response.status + ')', 'bi bi-exclamation-triangle-fill');
                        return;
                    }
                    const data = await response.json();
                    if (data.success) {
                        const mdl = bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal'));
                        if (mdl) mdl.hide();
                        mostrarToast(data.message || 'Usuario creado correctamente', 'bi bi-person-plus-fill');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        mostrarToast(data.message || 'No se pudo crear el usuario', 'bi bi-exclamation-triangle-fill');
                    }
                } catch (err) {
                    console.error('Fetch error:', err);
                    mostrarToast('Error de conexión: ' + err.message, 'bi bi-exclamation-triangle-fill');
                }
        });

        async function editarUsuario(id) {
            try {
                const res = await fetch('/usuarios/datos/' + id);
                const data = await res.json();
                if (!data.success) { mostrarToast('No se pudo cargar el usuario', 'bi bi-exclamation-triangle-fill'); return; }
                const u = data.user;
                document.getElementById('editUserId').value = u.id;
                document.getElementById('editName').value = u.name;
                document.getElementById('editNick').value = u.nick || '';
                document.getElementById('editEmail').value = u.email;
                document.getElementById('editCedula').value = u.cedula || '';
                document.getElementById('editTelefono').value = u.telefono || '';
                document.getElementById('editRol').value = u.rol;
                document.getElementById('editPassword').value = '';
                const preview = document.getElementById('editPhotoPreview');
                if (u.profile_photo_path) {
                    preview.innerHTML = `<img src="/storage/${u.profile_photo_path}?v=${Date.now()}" alt="Foto">`;
                    preview.classList.add('has-image');
                } else {
                    preview.innerHTML = `<div class="upload-placeholder"><i class="bi bi-camera-fill"></i><span>Foto</span></div><div class="upload-overlay"><i class="bi bi-camera-fill"></i></div>`;
                    preview.classList.remove('has-image');
                }
                const modal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
                modal.show();
            } catch(e) {
                mostrarToast('Error al cargar datos del usuario', 'bi bi-exclamation-triangle-fill');
            }
        }

        document.getElementById('editarUsuarioForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('/usuarios/actualizar', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData
                });
                const data = await response.json();
                if (data.success) {
                    const mdl = bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal'));
                    if (mdl) mdl.hide();
                    mostrarToast(data.message || 'Usuario actualizado', 'bi bi-check-circle-fill');
                    setTimeout(() => location.reload(), 800);
                } else {
                    mostrarToast(data.message || 'Error al actualizar', 'bi bi-exclamation-triangle-fill');
                }
            } catch(e) {
                mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
            }
        });

        function eliminarUsuario(id, nombre) {
            if (id === {{ auth()->id() }}) { mostrarToast('No puedes eliminarte a ti mismo', 'bi bi-exclamation-triangle-fill'); return; }
            Swal.fire({
                title: '¿Eliminar usuario?',
                html: `Se eliminará permanentemente a <strong style="color:#E50914;">${nombre}</strong>.<br><small style="color:#888;">Todos sus datos asociados también se eliminarán.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#333',
                background: '#121212', color: '#fff',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('/usuarios/eliminar', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id: id })
                        });
                        const data = await response.json();
                        if (data.success) {
                            mostrarToast(data.message || 'Usuario eliminado', 'bi bi-trash-fill');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            mostrarToast(data.message || 'Error al eliminar', 'bi bi-exclamation-triangle-fill');
                        }
                    } catch(e) {
                        mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
                    }
                }
            });
        }

        function cambiarEstatus(id, nombre, is_active) {
            const accion = is_active ? 'suspender' : 'activar';
            Swal.fire({
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
                html: `Estás a punto de ${accion} a <strong style="color:#E50914;">${nombre}</strong>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No',
                confirmButtonColor: '#E50914', cancelButtonColor: '#333',
                background: '#121212', color: '#fff',
                toast: true, showConfirmButton: true,
                customClass: { popup: 'oswa-confirm-toast' }
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
                            mostrarToast(data.message, 'bi bi-check-circle-fill');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            mostrarToast(data.message || 'No se pudo actualizar el estatus', 'bi bi-exclamation-triangle-fill');
                        }
                    } catch(e) {
                        mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
                    }
                }
            });
        }

        function mostrarNuevoRol() {
            const el = document.getElementById('nuevoRolModal');
            if (!el) { alert('Error: Modal no encontrado'); return; }
            document.getElementById('nuevoRolNombre').value = '';
            document.querySelectorAll('.permiso-check').forEach(c => c.checked = false);
            try {
                const modal = new bootstrap.Modal(el);
                modal.show();
            } catch(e) { alert('Error al abrir modal: ' + e.message); }
        }

        function getPermisosSeleccionados(className) {
            const checks = document.querySelectorAll('.' + className + ':checked');
            return Array.from(checks).map(c => c.value);
        }

        async function guardarNuevoRol(btn) {
            const inp = document.getElementById('nuevoRolNombre');
            if (!inp) { mostrarToast('Error interno', 'bi bi-exclamation-triangle-fill'); return; }
            const nombre = inp.value.trim().toLowerCase();
            if (!nombre || nombre.length < 3) { mostrarToast('El nombre debe tener al menos 3 caracteres', 'bi bi-exclamation-triangle-fill'); return; }
            if (!/^[a-zA-Z0-9_]+$/.test(nombre)) { mostrarToast('Solo letras, números y guión bajo', 'bi bi-exclamation-triangle-fill'); return; }
            const permisos = getPermisosSeleccionados('permiso-check');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creando...';
            try {
                const response = await fetch('{{ route('roles.guardar') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ nombre: nombre, permisos: permisos })
                });
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Error HTTP', response.status, text.substring(0, 200));
                    mostrarToast('Error del servidor (' + response.status + ')', 'bi bi-exclamation-triangle-fill');
                    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Crear Rol';
                    return;
                }
                const data = await response.json();
                if (data.success) {
                    const mdl = bootstrap.Modal.getInstance(document.getElementById('nuevoRolModal'));
                    if (mdl) mdl.hide();
                    mostrarToast('Rol "' + nombre + '" creado con ' + permisos.length + ' permiso(s)', 'bi bi-shield-plus');
                    setTimeout(() => location.reload(), 800);
                } else {
                    mostrarToast(data.message || 'Error al crear rol', 'bi bi-exclamation-triangle-fill');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Crear Rol';
                }
            } catch(e) {
                mostrarToast('Error de conexión: ' + e.message, 'bi bi-exclamation-triangle-fill');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Crear Rol';
            }
        }

        async function editarRol(nombre) {
            document.getElementById('editRolNombre').value = nombre;
            document.getElementById('editRolNombreInput').value = nombre;
            document.querySelectorAll('.edit-permiso-check').forEach(c => c.checked = false);
            try {
                const res = await fetch('/roles/permisos/' + encodeURIComponent(nombre));
                const data = await res.json();
                if (data.success && data.permisos) {
                    data.permisos.forEach(p => {
                        const cb = document.querySelector('.edit-permiso-check[value="' + p + '"]');
                        if (cb) cb.checked = true;
                    });
                }
            } catch(e) {}
            try {
                const modal = new bootstrap.Modal(document.getElementById('editarRolModal'));
                modal.show();
            } catch(e) {}
        }

        async function guardarEditarRol(btn) {
            const nombre = document.getElementById('editRolNombre').value;
            const permisos = getPermisosSeleccionados('edit-permiso-check');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';
            try {
                const response = await fetch('{{ route('roles.guardar') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ nombre: nombre, permisos: permisos, editar: true })
                });
                if (!response.ok) {
                    const text = await response.text();
                    mostrarToast('Error del servidor (' + response.status + ')', 'bi bi-exclamation-triangle-fill');
                    btn.disabled = false; btn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Permisos';
                    return;
                }
                const data = await response.json();
                if (data.success) {
                    const mdl = bootstrap.Modal.getInstance(document.getElementById('editarRolModal'));
                    if (mdl) mdl.hide();
                    mostrarToast('Permisos de "' + nombre + '" actualizados', 'bi bi-check-circle-fill');
                    setTimeout(() => location.reload(), 800);
                } else {
                    mostrarToast(data.message || 'Error', 'bi bi-exclamation-triangle-fill');
                    btn.disabled = false; btn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Permisos';
                }
            } catch(e) {
                mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
                btn.disabled = false; btn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Permisos';
            }
        }

        function exportarUsuarios() {
            window.open('/usuarios/exportar', '_blank');
        }

        function checkNetworkStatus() {
            const isOnline = navigator.onLine;
            document.querySelectorAll('.status-indicator').forEach(ind => {
                const dot = ind.querySelector('.status-dot');
                const text = ind.querySelector('.status-text');
                if (isOnline) {
                    ind.classList.replace('offline', 'online');
                    if (text) text.textContent = 'En línea';
                } else {
                    ind.classList.replace('online', 'offline');
                    if (text) text.textContent = 'Sin conexión';
                }
            });
        }
        window.addEventListener('online', checkNetworkStatus);
        window.addEventListener('offline', checkNetworkStatus);
        document.addEventListener('DOMContentLoaded', checkNetworkStatus);
    </script>

    @include('partials.perfiles')

</body>
</html>