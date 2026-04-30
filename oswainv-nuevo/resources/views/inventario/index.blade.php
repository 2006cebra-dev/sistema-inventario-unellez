<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSWA Inv - Gestión de Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root {
            --bg-dark: #141414; --bg-card: #181818; --bg-input: #333333;
            --border-color: #2b2b2b; --text-primary: #ffffff; --text-secondary: #b3b3b3;
            --accent-primary: #E50914; --accent-success: #00b894;
            --accent-danger: #e74c3c; --accent-warning: #fdcb6e; --accent-info: #0984e3;
            --topbar-height: 68px;
        }
        [data-theme="light"] {
            --bg-dark: #f5f6f8; --bg-card: #ffffff; --bg-input: #e9ecef;
            --border-color: #dee2e6; --text-primary: #212529; --text-secondary: #6c757d;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-primary); margin: 0; }
        
        .topbar { 
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); 
            background: linear-gradient(180deg, rgba(0,0,0,0.95) 0%, rgba(20,20,20,0.98) 100%);
            border-bottom: 1px solid var(--border-color); 
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999; backdrop-filter: blur(10px);
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
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; min-width: 220px; background: #1a1a1a; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.6); padding: 6px 0; z-index: 1000; display: none; }
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
        
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease; animation: fadeInUp 0.5s ease forwards; opacity: 0; }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }
        .stat-card:nth-child(5) { animation-delay: 0.25s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 50px rgba(0,0,0,0.3); }
        .stat-icon { width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-value { font-size: 1.75rem; font-weight: 700; }
        .stat-label { color: var(--text-secondary); font-size: 0.9rem; }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; margin-top: 20px; }
        .product-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; display: flex; gap: 14px; align-items: flex-start; transition: all 0.3s ease; overflow: hidden; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.5); }
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
        .chart-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; }
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
                background: #141414; padding: 1.5rem 5%; gap: 1rem; 
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
            border-radius: 12px; 
            overflow: hidden; 
            margin-top: 1.5rem; 
            margin-bottom: 2.5rem; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            
            background-color: #141414;
            background-image: url('https://images.unsplash.com/photo-1586528116311-ad8ed7c80a30?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .hero-vignette { 
            position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
            background: linear-gradient(90deg, rgba(20,20,20,0.95) 0%, rgba(20,20,20,0.5) 50%, transparent 100%), linear-gradient(to top, #141414 0%, transparent 20%);
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
        }
        .hero-description { 
            font-size: 1.1rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); 
            margin-bottom: 1.5rem; line-height: 1.4;
        }
        .hero-buttons { display: flex; gap: 15px; }

        .btn-play { 
            display: flex; align-items: center; justify-content: center; 
            background: white; color: black; border: none; border-radius: 4px; 
            padding: 8px 24px; font-weight: 600; font-size: 1.1rem; transition: all 0.2s; 
        }
        .btn-play:hover { background: rgba(255,255,255,0.75); }

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
            <div class="status-indicator online d-none d-md-flex ms-2">
                <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                <span class="status-text text-white" style="font-size: 0.75rem;">En línea</span>
            </div>
        </div>

        <div class="topbar-nav" id="topbarNav">
            <a href="{{ route('inventario') }}" class="active">Dashboard</a>
            <a href="{{ route('vencimientos') }}">Vencimientos</a>
            
            <div class="nav-dropdown">
                <a href="#" class="dropdown-toggle" onclick="event.preventDefault(); this.parentElement.classList.toggle('show')">Reportes</a>
                <div class="dropdown-menu-custom">
                    <a href="{{ route('exportar.pdf') }}" target="_blank" class="dropdown-item-custom">
                        <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Inventario (PDF)
                    </a>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->rol === 'admin')
                <a href="{{ route('auditoria') }}">Auditoría</a>
            @endif

            <div class="mobile-user-section d-md-none mt-auto pt-4 border-top border-secondary">
                <div class="status-indicator online mb-3" style="width: fit-content;">
                    <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6);"></span>
                    <span class="status-text text-white" style="font-size: 0.8rem;">En línea</span>
                </div>
                <div class="user-info mb-3 d-flex align-items-center gap-2">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
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
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
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
                    <button class="dd-item" onclick="cambiarCuenta(event)"><i class="bi bi-arrow-left-right"></i> Cambiar de Cuenta</button>
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
        <div class="netflix-hero" style="background: url('{{ asset('img/refrigeracion_centros_datos.jpg') }}') center/cover no-repeat; background-color: #141414;">
            
            <div class="hero-vignette" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(20,20,20,0.95) 0%, rgba(20,20,20,0.6) 50%, transparent 100%), linear-gradient(to top, #141414 0%, transparent 20%); z-index: 1;"></div>
            
            <div class="hero-content" style="position: relative; z-index: 2;">
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
            <h4 class="mb-0 fw-bold">Panel de Control</h4>
            <button class="btn-nuevo" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Producto
            </button>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(229,9,20,0.15); color: var(--accent-primary);"><i class="bi bi-box-seam"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="totalProductos">{{ $totalProductos }}</div>
                        <div class="stat-label">Total Productos</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(9,132,227,0.15); color: var(--accent-info);"><i class="bi bi-stack"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="stockTotal">{{ number_format($stockTotal) }}</div>
                        <div class="stat-label">Unidades en Almacén</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(253,203,110,0.15); color: var(--accent-warning);"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="alertasStock">{{ $alertasStock }}</div>
                        <div class="stat-label">Alertas de Bajo Stock</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" id="cardCapital">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(0,184,148,0.15); color: var(--accent-success);"><i class="bi bi-currency-dollar"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="capitalInvertido">${{ number_format($capitalInvertido, 2) }}</div>
                        <div class="stat-label">Capital Invertido</div>
                        <div class="stat-sub" style="font-size:0.75rem;color:var(--text-secondary);margin-top:2px;">Eqv: Bs. {{ number_format($capitalInvertidoBs ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background: rgba(229,9,20,0.15); color: var(--accent-primary);"><i class="bi bi-bank"></i></div>
                    <div class="ms-3">
                        <div class="stat-value" id="tasaBcv">{{ number_format($tasaBcv ?? 0, 2) }}</div>
                        <div class="stat-label">Tasa BCV (Bs/USD)</div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($esAdmin && count($requisicionesPendientes) > 0)
        <div class="d-flex justify-content-between align-items-center mt-5 mb-2">
            <h5 class="mb-0"><i class="bi bi-clipboard-check me-2" style="color: var(--accent-warning);"></i>Requisiciones Pendientes</h5>
            <span style="color:var(--accent-warning);font-weight:500;">{{ count($requisicionesPendientes) }} pendientes</span>
        </div>
        <div class="table-responsive" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem;">
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
        
        <div class="d-flex justify-content-between align-items-center mt-5 mb-2">
            <h5 class="mb-0"><i class="bi bi-grid me-2"></i>Catálogo de Productos</h5>
            <span style="color:var(--text-secondary);font-weight:500;">{{ $productos->count() }} productos</span>
        </div>
        
        <div class="products-grid" id="productsGrid">
            @forelse($productos as $producto)
            <div class="product-card {{ $producto->stock <= 2 ? 'stock-critical' : ($producto->stock <= 5 ? 'stock-low' : 'stock-normal') }}" data-producto-id="{{ $producto->id }}" data-stock="{{ $producto->stock }}" data-nombre="{{ $producto->nombre }}" data-codigo="{{ $producto->codigo }}">
                @if($producto->imagen)
                    @if(filter_var($producto->imagen, FILTER_VALIDATE_URL))
                        <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="product-card-img">
                    @else
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="product-card-img">
                    @endif
                @else
                    <div class="product-card-img-placeholder"><i class="bi bi-image"></i></div>
                @endif
                
                <div class="product-card-info">
                    <div class="product-card-title">{{ $producto->nombre }}</div>
                    <div class="product-card-meta">{{ $producto->marca ?? 'Sin marca' }} • {{ $producto->categoria }}</div>
                    <div class="product-card-code"><i class="bi bi-upc-scan"></i> {{ $producto->codigo }}</div>
                    <div class="mt-1" style="font-weight: 700; font-size: 0.95rem; color: var(--accent-success);">${{ number_format($producto->precio, 2) }}</div>
                    @if($producto->dias_estimados !== null)
                        <div class="mt-1" style="font-size: 0.75rem; color: {{ $producto->dias_estimados <= 7 ? '#e74c3c' : '#b3b3b3' }};">
                            <i class="bi bi-calendar-check me-1"></i>~{{ $producto->dias_estimados }} días de stock
                        </div>
                    @else
                        <div class="mt-1" style="font-size: 0.75rem; color: #777;">
                            <i class="bi bi-dash-circle me-1"></i>Sin datos de salida
                        </div>
                    @endif
                </div>
                
                <div class="product-card-controls">
                    <div class="stock-pill">
                        <button class="stock-pill-btn stock-pill-minus" onclick="ajustarStock({{ $producto->id }}, 'restar')"><i class="bi bi-dash"></i></button>
                        <input type="number" class="stock-pill-value" value="{{ $producto->stock }}" min="0" id="stock-{{ $producto->id }}" onchange="ajustarStockDirecto({{ $producto->id }}, this.value)">
                        <button class="stock-pill-btn stock-pill-plus" onclick="ajustarStock({{ $producto->id }}, 'sumar')"><i class="bi bi-plus"></i></button>
                    </div>
                    @if($esAdmin)
                    <div class="product-card-actions">
                        <button class="card-action-btn card-action-btn-transfer" title="Transferir" onclick="abrirTransferencia({{ $producto->id }}, '{{ $producto->nombre }}', document.getElementById('stock-{{ $producto->id }}').value)"><i class="bi bi-truck"></i></button>
                        <button class="card-action-btn card-action-btn-order" title="Orden de Compra" onclick="generarOrden({{ $producto->id }}, '{{ $producto->nombre }}')"><i class="bi bi-cart"></i></button>
                        <button class="card-action-btn card-action-btn-edit" title="Editar" onclick="editarProducto({{ $producto->id }})"><i class="bi bi-pencil"></i></button>
                        <button class="card-action-btn card-action-btn-delete" title="Eliminar" onclick="eliminarProducto({{ $producto->id }})"><i class="bi bi-trash"></i></button>
                    </div>
                    @else
                    <div class="product-card-actions">
                        <button class="card-action-btn" style="background: rgba(0,184,148,0.15); color: #00b894;" title="Pedir Material" onclick="abrirRequisicion({{ $producto->id }}, '{{ $producto->nombre }}')"><i class="bi bi-hand-index"></i></button>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align:center; padding:4rem 0; color:var(--text-secondary);">
                <i class="bi bi-inbox" style="font-size:3rem;"></i>
                <p class="mt-3">Aún no hay productos registrados en el inventario.</p>
            </div>
            @endforelse
        </div>
        
        <div class="charts-grid">
            <div class="chart-card">
                <h5><i class="bi bi-pie-chart me-2"></i>Distribución por Categorías</h5>
                <div class="chart-container">
                    <canvas id="categoriaChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h5><i class="bi bi-pie-chart me-2"></i>Estado del Inventario</h5>
                <div class="chart-container">
                    <canvas id="stockChart"></canvas>
                </div>
                <div class="d-flex justify-content-around mt-3 text-center">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-success);" id="chartSaludable">{{ $stockSaludable }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Saludable</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-danger);" id="chartCritico">{{ $stockCritico }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Crítico</div>
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
    
    <div class="modal fade" id="nuevoProductoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo Producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="nuevoProductoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="imagen_url" id="imagenUrlInput">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Código de Barras</label>
                                <input type="text" class="form-control" name="codigo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <input type="text" class="form-control" name="marca">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría</label>
                                <input type="text" class="form-control" name="categoria" value="General">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio ($)</label>
                                <input type="number" class="form-control" name="precio" step="0.01" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Inicial</label>
                                <input type="number" class="form-control" name="stock" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" name="fecha_vencimiento">
                            </div>
                            <div class="col-md-6" id="cajaFotoPc">
                                <label class="form-label">Foto del Producto</label>
                                <input type="file" class="form-control" name="imagen" accept="image/*">
                                <input type="hidden" name="imagen_base64" id="imagen_base64">
                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="abrirCamara('producto')">📷 Tomar Foto</button>
                                <img id="previewCamaraProducto" style="display:none; max-width:100%; margin-top:8px; border-radius:8px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--accent-primary); border: none;">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editarProductoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="editarProductoForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" id="editCodigo" name="codigo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marca</label>
                                <input type="text" class="form-control" id="editMarca" name="marca">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="editCategoria" name="categoria">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" id="editPrecio" name="precio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Vencimiento</label>
                                <input type="date" class="form-control" id="editVencimiento" name="fecha_vencimiento">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="transferenciaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-truck me-2"></i>Transferir a Sucursal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="transferenciaForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <input type="text" class="form-control" id="transferProducto" readonly>
                            <input type="hidden" id="transferProductoId" name="producto_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Actual</label>
                            <input type="text" class="form-control" id="transferStock" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sucursal Destino</label>
                            <select class="form-select" id="sucursalDestino" name="sucursal" required>
                                <option value="">Seleccionar...</option>
                                <option value="Caracas">Caracas</option>
                                <option value="Maracaibo">Maracaibo</option>
                                <option value="Valencia">Valencia</option>
                                <option value="Barquisimeto">Barquisimeto</option>
                                <option value="San Cristóbal">San Cristóbal</option>
                                <option value="Mérida">Mérida</option>
                                <option value="Puerto La Cruz">Puerto La Cruz</option>
                                <option value="Maturín">Maturín</option>
                                <option value="Ciudad Guayana">Ciudad Guayana</option>
                                <option value="Coro">Coro</option>
                                <option value="Cumaná">Cumaná</option>
                                <option value="Guanare">Guanare</option>
                                <option value="San Juan de los Morros">San Juan de los Morros</option>
                                <option value="Trujillo">Trujillo</option>
                                <option value="San Felipe">San Felipe</option>
                                <option value="Barcelona">Barcelona</option>
                                <option value="Porlamar">Porlamar</option>
                                <option value="La Guaira">La Guaira</option>
                                <option value="San Fernando de Apure">San Fernando de Apure</option>
                                <option value="Puerto Ayacucho">Puerto Ayacucho</option>
                                <option value="Tucupita">Tucupita</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad a Enviar</label>
                            <input type="number" class="form-control" id="transferCantidad" name="cantidad" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto de Factura/Soporte</label>
                            <input type="file" class="form-control" id="soporteFile" accept="image/*">
                            <input type="hidden" name="soporte_base64" id="soporte_base64">
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="abrirCamara('soporte')">📷 Foto de Factura/Soporte</button>
                            <img id="previewCamaraSoporte" style="display:none; max-width:100%; margin-top:8px; border-radius:8px;">
                        </div>
                        <div class="mb-3">
                            <div class="map-label" style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 5px;"><i class="bi bi-geo-alt"></i> Ruta de transferencia (Origen: Barinas)</div>
                            <div id="map"></div>
                            <div id="route-stats" class="route-stats" style="display:none;">
                                <div class="stat-item"><span class="stat-label">Distancia</span><span class="stat-value" id="stat-distancia">0 km</span></div>
                                <div class="stat-item"><span class="stat-label">Costo Flete</span><span class="stat-value" style="color: var(--accent-success);" id="stat-flete">$0.00</span></div>
                                <div class="stat-item"><span class="stat-label">Tiempo Est.</span><span class="stat-value" id="stat-tiempo">0 h</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-truck me-1"></i>Procesar Transferencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="ordenCompraModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cart me-2"></i>Orden de Compra</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-center">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 4rem; color: var(--accent-info);"></i>
                    <p class="mt-3">Generando PDF de reorder...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-info" onclick="descargarOrden()"><i class="bi bi-download me-1"></i>Descargar PDF</button>
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
            Swal.fire({
                title: 'Mi Cuenta',
                html: `<div style="text-align:left;background:var(--bg-card);border-radius:8px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div style="width:48px;height:48px;background:var(--accent-primary);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                            <div style="color:var(--text-secondary);font-size:0.85rem;">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        </div>
                    </div>
                </div>`,
                confirmButtonText: 'Cerrar', confirmButtonColor: '#E50914', background: 'var(--bg-dark)', color: 'var(--text-primary)'
            });
        }
        
        function mostrarAtajos() {
            document.getElementById('userDropdownMenu').style.display = 'none';
            Swal.fire({
                title: 'Atajos de Teclado',
                html: `<div style="text-align:left;">
                    <p><kbd>Alt + B</kbd> Buscar productos</p>
                    <p><kbd>Alt + E</kbd> Abrir Escáner</p>
                    <p><kbd>Alt + N</kbd> Nuevo Producto</p>
                </div>`,
                confirmButtonText: 'Entendido', confirmButtonColor: '#E50914', background: 'var(--bg-card)', color: 'var(--text-primary)'
            });
        }
        
        function cambiarCuenta(e) {
            e.preventDefault();
            document.getElementById('userDropdownMenu').style.display = 'none';
            Swal.fire({
                title: 'Cambiando de cuenta...', text: 'Cerrando sesión actual', icon: 'info', timer: 1500, showConfirmButton: false, background: 'var(--bg-card)', color: 'var(--text-primary)'
            }).then(() => { document.getElementById('logout-form').submit(); });
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                switch(e.key.toLowerCase()) {
                    case 'b': e.preventDefault(); document.getElementById('topbarSearchInput').focus(); break;
                    case 'e': e.preventDefault(); document.querySelector('.scanner-fab').click(); break;
                    case 'n': e.preventDefault(); new bootstrap.Modal(document.getElementById('nuevoProductoModal')).show(); break;
                }
            }
        });
        
        // BÚSQUEDA
        document.getElementById('topbarSearchInput').addEventListener('input', function() {
            const texto = this.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const nombre = (card.dataset.nombre || '').toLowerCase();
                const codigo = (card.dataset.codigo || '').toLowerCase();
                card.style.display = (nombre.includes(texto) || codigo.includes(texto)) ? '' : 'none';
            });
        });
        
        // GRÁFICOS INICIALES
        const categoriaData = @json($categorias);
        const categoriaChart = new Chart(document.getElementById('categoriaChart'), {
            type: 'doughnut',
            data: { labels: Object.keys(categoriaData).length ? Object.keys(categoriaData) : ['Sin datos'], datasets: [{ data: Object.keys(categoriaData).length ? Object.values(categoriaData) : [1], backgroundColor: ['#E50914','#00b894','#fdcb6e','#0984e3','#e74c3c'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#b3b3b3' } } } }
        });
        
        const stockChart = new Chart(document.getElementById('stockChart'), {
            type: 'pie',
            data: { labels: ['Stock Saludable', 'Stock Crítico'], datasets: [{ data: [{{ $stockSaludable }}, {{ $stockCritico }}], backgroundColor: ['#00b894','#e74c3c'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        
        // ESTADO DE RED
        function updateStatusIndicator() {
            const indicators = document.querySelectorAll('.status-indicator');
            indicators.forEach(indicator => {
                const dot = indicator.querySelector('.status-dot');
                const text = indicator.querySelector('.status-text');

                if (navigator.onLine) {
                    indicator.classList.replace('offline', 'online');
                    dot.style.background = '#00b894';
                    dot.style.boxShadow = '0 0 6px rgba(0,184,148,0.6)';
                    if(text) text.textContent = 'En línea';
                } else {
                    indicator.classList.replace('online', 'offline');
                    dot.style.background = '#e74c3c';
                    dot.style.boxShadow = '0 0 6px rgba(231,76,60,0.6)';
                    if(text) text.textContent = 'Sin red';
                }
            });
        }
        window.addEventListener('online', updateStatusIndicator);
        window.addEventListener('offline', updateStatusIndicator);
        updateStatusIndicator();
        
        // TEMAS
        function initTheme() {
            const saved = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', saved);
            const icon = document.querySelector('.theme-toggle i');
            if (icon) icon.className = saved === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        }
        function toggleTheme() {
            const nuevo = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', nuevo);
            localStorage.setItem('theme', nuevo);
            const icon = document.querySelector('.theme-toggle i');
            if (icon) icon.className = nuevo === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        }
        initTheme();
        
        function showNotification(icon, title, isError = false) {
            Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 2000, background: isError ? '#dc3545' : '#00b894', color: '#fff' });
        }
        
        // --- LA MAGIA DEL STOCK CORREGIDA (NO BORRA GRÁFICOS) ---
        async function ajustarStock(productoId, accion) {
            const stockInput = document.getElementById('stock-' + productoId);
            const isIncrement = accion === 'sumar';
            try {
                const response = await fetch('{{ route('ajustar.stock') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id: productoId, accion: accion }) });
                const data = await response.json();
                if (data.success) {
                    stockInput.value = data.nuevo_stock;
                    
                    const floatEl = document.createElement('span');
                    floatEl.style.cssText = 'position:absolute; z-index:100; font-size:1rem; font-weight:bold; pointer-events:none;';
                    floatEl.className = isIncrement ? 'float-plus' : 'float-minus';
                    floatEl.innerText = isIncrement ? '+1' : '-1';
                    stockInput.parentElement.appendChild(floatEl);
                    setTimeout(() => floatEl.remove(), 800);
                    
                    stockInput.classList.add(isIncrement ? 'flash-green' : 'flash-red');
                    setTimeout(() => stockInput.classList.remove('flash-green', 'flash-red'), 400);
                    
                    const card = document.querySelector('.product-card[data-producto-id="' + productoId + '"]');
                    if (card) {
                        card.className = 'product-card ' + (data.nuevo_stock <= 2 ? 'stock-critical' : (data.nuevo_stock <= 5 ? 'stock-low' : 'stock-normal'));
                        card.dataset.stock = data.nuevo_stock;
                    }
                    actualizarTodo(data);
                    showNotification('success', data.nuevo_stock === 0 ? '¡Stock agotado!' : 'Stock actualizado');
                }
            } catch (e) { showNotification('error', 'Error de conexión', true); }
        }
        
        async function ajustarStockDirecto(productoId, valor) {
            const nuevoValor = Math.max(0, parseInt(valor) || 0);
            try {
                const response = await fetch('{{ route('ajustar.stock') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id: productoId, accion: 'set', valor: nuevoValor }) });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('stock-' + productoId).value = data.nuevo_stock;
                    const card = document.querySelector('.product-card[data-producto-id="' + productoId + '"]');
                    if (card) {
                        card.className = 'product-card ' + (data.nuevo_stock <= 2 ? 'stock-critical' : (data.nuevo_stock <= 5 ? 'stock-low' : 'stock-normal'));
                        card.dataset.stock = data.nuevo_stock;
                    }
                    actualizarTodo(data);
                    showNotification('success', 'Stock actualizado');
                }
            } catch (e) { showNotification('error', 'Error', true); }
        }
        
        function actualizarTodo(data) {
            if(document.getElementById('totalProductos')) document.getElementById('totalProductos').textContent = data.total_productos || {{ $totalProductos }};
            if(document.getElementById('stockTotal')) document.getElementById('stockTotal').textContent = Number(data.stock_total || 0).toLocaleString();
            if(document.getElementById('alertasStock')) document.getElementById('alertasStock').textContent = data.alertas_stock || 0;
            if(document.getElementById('capitalInvertido')) document.getElementById('capitalInvertido').textContent = '$' + Number(data.capital_invertido || 0).toLocaleString('en', { minimumFractionDigits: 2 });
            
            const bsElement = document.querySelector('#cardCapital .stat-sub');
            if(bsElement) bsElement.textContent = 'Eqv: Bs. ' + Number(data.capital_invertido_bs || 0).toLocaleString('en', { minimumFractionDigits: 2 });

            if(document.getElementById('tasaBcv')) document.getElementById('tasaBcv').textContent = Number(data.tasa_bcv || 0).toLocaleString('en', { minimumFractionDigits: 2 });

            let saludable = 0; let critico = 0;
            document.querySelectorAll('.product-card').forEach(card => {
                const stock = parseInt(card.dataset.stock) || 0;
                if (stock <= 5) critico++; else saludable++;
            });

            if(document.getElementById('chartSaludable')) document.getElementById('chartSaludable').textContent = saludable;
            if(document.getElementById('chartCritico')) document.getElementById('chartCritico').textContent = critico;
            
            if (typeof stockChart !== 'undefined') {
                stockChart.data.datasets[0].data = [saludable, critico];
                stockChart.update();
            }
        }
        
        // BOT
        function toggleBotWindow() {
            document.getElementById('botWindow').classList.toggle('show');
        }
        async function enviarBot() {
            const input = document.getElementById('botInput');
            const pregunta = input.value.trim();
            if (!pregunta) return;
            const chatHistory = document.getElementById('botChatHistory');
            chatHistory.innerHTML += `<div class="chat-bubble user-bubble">${pregunta}</div>`;
            input.value = '';
            chatHistory.scrollTop = chatHistory.scrollHeight;
            
            const loadingId = 'loading-' + Date.now();
            chatHistory.innerHTML += `<div id="${loadingId}" class="chat-bubble bot-bubble">Pensando...</div>`;
            chatHistory.scrollTop = chatHistory.scrollHeight;

            try {
                const response = await fetch('/oswa-bot', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ pregunta: pregunta }) });
                const data = await response.json();
                document.getElementById(loadingId).remove();
                chatHistory.innerHTML += `<div class="chat-bubble bot-bubble">${data.respuesta}</div>`;
            } catch (e) { 
                document.getElementById(loadingId).remove();
                chatHistory.innerHTML += `<div class="chat-bubble bot-bubble" style="color: #e74c3c;">Error de conexión.</div>`;
            }
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
        
        // CRUD PRODUCTOS
        async function eliminarProducto(id) {
            const confirm = await Swal.fire({ title: '¿Eliminar?', text: 'No se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí', cancelButtonText: 'Cancelar' });
            if (confirm.isConfirmed) {
                try {
                    const response = await fetch('{{ route('eliminar.producto') }}', { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ id: id }) });
                    const data = await response.json();
                    if (data.success) { showNotification('success', 'Eliminado'); setTimeout(() => location.reload(), 1500); }
                } catch (e) { showNotification('error', 'Error', true); }
            }
        }
        
        function editarProducto(id) {
            const producto = {!! $productos->toJson() !!};
            const prod = producto.find(p => p.id === id);
            if (!prod) return;
            document.getElementById('editId').value = prod.id;
            document.getElementById('editNombre').value = prod.nombre;
            document.getElementById('editCodigo').value = prod.codigo;
            document.getElementById('editMarca').value = prod.marca || '';
            document.getElementById('editCategoria').value = prod.categoria || '';
            document.getElementById('editPrecio').value = prod.precio;
            document.getElementById('editVencimiento').value = prod.fecha_vencimiento || '';
            new bootstrap.Modal(document.getElementById('editarProductoModal')).show();
        }

        document.getElementById('editarProductoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('{{ route('actualizar.producto') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: new FormData(this) });
                const data = await response.json();
                if (data.success) {
                    showNotification('success', 'Producto guardado');
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (e) { showNotification('error', 'Error', true); }
        });
        
        document.getElementById('nuevoProductoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('{{ route('guardar.producto') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: new FormData(this) });
                const data = await response.json();
                if (data.success) { showNotification('success', 'Producto guardado'); setTimeout(() => location.reload(), 1500); }
            } catch (e) { showNotification('error', 'Error', true); }
        });

        // TRANSFERENCIAS
        function abrirTransferencia(id, nombre, stock) {
            document.getElementById('transferProducto').value = nombre;
            document.getElementById('transferProductoId').value = id;
            document.getElementById('transferStock').value = stock;
            document.getElementById('transferCantidad').max = stock;
            document.getElementById('transferCantidad').value = 1;
            new bootstrap.Modal(document.getElementById('transferenciaModal')).show();
            if (typeof google !== 'undefined' && google.maps) initTransferMap();
        }

        document.getElementById('transferenciaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const cantidad = parseInt(document.getElementById('transferCantidad').value);
            const stockActual = parseInt(document.getElementById('transferStock').value);
            if (cantidad > stockActual) return showNotification('error', 'Cantidad mayor al stock', true);
            try {
                const response = await fetch('{{ route('transferir.producto') }}', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ producto_id: document.getElementById('transferProductoId').value, cantidad: cantidad, sucursal: document.getElementById('sucursalDestino').value, soporte_base64: document.getElementById('soporte_base64').value })
                });
                const data = await response.json();
                if (data.success) {
                    const pdfUrl = `{{ route('transferencia.pdf') }}?producto=${encodeURIComponent(data.producto)}&cantidad=${cantidad}&sucursal=${encodeURIComponent(document.getElementById('sucursalDestino').value)}&distancia=${data.distancia || 0}&costo=${data.costo_flete || 0}&fecha=${encodeURIComponent(data.fecha)}`;
                    Swal.fire({ title: 'Éxito', text: 'Transferencia lista', icon: 'success', html: `<a href="${pdfUrl}" target="_blank" class="btn btn-primary mt-3">Descargar PDF</a>` });
                }
            } catch (e) { showNotification('error', 'Error', true); }
        });

        function generarOrden(id, nombre) { window.location.href = '/orden-compra/' + id; }
        
        // REQUISICIONES
        function abrirRequisicion(productoId, nombre) {
            document.getElementById('requisicionProducto').value = nombre;
            document.getElementById('requisicionProductoId').value = productoId;
            document.getElementById('requisicionCantidad').value = '';
            new bootstrap.Modal(document.getElementById('requisicionModal')).show();
        }
        
        document.getElementById('requisicionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const formData = new FormData(this);
                const response = await fetch('{{ route('requisiciones.solicitar') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: new URLSearchParams(Object.fromEntries(formData))
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Solicitud enviada', text: 'El administrador revisará tu pedido', timer: 2000, showConfirmButton: false });
                    bootstrap.Modal.getInstance(document.getElementById('requisicionModal')).hide();
                }
            } catch (e) { showNotification('error', 'Error al enviar', true); }
        });
        
        async function aprobarRequisicion(id) {
            Swal.fire({ title: '¿Aprobar requisición?', text: 'Se descontará el stock del producto', icon: 'question', showCancelButton: true, confirmButtonText: 'Aprobar', cancelButtonText: 'Cancelar', confirmButtonColor: '#00b894' }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('{{ route('requisiciones.aprobar') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id: id })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Aprobada', text: data.message, timer: 2000, showConfirmButton: false });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        }
                    } catch (e) { showNotification('error', 'Error de conexión', true); }
                }
            });
        }
        
        async function rechazarRequisicion(id) {
            Swal.fire({ title: '¿Rechazar requisición?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Rechazar', cancelButtonText: 'Cancelar', confirmButtonColor: '#e74c3c' }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('{{ route('requisiciones.rechazar') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id: id })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Rechazada', text: data.message, timer: 2000, showConfirmButton: false });
                            setTimeout(() => location.reload(), 1500);
                        }
                    } catch (e) { showNotification('error', 'Error de conexión', true); }
                }
            });
        }
        
        // CAMARA UNIVERSAL ROBUSTA
        let camaraStream = null;
        let camaraTarget = null;
        const videoUniversal = document.getElementById('videoElementUniversal');
        const canvasUniversal = document.getElementById('canvasHiddenUniversal');

        function abrirCamara(tipo) {
            if (camaraStream) return;
            camaraTarget = tipo;
            navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: "environment", width: { ideal: 1280 }, height: { ideal: 720 } } 
            })
            .then(stream => {
                camaraStream = stream;
                videoUniversal.srcObject = stream;
                const modalEl = document.getElementById('modalCamaraUniversal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            })
            .catch(err => Swal.fire('Error', 'No se pudo acceder a la cámara: ' + err.message, 'error'));
        }

        document.getElementById('modalCamaraUniversal').addEventListener('hidden.bs.modal', function () {
            if (camaraStream) {
                camaraStream.getTracks().forEach(track => track.stop());
                camaraStream = null;
                videoUniversal.srcObject = null;
            }
            camaraTarget = null;
        });

        document.getElementById('btnCapturarUniversal').addEventListener('click', function() {
            if (!camaraStream || !camaraTarget) return;
            canvasUniversal.width = videoUniversal.videoWidth;
            canvasUniversal.height = videoUniversal.videoHeight;
            canvasUniversal.getContext('2d').drawImage(videoUniversal, 0, 0);
            const base64 = canvasUniversal.toDataURL('image/jpeg', 0.85);
            
            if (camaraTarget === 'producto') {
                document.getElementById('imagen_base64').value = base64;
                const preview = document.getElementById('previewCamaraProducto');
                preview.src = base64; preview.style.display = 'block';
            } else if (camaraTarget === 'soporte') {
                document.getElementById('soporte_base64').value = base64;
                const preview = document.getElementById('previewCamaraSoporte');
                preview.src = base64; preview.style.display = 'block';
            }
            bootstrap.Modal.getInstance(document.getElementById('modalCamaraUniversal')).hide();
        });
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

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="text-danger"><i class="bi bi-boxes fs-1"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Gestión de Stock</h5>
                                    <p class="text-secondary" style="font-size: 0.9rem;">Control exacto de unidades, cálculo de capital invertido e indicadores de alerta para productos con bajo inventario.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="text-warning"><i class="bi bi-clock-history fs-1"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Control de Vencimientos</h5>
                                    <p class="text-secondary" style="font-size: 0.9rem;">Monitoreo en tiempo real de lotes próximos a caducar para evitar pérdidas de mercancía y optimizar la rotación.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="text-info"><i class="bi bi-shield-lock-fill fs-1"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Auditoría Criptográfica</h5>
                                    <p class="text-secondary" style="font-size: 0.9rem;">Registro inmutable de movimientos protegidos con firma digital (SHA-256) para garantizar la transparencia del usuario.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="text-success"><i class="bi bi-file-earmark-pdf-fill fs-1"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Reportes Automatizados</h5>
                                    <p class="text-secondary" style="font-size: 0.9rem;">Generación instantánea de reportes en PDF listos para imprimir, firmar y entregar a gerencia.</p>
                                </div>
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

                            <!-- Slide 2: Stock -->
                            <div class="carousel-item h-100">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                    <i class="bi bi-boxes mb-3" style="font-size: 4rem; color: #E50914;"></i>
                                    <h3 class="fw-bold mb-3" style="color: var(--text-primary);">1. Control de Stock</h3>
                                    <p style="font-size: 1.1rem; max-width: 85%; color: var(--text-secondary);">Monitoreo en tiempo real de entradas y salidas. El sistema calcula automáticamente el capital invertido y genera alertas visuales cuando un producto alcanza su nivel mínimo de inventario.</p>
                                </div>
                            </div>

                            <!-- Slide 3: Vencimientos -->
                            <div class="carousel-item h-100">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                    <i class="bi bi-calendar-x mb-3" style="font-size: 4rem; color: #ffc107;"></i>
                                    <h3 class="fw-bold mb-3" style="color: var(--text-primary);">2. Gestión de Vencimientos</h3>
                                    <p style="font-size: 1.1rem; max-width: 85%; color: var(--text-secondary);">Seguimiento estricto de la vida útil de los productos. Algoritmos de alerta temprana notifican a los administradores sobre lotes próximos a caducar, evitando pérdidas económicas.</p>
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
    </script>
</body>
</html>