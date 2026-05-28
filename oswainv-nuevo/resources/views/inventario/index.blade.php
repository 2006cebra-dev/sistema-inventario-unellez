<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OSWA Inv - Gestión de Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root {
            --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b;
            --bg-dark: #121212; --bg-input: #2a2a2a; --border-color: #2b2b2b;
            --text-primary: #e5e5e5; --text-secondary: #a3a3a3; --accent-primary: #E50914;
            --accent-success: #00b894; --accent-danger: #e74c3c; --accent-warning: #fdcb6e;
            --accent-info: #0984e3; --topbar-height: 68px;
        }
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
        
        /* --- TOP PRODUCTOS LISTA --- */
        .top-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; transition: background 0.2s; cursor: pointer; }
        .top-item:hover { background: rgba(255,255,255,0.04); }
        .top-item:active { background: rgba(229,9,20,0.08); }
        .top-avatar { width: 44px; height: 44px; border-radius: 10px; background: #2a2a2a; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; color: #fff; flex-shrink: 0; }
        .top-info { flex: 1; min-width: 0; }
        .top-name { font-size: 0.85rem; font-weight: 600; color: #e5e5e5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 6px; }
        .top-bar-wrap { height: 6px; background: #2a2a2a; border-radius: 3px; overflow: hidden; }
        .top-bar { height: 100%; background: linear-gradient(90deg, #E50914, #ff6b6b); border-radius: 3px; transition: width 0.8s ease; min-width: 4px; }
        .top-count { font-size: 1rem; font-weight: 700; color: #E50914; font-variant-numeric: tabular-nums; flex-shrink: 0; min-width: 30px; text-align: right; }
        
        /* --- GRID DE GRÁFICAS PREMIUM --- */
        .oswa-charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .oswa-chart-card { background: rgba(28, 28, 28, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(43, 43, 43, 0.8); border-radius: 16px; padding: 1.5rem; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); animation: fadeInUp 0.5s ease forwards; opacity: 0; position: relative; overflow: hidden; }
        .oswa-chart-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(229, 9, 20, 0.3), transparent); }
        .oswa-chart-card:nth-child(1) { animation-delay: 0.1s; }
        .oswa-chart-card:nth-child(2) { animation-delay: 0.2s; }
        .oswa-chart-card:nth-child(3) { animation-delay: 0.3s; }
        .oswa-chart-card:hover { transform: translateY(-5px); border-color: rgba(229, 9, 20, 0.4); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(229, 9, 20, 0.1); }
        .oswa-chart-card.full-width { grid-column: 1 / -1; }
        .oswa-chart-header { margin-bottom: 1rem; }
        .oswa-chart-title { color: #fff; font-size: 1.1rem; font-weight: 600; margin: 0; }
        .oswa-icon-box { width: 40px; height: 40px; background: rgba(229, 9, 20, 0.1); color: #E50914; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .oswa-icon-green { color: #25D366 !important; background: rgba(37, 211, 102, 0.1) !important; }
        .oswa-chart-body { position: relative; }
        .oswa-live-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(37, 211, 102, 0.1); color: #25D366; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .pulse-dot { width: 8px; height: 8px; background: #25D366; border-radius: 50%; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.3); } }
        @media (max-width: 768px) { .oswa-charts-grid { grid-template-columns: 1fr; } .oswa-chart-card.full-width { grid-column: auto; } }
        
        .bot-fab { position: fixed; bottom: 30px; left: 30px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #0984e3, #00b894); color: white; border: none; font-size: 1.8rem; box-shadow: 0 8px 25px rgba(9,132,227,0.4); z-index: 9999; cursor: pointer; transition: transform 0.3s; display: flex; align-items: center; justify-content: center; }
        .bot-fab:hover { transform: scale(1.1); }

        .oswa-chat-window { position: fixed; bottom: 100px; left: 30px; width: 350px; height: 500px; background-color: #141414; border: 1px solid #2a2a2a; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.8); display: none; flex-direction: column; z-index: 9998; overflow: hidden; }
        .oswa-chat-window.show { display: flex; }
        .oswa-chat-header { background-color: #0f0f0f; padding: 15px; border-bottom: 1px solid #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .bot-avatar { background-color: #E50914; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .oswa-chat-body { flex: 1; padding: 15px; overflow-y: auto; background-color: #141414; display: flex; flex-direction: column; gap: 12px; }
        .oswa-chat-body::-webkit-scrollbar { width: 6px; }
        .oswa-chat-body::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }
        .chat-bubble { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 0.85rem; line-height: 1.4; animation: fadeIn 0.3s ease; }
        .user-bubble { align-self: flex-end; background: #E50914; color: white; border-bottom-right-radius: 4px; }
        .bot-bubble { align-self: flex-start; background: #2b2b2b; color: white; border-bottom-left-radius: 4px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .oswa-chat-footer { padding: 15px; background-color: #0f0f0f; border-top: 1px solid #2a2a2a; }
        .oswa-chat-footer input { background-color: #222; border: 1px solid #333; color: white; width: 100%; border-radius: 4px; padding: 8px; }
        .oswa-chat-footer input:focus { background-color: #2a2a2a; border-color: #E50914; color: white; outline: none; }

        .oswa-quick-replies-container { display: flex; gap: 8px; padding: 10px 15px; overflow-x: auto; white-space: nowrap; background: transparent; width: 100%; box-sizing: border-box; }
        .oswa-quick-replies-container::-webkit-scrollbar { height: 5px; }
        .oswa-quick-replies-container::-webkit-scrollbar-track { background: #1c1c1c; border-radius: 10px; }
        .oswa-quick-replies-container::-webkit-scrollbar-thumb { background: #E50914; border-radius: 10px; }
        .bot-chip { flex-shrink: 0; background: transparent; border: 1px solid #E50914; color: #E50914; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease-in-out; }
        .bot-chip:hover { background: #E50914; color: #ffffff; }

        .professional-footer { text-align: center; padding: 1.5rem 4%; margin-top: 2rem; border-top: 1px solid var(--border-color); background-color: var(--bg-dark); color: var(--text-secondary); font-size: 0.85rem; transition: all 0.3s ease; }
        .professional-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .professional-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }

        /* HERO VIDEO NETFLIX */
        .netflix-hero { position: relative; height: 55vh; min-height: 400px; width: 100%; border-radius: 15px; overflow: hidden; margin-top: 1.5rem; margin-bottom: 2.5rem; box-shadow: inset 0 0 100px #000; background-color: #141414; background-size: cover; background-position: center; background-repeat: no-repeat; }
        .hero-vignette { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(77deg, rgba(18,18,18,1) 0%, rgba(18,18,18,0.8) 30%, transparent 85%), linear-gradient(to top, #121212 0%, transparent 20%); z-index: 1; }
        .hero-content { position: absolute; top: 50%; transform: translateY(-50%); left: 5%; z-index: 2; max-width: 600px; }
        .hero-logo-small { height: 25px; filter: brightness(0) invert(1); }
        .hero-title { font-size: 4rem; font-weight: 800; color: white; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); letter-spacing: -1px; }
        .hero-description { font-size: 1.1rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); margin-bottom: 1.5rem; line-height: 1.4; }
        .hero-buttons { display: flex; gap: 15px; }
        .btn-play { display: flex; align-items: center; justify-content: center; background: #E50914; color: white; border: none; border-radius: 6px; padding: 8px 24px; font-weight: 600; font-size: 1.1rem; transition: all 0.2s; }
        .btn-play:hover { background: #f40612; transform: scale(1.05); }
        .btn-more { display: flex; align-items: center; justify-content: center; background: rgba(109, 109, 110, 0.7); color: white; border: none; border-radius: 4px; padding: 8px 24px; font-weight: 600; font-size: 1.1rem; transition: all 0.2s; }
        .btn-more:hover { background: rgba(109, 109, 110, 0.4); }
        @media (max-width: 768px) { .netflix-hero { height: 50vh; } .hero-title { font-size: 2.5rem; } .hero-description { font-size: 0.95rem; } .btn-play, .btn-more { padding: 6px 16px; font-size: 0.95rem; } }

        /* ANIMACIONES PREMIUM PARA EL HERO */
        .hero-content > .d-flex { opacity: 0; animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .hero-content .hero-title { opacity: 0; animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s forwards; }
        .hero-content .hero-description { opacity: 0; animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.4s forwards; }
        .hero-content .hero-buttons { opacity: 0; animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.6s forwards; }
        .hero-subtitle-rgb { background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914); background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: rgbText 4s ease infinite; letter-spacing: 2px; font-size: 0.9rem; font-weight: 800; margin-left: 8px; text-transform: uppercase; }

        /* ESTILOS DE LA ENTRADA CINEMÁTICA */
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

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #B20710; border-radius: 10px; border: 2px solid #0a0a0a; }
        ::-webkit-scrollbar-thumb:hover { background: #E50914; }

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
    </style>
</head>
<body data-theme="dark">

    <!-- PANTALLA DE CARGA (LOADER OSWA INV) -->
    <div id="oswa-loader" class="oswa-loader-wrapper">
        <div class="loader-content">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="loader-logo">
            <div class="loader-bar-container"><div id="loader-bar" class="loader-bar"></div></div>
            <div id="loader-text" class="loader-text">INICIANDO MÓDULOS DEL SISTEMA...</div>
            <div id="loader-percentage" class="loader-percentage">0%</div>
        </div>
    </div>

    <!-- OVERLAY DE ENTRADA CINEMÁTICA -->
    <div id="cinematic-intro" class="cinematic-overlay">
        <div class="cinematic-content">
            <img src="{{ asset('img/logo-unellez.png') }}" id="intro-logo" class="intro-logo" alt="UNELLEZ">
            <div id="intro-quote" class="intro-quote d-none">
                <h2 class="quote-text">La ingeniería no es solo código.</h2>
                <h1 class="quote-highlight">Es diseñar el futuro.</h1>
            </div>
        </div>
    </div>
    
    @include('partials.navbar')
    
    <main class="main-content">
        <!-- BANNER OPERATIVO COMPACTO (REDISEÑADO) -->
        <div class="welcome-banner mb-4 animate-page-enter" style="position: relative; border-radius: 16px; overflow: hidden; background-image: url('{{ asset('img/refrigeracion_centros_datos.jpg') }}'); background-size: cover; background-position: center; border: 1px solid var(--n-border); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(18,18,18,0.98) 0%, rgba(18,18,18,0.85) 45%, rgba(229,9,20,0.15) 100%); z-index: 1;"></div>
            <div class="p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="position: relative; z-index: 2;">
                <div>
                    <div class="d-flex align-items-center mb-2 gap-2">
                        <img src="{{ asset('img/logo-unellez.png') }}" style="height: 22px; filter: brightness(0) invert(1);" alt="UNELLEZ">
                        <span class="hero-subtitle-rgb" style="font-size: 0.75rem; letter-spacing: 2px;">SISTEMA DE INVENTARIO</span>
                    </div>
                    <h3 class="text-white fw-bold mb-1" style="letter-spacing: -0.5px;">Panel Operativo OSWA Inv</h3>
                    <p class="mb-0" style="color: #a3a3a3; font-size: 0.9rem;">Auditoría en tiempo real y seguridad criptográfica activa.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm fw-bold d-flex align-items-center gap-2" style="background: rgba(229,9,20,0.15); color: #E50914; border: 1px solid rgba(229,9,20,0.3); border-radius: 8px; padding: 8px 16px; transition: all 0.2s;" onmouseover="this.style.background='rgba(229,9,20,0.3)'" onmouseout="this.style.background='rgba(229,9,20,0.15)'" data-bs-toggle="modal" data-bs-target="#modalArquitecturaVIP">
                        <i class="bi bi-info-circle"></i> Ver Info del Proyecto
                    </button>
                </div>
            </div>
        </div>
        
        <div id="panel-estadisticas" class="mt-5 pt-3">
        
        @if(Auth::check() && Auth::user()->rol === 'admin')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-white mb-0 font-weight-bold">Resumen de Inventario</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('exportar.pdf') }}" class="btn btn-sm fw-bold d-flex align-items-center gap-2" style="background: rgba(0,184,148,0.15); color: #00b894; border: 1px solid rgba(0,184,148,0.3); border-radius: 8px; padding: 8px 16px; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,184,148,0.3)'" onmouseout="this.style.background='rgba(0,184,148,0.15)'">
                    <i class="bi bi-filetype-pdf"></i> Exportar PDF
                </a>
                <a href="{{ route('catalogo') }}" class="btn btn-danger" style="background-color: #E50914; border: none;">
                    <i class="bi bi-grid-3x3-gap"></i> Ir al Catálogo
                </a>
            </div>
        </div>

        <div class="row mb-5 oswa-3d-wrapper">
            <div class="col-12">
                <div class="stats-grid">
                    <div class="stat-card oswa-3d-card" style="background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px;">
                        <div class="stat-icon oswa-3d-icon" style="color: var(--accent-primary);"><i class="bi bi-box-seam-fill"></i></div>
                        <div class="stat-value" id="totalProductos" style="transform: translateZ(30px);">{{ $totalProductos ?? 0 }}</div>
                        <div class="stat-label" style="transform: translateZ(20px);">Total Productos</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px;">
                        <div class="stat-icon oswa-3d-icon" style="color: var(--accent-info);"><i class="bi bi-stack"></i></div>
                        <div class="stat-value" id="stockTotal" style="transform: translateZ(30px);">{{ number_format($stockTotal ?? 0) }}</div>
                        <div class="stat-label" style="transform: translateZ(20px);">Unidades en Almacén</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color: #1a1a1a; border: 1px solid rgba(229,9,20,0.4); border-radius: 12px;">
                        <div class="stat-icon oswa-3d-icon" style="color: var(--n-red);"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div class="stat-value" id="alertasStock" style="color: var(--n-red); transform: translateZ(30px);">{{ $alertasStock ?? 0 }}</div>
                        <div class="stat-label" style="transform: translateZ(20px);">Alertas de Bajo Stock</div>
                    </div>
                    <div class="stat-card oswa-3d-card" id="cardCapital" style="background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px;">
                        <div class="stat-icon oswa-3d-icon" style="color: #10b981;"><i class="bi bi-currency-dollar"></i></div>
                        <div class="stat-value" id="capitalInvertido" style="color: #10b981; transform: translateZ(30px);">${{ number_format($capitalInvertido ?? 0, 2) }}</div>
                        <div class="stat-label" style="transform: translateZ(20px);">Capital Invertido</div>
                        <div class="stat-sub" style="font-size:0.7rem;color:#64748b;margin-top:4px;font-family:'Consolas',monospace; transform: translateZ(20px);">Eqv: Bs. {{ number_format($capitalInvertidoBs ?? 0, 2) }}</div>
                    </div>
                    <div class="stat-card oswa-3d-card" style="background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px;">
                        <div class="stat-icon oswa-3d-icon" style="color: var(--accent-primary);"><i class="bi bi-bank2"></i></div>
                        <div class="stat-value" id="tasaBcv" style="transform: translateZ(30px);">{{ number_format($tasaBcv ?? 0, 2) }}</div>
                        <div class="stat-label" style="transform: translateZ(20px);">Tasa BCV (Bs/USD)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="oswa-alerts-container mb-5" style="background-color: #141414; border: 1px solid #2a2a2a; border-radius: 12px; padding: 20px;">
            <h5 class="text-white mb-3" style="border-bottom: 1px solid #2a2a2a; padding-bottom: 10px;">
                <i class="bi bi-exclamation-triangle text-danger"></i> Alertas Críticas del Inventario
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="text-secondary mb-3"><i class="bi bi-box-seam me-1"></i> Bajo Stock (según mínimo configurable)</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        @if(isset($productosBajoStock) && $productosBajoStock->count() > 0)
                            @foreach($productosBajoStock as $prod)
                                <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div>{{ $prod->nombre }}</div>
                                        <small style="color:#888;font-size:0.7rem;">Mín: {{ $prod->stock_minimo }} {{ $prod->unidad_medida }}</small>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger rounded-pill">{{ $prod->stock }} {{ $prod->unidad_medida }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item bg-transparent text-success px-0"><i class="bi bi-check-circle me-1"></i> Stock en niveles óptimos.</li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-secondary mb-3"><i class="bi bi-calendar-x me-1"></i> Próximos a Vencer (30 días o menos)</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        @if(isset($productosPorVencer) && $productosPorVencer->count() > 0)
                            @foreach($productosPorVencer as $prod)
                                <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 px-0 d-flex justify-content-between align-items-center">
                                    {{ $prod->nombre }}
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning rounded-pill">Vence: {{ \Carbon\Carbon::parse($prod->fecha_vencimiento)->format('d/m/Y') }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item bg-transparent text-success px-0"><i class="bi bi-check-circle me-1"></i> Ningún producto por vencer.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="oswa-chart-card full-width mb-5">
            <div class="oswa-chart-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="oswa-icon-box oswa-icon-green"><i class="bi bi-graph-up-arrow"></i></div>
                    <h5 class="oswa-chart-title">Tendencia de Salidas</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select id="filtroGraficas" class="form-select bg-dark text-white border-secondary" style="width: auto; font-size: 0.85rem;" onchange="cargarGraficas()">
                        <option value="7_dias">Últimos 7 días</option>
                        <option value="este_mes">Este Mes</option>
                        <option value="mes_pasado">Mes Pasado</option>
                    </select>
                    <span class="oswa-live-badge"><span class="pulse-dot"></span> EN VIVO</span>
                </div>
            </div>
            <div class="oswa-chart-body" style="min-height: 250px; position: relative;">
                <canvas id="chartTendencia"></canvas>
            </div>
        </div>

        <div class="oswa-charts-grid">
            <div class="oswa-chart-card">
                <div class="oswa-chart-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="oswa-icon-box"><i class="bi bi-bar-chart-fill"></i></div>
                        <h5 class="oswa-chart-title">Top 5: Productos Más Vendidos</h5>
                    </div>
                </div>
                <div class="oswa-chart-body" id="container-top-productos">
                    <div id="topProductosList"></div>
                    <div id="empty-top-productos" class="text-center" style="color: #666;display:none;">
                        <i class="bi bi-inbox fs-1 mb-2"></i>
                        <p class="mb-0 font-monospace">Esperando primeros registros...</p>
                    </div>
                </div>
            </div>

            <div class="oswa-chart-card">
                <div class="oswa-chart-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="oswa-icon-box"><i class="bi bi-pie-chart-fill"></i></div>
                        <h5 class="oswa-chart-title">Distribución por Categorías</h5>
                    </div>
                </div>
                <div class="oswa-chart-body" style="min-height: 300px; position: relative;">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        @endif

        @if(Auth::check() && Auth::user()->rol === 'empleado')

            @php $misionEmpleado = Auth::user()->misiones()->where('estado', 'pendiente')->first(); @endphp

            @if($misionEmpleado)
            <div class="row mt-4 mb-4">
                <div class="col-12">
                    <div class="stat-card oswa-3d-card" style="padding: 1.5rem 2rem; border-left: 4px solid #ffc107; background: linear-gradient(135deg, #1c1c1c 0%, #1a1a1a 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="oswa-3d-icon" style="font-size: 2rem; color: #ffc107;"><i class="bi bi-flag-fill"></i></div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.75rem; color: #ffc107; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Misión Activa</div>
                                <div style="font-size: 1.3rem; font-weight: 700; color: #fff; margin: 2px 0 4px;">{{ $misionEmpleado->titulo }}</div>
                                @if($misionEmpleado->descripcion)
                                    <div style="color: #888; font-size: 0.85rem; margin-bottom: 6px;">{{ $misionEmpleado->descripcion }}</div>
                                @endif
                                @if($misionEmpleado->fecha_vencimiento)
                                    @php
                                        $hoy = \Carbon\Carbon::now();
                                        $venc = \Carbon\Carbon::parse($misionEmpleado->fecha_vencimiento);
                                        $diasRestantes = $hoy->diffInDays($venc, false);
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="font-size: 0.8rem; color: {{ $diasRestantes <= 1 ? '#E50914' : '#ffc107' }}; font-weight: 600;">
                                            <i class="bi bi-clock me-1"></i>
                                            @if($diasRestantes > 0)
                                                {{ $diasRestantes }} día(s) restante(s)
                                            @elseif($diasRestantes == 0)
                                                Vence hoy
                                            @else
                                                Vencida
                                            @endif
                                        </div>
                                    </div>
                                @else
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <h5 class="text-white fw-bold mb-4 mt-4" style="font-size:1.1rem;">
                <i class="bi bi-grid-3x3-gap me-2" style="color:var(--accent-primary);"></i>Acceso Rápido
            </h5>

            <div class="row g-4 mt-0">
                <div class="col-md-4">
                    <a href="{{ route('escaner') }}" style="text-decoration: none;">
                        <div class="stat-card oswa-3d-card" style="text-align: center; cursor: pointer; display: block; padding: 2rem 1.5rem;">
                            <div class="stat-icon oswa-3d-icon" style="color: #0984e3; font-size: 2.5rem;"><i class="bi bi-upc-scan"></i></div>
                            <div class="stat-value" style="font-size: 1.2rem; color: #fff; font-weight: 600;">Escaner</div>
                            <div class="stat-label" style="font-size: 0.8rem;">Lectura de códigos</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('catalogo') }}" style="text-decoration: none;">
                        <div class="stat-card oswa-3d-card" style="text-align: center; cursor: pointer; display: block; padding: 2rem 1.5rem;">
                            <div class="stat-icon oswa-3d-icon" style="color: #E50914; font-size: 2.5rem;"><i class="bi bi-box-seam"></i></div>
                            <div class="stat-value" style="font-size: 1.2rem; color: #fff; font-weight: 600;">Catálogo</div>
                            <div class="stat-label" style="font-size: 0.8rem;">Productos disponibles</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('requisiciones.crear') }}" style="text-decoration: none;">
                        <div class="stat-card oswa-3d-card" style="text-align: center; cursor: pointer; display: block; padding: 2rem 1.5rem;">
                            <div class="stat-icon oswa-3d-icon" style="color: #ffc107; font-size: 2.5rem;"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="stat-value" style="font-size: 1.2rem; color: #fff; font-weight: 600;">Requisiciones</div>
                            <div class="stat-label" style="font-size: 0.8rem;">Solicitar productos</div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-md-5">
                    <div class="stat-card" style="padding: 1.2rem;">
                        <div style="font-size: 0.75rem; color: #E50914; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 1rem;">
                            <i class="bi bi-bar-chart-fill me-1"></i> Resumen del Día
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div style="background: #141414; border-radius: 8px; padding: 0.8rem; text-align: center;">
                                    <div style="color: #0984e3; font-size: 1.5rem; font-weight: 800;">{{ $movimientosHoy }}</div>
                                    <div style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Movimientos</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background: #141414; border-radius: 8px; padding: 0.8rem; text-align: center;">
                                    <div style="color: #ffc107; font-size: 1.5rem; font-weight: 800;">{{ $requisicionesHoy }}</div>
                                    <div style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Solicitudes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="stat-card" style="padding: 1.2rem;">
                        <div style="font-size: 0.75rem; color: #E50914; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 0.8rem;">
                            <i class="bi bi-bell-fill me-1"></i> Notificaciones
                        </div>
                        @forelse($notificaciones as $notif)
                            <div style="display: flex; align-items: center; gap: 10px; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <i class="bi {{ $notif['icon'] }}" style="color: {{ $notif['color'] }}; font-size: 1.1rem;"></i>
                                <span style="color: #ccc; font-size: 0.85rem;">{{ $notif['text'] }}</span>
                            </div>
                        @empty
                            <div style="color: #555; font-size: 0.85rem; text-align: center; padding: 1rem 0;">
                                <i class="bi bi-check2-circle" style="color: #00b894;"></i> Sin novedades
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="stat-card" style="padding: 1.2rem;">
                        <div style="font-size: 0.75rem; color: #E50914; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 0.8rem;">
                            <i class="bi bi-file-earmark-text me-1"></i> Mis Requisiciones
                        </div>
                        @if($misRequisiciones->count() > 0)
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                    <thead>
                                        <tr style="border-bottom: 1px solid #2b2b2b; color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <th style="padding: 0.6rem 0.8rem; text-align: left;">Producto</th>
                                            <th style="padding: 0.6rem 0.8rem; text-align: center;">Cantidad</th>
                                            <th style="padding: 0.6rem 0.8rem; text-align: center;">Estado</th>
                                            <th style="padding: 0.6rem 0.8rem; text-align: right;">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($misRequisiciones as $req)
                                            @php
                                                $colorEstado = $req->estado === 'Aprobada' ? '#00b894' : ($req->estado === 'Rechazada' ? '#E50914' : '#ffc107');
                                            @endphp
                                            <tr style="border-bottom: 1px solid #1f1f1f;">
                                                <td style="padding: 0.6rem 0.8rem; color: #fff;">{{ $req->producto->nombre ?? '—' }}</td>
                                                <td style="padding: 0.6rem 0.8rem; text-align: center; color: #aaa;">{{ $req->cantidad }}</td>
                                                <td style="padding: 0.6rem 0.8rem; text-align: center;">
                                                    <span style="background: rgba({{ $req->estado === 'Aprobada' ? '0,184,148' : ($req->estado === 'Rechazada' ? '229,9,20' : '255,193,7') }},0.15); color: {{ $colorEstado }}; border: 1px solid rgba({{ $req->estado === 'Aprobada' ? '0,184,148' : ($req->estado === 'Rechazada' ? '229,9,20' : '255,193,7') }},0.3); border-radius: 12px; padding: 0.15rem 0.6rem; font-size: 0.7rem; font-weight: 600;">
                                                        {{ $req->estado }}
                                                    </span>
                                                </td>
                                                <td style="padding: 0.6rem 0.8rem; text-align: right; color: #666; font-size: 0.75rem;">{{ $req->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div style="color: #555; font-size: 0.85rem; text-align: center; padding: 1rem 0;">
                                <i class="bi bi-inbox"></i> No has realizado requisiciones aún
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        </div>
        
    </main>
    
    <button id="btn-chatbot-toggle" class="bot-fab"><i class="bi bi-robot"></i></button>
    <div id="oswa-chatbot-window" class="oswa-chat-window">
        <div class="oswa-chat-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="bot-avatar"><i class="bi bi-robot"></i></div>
                <div>
                    <h6 class="mb-0 text-white fw-bold">OSWA Asistente</h6>
                    <small style="color: #25D366; font-size: 0.75rem;">● En línea</small>
                </div>
            </div>
            <button id="close-chatbot-btn" class="btn text-white p-0"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="oswa-chat-body" id="botChatHistory">
            <div class="chat-bubble bot-bubble">¡Epa! Soy la Inteligencia Artificial de tu inventario. ¿En qué te ayudo?</div>
        </div>
        <div class="oswa-quick-replies-container">
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('📦 Ver Catálogo')">📦 Ver Catálogo</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('🤝 Añadir Proveedor')">🤝 Añadir Proveedor</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('📊 Reporte de Stock')">📊 Reporte de Stock</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('🛠️ Soporte')">🛠️ Soporte</button>
        </div>
        <div class="oswa-chat-footer">
            <div class="input-group">
                <input type="text" id="botInput" class="form-control" placeholder="Escribe tu consulta..." onkeypress="if(event.key==='Enter') enviarBot()">
                <button class="btn btn-danger" id="send-chatbot-btn" onclick="enviarBot()"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>

    <!-- MOTOR DE BOOTSTRAP PARA MODALES -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPTS DEL CHATBOT Y DE GRÁFICOS -->
    <script>
        document.getElementById('btn-chatbot-toggle').addEventListener('click', function() {
            document.getElementById('oswa-chatbot-window').classList.toggle('show');
        });

        document.getElementById('close-chatbot-btn').addEventListener('click', function() {
            document.getElementById('oswa-chatbot-window').classList.remove('show');
        });

        const oswaBotRespuestas = [
            { keywords: ['hola', 'buenas', 'epa', 'saludos', 'que tal'], response: "¡Epa! Soy OSWA, tu asistente de inventario. 🤖 ¿Quieres saber sobre capital, vencimientos, stock o precios?" },
            { keywords: ['vence', 'vencimiento', 'vencidos', 'caduca', 'expira'], response: "⚠️ Revisa la sección de 'Alertas Críticas' en tu Dashboard. Ahí tienes el listado exacto de lo que está por caducar pronto." },
            { keywords: ['capital', 'inversion', 'dinero', 'plata', 'total'], response: "💰 El capital invertido y la tasa actual del BCV se calculan en tiempo real. Puedes ver los montos exactos en las tarjetas verdes del Resumen de Inventario." },
            { keywords: ['precio', 'cuesta', 'valor', 'costo'], response: "🏷️ Los precios detallados están en el Catálogo. Puedes usar la barra de búsqueda superior para encontrar el valor de un producto en específico." },
            { keywords: ['stock', 'cantidad', 'quedan', 'disponible', 'inventario', 'falta'], response: "📦 Si hay productos con menos de 5 unidades, te saldrán en las Alertas de Bajo Stock. Para el resto, revisa el Catálogo." },
            { keywords: ['proveedor', 'proveedores', 'comprar', 'surtir'], response: "🤝 Puedes registrar nuevas entradas, compras o contactar a las marcas directamente desde el módulo de Proveedores arriba en el menú." },
            { keywords: ['gracias', 'listo', 'fino', 'ok', 'perfecto'], response: "¡A la orden siempre! 😎 Avísame si necesitas algo más." },
            { keywords: ['requisicion', 'pedir', 'solicitar', 'material'], response: "📋 Para hacer una requisición, ve al Catálogo y dale clic al botón rojo 'Hacer Requisición'. Selecciona los productos y envía tu solicitud." },
            { keywords: ['grafica', 'grafico', 'tendencia', 'venta'], response: "📊 Las gráficas del Dashboard muestran el Top 5 de productos más vendidos, la distribución por categorías y la tendencia de salidas de los últimos 7 días." },
            { keywords: ['soporte', 'ayuda', 'problema', 'error', 'falla', 'contacto', 'auxilio'], response: "🛠️ ¿Tienes algún inconveniente con el sistema? Escribe directamente haciendo clic en el contacto que necesites: <br><br> <div class='d-flex flex-wrap gap-2'><a href='https://wa.me/584122266083' target='_blank' class='btn btn-sm text-white' style='background-color: #25D366; border-radius: 6px; border: none;'><i class='bi bi-whatsapp'></i> Carlos</a> <a href='https://wa.me/584145207044' target='_blank' class='btn btn-sm text-white' style='background-color: #25D366; border-radius: 6px; border: none;'><i class='bi bi-whatsapp'></i> Yorgelis</a></div>" },
            { keywords: ['ayuda', 'help', 'como', 'como uso', 'que puedo'], response: "🛠️ Puedo ayudarte con info sobre: stock, precios, vencimientos, capital invertido, proveedores, gráficas y requisiciones. ¡Pregúntame lo que necesites!" }
        ];

        function procesarMensajeBot(mensajeUsuario) {
            let texto = mensajeUsuario.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            for (let item of oswaBotRespuestas) {
                if (item.keywords.some(kw => texto.includes(kw))) { return item.response; }
            }
            return "Oye, no capté bien la idea. Intenta preguntarme por el capital, vencimientos, stock, precios, proveedores o soporte.";
        }

        function agregarMensaje(texto, tipo) {
            const container = document.getElementById('botChatHistory');
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble ' + (tipo === 'user' ? 'user-bubble' : 'bot-bubble');
            bubble.innerHTML = texto;
            container.appendChild(bubble);
            container.scrollTop = container.scrollHeight;
        }

        function enviarBot() {
            const input = document.getElementById('botInput');
            const texto = input.value.trim();
            if (!texto) return;
            agregarMensaje(texto, 'user');
            input.value = '';
            setTimeout(() => {
                const respuesta = procesarMensajeBot(texto);
                agregarMensaje(respuesta, 'bot');
            }, 500);
        }

        function enviarOpcionRapida(opcion) {
            document.getElementById('botInput').value = opcion;
            enviarBot();
        }

        // Gráficos Dinámicos del Dashboard
        let chartTendenciaInst = null;
        let chartCatInst = null;

        function cargarGraficas() {
            if (typeof Chart === 'undefined') return;
            const rango = document.getElementById('filtroGraficas') ? document.getElementById('filtroGraficas').value : '7_dias';

            fetch(`/api/graficas?rango=${rango}`)
                .then(res => { if (!res.ok) throw new Error('Error ' + res.status); return res.json(); })
                .then(data => {
                    if (!data || !data.tendencias_labels) return;
                    // 1. Gráfica de Tendencias
                    if (chartTendenciaInst) chartTendenciaInst.destroy();
                    const ctxTrend = document.getElementById('chartTendencia');
                    if (ctxTrend && data.tendencias_labels.length > 0) {
                        const ctx = ctxTrend.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(37, 211, 102, 0.3)');
                        gradient.addColorStop(1, 'rgba(37, 211, 102, 0.0)');

                        chartTendenciaInst = new Chart(ctx, {
                            type: 'line',
                            data: { labels: data.tendencias_labels, datasets: [{ label: 'Salidas', data: data.tendencias, borderColor: '#25D366', backgroundColor: gradient, borderWidth: 2, fill: true, tension: 0.4, pointBackgroundColor: '#25D366', pointRadius: 4 }] },
                            options: { animation: { duration: 800 }, responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#2b2b2b' }, ticks: { color: '#888' } }, x: { grid: { color: '#2b2b2b' }, ticks: { color: '#e5e5e5' } } } }
                        });
                    }

                    // 2. Top Productos con foto + nombre
                    const topList = document.getElementById('topProductosList');
                    const emptyTopElement = document.getElementById('empty-top-productos');
                    const topLabels = data.top_labels || [];
                    const topData = data.top_productos || [];
                    const topPhotos = data.top_photos || [];
                    const topIds = data.top_ids || [];

                    if (topLabels.length > 0) {
                        emptyTopElement.style.display = 'none';
                        const max = Math.max(...topData);
                        topList.innerHTML = topLabels.map((name, i) => {
                            const pct = max > 0 ? (topData[i] / max) * 100 : 0;
                            const foto = topPhotos[i] || null;
                            const id = topIds[i] || null;
                            const initials = name.charAt(0).toUpperCase();
                            const safeInitials = initials.replace(/['"&<>]/g, '');
                            const avatarHtml = foto
                                ? '<div style="width:44px;height:44px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#2a2a2a;"><img src="' + foto + '" alt="" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display=\'none\';this.parentNode.textContent=\'' + safeInitials + '\'"></div>'
                                : '<div class="top-avatar">' + safeInitials + '</div>';
                            return '<div class="top-item"' + (id ? ' onclick="window.location.href=\'/productos/' + id + '/editar\'"' : '') + '>'
                                + avatarHtml
                                + '<div class="top-info"><div class="top-name">' + name + '</div><div class="top-bar-wrap"><div class="top-bar" style="width:' + pct + '%"></div></div></div>'
                                + '<div class="top-count">' + topData[i] + ' uds</div>'
                                + '</div>';
                        }).join('');
                    } else {
                        topList.innerHTML = '';
                        emptyTopElement.style.display = 'block';
                    }

                    // 3. Categorías
                    if (chartCatInst) chartCatInst.destroy();
                    const ctxCat = document.getElementById('chartCategorias');
                    if (ctxCat && data.categorias_labels && data.categorias_labels.length > 0) {
                        chartCatInst = new Chart(ctxCat.getContext('2d'), {
                            type: 'doughnut',
                            data: { labels: data.categorias_labels, datasets: [{ data: data.categorias, backgroundColor: ['#E50914', '#2b90d9', '#4CAF50', '#ffc107', '#9c27b0'], borderColor: '#1c1c1c', borderWidth: 2 }] },
                            options: { animation: { duration: 800 }, responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#e5e5e5', padding: 15 } } }, cutout: '65%' }
                        });
                    }
                })
                .catch(() => {});
        }

        // Cargar por primera vez al iniciar
        document.addEventListener('DOMContentLoaded', cargarGraficas);

        function sincronizarDashboard() {
            fetch('/api/stats/global')
                .then(res => { if (!res.ok) throw new Error('Error ' + res.status); return res.json(); })
                .then(data => {
                    if (!data) return;
                    const el = id => document.getElementById(id);
                    const tp = el('totalProductos'), st = el('stockTotal'), as = el('alertasStock');
                    const ci = el('capitalInvertido'), tb = el('tasaBcv');
                    if (tp) tp.innerText = data.totalProductos ?? tp.innerText;
                    if (st) st.innerText = data.stockTotal ?? st.innerText;
                    if (as) as.innerText = data.alertasStock ?? as.innerText;
                    if (ci) ci.innerText = data.capitalInvertido ? '$' + data.capitalInvertido : ci.innerText;
                    if (tb) tb.innerText = data.tasaBcv ?? tb.innerText;
                    if (typeof cargarGraficas === 'function') cargarGraficas();
                })
                .catch(() => {});
        }

        // Ejecutar cada 10 segundos
        setInterval(sincronizarDashboard, 10000);
    </script>

    <!-- SCRIPT DEL LOADER OSWA -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById('oswa-loader');
            if (!loader) return;

            if (sessionStorage.getItem('oswa_loaded')) {
                loader.style.display = 'none';
                return;
            }

            let progress = 0;
            const bar = document.getElementById('loader-bar');
            const percentageText = document.getElementById('loader-percentage');
            const statusText = document.getElementById('loader-text');

            const messages = [
                "INICIANDO MÓDULOS DE OSWA INV...",
                "SINCRONIZANDO BASE DE DATOS...",
                "CARGANDO CATÁLOGO DE PRODUCTOS...",
                "VALIDANDO CREDENCIALES DE USUARIO...",
                "SISTEMA LISTO Y OPERATIVO."
            ];

            const interval = setInterval(() => {
                progress += Math.floor(Math.random() * 5) + 1; 
                if (progress > 100) progress = 100;

                if(bar) bar.style.width = progress + '%';
                if(percentageText) percentageText.innerText = progress + '%';

                if(statusText) {
                    if (progress < 25) statusText.innerText = messages[0];
                    else if (progress < 50) statusText.innerText = messages[1];
                    else if (progress < 75) statusText.innerText = messages[2];
                    else if (progress < 95) statusText.innerText = messages[3];
                    else statusText.innerText = messages[4];
                }

                if (progress === 100) {
                    clearInterval(interval);
                    sessionStorage.setItem('oswa_loaded', 'true'); 
                    setTimeout(() => {
                        loader.style.opacity = '0';
                        loader.style.visibility = 'hidden';
                        setTimeout(() => { loader.style.display = 'none'; }, 500); 
                    }, 600); 
                }
            }, 40); 
        });
    </script>

<!-- FOOTER GLOBAL OSWA INV -->
<footer class="professional-footer mt-5 pt-4 pb-4" style="text-align: center; border-top: 1px solid #2b2b2b; color: #a3a3a3; font-size: 0.85rem; background-color: transparent;">
    <div class="mb-2">
        &copy; <script>document.write(new Date().getFullYear())</script> <strong class="text-white">OSWA Inv</strong>. Todos los derechos reservados.
    </div>
    <div class="mb-2">
        Desarrollado con <i class="bi bi-code-slash text-secondary"></i> y <i class="bi bi-heart-fill text-danger"></i> por <span class="text-white fw-bold">Carlos Braca & Yorgelys Blanco</span>
    </div>
    
    <div class="d-flex align-items-center justify-content-center gap-3 mt-3" style="font-size: 0.85rem;">
        <span style="color: #888888;">Ingeniería en Informática — V Semestre</span>
        
        <div style="width: 1px; height: 16px; background-color: #444444;"></div>
        
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 22px; filter: brightness(0) invert(1) opacity(0.9);">
            <strong class="text-white" style="letter-spacing: 1px;">UNELLEZ</strong>
        </div>
    </div>
</footer>

<!-- MODAL PREMIUM DE PRESENTACIÓN DEL PROYECTO -->
    <div class="modal fade" id="modalArquitecturaVIP" tabindex="-1" aria-hidden="true" style="z-index: 999999;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="background-color: #121212; border: 1px solid #333; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.8);">
                
                <!-- Header con gradiente UNELLEZ -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #E50914 0%, #8B0000 100%); padding: 20px 30px;">
                    <h4 class="modal-title text-white fw-bold d-flex align-items-center gap-3">
                        <img src="{{ asset('img/logo-unellez.png') }}" style="height: 35px; filter: brightness(0) invert(1);" alt="UNELLEZ">
                        OSWA Inv - Arquitectura del Sistema
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body Animado -->
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Columna Izquierda: Contexto -->
                        <div class="col-md-5 p-4 p-lg-5" style="background: #1a1a1a; border-right: 1px solid #2a2a2a;">
                            <h4 class="text-white fw-bold mb-3">Proyecto de Ingeniería</h4>
                            <p class="text-secondary" style="font-size: 1rem; line-height: 1.6;">
                                Desarrollado por <strong class="text-light">Carlos Braca</strong> y <strong class="text-light">Yorgelys Blanco</strong> (V Semestre, UNELLEZ).<br><br>
                                OSWA Inv no es solo un gestor de inventario visual; es una solución de grado empresarial diseñada con un enfoque estricto en la <strong>trazabilidad de transacciones</strong> y la prevención de fraudes internos.
                            </p>
                            
                            <div class="mt-4 pt-4" style="border-top: 1px solid #333;">
                                <div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50 p-3 mb-3 w-100 text-start" style="font-size: 0.9rem;">
                                    <i class="bi bi-shield-lock-fill fs-5 me-2 align-middle"></i> Backend: Laravel 10 (PHP)
                                </div>
                                <div class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-50 p-3 mb-3 w-100 text-start" style="font-size: 0.9rem;">
                                    <i class="bi bi-window-desktop fs-5 me-2 align-middle"></i> Frontend: HTML5, CSS3, JS + Bootstrap
                                </div>
                                <div class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 p-3 w-100 text-start" style="font-size: 0.9rem;">
                                    <i class="bi bi-database-fill fs-5 me-2 align-middle"></i> Base de Datos: MySQL Relacional
                                </div>
                            </div>
                        </div>

<!-- Columna Derecha: Features (Transacciones) -->
                        <div class="col-md-7 p-4 p-lg-5" style="background: #141414;">
                            <h5 class="text-white fw-bold mb-4 border-bottom border-secondary pb-3">Transacciones y Lógicas Clave</h5>

                            <!-- Feature 1: Criptografía -->
                            <div class="d-flex mb-4" style="gap: 16px;">
                                <div class="bg-success bg-opacity-10 text-success border border-success border-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 55px; height: 55px; border-radius: 12px; font-size: 1.6rem; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);">
                                    <i class="bi bi-file-earmark-lock2-fill"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-bold mb-1">Auditoría Criptográfica (SHA-256)</h6>
                                    <p class="text-secondary mb-0" style="font-size: 0.85rem;">El núcleo de seguridad. Cada entrada, salida o ajuste genera un hash único basado en los datos del movimiento. Si la base de datos es alterada externamente, el sistema detecta la discrepancia y marca la transacción como <span class="badge bg-danger">ALTERADA</span> en rojo.</p>
                                </div>
                            </div>

                            <!-- Feature 2: Roles -->
                            <div class="d-flex mb-4" style="gap: 16px;">
                                <div class="bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 55px; height: 55px; border-radius: 12px; font-size: 1.6rem; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-bold mb-1">Control de Roles Dinámico</h6>
                                    <p class="text-secondary mb-0" style="font-size: 0.85rem;">La vista se muta según el usuario. El <strong class="text-light">Empleado</strong> tiene una interfaz limpia para escanear y generar solicitudes (Requisiciones). El <strong class="text-light">Administrador</strong> posee control total: aprueba transacciones, emite órdenes de compra y respalda la BD.</p>
                                </div>
                            </div>

                            <!-- Feature 3: Inteligencia -->
                            <div class="d-flex mb-4" style="gap: 16px;">
                                <div class="bg-info bg-opacity-10 text-info border border-info border-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 55px; height: 55px; border-radius: 12px; font-size: 1.6rem; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-bold mb-1">Cálculos en Tiempo Real y PDF</h6>
                                    <p class="text-secondary mb-0" style="font-size: 0.85rem;">Motor de reportes impulsado por <code>DomPDF</code> que genera guías corporativas. Además, el Dashboard consume APIs externas para calcular el capital del inventario equivalente en Bs. según el BCV en vivo.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #0a0a0a;">
                    <button type="button" class="btn btn-outline-light px-4 fw-bold" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <!-- INYECTAMOS EL MOTOR DE PERFILES -->
    <!-- SCRIPT DE LA ENTRADA CINEMÁTICA -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('cinematic-intro');
            if (!overlay) return;
            
            // Si la intro ya se reprodujo en esta sesión, la quitamos inmediatamente
            if (sessionStorage.getItem('oswaIntroPlayed') === 'true') {
                overlay.remove();
                return;
            }

            const logo = document.getElementById('intro-logo');
            const quote = document.getElementById('intro-quote');
            
            // 1. Reproducir Sonido (Silencioso si el navegador lo bloquea)
            try {
                const cinematicSound = new Audio('{{ asset("sounds/intro.mp3") }}');
                cinematicSound.volume = 0.8;
                cinematicSound.play().catch(e => console.log("Autoplay de audio bloqueado"));
            } catch (e) {}

            // 2. Secuencia de tiempos (Timeouts) - reducida para velocidad
            // A los 1 segundos: Ocultar logo, mostrar frase
            setTimeout(() => {
                if(logo) logo.classList.add('d-none');
                if(quote) {
                    quote.classList.remove('d-none');
                    quote.classList.add('show');
                }
            }, 1000);

            // A los 2.5 segundos: Desvanecer la pantalla negra completa
            setTimeout(() => {
                overlay.classList.add('fade-out');
                sessionStorage.setItem('oswaIntroPlayed', 'true');
            }, 2500);

            // A los 3.5 segundos: Eliminar el código HTML del overlay
            setTimeout(() => {
                overlay.remove();
            }, 3500);
        });
    </script>

</body>
</html>