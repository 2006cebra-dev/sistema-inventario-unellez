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
        [data-theme="light"] {
            --bg-dark: #121212; --bg-card: #1c1c1c; --bg-input: #2a2a2a;
            --border-color: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }

        /* Glassmorphism Navbar */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; overflow: visible !important;
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%) !important;
            backdrop-filter: blur(10px); border: none !important;
        }
        .topbar::-webkit-scrollbar { display: none; }
        
        .topbar-left { display: flex; align-items: center; gap: 2rem; }
        .topbar-logo { white-space: nowrap; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .topbar-logo .logo-text { display: inline-block !important;
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
        }
        @keyframes rgbText { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        
        .logo-nav-unellez { height: 35px; filter: brightness(0) invert(1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; margin-right: 10px; }
        .logo-nav-unellez:hover { transform: scale(1.2); filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8)); }
        
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0; }
        .topbar-nav a:hover, .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: var(--accent-primary); border-radius: 1px; }
        
        .nav-dropdown { position: relative; }
        .nav-dropdown .dropdown-toggle { cursor: pointer; }
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #0d0d0d; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.8); padding: 6px 0; z-index: 1000; display: none; }
        .nav-dropdown.show .dropdown-menu-custom { display: block; }
        .dropdown-item-custom { display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; }
        .dropdown-item-custom:hover { background: rgba(229,9,20,0.1); color: #fff; }
        .dropdown-item-custom.text-muted { color: #666; cursor: default; }
        .dropdown-item-custom.text-muted:hover { background: transparent; color: #666; }
        
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .topbar-search { position: relative; }
        .topbar-search input { width: 220px; padding: 7px 14px 7px 34px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 6px; color: #ffffff; font-size: 0.85rem; transition: all 0.3s; }
        .topbar-search input::placeholder { color: rgba(255,255,255,0.6); }
        .topbar-search input:focus { outline: none; background: rgba(255,255,255,0.12); border-color: var(--accent-primary); width: 280px; box-shadow: 0 0 12px rgba(229,9,20,0.15); }
        .topbar-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.6); font-size: 0.85rem; }

        .status-indicator { display: flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); font-size: 0.72rem; font-weight: 500; transition: all 0.3s; white-space: nowrap; flex-shrink: 0; height: fit-content; }
        .status-indicator .status-dot { width: 7px; height: 7px; border-radius: 50%; transition: background 0.3s ease; }
        .status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 8px rgba(0,184,148,0.7); }
        .status-indicator.online .status-text { color: #ccc; }
        .status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 8px rgba(231,76,60,0.7); }
        .status-indicator.offline .status-text { color: #e74c3c; }
        
        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }
        
        .btn-nuevo { background: linear-gradient(135deg, var(--accent-primary), #ff6b6b); color: white; padding: 10px 20px; border-radius: 4px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-nuevo:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(229,9,20,0.4); }
        
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
        
        .theme-toggle { background: transparent; border: none; font-size: 1.2rem; cursor: pointer; color: #e5e5e5; }
        
        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; cursor: pointer; }
        .dropdown-menu-netflix { position: absolute; top: 110%; right: 0; min-width: 260px; background: #141414; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 12px 40px rgba(0,0,0,0.8); padding: 8px 0; z-index: 99999; display: none; }
        .user-dropdown:hover .dropdown-menu-netflix, .dropdown-menu-netflix.show { display: block; }
        .dropdown-menu-netflix .dropdown-header { padding: 14px 16px 10px; border-bottom: 1px solid #222; }
        .dropdown-menu-netflix .dd-name { font-weight: 700; font-size: 0.9rem; color: #fff; }
        .dropdown-menu-netflix .dd-email { font-size: 0.75rem; color: #888; margin-top: 2px; }
        .dropdown-menu-netflix .dd-role { font-size: 0.7rem; color: var(--accent-primary); margin-top: 2px; text-transform: uppercase; }
        .dropdown-menu-netflix .dd-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; cursor: pointer; border: none; background: none; width: 100%; text-align: left; text-decoration: none; }
        .dropdown-menu-netflix .dd-item:hover { background: #1f1f1f; color: #fff; }
        .dropdown-menu-netflix .dd-divider { height: 1px; background: #222; margin: 6px 0; }
        .dropdown-menu-netflix .dd-logout { color: var(--accent-danger); }
        
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

        @media (max-width: 768px) {
            .topbar { padding: 0 5%; }
            .topbar-left { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 0; }
            .topbar-logo { font-size: 1.3rem; }
            .topbar-logo .logo-text { display: none; }
            .menu-toggle { display: block !important; margin-left: auto; font-size: 2rem; padding: 0; }
            .topbar-right { display: none !important; }
            .topbar-nav { display: none; }
            .topbar-nav.show { display: flex; flex-direction: column; position: absolute; top: var(--topbar-height); left: 0; right: 0; background: #000000; padding: 1.5rem 5%; gap: 1rem; border-bottom: 1px solid var(--border-color); height: calc(100vh - var(--topbar-height)); overflow-y: auto; z-index: 1000; }
            .mobile-user-section { display: flex; flex-direction: column; gap: 10px; padding-top: 15px; margin-top: auto; border-top: 1px solid var(--border-color); }
        }
        @media (min-width: 769px) { .menu-toggle { display: none !important; } .mobile-user-section { display: none !important; } }
        
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
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; box-shadow: inset 0 0 5px rgba(0,0,0,0.5); }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #E50914, #ff6b6b); }

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
    
    <nav class="topbar" id="topbar">
        <div class="topbar-left d-flex align-items-center gap-3">
            <div class="topbar-logo d-flex align-items-center gap-2">
                <img src="{{ asset('img/logo-unellez.png') }}" class="logo-nav-unellez" alt="Logo"> 
                <span class="logo-text">OSWA Inv</span>
            </div>
            <div class="status-indicator online d-none d-md-flex ms-2 me-4">
                <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                <span class="status-text text-white" style="font-size: 0.75rem;">En línea</span>
            </div>
        </div>

        <div class="topbar-nav" id="topbarNav">
            <a href="{{ route('inventario') }}" class="{{ request()->routeIs('inventario') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('catalogo') }}" class="{{ request()->routeIs('catalogo') ? 'active' : '' }}">Catálogo</a>
            <a href="{{ route('proveedores') }}" class="{{ request()->routeIs('proveedores') ? 'active' : '' }}">Proveedores</a>
            
            <div class="nav-dropdown">
                <a href="#" class="dropdown-toggle" onclick="event.preventDefault(); this.parentElement.classList.toggle('show')">Reportes</a>
                <div class="dropdown-menu-custom">
                    <a href="{{ route('exportar.pdf') }}" target="_blank" class="dropdown-item-custom">
                        <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Inventario (PDF)
                    </a>
                    @if(auth()->user()?->rol === 'admin')
                    <a href="{{ route('respaldo.db') }}" class="dropdown-item-custom">
                        <i class="bi bi-database-down text-info"></i> Respaldar Base de Datos
                    </a>
                    @endif
                </div>
            </div>

            <div class="mobile-user-section d-md-none mt-auto pt-4 border-top border-secondary">
                <div class="status-indicator online mb-3" style="width: fit-content;">
                    <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                    <span class="status-text text-white" style="font-size: 0.8rem;">En línea</span>
                </div>
                <div class="user-info mb-3 d-flex align-items-center gap-2">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div>
                        <div class="text-white fw-bold" style="font-size: 0.9rem;">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                        <div class="text-secondary" style="font-size: 0.8rem;">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right"></i> Salir del Sistema
                    </button>
                </form>
            </div>
        </div>

        <div class="topbar-right d-none d-md-flex align-items-center gap-3">
            <button class="theme-toggle" onclick="toggleTheme()" title="Modo claro/oscuro"><i class="bi bi-moon-fill"></i></button>
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="topbarSearchInput" placeholder="Buscar productos...">
            </div>
            <div class="user-dropdown" id="userDropdown">
                <div class="d-flex align-items-center gap-2" onclick="toggleUserDropdown()">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <i class="bi bi-caret-down-fill" id="dropdownArrow" style="color:#888;font-size:0.7rem;transition:transform 0.2s;"></i>
                </div>
                <div class="dropdown-menu-netflix" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dd-name">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                        <div class="dd-email">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        <div class="dd-role">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                    </div>
                    <button class="dd-item" onclick="mostrarMiCuenta()"><i class="bi bi-person-circle"></i> Mi Cuenta</button>
                    <button class="dd-item" onclick="mostrarAtajos()"><i class="bi bi-keyboard"></i> Atajos de Teclado</button>
                    @if(auth()->user()?->rol === 'admin')
                    <a href="{{ route('usuarios.index') }}" class="dd-item"><i class="bi bi-people"></i> Administrar Usuarios</a>
                    @endif
                    <button type="button" class="dd-item text-white" onclick="abrirSelectorPerfiles(event)" style="background: none; border: none; cursor: pointer; width: 100%; text-align: left;">
                        <i class="bi bi-arrow-left-right text-danger"></i> Cambiar de Cuenta
                    </button>
                    <div class="dd-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 w-100">
                        @csrf
                        <button type="submit" class="dd-item dd-logout w-100 text-start" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="bi bi-box-arrow-right"></i> Cambiar Usuario / Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <button class="menu-toggle d-md-none" onclick="toggleSidebar()" style="background: transparent; border: none; color: white; font-size: 2rem; padding: 0;">
            <i class="bi bi-list"></i>
        </button>
    </nav>
    
    <main class="main-content">
        <div class="netflix-hero" style="background-image: url('{{ asset('img/refrigeracion_centros_datos.jpg') }}');">
            <div class="hero-vignette"></div>
            <div class="hero-content">
                <div class="d-flex align-items-center mb-2">
                    <img src="{{ asset('img/logo-unellez.png') }}" class="hero-logo-small" alt="UNELLEZ">
                    <span class="hero-subtitle-rgb">SISTEMA DE INVENTARIO</span>
                </div>
                <h1 class="hero-title">OSWA Inv</h1>
                <p class="hero-description text-light">
                    Control total y auditoría en tiempo real. Supervisa entradas, salidas y mantén el flujo de inventario optimizado con seguridad criptográfica.
                </p>
                <div class="hero-buttons">
                    <button class="btn-play" data-bs-toggle="modal" data-bs-target="#presentacionModal">
                        <i class="bi bi-rocket-takeoff-fill fs-4 me-2"></i> Explorar
                    </button>
                    <button class="btn-more" data-bs-toggle="modal" data-bs-target="#powerPointModal">
                        <i class="bi bi-file-earmark-slides fs-5 me-2"></i> Más info
                    </button>
                </div>
            </div>
        </div>
        
        <div id="panel-estadisticas" class="mt-4 pt-2">
        
        @if(Auth::check() && Auth::user()->rol === 'admin')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white mb-0 font-weight-bold">Resumen de Inventario</h4>
            <a href="{{ route('catalogo') }}" class="btn btn-danger" style="background-color: #E50914; border: none;">
                <i class="bi bi-grid-3x3-gap"></i> Ir al Catálogo
            </a>
        </div>

        <div class="row mb-4 oswa-3d-wrapper">
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

        <div class="oswa-alerts-container mb-4" style="background-color: #141414; border: 1px solid #2a2a2a; border-radius: 12px; padding: 20px;">
            <h5 class="text-white mb-3" style="border-bottom: 1px solid #2a2a2a; padding-bottom: 10px;">
                <i class="bi bi-exclamation-triangle text-danger"></i> Alertas Críticas del Inventario
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="text-secondary mb-3"><i class="bi bi-box-seam me-1"></i> Bajo Stock (Menos de 5 unidades)</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        @if(isset($productosBajoStock) && $productosBajoStock->count() > 0)
                            @foreach($productosBajoStock as $prod)
                                <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 px-0 d-flex justify-content-between align-items-center">
                                    {{ $prod->nombre }}
                                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger rounded-pill">Quedan {{ $prod->stock }}</span>
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

        <div class="oswa-chart-card full-width mb-4">
            <div class="oswa-chart-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="oswa-icon-box oswa-icon-green"><i class="bi bi-graph-up-arrow"></i></div>
                    <h5 class="oswa-chart-title">Tendencia de Salidas (Últimos 7 días)</h5>
                </div>
                <span class="oswa-live-badge">
                    <span class="pulse-dot"></span> DATOS EN TIEMPO REAL
                </span>
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
                <div class="oswa-chart-body" style="min-height: 300px; position: relative; display: flex; justify-content: center; align-items: center;" id="container-top-productos">
                    <canvas id="chartTopProductos" style="display: none;"></canvas>
                    <div id="empty-top-productos" class="text-center" style="color: #666;">
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
        
        @else

        <div class="row mb-4">
            <div class="col-12">
                <div class="p-4" style="background-color: #141414; border: 1px solid #2a2a2a; border-radius: 12px; border-left: 5px solid #E50914;">
                    <h3 class="text-white fw-bold mb-1">¡Hola, {{ Auth::user()->name ?? 'Usuario' }}!</h3>
                    <p class="mb-0" style="color: #cccccc;">Bienvenido a tu panel operativo. ¿Qué deseas hacer hoy?</p>
                </div>
            </div>
        </div>

        <div class="row oswa-3d-wrapper">
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center oswa-3d-card">
                    <div class="card-body py-5 d-flex flex-column justify-content-center" style="transform-style: preserve-3d;">
                        <div class="mb-3 oswa-3d-icon"><i class="bi bi-grid-3x3-gap" style="font-size: 3rem; color: #E50914;"></i></div>
                        <h5 class="text-white fw-bold" style="transform: translateZ(30px);">Explorar Catálogo</h5>
                        <p style="color: #cccccc; font-size: 0.9rem; transform: translateZ(20px);">Busca productos y verifica el stock actual.</p>
                        <div style="transform: translateZ(40px);">
                            <a href="{{ url('/catalogo') }}" class="btn btn-outline-light w-100 mt-2">Ir al Catálogo</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center oswa-3d-card">
                    <div class="card-body py-5 d-flex flex-column justify-content-center" style="transform-style: preserve-3d;">
                        <div class="mb-3 oswa-3d-icon"><i class="bi bi-upc-scan" style="font-size: 3rem; color: #25D366;"></i></div>
                        <h5 class="text-white fw-bold" style="transform: translateZ(30px);">Escanear Producto</h5>
                        <p style="color: #cccccc; font-size: 0.9rem; transform: translateZ(20px);">Usa la cámara para buscar información al instante.</p>
                        <div style="transform: translateZ(40px);">
                            <a href="{{ url('/catalogo?openScanner=true') }}" class="btn btn-outline-success w-100 mt-2">Abrir Escáner</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center oswa-3d-card">
                    <div class="card-body py-5 d-flex flex-column justify-content-center" style="transform-style: preserve-3d;">
                        <div class="mb-3 oswa-3d-icon"><i class="bi bi-cart-plus" style="font-size: 3rem; color: #0dcaf0;"></i></div>
                        <h5 class="text-white fw-bold" style="transform: translateZ(30px);">Pedir Material</h5>
                        <p style="color: #cccccc; font-size: 0.9rem; transform: translateZ(20px);">Crea una requisición nueva para el administrador.</p>
                        <div style="transform: translateZ(40px);">
                            <a href="{{ url('/requisiciones/crear') }}" class="btn btn-outline-info w-100 mt-2">Crear Solicitud</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endif
        
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

    <!-- SCRIPT LIMPIO: SÓLO LAS FUNCIONES DEL DASHBOARD, SIN RESTOS DE PERFILES -->
    <script>
        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            if (arrow) arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
        
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(e.target)) { 
                const menu = document.getElementById('userDropdownMenu');
                if (menu) menu.style.display = 'none'; 
            }
        });
        
        function mostrarMiCuenta() {
            document.getElementById('userDropdownMenu').style.display = 'none';
            const photoPath = @json(auth()->user()?->profile_photo_path);
            const userName = @json(auth()->user()?->name ?? 'Usuario');
            const userEmail = @json(auth()->user()?->email ?? 'Sin correo');
            const initial = userName.charAt(0).toUpperCase();
            
            let avatarHtml;
            if (photoPath) {
                avatarHtml = `<img src="{{ asset('storage') }}/${photoPath}" alt="${userName}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #444;">`;
            } else {
                avatarHtml = `<div style="width:60px;height:60px;background-color:#E50914;color:white;border-radius:8px;display:flex;justify-content:center;align-items:center;font-weight:bold;font-size:1.8rem;">${initial}</div>`;
            }
            
            Swal.fire({
                title: 'Mi Cuenta',
                html: `<div style="text-align:left;background:var(--bg-card);border-radius:8px;padding:16px;">
                    <div style="background-color:#222;border-radius:8px;padding:15px;display:flex;align-items:center;gap:15px;margin-bottom:16px;">
                        <div>${avatarHtml}</div>
                        <div>
                            <h5 style="color:white;margin:0;font-weight:bold;">${userName}</h5>
                            <p style="color:#aaa;margin:0;font-size:0.9rem;">${userEmail}</p>
                        </div>
                    </div>
                </div>`,
                confirmButtonText: 'Cerrar', confirmButtonColor: '#E50914', background: 'var(--bg-dark)', color: 'var(--text-primary)'
            });
        }
    </script>
    
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

        // Gráficos del Dashboard
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') return;

            const ventasLabels = {!! json_encode($nombresProductos ?? []) !!};
            const ventasData = {!! json_encode($ventasProductos ?? []) !!};

            if (ventasLabels.length > 0) {
                const chartElement = document.getElementById('chartTopProductos');
                const emptyElement = document.getElementById('empty-top-productos');
                if (chartElement) chartElement.style.display = 'block';
                if (emptyElement) emptyElement.style.display = 'none';
                
                if(chartElement) {
                    const ctxTop = chartElement.getContext('2d');
                    new Chart(ctxTop, {
                        type: 'bar',
                        data: {
                            labels: ventasLabels,
                            datasets: [{ label: 'Unidades Vendidas', data: ventasData, backgroundColor: '#E50914', borderColor: '#ff0f1b', borderWidth: 1, borderRadius: 4 }]
                        },
                        options: { animation: { duration: 1000 }, hover: { animationDuration: 0 }, responsiveAnimationDuration: 0, indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { color: '#2a2a2a' }, ticks: { color: '#aaa' } }, y: { grid: { display: false }, ticks: { color: '#f1f1f1', font: { size: 12 } } } } }
                    });
                }
            }

            const categoriasLabels = {!! json_encode(isset($categorias) ? array_keys($categorias->toArray()) : []) !!};
            const categoriasData = {!! json_encode(isset($categorias) ? array_values($categorias->toArray()) : []) !!};
            
            if (categoriasLabels.length > 0) {
                const ctxCat = document.getElementById('chartCategorias');
                if(ctxCat) {
                    new Chart(ctxCat.getContext('2d'), {
                        type: 'doughnut',
                        data: { labels: categoriasLabels, datasets: [{ data: categoriasData, backgroundColor: ['#E50914', '#2b90d9', '#4CAF50', '#ffc107', '#9c27b0', '#ff5722', '#00bcd4'], borderColor: '#1c1c1c', borderWidth: 2 }] },
                        options: { animation: { duration: 1000 }, responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#e5e5e5', font: { family: 'Inter' }, padding: 15 } } }, cutout: '65%' }
                    });
                }
            }

            const diasLabels = {!! json_encode($labelsTendencia ?? []) !!};
            const salidasData = {!! json_encode($datosTendencia ?? []) !!};

            if (diasLabels.length > 0) {
                const ctxTrend = document.getElementById('chartTendencia');
                if(ctxTrend) {
                    const ctx = ctxTrend.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(37, 211, 102, 0.3)');
                    gradient.addColorStop(1, 'rgba(37, 211, 102, 0.0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: { labels: diasLabels, datasets: [{ label: 'Salidas Diarias', data: salidasData, borderColor: '#25D366', backgroundColor: gradient, borderWidth: 2, fill: true, tension: 0.4, pointBackgroundColor: '#25D366', pointBorderColor: '#fff', pointBorderWidth: 1, pointRadius: 4 }] },
                        options: { animation: { duration: 1000 }, responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#e5e5e5', font: { family: 'Inter' } } } }, scales: { y: { beginAtZero: true, grid: { color: '#2b2b2b' }, ticks: { color: '#888', font: { family: 'Consolas', size: 12 } } }, x: { grid: { color: '#2b2b2b' }, ticks: { color: '#e5e5e5', font: { family: 'Inter', size: 13 } } } } }
                    });
                }
            }
        });
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

    <footer class="professional-footer">
        <div class="mb-1">&copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.</div>
        <div>Desarrollado con <i class="bi bi-code-slash text-primary"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span></div>
        <div class="mt-2 d-flex align-items-center justify-content-center" style="font-size: 0.75rem; opacity: 0.8;">
            <span>Ingeniería en Informática — V Semestre |</span>
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 18px; margin-left: 8px; margin-right: 4px; filter: brightness(0) invert(1);">
            <strong style="letter-spacing: 0.5px;">UNELLEZ</strong>
        </div>
    </footer>

    <!-- MODAL DE PRESENTACIÓN DEL PROYECTO -->
    <div class="modal fade" id="presentacionModal" tabindex="-1" aria-labelledby="presentacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color: #1a1a1a; border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 15px; overflow: hidden;">
                <div class="modal-header border-0" style="background: linear-gradient(90deg, #E50914, #8B0000);">
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2" id="presentacionModalLabel">
                        <i class="bi bi-box-seam"></i> Descubre OSWA Inv
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5 text-white">
                    <div class="text-center mb-5">
                        <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 70px; filter: brightness(0) invert(1);" class="mb-3">
                        <h3 class="fw-bold">Ingeniería Aplicada al Inventario</h3>
                        <p class="text-secondary">Diseñado para optimizar, asegurar y registrar cada movimiento en el almacén.</p>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-danger px-5 py-2 fw-bold" data-bs-dismiss="modal">¡Comenzar a usar!</button>
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

            // 2. Secuencia de tiempos (Timeouts)
            // A los 3 segundos: Ocultar logo, mostrar frase
            setTimeout(() => {
                if(logo) logo.classList.add('d-none');
                if(quote) {
                    quote.classList.remove('d-none');
                    quote.classList.add('show');
                }
            }, 3000);

            // A los 6.5 segundos: Desvanecer la pantalla negra completa
            setTimeout(() => {
                overlay.classList.add('fade-out');
                sessionStorage.setItem('oswaIntroPlayed', 'true');
            }, 6500);

            // A los 8 segundos: Eliminar el código HTML del overlay para que no estorbe
            setTimeout(() => {
                overlay.remove();
            }, 8000);
        });
    </script>

    @include('partials.perfiles')

</body>
</html>