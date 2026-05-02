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
            --bg-main: #121212;
            --bg-card: #1c1c1c;
            --n-red: #E50914;
            --n-border: #2b2b2b;
            --bg-dark: #121212;
            --bg-input: #2a2a2a;
            --border-color: #2b2b2b;
            --text-primary: #e5e5e5;
            --text-secondary: #a3a3a3;
            --accent-primary: #E50914;
            --accent-success: #00b894;
            --accent-danger: #e74c3c;
            --accent-warning: #fdcb6e;
            --accent-info: #0984e3;
            --topbar-height: 68px;
        }
        [data-theme="light"] {
            --bg-dark: #121212; --bg-card: #1c1c1c; --bg-input: #2a2a2a;
            --border-color: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }

        /* Glassmorphism Navbar */
        .topbar {
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%) !important;
            backdrop-filter: blur(10px);
            border: none !important;
        }
        
        .topbar { 
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999;
            overflow: visible !important;
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
        
        .logo-nav-unellez {
            height: 35px;
            filter: brightness(0) invert(1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            margin-right: 10px;
        }
        .logo-nav-unellez:hover {
            transform: scale(1.2);
            filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
        }
        
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

        .status-indicator { 
    display: flex; align-items: center; gap: 6px; 
    padding: 5px 12px; border-radius: 20px; 
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); 
    font-size: 0.72rem; font-weight: 500; transition: all 0.3s;
    white-space: nowrap; flex-shrink: 0; height: fit-content;
}
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
        
        .stat-card {
            background: #1c1c1c !important;
            border: 1px solid #2b2b2b !important;
            border-radius: 15px !important;
            padding: 1.5rem;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
            overflow: hidden;
            position: relative;
        }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }
        .stat-card:nth-child(5) { animation-delay: 0.25s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card:hover {
            transform: translateY(-8px) scale(1.05);
            border-color: #E50914 !important;
            z-index: 100;
            box-shadow: 0 15px 30px rgba(0,0,0,0.6);
        }
        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        .stat-value {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 1.8rem;
            font-weight: 800;
        }
        .stat-label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.75rem;
            color: #888;
        }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; margin-top: 20px; }
        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--n-border);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: var(--n-red);
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            z-index: 5;
        }
        .product-card.stock-critical { border-left: 4px solid #E50914; }
        .product-card.stock-low { border-left: 4px solid #fdcb6e; }
        .product-card.stock-normal { border-left: 4px solid #00b894; }
        .product-card-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
        .product-card-img-placeholder { width: 60px; height: 60px; border-radius: 8px; background: #222; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid var(--border-color); }
        .product-card-img-placeholder i { color: #555; font-size: 1.4rem; }
        .product-card-info { flex: 1; min-width: 0; }
        .product-card-title { font-weight: 700; font-size: 0.95rem; color: var(--text-primary); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-card-meta { font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 3px; }
        .product-card-code { font-size: 0.72rem; color: #777; display: flex; align-items: center; gap: 4px; }
        .product-card-controls { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
        
        .stock-pill { display: flex; align-items: center; background: rgba(0,0,0,0.2); border-radius: 8px; padding: 3px 4px; gap: 2px; border: 1px solid var(--border-color); }
        .stock-pill-btn { width: 28px; height: 28px; border-radius: 6px; border: none; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
        .stock-pill-minus { background: rgba(229,9,20,0.2); color: #E50914; }
        .stock-pill-minus:hover { background: rgba(229,9,20,0.4); }
        .stock-pill-plus { background: rgba(0,184,148,0.2); color: #00b894; }
        .stock-pill-plus:hover { background: rgba(0,184,148,0.4); }
        .stock-pill-value { width: 36px; text-align: center; font-weight: 700; font-size: 0.85rem; color: var(--text-primary); background: none; border: none; outline: none; }
        
        .product-card-actions { display: flex; gap: 6px; }
        .card-action-btn { width: 32px; height: 32px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 0.85rem; }
        .card-action-btn-edit { background: rgba(13,110,253,0.15); color: #0d6efd; }
        .card-action-btn-edit:hover { background: rgba(13,110,253,0.35); }
        .card-action-btn-transfer { background: rgba(253,126,20,0.15); color: #fd7e14; }
        .card-action-btn-transfer:hover { background: rgba(253,126,20,0.35); }
        .card-action-btn-delete { background: rgba(229,9,20,0.15); color: #E50914; }
        .card-action-btn-delete:hover { background: rgba(229,9,20,0.35); }
        .card-action-btn-order { background: rgba(13,202,240,0.15); color: #0dcaf0; }
        .card-action-btn-order:hover { background: rgba(13,202,240,0.35); }
        
        /* Gráficos */
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem; }
        @media (max-width: 991.98px) { .charts-grid { grid-template-columns: 1fr; } }
        .chart-card { background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; padding: 1.5rem; transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; }
        .chart-card:hover { transform: translateY(-5px) scale(1.02); border-color: var(--n-red); box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
        .chart-container { position: relative; height: 280px; width: 100%; }
        
        .bot-fab { position: fixed; bottom: 20px; left: 20px; width: 60px; height: 60px; border-radius: 50%; background: #E50914; color: white; border: none; font-size: 1.8rem; box-shadow: 0 8px 25px rgba(229,9,20,0.5); z-index: 9999; cursor: pointer; transition: transform 0.3s; display: flex; align-items: center; justify-content: center; }
        .floating-bot-window { position: fixed; bottom: 90px; left: 20px; width: 340px; height: 450px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.6); z-index: 9998; display: flex; flex-direction: column; opacity: 0; pointer-events: none; transform: translateY(20px); transition: all 0.3s ease; overflow: hidden; }
        .floating-bot-window.show { opacity: 1; pointer-events: all; transform: translateY(0); }
        .bot-header { background: #141414; padding: 15px; color: white; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
        .bot-header button { background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; }
        .bot-chat-history { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; background: var(--bg-dark); }
        .bot-chat-history::-webkit-scrollbar { width: 6px; }
        .bot-chat-history::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }
        .chat-bubble { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 0.85rem; line-height: 1.4; animation: fadeIn 0.3s ease; }
        .user-bubble { align-self: flex-end; background: #E50914; color: white; border-bottom-right-radius: 4px; }
        .bot-bubble { align-self: flex-start; background: #2b2b2b; color: white; border-bottom-left-radius: 4px; }
        .bot-input-area { padding: 10px; background: var(--bg-card); display: flex; gap: 8px; }
        .bot-input-area input { flex: 1; padding: 10px 15px; background: var(--bg-input); border: none; border-radius: 20px; color: var(--text-primary); outline: none; }
        .bot-input-area button { width: 40px; height: 40px; border-radius: 50%; background: #E50914; color: white; border: none; cursor: pointer; }

        .oswa-quick-replies-container {
            display: flex;
            gap: 8px;
            padding: 10px 15px;
            overflow-x: auto;
            white-space: nowrap;
            background: transparent;
            width: 100%;
            box-sizing: border-box;
        }
        .oswa-quick-replies-container::-webkit-scrollbar { height: 5px; }
        .oswa-quick-replies-container::-webkit-scrollbar-track { background: #1c1c1c; border-radius: 10px; }
        .oswa-quick-replies-container::-webkit-scrollbar-thumb { background: #E50914; border-radius: 10px; }
        .oswa-quick-replies-container::-webkit-scrollbar-thumb:hover { background: #ff1523; }

        .bot-chip {
            flex-shrink: 0;
            background: transparent;
            border: 1px solid #E50914;
            color: #E50914;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .bot-chip:hover { background: #E50914; color: #ffffff; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .oswa-bot-card { display: none; }
        .bot-response { margin-top: 1rem; padding: 1rem; border-radius: 4px; background: var(--bg-input); display: none; }

        .scanner-fab { position: fixed; bottom: 2rem; right: 2rem; width: 60px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, var(--accent-primary), #ff6b6b); border: none; color: white; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 30px rgba(229,9,20,0.4); z-index: 9999; transition: transform 0.3s; }
        .scanner-fab:hover { transform: scale(1.05); }
        
        .modal-content { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 1rem 1.5rem; }
        .modal-title { color: var(--text-primary); }
        .btn-close { filter: invert(1); }
        [data-theme="light"] .btn-close { filter: invert(0); }
        .modal-body { padding: 1.5rem; }
        .form-control, .form-select { background: var(--bg-input); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 4px; padding: 10px; }
        .form-control:focus, .form-select:focus { background: var(--bg-input); border-color: var(--accent-primary); color: var(--text-primary); box-shadow: none; }
        .form-label { color: var(--text-secondary); font-size: 0.9rem; }
        
        .theme-toggle { background: transparent; border: none; font-size: 1.2rem; cursor: pointer; color: #e5e5e5; }
        
        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 32px; height: 32px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem; cursor: pointer; }
        .dropdown-menu-netflix { position: fixed; top: 70px; right: 4%; width: 260px; background: #141414; border: 1px solid #2a2a2a; border-radius: 8px; box-shadow: 0 12px 40px rgba(0,0,0,0.8); padding: 8px 0; z-index: 99999; display: none; }
        .dropdown-menu-netflix .dropdown-header { padding: 14px 16px 10px; border-bottom: 1px solid #222; }
        .dropdown-menu-netflix .dd-name { font-weight: 700; font-size: 0.9rem; color: #fff; }
        .dropdown-menu-netflix .dd-email { font-size: 0.75rem; color: #888; margin-top: 2px; }
        .dropdown-menu-netflix .dd-role { font-size: 0.7rem; color: var(--accent-primary); margin-top: 2px; text-transform: uppercase; }
        .dropdown-menu-netflix .dd-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #ccc; font-size: 0.85rem; cursor: pointer; border: none; background: none; width: 100%; text-align: left; text-decoration: none; }
        .dropdown-menu-netflix .dd-item:hover { background: #1f1f1f; color: #fff; }
        .dropdown-menu-netflix .dd-divider { height: 1px; background: #222; margin: 6px 0; }
        .dropdown-menu-netflix .dd-logout { color: var(--accent-danger); }
        
        .float-plus, .float-minus { animation: floatUp 0.8s ease-out forwards; }
        .float-plus { color: var(--accent-success); }
        .float-minus { color: var(--accent-danger); }
        @keyframes floatUp { 0% { transform: translateY(0); opacity: 1; } 100% { transform: translateY(-30px); opacity: 0; } }
        .flash-green { animation: flashGreen 0.4s ease-out; }
        .flash-red { animation: flashRed 0.4s ease-out; }
        @keyframes flashGreen { 50% { background: rgba(16,185,129,0.5); } }
        @keyframes flashRed { 50% { background: rgba(239,68,68,0.5); } }
        
        #map { height: 300px; width: 100%; border-radius: 8px; border: 1px solid var(--border-color); }
        .route-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 12px; }
        .stat-item { background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 12px; text-align: center; }
        .stat-item .stat-label { font-size: 0.7rem; color: var(--text-secondary); display: block; text-transform: uppercase; }
        .stat-item .stat-value { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); }

        @media (max-width: 768px) {
            .topbar { padding: 0 5%; }
            .topbar-left { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 0; }
            .topbar-logo { font-size: 1.3rem; }
            .topbar-logo .logo-text { display: none; }
            .menu-toggle { display: block !important; margin-left: auto; font-size: 2rem; padding: 0; }
            .topbar-right { display: none !important; }

            .topbar-nav { display: none; }
            .topbar-nav.show { 
                display: flex; flex-direction: column; position: absolute; 
                top: var(--topbar-height); left: 0; right: 0; 
                background: #000000; padding: 1.5rem 5%; gap: 1rem; 
                border-bottom: 1px solid var(--border-color); height: calc(100vh - var(--topbar-height));
                overflow-y: auto; z-index: 1000;
            }
            .mobile-user-section { 
                display: flex; flex-direction: column; gap: 10px; 
                padding-top: 15px; margin-top: auto; border-top: 1px solid var(--border-color); 
            }
        }
        @media (min-width: 769px) {
            .menu-toggle { display: none !important; }
            .mobile-user-section { display: none !important; }
        }
        .professional-footer {
            text-align: center;
            padding: 1.5rem 4%;
            margin-top: 2rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-dark);
            color: var(--text-secondary);
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .professional-footer span.highlight {
            color: var(--text-primary);
            font-weight: 600;
        }
        .professional-footer .heart-icon {
            color: var(--accent-danger);
            animation: heartbeat 1.5s infinite;
            display: inline-block;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* HERO VIDEO NETFLIX */
        .netflix-hero {
            position: relative;
            height: 55vh;
            min-height: 400px;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 1.5rem;
            margin-bottom: 2.5rem;
            box-shadow: inset 0 0 100px #000;

            background-color: #141414;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .hero-vignette {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(77deg, rgba(18,18,18,1) 0%, rgba(18,18,18,0.8) 30%, transparent 85%),
                        linear-gradient(to top, #121212 0%, transparent 20%);
            z-index: 1;
        }
        .hero-content { 
            position: absolute; 
            top: 50%; 
            transform: translateY(-50%);
            left: 5%; 
            z-index: 2; 
            max-width: 600px; 
        }
        .hero-logo-small { height: 25px; filter: brightness(0) invert(1); }
        .hero-title {
            font-size: 4rem; font-weight: 800; color: white; margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            letter-spacing: -1px;
        }
        .hero-description { 
            font-size: 1.1rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); 
            margin-bottom: 1.5rem; line-height: 1.4;
        }
        .hero-buttons { display: flex; gap: 15px; }

        .btn-play { 
            display: flex; align-items: center; justify-content: center; 
            background: #E50914; color: white; border: none; border-radius: 6px; 
            padding: 8px 24px; font-weight: 600; font-size: 1.1rem; transition: all 0.2s; 
        }
        .btn-play:hover { background: #f40612; transform: scale(1.05); }

        .btn-more { 
            display: flex; align-items: center; justify-content: center; 
            background: rgba(109, 109, 110, 0.7); color: white; border: none; 
            border-radius: 4px; padding: 8px 24px; font-weight: 600; font-size: 1.1rem; 
            transition: all 0.2s; 
        }
        .btn-more:hover { background: rgba(109, 109, 110, 0.4); }

        @media (max-width: 768px) {
            .netflix-hero { height: 50vh; }
            .hero-title { font-size: 2.5rem; }
            .hero-description { font-size: 0.95rem; }
            .btn-play, .btn-more { padding: 6px 16px; font-size: 0.95rem; }
        }

        /* ANIMACIONES PREMIUM PARA EL HERO */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Cascada de animaciones con retrasos (delays) */
        .hero-content > .d-flex {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .hero-content .hero-title {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s forwards;
        }

        .hero-content .hero-description {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.4s forwards;
        }

        .hero-content .hero-buttons {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.6s forwards;
        }

        /* Nuevo estilo animado RGB para el subtítulo del Hero */
        .hero-subtitle-rgb {
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
            letter-spacing: 2px;
            font-size: 0.9rem;
            font-weight: 800;
            margin-left: 8px;
            text-transform: uppercase;
        }

        /* ESTILOS DE LA ENTRADA CINEMÁTICA */
        .cinematic-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: #000000; z-index: 99999;
            display: flex; justify-content: center; align-items: center;
            transition: opacity 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cinematic-content { text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }

        /* Animación del Logo */
        .intro-logo {
            width: 180px; filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255,255,255,0.5));
            opacity: 0; animation: pulseGlow 3s forwards;
        }

        /* Animación de la Frase */
        .intro-quote { opacity: 0; transform: scale(0.9); }
        .intro-quote.show { animation: textExplosion 3.5s forwards; }

        .quote-text { color: var(--text-secondary); font-size: 1.5rem; font-weight: 300; margin-bottom: 10px; letter-spacing: 2px; }
        .quote-highlight { 
            color: #ffffff; font-size: 3rem; font-weight: 800; text-transform: uppercase; letter-spacing: 4px;
            text-shadow: 0 0 20px rgba(229, 9, 20, 0.8), 0 0 40px rgba(229, 9, 20, 0.4); /* Brillo rojo intenso */
        }

        /* Keyframes */
        @keyframes pulseGlow {
            0% { opacity: 0; transform: scale(0.8); }
            30% { opacity: 1; transform: scale(1.05); filter: brightness(0) invert(1) drop-shadow(0 0 30px rgba(255,255,255,1)); }
            80% { opacity: 1; transform: scale(1); filter: brightness(0) invert(1) drop-shadow(0 0 10px rgba(255,255,255,0.3)); }
            100% { opacity: 0; transform: scale(1.1); }
        }

        @keyframes textExplosion {
            0% { opacity: 0; transform: scale(0.8); filter: blur(10px); }
            20% { opacity: 1; transform: scale(1.1); filter: blur(0); }
            80% { opacity: 1; transform: scale(1); filter: blur(0); }
            100% { opacity: 0; transform: scale(1.2); filter: blur(10px); }
        }

        /* Clase para desaparecer el overlay */
        .cinematic-overlay.fade-out { opacity: 0; pointer-events: none; }

        /* Boton Netflix para Respaldo */
        .btn-netflix {
            background: var(--n-red) !important;
            color: #fff !important;
            border: none !important;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(229,9,20,0.4);
            transition: all 0.3s ease;
        }
        .btn-netflix:hover {
            background: #c10711 !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229,9,20,0.6);
        }

        /* =========================================
           SCROLLBAR HACKER VIP (Estilo Gaming)
           ========================================= */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
            border-left: 1px solid #1a1a1a;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #B20710, #E50914);
            border-radius: 10px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.5);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #E50914, #ff6b6b);
        }
    </style>
</head>
<body data-theme="dark">
    
    <!-- OVERLAY DE ENTRADA CINEMÁTICA -->
    <div id="cinematic-intro" class="cinematic-overlay">
        <div class="cinematic-content">
            <!-- Fase 1: Logo -->
            <img src="{{ asset('img/logo-unellez.png') }}" id="intro-logo" class="intro-logo" alt="UNELLEZ">
            
            <!-- Fase 2: Frase Motivacional -->
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
                    <a href="{{ route('usuarios.index') }}" class="dd-item"><i class="bi bi-people"></i> Administrar Usuarios</a>
                    <button class="dd-item" onclick="abrirSelectorPerfiles(event)" href="#"><i class="bi bi-arrow-left-right"></i> Cambiar de Cuenta</button>
                    <div class="dd-divider"></div>
                    <button class="dd-item dd-logout" onclick="document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
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
        <div class="d-flex justify-content-between align-items-center mb-4" style="flex-wrap: wrap; gap: 10px;">
            <h4 class="mb-0 fw-bold">Resumen de Inventario</h4>
            <a href="{{ route('catalogo') }}" class="btn-nuevo"><i class="bi bi-grid me-2"></i>Ir al Catálogo</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--accent-primary);"><i class="bi bi-box-seam-fill"></i></div>
                <div class="stat-value" id="totalProductos">{{ $totalProductos }}</div>
                <div class="stat-label">Total Productos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--accent-info);"><i class="bi bi-stack"></i></div>
                <div class="stat-value" id="stockTotal">{{ number_format($stockTotal) }}</div>
                <div class="stat-label">Unidades en Almacén</div>
            </div>
            <div class="stat-card" style="border-color: rgba(229,9,20,0.4) !important;">
                <div class="stat-icon" style="color: var(--n-red);"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="stat-value" id="alertasStock" style="color: var(--n-red);">{{ $alertasStock }}</div>
                <div class="stat-label">Alertas de Bajo Stock</div>
            </div>
            <div class="stat-card" id="cardCapital">
                <div class="stat-icon" style="color: #10b981;"><i class="bi bi-currency-dollar"></i></div>
                <div class="stat-value" id="capitalInvertido" style="color: #10b981;">${{ number_format($capitalInvertido, 2) }}</div>
                <div class="stat-label">Capital Invertido</div>
                <div class="stat-sub" style="font-size:0.7rem;color:#64748b;margin-top:4px;font-family:'Consolas',monospace;">Eqv: Bs. {{ number_format($capitalInvertidoBs ?? 0, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--accent-primary);"><i class="bi bi-bank2"></i></div>
                <div class="stat-value" id="tasaBcv">{{ number_format($tasaBcv ?? 0, 2) }}</div>
                <div class="stat-label">Tasa BCV (Bs/USD)</div>
            </div>
        </div>
        
        <!-- Gráfico Top 5 Productos Más Vendidos -->
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <div class="card stat-card">
                    <h5 class="text-white mb-3 fw-bold"><i class="bi bi-bar-chart-fill text-danger me-2"></i> Top 5: Productos Más Vendidos</h5>
                    <canvas id="graficoVentas" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Panel de Alertas Críticas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <div class="card-header border-bottom border-danger border-opacity-25 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-exclamation-triangle text-danger me-2"></i> Tendencias de Vencimiento</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Columna Stock -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="text-secondary mb-3"><i class="bi bi-box-seam me-1"></i> Bajo Stock (Menos de 5 unidades)</h6>
                                <ul class="list-group list-group-flush bg-transparent">
                                    @forelse($productosBajoStock as $prod)
                                        <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 px-0 d-flex justify-content-between align-items-center">
                                            {{ $prod->nombre }}
                                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger rounded-pill">Quedan {{ $prod->stock }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-transparent text-success px-0"><i class="bi bi-check-circle me-1"></i> Stock en niveles óptimos.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <!-- Columna Vencimientos -->
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-3"><i class="bi bi-calendar-x me-1"></i> Próximos a Vencer (30 días o menos)</h6>
                                <ul class="list-group list-group-flush bg-transparent">
                                    @forelse($productosPorVencer as $prod)
                                        <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 px-0 d-flex justify-content-between align-items-center">
                                            {{ $prod->nombre }}
                                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning rounded-pill">Vence: {{ \Carbon\Carbon::parse($prod->fecha_vencimiento)->format('d/m/Y') }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-transparent text-success px-0"><i class="bi bi-check-circle me-1"></i> Ningún producto por vencer.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($esAdmin && count($requisicionesPendientes) > 0)
        <div class="d-flex justify-content-between align-items-center mt-5 mb-2">
            <h5 class="mb-0"><i class="bi bi-clipboard-check me-2" style="color: var(--accent-warning);"></i>Requisiciones Pendientes</h5>
            <span style="color:var(--accent-warning);font-weight:500;">{{ count($requisicionesPendientes) }} pendientes</span>
        </div>
        <div class="table-responsive" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; padding: 1rem; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
            <table class="table mb-0" style="color: var(--text-primary);">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <th>Empleado</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requisicionesPendientes as $req)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td>{{ $req->user->name }}</td>
                        <td>{{ $req->producto->nombre }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $req->cantidad }}</span></td>
                        <td style="color: var(--text-secondary); font-size: 0.85rem;">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <button class="btn btn-sm btn-success me-1" onclick="aprobarRequisicion({{ $req->id }})"><i class="bi bi-check-lg"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="rechazarRequisicion({{ $req->id }})"><i class="bi bi-x-lg"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <div class="row mt-4">
            <div class="col-12 col-lg-6 mb-4">
                <div class="p-4 h-100" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <h5 class="text-white mb-4"><i class="bi bi-pie-chart me-2"></i> Distribución por Categorías</h5>
                    <div class="chart-container">
                        <canvas id="categoriaChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-6 mb-4"> 
                <div class="p-4 h-100" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <h5 class="text-white mb-4"><i class="bi bi-pie-chart-fill me-2 text-danger"></i> Estado del Inventario</h5>
                    
                    <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="inventarioChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- GRÁFICO DE TENDENCIAS: VENTAS / SALIDAS RECIENTES -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="p-4" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-white m-0"><i class="bi bi-graph-up-arrow me-2 text-success"></i> Tendencia de Salidas (Últimos 7 días)</h5>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">Datos en Tiempo Real</span>
                    </div>
                    
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
    
    <button class="bot-fab" onclick="toggleBotWindow()"><i class="bi bi-robot"></i></button>
    <div class="floating-bot-window" id="botWindow">
        <div class="bot-header"><span><i class="bi bi-robot me-2"></i> OSWA-Bot IA</span><button onclick="toggleBotWindow()"><i class="bi bi-x-lg"></i></button></div>
        <div class="bot-chat-history" id="botChatHistory">
            <div class="chat-bubble bot-bubble">¡Epa! Soy la Inteligencia Artificial de tu inventario. ¿En qué te ayudo?</div>
        </div>
        <div class="oswa-quick-replies-container">
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('📦 Ver Catálogo')">📦 Ver Catálogo</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('🤝 Añadir Proveedor')">🤝 Añadir Proveedor</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('📊 Reporte de Stock')">📊 Reporte de Stock</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('🛠️ Soporte')">🛠️ Soporte</button>
        </div>
        <div class="bot-input-area">
            <input type="text" id="botInput" placeholder="Pregúntame algo..." onkeypress="if(event.key==='Enter') enviarBot()">
            <button onclick="enviarBot()"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>

    <div class="modal fade" id="modalCamaraUniversal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--accent-primary);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title">📷 Captura de Imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body text-center p-0" style="background: #000;">
                    <video id="videoElementUniversal" width="100%" height="auto" autoplay playsinline muted style="display: block;"></video>
                    <canvas id="canvasHiddenUniversal" style="display:none;"></canvas>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-danger" id="btnCapturarUniversal"><i class="bi bi-camera-fill"></i> Capturar</button>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('escaner') }}" class="scanner-fab" title="Escáner (Alt+E)"><i class="bi bi-upc-scan"></i></a>
    
    <div class="modal fade" id="modalCamaraUniversal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--accent-primary);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title">📷 Captura de Imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body text-center p-0" style="background: #000;">
                    <video id="videoElementUniversal" width="100%" height="auto" autoplay playsinline muted style="display: block;"></video>
                    <canvas id="canvasHiddenUniversal" style="display:none;"></canvas>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-danger" id="btnCapturarUniversal"><i class="bi bi-camera-fill"></i> Capturar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="requisicionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px;">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title" style="color: var(--text-primary);"><i class="bi bi-hand-index me-2" style="color: var(--accent-success);"></i>Solicitar Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="requisicionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <input type="text" class="form-control" id="requisicionProducto" readonly>
                            <input type="hidden" id="requisicionProductoId" name="producto_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad a Solicitar</label>
                            <input type="number" class="form-control" id="requisicionCantidad" name="cantidad" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" style="background: var(--accent-success); border: none;"><i class="bi bi-send me-1"></i>Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        
        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
        
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (!dropdown.contains(e.target)) { document.getElementById('userDropdownMenu').style.display = 'none'; }
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

        const csrfTokenProfile = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        let modoEdicionActivo = false;

        window.abrirSelectorPerfiles = function(e) {
            if (e) e.preventDefault();
            document.getElementById('userDropdownMenu').style.display = 'none';
            document.getElementById('oswa-profile-selector').classList.remove('oswa-hidden');
        };

        window.cerrarSelectorPerfiles = function() {
            document.getElementById('oswa-profile-selector').classList.add('oswa-hidden');
        };

        window.ejecutarCambioPerfil = async function(userId) {
            document.body.style.cursor = 'wait';
            const overlay = document.getElementById('oswa-profile-selector');
            overlay.style.opacity = '0.5';
            overlay.style.pointerEvents = 'none';

            try {
                const response = await fetch('/cambiar-perfil-netflix', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfTokenProfile,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert('Error al cambiar de cuenta. Verifica los IDs en la base de datos.');
                    restaurarVistaSelector();
                }
            } catch (error) {
                console.error("Error en la conexión:", error);
                alert('Hubo un error de conexión con el servidor.');
                restaurarVistaSelector();
            }
        }

        function restaurarVistaSelector() {
            document.body.style.cursor = 'default';
            const overlay = document.getElementById('oswa-profile-selector');
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
        }

        window.seleccionarPerfilConCarga = function(userId) {
            document.getElementById('oswa-global-loader').classList.remove('oswa-hidden');
            ejecutarAnimacionCarga('AUTENTICANDO CREDENCIALES Y PERMISOS...', false, function() {
                window.ejecutarCambioPerfil(userId);
            });
        }

        window.toggleModoAdministracionPerfiles = function() {
            modoEdicionActivo = !modoEdicionActivo;
            const btnManage = document.querySelector('.oswa-btn-manage');
            const btnCancel = document.querySelector('.oswa-btn-cancel');
            const iconosEdicion = document.querySelectorAll('.oswa-edit-icon');

            if (modoEdicionActivo) {
                btnManage.classList.add('oswa-hidden');
                btnCancel.classList.remove('oswa-hidden');
                iconosEdicion.forEach(icon => icon.classList.remove('oswa-hidden'));
            } else {
                btnManage.classList.remove('oswa-hidden');
                btnCancel.classList.add('oswa-hidden');
                iconosEdicion.forEach(icon => icon.classList.add('oswa-hidden'));
            }
        }

        window.abrirModalCreacion = function() {
            document.getElementById('oswa-modal-create').classList.remove('oswa-hidden');
        }
        window.cerrarModalCreacion = function() {
            document.getElementById('oswa-modal-create').classList.add('oswa-hidden');
        }
        window.enviarFormularioCreacion = async function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const result = await fetchPostRequest('{{ route("perfil.crear") }}', formData);
            if (result.success) window.location.reload();
        }

        window.abrirModalEdicion = function(userId, userName) {
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-user-name').value = userName;
            const previewAvatar = document.getElementById('oswa-edit-avatar-preview');
            previewAvatar.innerText = userName.charAt(0).toUpperCase();
            previewAvatar.style.backgroundImage = 'none';
            document.getElementById('oswa-modal-edit').classList.remove('oswa-hidden');
        }
        window.cerrarModalEdicion = function() {
            document.getElementById('oswa-modal-edit').classList.add('oswa-hidden');
        }

        window.previewPhoto = function(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('oswa-edit-avatar-preview');
                output.innerText = '';
                output.style.backgroundImage = `url(${reader.result})`;
                output.style.backgroundSize = 'cover';
                output.style.backgroundPosition = 'center';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        window.enviarFormularioEdicion = async function(e) {
            e.preventDefault();
            const form = e.target;
            const userId = document.getElementById('edit-user-id').value;
            const nuevoNombre = document.getElementById('edit-user-name').value;
            const formData = new FormData(form);

            const btnSubmit = form.querySelector('button[type="submit"]');
            const btnOriginalText = btnSubmit.innerText;
            btnSubmit.innerText = "Guardando...";
            btnSubmit.disabled = true;

            try {
                const result = await fetchPostRequest(`/perfiles/actualizar/${userId}`, formData);

                if (result.success) {
                    cerrarModalEdicion();
                    const avatarContainer = document.querySelector(`div[onclick="seleccionarPerfilConCarga(${userId})"]`);
                    if (avatarContainer) {
                        const nameSpan = avatarContainer.nextElementSibling;
                        if (nameSpan && nameSpan.classList.contains('oswa-name')) {
                            nameSpan.innerText = nuevoNombre;
                        }
                        const previewDiv = document.getElementById('oswa-edit-avatar-preview');
                        const nuevaImagenBg = previewDiv.style.backgroundImage;
                        if (nuevaImagenBg && nuevaImagenBg !== 'none' && nuevaImagenBg !== '') {
                            const urlFoto = nuevaImagenBg.slice(4, -1).replace(/"/g, "");
                            avatarContainer.innerHTML = `<img src="${urlFoto}" alt="${nuevoNombre}" class="oswa-avatar" style="width: 100%; height: 100%; object-fit: cover;">`;
                        }
                    }
                } else {
                    alert('Hubo un problema al guardar el perfil en el servidor.');
                }
            } catch (error) {
                console.error("Error al guardar:", error);
            } finally {
                btnSubmit.innerText = btnOriginalText;
                btnSubmit.disabled = false;
            }
        }

        window.eliminarPerfil = async function() {
            const userId = document.getElementById('edit-user-id').value;
            const userName = document.getElementById('edit-user-name').value;
            if (!confirm(`⚠️ ¿Estás 100% seguro de que deseas eliminar el perfil de "${userName}"? Esta acción no se puede deshacer y borrará sus datos de acceso.`)) {
                return;
            }
            document.body.style.cursor = 'wait';
            const btnDelete = document.querySelector('.oswa-btn-delete');
            const originalText = btnDelete.innerText;
            btnDelete.innerText = "Eliminando...";
            btnDelete.disabled = true;
            try {
                const response = await fetch(`/perfiles/eliminar/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenProfile,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Error al eliminar el perfil.');
                }
            } catch (error) {
                console.error("Error al eliminar:", error);
                alert('Hubo un error de conexión con el servidor.');
            } finally {
                document.body.style.cursor = 'default';
                btnDelete.innerText = originalText;
                btnDelete.disabled = false;
            }
        }

        async function fetchPostRequest(url, formData) {
            document.body.style.cursor = 'wait';
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenProfile,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();
                document.body.style.cursor = 'default';
                if (response.ok) return data;
                throw new Error(data.message || 'Error desconocido');
            } catch (error) {
                alert('Hubo un error: ' + error.message);
                document.body.style.cursor = 'default';
                return { success: false };
            }
        }

        // --- PANTALLA DE CARGA GLOBAL ENTERPRISE ---
        window.addEventListener('load', function() {
            ejecutarAnimacionCarga('SINCRONIZANDO BASES DE DATOS RELACIONALES...', true);
        });

        function ejecutarAnimacionCarga(textoMensaje, ocultarAlTerminar, callback) {
            const loader = document.getElementById('oswa-global-loader');
            const bar = document.getElementById('oswa-progress-bar');
            const perc = document.getElementById('oswa-loader-percentage');
            const textNode = document.getElementById('oswa-loader-dynamic-text');

            let progress = 0;
            bar.style.width = '0%';
            perc.innerText = '0%';
            textNode.innerText = textoMensaje;

            const interval = setInterval(() => {
                progress += Math.floor(Math.random() * 15) + 10;
                
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    
                    setTimeout(() => {
                        if (ocultarAlTerminar) {
                            loader.classList.add('oswa-hidden');
                        }
                        if (callback) callback();
                    }, 500);
                }
                
                bar.style.width = progress + '%';
                perc.innerText = progress + '%';
            }, 150);
        }
    </script>
    
    <script>
        // GOOGLE MAPS
        const BARINAS = { lat: 8.6226, lng: -70.2039 };
        const sucursalesCoords = { 'Caracas':{lat:10.4806,lng:-66.8983,dist:500}, 'Maracaibo':{lat:10.6427,lng:-71.6125,dist:450}, 'Valencia':{lat:10.1620,lng:-68.0077,dist:350} };
        let transferMap=null, originMarker=null, destMarker=null, routeLine=null;
        
        function initTransferMap() {
            const mapContainer = document.getElementById('map');
            if (!mapContainer || !navigator.onLine) return;
            if (transferMap) return;
            transferMap = new google.maps.Map(mapContainer, { center: BARINAS, zoom: 6, streetViewControl: true });
            originMarker = new google.maps.Marker({ position: BARINAS, map: transferMap, title: 'Barinas (Origen)', icon: { path: google.maps.SymbolPath.CIRCLE, scale: 10, fillColor: '#00b894', fillOpacity: 1, strokeColor: '#fff', strokeWeight: 2 } });
            
            document.getElementById('sucursalDestino').addEventListener('change', function() {
                if(!this.value) return;
                const destino = sucursalesCoords[this.value];
                if(!destino) return;
                if(destMarker) destMarker.setMap(null);
                if(routeLine) routeLine.setMap(null);
                destMarker = new google.maps.Marker({ position: destino, map: transferMap, title: this.value });
                routeLine = new google.maps.Polyline({ path: [BARINAS, destino], strokeColor: '#E50914', strokeWeight: 3 });
                routeLine.setMap(transferMap);
                const bounds = new google.maps.LatLngBounds(); bounds.extend(BARINAS); bounds.extend(destino);
                transferMap.fitBounds(bounds);
                
                document.getElementById('route-stats').style.display = 'grid';
                document.getElementById('stat-distancia').textContent = destino.dist + ' km';
                document.getElementById('stat-flete').textContent = '$' + (destino.dist * 0.25).toFixed(2);
                document.getElementById('stat-tiempo').textContent = (destino.dist / 80).toFixed(1) + ' h';
            });
        }
    </script>

    <footer class="professional-footer">
        <div class="mb-1">
            &copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.
        </div>
        <div>
            Desarrollado con <i class="bi bi-code-slash text-primary"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span>
        </div>
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

                    <div class="row g-4 mb-4">
                        <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-start">
                            <i class="bi bi-box-seam text-danger fs-2 me-3"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Catálogo Inteligente</h5>
                                <p class="text-secondary" style="font-size: 0.9rem;">Control exacto de stock con un sistema de semáforo visual que alerta sobre vencimientos en tiempo real en cada tarjeta.</p>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-start">
                            <i class="bi bi-buildings text-warning fs-2 me-3"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Red de Proveedores</h5>
                                <p class="text-secondary" style="font-size: 0.9rem;">Directorio corporativo vinculado al inventario, permitiendo emitir órdenes de abastecimiento directo a la base de datos.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-start">
                            <i class="bi bi-shield-lock text-info fs-2 me-3"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Auditoría Criptográfica</h5>
                                <p class="text-secondary" style="font-size: 0.9rem;">Registro inmutable de movimientos protegidos con firma digital (SHA-256) para garantizar trazabilidad y evitar alteraciones.</p>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-start">
                            <i class="bi bi-file-earmark-pdf text-success fs-2 me-3"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Reportes Automatizados</h5>
                                <p class="text-secondary" style="font-size: 0.9rem;">Generación instantánea de reportes en PDF listos para imprimir, firmar y entregar a la gerencia.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-danger px-5 py-2 fw-bold" data-bs-dismiss="modal">¡Comenzar a usar!</button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL ESTILO POWERPOINT (CARRUSEL ADAPTABLE) -->
    <div class="modal fade" id="powerPointModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <!-- Fondo y borde dinámico -->
            <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
                
                <div class="modal-body p-0">
                    <div id="carouselOSWA" class="carousel slide" data-bs-ride="false">
                        
                        <!-- Indicadores -->
                        <div class="carousel-indicators" style="margin-bottom: 10px;">
                            <button type="button" data-bs-target="#carouselOSWA" data-bs-slide-to="0" class="active" style="background-color: var(--text-secondary);"></button>
                            <button type="button" data-bs-target="#carouselOSWA" data-bs-slide-to="1" style="background-color: var(--text-secondary);"></button>
                            <button type="button" data-bs-target="#carouselOSWA" data-bs-slide-to="2" style="background-color: var(--text-secondary);"></button>
                            <button type="button" data-bs-target="#carouselOSWA" data-bs-slide-to="3" style="background-color: var(--text-secondary);"></button>
                        </div>

                        <!-- Diapositivas -->
                        <div class="carousel-inner" style="height: 450px;">
                            
                            <!-- Slide 1: Portada -->
                            <div class="carousel-item active h-100">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                    <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 80px;" class="mb-4 logo-nav-unellez">
                                    <h2 class="fw-bold mb-2" style="color: #E50914;">OSWA Inv v1.0</h2>
                                    <h5 style="color: var(--text-secondary);" class="mb-4">Sistema Gestor de Inventario</h5>
                                    <p style="font-size: 0.95rem; max-width: 80%; color: var(--text-primary);">Proyecto académico de Ingeniería en Informática enfocado en la optimización, seguridad y control de almacenes.</p>
                                    <div class="mt-4 pt-3 border-top w-75" style="border-color: var(--border-color) !important;">
                                        <span style="color: var(--text-secondary); display: block; margin-bottom: 5px; font-size: 0.9rem;">Desarrollado por:</span>
                                        <span class="fw-bold" style="color: var(--text-primary); font-size: 1.1rem;">Carlos Braca & Yorgelis Blanco</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 2: Catálogo y Vencimientos -->
                            <div class="carousel-item h-100">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                    <i class="bi bi-grid-fill mb-3" style="font-size: 4rem; color: #E50914;"></i>
                                    <h3 class="fw-bold mb-3" style="color: var(--text-primary);">1. Catálogo Dinámico</h3>
                                    <p style="font-size: 1.1rem; max-width: 85%; color: var(--text-secondary);">Toda la mercancía en un solo lugar. Integrado con un <strong>semáforo de vencimientos</strong> que cambia de color automáticamente para indicar qué productos deben rotarse con urgencia.</p>
                                </div>
                            </div>

                            <!-- Slide 3: Proveedores -->
                            <div class="carousel-item h-100">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                    <i class="bi bi-buildings mb-3" style="font-size: 4rem; color: #ffc107;"></i>
                                    <h3 class="fw-bold mb-3" style="color: var(--text-primary);">2. Gestión de Suministros</h3>
                                    <p style="font-size: 1.1rem; max-width: 85%; color: var(--text-secondary);">Un directorio estilo ERP. Visualiza qué mercancía despacha cada empresa y utiliza el botón de <strong>Abastecer</strong> para inyectar stock al catálogo con un solo clic.</p>
                                </div>
                            </div>

                            <!-- Slide 4: Auditoría -->
                            <div class="carousel-item h-100">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                    <i class="bi bi-shield-lock mb-3" style="font-size: 4rem; color: #0dcaf0;"></i>
                                    <h3 class="fw-bold mb-3" style="color: var(--text-primary);">3. Auditoría Criptográfica</h3>
                                    <p style="font-size: 1.1rem; max-width: 85%; color: var(--text-secondary);">Registro inmutable de acciones. Cada movimiento importante en el sistema es sellado utilizando encriptación SHA-256, garantizando la transparencia y la trazabilidad de los usuarios.</p>
                                </div>
                            </div>

                        </div>

                        <!-- Flechas de navegación -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselOSWA" data-bs-slide="prev" style="width: 10%;">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 3rem; height: 3rem; filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselOSWA" data-bs-slide="next" style="width: 10%;">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="width: 3rem; height: 3rem; filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
                
                <!-- Pie de página dinámico -->
                <div class="modal-footer border-0 justify-content-end p-3" style="background-color: transparent;">
                    <button type="button" class="btn btn-outline-danger btn-sm px-4" data-bs-dismiss="modal">Cerrar Presentación</button>
                </div>

            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    
    <script>
        function toggleSidebar() {
            const nav = document.getElementById('topbarNav');
            nav.classList.toggle('show');
        }

        // Secuencia de entrada cinemática
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('cinematic-intro');
            
            // Si la intro ya se reprodujo en esta sesión, la quitamos inmediatamente
            if (sessionStorage.getItem('oswaIntroPlayed') === 'true') {
                if(overlay) overlay.remove();
                return;
            }

            const logo = document.getElementById('intro-logo');
            const quote = document.getElementById('intro-quote');
            
            // 1. Reproducir Sonido
            try {
                const cinematicSound = new Audio('{{ asset("sounds/intro.mp3") }}');
                cinematicSound.volume = 0.8;
                cinematicSound.play();
            } catch (e) {
                console.log("El navegador bloqueó el autoplay del audio.", e);
            }

            // 2. Secuencia de tiempos (Timeouts)
            // A los 3 segundos: Ocultar logo, mostrar frase
            setTimeout(() => {
                logo.classList.add('d-none');
                quote.classList.remove('d-none');
                quote.classList.add('show');
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

        // Gráfico Top 5 Productos Más Vendidos
        const ventasLabels = {!! json_encode($nombresProductos ?? []) !!};
        const ventasData = {!! json_encode($ventasProductos ?? []) !!};

        if (ventasLabels.length > 0) {
            const ctx = document.getElementById('graficoVentas').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ventasLabels,
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: ventasData,
                        backgroundColor: '#E50914',
                        borderColor: '#ff0f1b',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: { color: '#e5e5e5', font: { family: 'Inter' } }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#2b2b2b' },
                            ticks: { color: '#888', font: { family: 'Consolas', size: 12 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#e5e5e5', font: { family: 'Inter', size: 13 } }
                        }
                    }
                }
            });
        }
    </script>
<!-- Pantalla de Selección de Perfiles Estilo Netflix (ACTUALIZADA) -->
<div id="oswa-profile-selector" class="oswa-netflix-overlay oswa-hidden">
    <div class="oswa-netflix-content">
        <h1 class="oswa-netflix-title">¿Quién está gestionando ahora?</h1>
        
        <div class="oswa-netflix-profiles">
            @foreach($users as $user)
            <div class="oswa-profile-card">
                <div class="oswa-edit-icon oswa-hidden" onclick="abrirModalEdicion({{ $user->id }}, '{{ $user->name }}')">
                    <i class="bi bi-pencil-fill"></i>
                </div>
                <div class="oswa-avatar-container" onclick="seleccionarPerfilConCarga({{ $user->id }})">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="oswa-avatar-img">
                    @else
                        <div class="oswa-avatar oswa-avatar-initials" style="background-color: {{ $loop->iteration == 1 ? '#E50914' : ($loop->iteration == 2 ? '#2b90d9' : '#4CAF50') }};">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <span class="oswa-name">{{ $user->name }}</span>
            </div>
            @endforeach
            
            <div class="oswa-profile-card" onclick="abrirModalCreacion()">
                <div class="oswa-avatar oswa-add-profile">
                    <i class="bi bi-plus-circle" style="font-size: 3rem;"></i>
                </div>
                <span class="oswa-name">Agregar perfil</span>
            </div>
        </div>

        <button class="oswa-btn-manage" onclick="toggleModoAdministracionPerfiles()">Administrar perfiles</button>
        <button class="oswa-btn-cancel oswa-hidden" onclick="toggleModoAdministracionPerfiles()">Listo</button>
    </div>
</div>

<!-- Modal: Editar Perfil -->
<div id="oswa-modal-edit" class="oswa-modal oswa-hidden">
    <div class="oswa-modal-content">
        <span class="oswa-close" onclick="cerrarModalEdicion()">&times;</span>
        <h2>Editar perfil</h2>
        <div id="oswa-edit-avatar-preview" class="oswa-avatar oswa-avatar-initials" style="background-color: #E50914; margin: 0 auto 1.5rem;">C</div>
        <form id="oswa-form-edit" enctype="multipart/form-data" onsubmit="enviarFormularioEdicion(event)">
            <input type="hidden" id="edit-user-id" name="user_id">
            <div class="oswa-input-group">
                <input type="text" id="edit-user-name" name="name" required placeholder="Nuevo nombre de perfil">
            </div>
            <div class="oswa-input-group">
                <label for="edit-profile-photo" class="oswa-btn-manage" style="cursor: pointer; display: inline-block;">Cambiar foto</label>
                <input type="file" id="edit-profile-photo" name="profile_photo" accept="image/*" class="oswa-hidden" onchange="previewPhoto(event)">
            </div>
            <button type="submit" class="oswa-btn-action">Guardar</button>
            <button type="button" class="oswa-btn-delete" onclick="eliminarPerfil()">Eliminar perfil</button>
        </form>
    </div>
</div>

<!-- Modal: Crear Perfil -->
<div id="oswa-modal-create" class="oswa-modal oswa-hidden">
    <div class="oswa-modal-content">
        <span class="oswa-close" onclick="cerrarModalCreacion()">&times;</span>
        <h2>Crear perfil</h2>
        <form id="oswa-form-create" onsubmit="enviarFormularioCreacion(event)">
            <div class="oswa-input-group">
                <input type="text" name="name" required placeholder="Nombre del nuevo perfil">
            </div>
            <button type="submit" class="oswa-btn-action">Crear</button>
        </form>
    </div>
</div>

<!-- Pantalla de Carga -->
<div id="oswa-global-loader" class="oswa-loader-overlay oswa-hidden">
    <div class="oswa-loader-content">
        <img src="{{ asset('img/logo-unellez.png') }}" alt="OSWA Inv Logo" class="oswa-loader-logo">
        <div class="oswa-progress-container">
            <div class="oswa-progress-bar" id="oswa-progress-bar"></div>
        </div>
        <p class="oswa-loader-text" id="oswa-loader-dynamic-text">INICIANDO MÓDULOS DEL SISTEMA...</p>
        <span class="oswa-loader-percentage" id="oswa-loader-percentage">0%</span>
    </div>
</div>

<!-- Estilos Pantalla de Perfiles -->
<style>
.oswa-netflix-overlay {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background-color: #141414;
    z-index: 99999;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: opacity 0.4s ease, visibility 0.4s;
}
.oswa-netflix-overlay.oswa-hidden {
    opacity: 0;
    visibility: hidden;
}
.oswa-netflix-content {
    text-align: center;
    animation: zoomIn 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
}
@keyframes zoomIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.oswa-netflix-title {
    color: #fff;
    font-size: 3.5vw;
    font-weight: 500;
    margin-bottom: 2rem;
}
.oswa-netflix-profiles {
    display: flex;
    justify-content: center;
    gap: 2vw;
    margin-bottom: 3rem;
}
.oswa-profile-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: transform 0.2s;
}
.oswa-profile-card:hover .oswa-avatar,
.oswa-profile-card:hover .oswa-avatar-initials {
    border: 4px solid white;
}
.oswa-profile-card:hover .oswa-name {
    color: white;
}
.oswa-avatar-container {
    cursor: pointer;
}
.oswa-avatar {
    width: 10vw;
    height: 10vw;
    max-width: 150px;
    max-height: 150px;
    min-width: 84px;
    min-height: 84px;
    border-radius: 4px;
    border: 4px solid transparent;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 4rem;
    font-weight: bold;
    box-sizing: border-box;
    transition: border 0.2s ease;
    overflow: hidden;
}
.oswa-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover !important;
    object-position: center;
}
.oswa-avatar-img {
    width: 10vw;
    height: 10vw;
    max-width: 150px;
    max-height: 150px;
    min-width: 84px;
    min-height: 84px;
    border-radius: 4px;
    border: 4px solid transparent;
    object-fit: cover;
    box-sizing: border-box;
    transition: border 0.2s ease;
}
#oswa-edit-avatar-preview {
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
}
.oswa-add-profile {
    background-color: transparent;
    border: 2px solid grey !important;
    color: grey;
}
.oswa-profile-card:hover .oswa-add-profile {
    background-color: white;
    color: #141414;
}
.oswa-name {
    color: grey;
    margin-top: 15px;
    font-size: 1.2rem;
    transition: color 0.2s;
}
.oswa-edit-icon {
    position: absolute;
    top: 5px; right: 5px;
    background: rgba(128, 128, 128, 0.7);
    color: white;
    width: 30px; height: 30px;
    border-radius: 50%;
    display: flex; justify-content: center; align-items: center;
    cursor: pointer;
    z-index: 10;
    font-size: 0.9rem;
}
.oswa-edit-icon:hover { background: #E50914; }
.oswa-btn-manage {
    background: transparent;
    border: 1px solid grey;
    color: grey;
    padding: 10px 30px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    margin: 10px;
}
.oswa-btn-manage:hover {
    color: white;
    border-color: white;
}
.oswa-btn-cancel {
    background: transparent;
    border: 1px solid grey;
    color: grey;
    padding: 10px 30px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    margin: 10px;
}
.oswa-btn-cancel:hover { background: #333; color: white; border-color: white; }
.oswa-btn-action {
    background: transparent;
    border: 1px solid grey;
    color: grey;
    padding: 10px 30px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    margin: 10px;
}
.oswa-btn-action:hover {
    color: white;
    border-color: white;
}
.oswa-btn-delete {
    background: transparent;
    border: 1px solid grey;
    color: grey;
    padding: 10px 30px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    margin: 10px;
}
.oswa-btn-delete:hover { background: #E50914; color: white; border-color: #E50914; }
.oswa-modal {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background-color: rgba(0, 0, 0, 0.85);
    z-index: 100000;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: opacity 0.3s ease, visibility 0.3s;
}
.oswa-modal.oswa-hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.oswa-modal-content {
    background: #1c1c1c;
    padding: 2.5rem;
    border-radius: 8px;
    width: 90%;
    max-width: 450px;
    position: relative;
    animation: zoomIn 0.3s ease;
}
.oswa-modal-content h2 {
    color: #fff;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
}
.oswa-close {
    position: absolute;
    top: 15px; right: 20px;
    color: grey;
    font-size: 2rem;
    cursor: pointer;
    transition: color 0.2s;
}
.oswa-close:hover { color: white; }
.oswa-input-group {
    margin-bottom: 1rem;
}
.oswa-input-group input[type="text"] {
    width: 100%;
    padding: 12px 16px;
    background: #333;
    border: 1px solid #444;
    border-radius: 6px;
    color: #fff;
    font-size: 1rem;
    outline: none;
    transition: border 0.2s;
}
.oswa-input-group input[type="text"]:focus {
    border-color: #E50914;
}
.oswa-loader-overlay {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background-color: #000000;
    z-index: 999999;
    display: flex; justify-content: center; align-items: center;
    transition: opacity 0.6s ease;
}
.oswa-loader-overlay.oswa-hidden {
    opacity: 0;
    pointer-events: none;
}
.oswa-loader-content {
    text-align: center;
    color: white;
    font-family: 'Courier New', Courier, monospace;
}
.oswa-loader-logo {
    width: 180px;
    margin-bottom: 30px;
}
.oswa-progress-container {
    width: 350px;
    height: 3px;
    background-color: #333;
    margin: 0 auto 15px auto;
}
.oswa-progress-bar {
    height: 100%;
    width: 0%;
    background-color: #E50914;
    transition: width 0.2s ease;
}
.oswa-loader-text {
    font-size: 0.85rem;
    letter-spacing: 2px;
    margin-bottom: 8px;
    text-transform: uppercase;
}
.oswa-loader-percentage {
    font-size: 0.8rem;
    font-weight: bold;
}
@media (max-width: 768px) {
    .oswa-netflix-profiles { flex-wrap: wrap; gap: 15px; }
    .oswa-netflix-title { font-size: 1.8rem; }
    .oswa-avatar, .oswa-avatar-img { width: 100px; height: 100px; font-size: 2.5rem; }
}
</style>

    </body>
</html>