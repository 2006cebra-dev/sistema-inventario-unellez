<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OSWA Inv - Sistema de Inventario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0a;
            color: #e5e5e5;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar-landing {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            background: rgba(10,10,10,0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            z-index: 100;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .navbar-brand img {
            height: 36px;
            filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(229,9,20,0.3));
            transform: perspective(400px) rotateY(-5deg);
            transition: transform 0.4s;
        }

        .navbar-brand:hover img {
            transform: perspective(400px) rotateY(0deg) scale(1.05);
        }

        .navbar-brand span {
            font-weight: 800;
            font-size: 1.3rem;
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: rgbText 4s ease infinite;
            filter: drop-shadow(0 0 8px rgba(229,9,20,0.3));
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .navbar-links a {
            color: #a3a3a3;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-links a:hover {
            color: #fff;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-login {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.15);
            padding: 8px 22px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.3);
            color: #fff;
        }

        .btn-register-nav {
            background: #E50914;
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(229,9,20,0.3);
        }

        .btn-register-nav:hover {
            background: #b8070f;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(229,9,20,0.5);
            color: #fff;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 6rem 2rem 4rem;
            background: radial-gradient(ellipse at center, #141414 0%, #0a0a0a 70%);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(229,9,20,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero-content {
            text-align: center;
            max-width: 850px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease forwards;
        }

        .hero-logo {
            height: 100px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1) drop-shadow(0 0 20px rgba(255,255,255,0.15));
            animation: floatGlow 4s ease-in-out infinite;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(229,9,20,0.1);
            border: 1px solid rgba(229,9,20,0.2);
            border-radius: 20px;
            padding: 8px 22px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #E50914;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 2rem;
        }

        .hero-badge .badge-dot {
            width: 6px; height: 6px;
            background: #E50914;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1.05;
            margin-bottom: 1.2rem;
            letter-spacing: -2px;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, #E50914, #ff6b6b, #B20710);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: rgbText 4s ease infinite;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: #ccc;
            margin-bottom: 1rem;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .hero-subtitle em {
            color: #fff;
            font-style: normal;
            font-weight: 600;
        }

        .hero-description {
            font-size: 1.05rem;
            color: #888;
            margin-bottom: 2.5rem;
            line-height: 1.8;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #E50914;
            color: #fff;
            border: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 6px 25px rgba(229,9,20,0.4);
        }

        .btn-primary-custom:hover {
            background: #b8070f;
            transform: translateY(-2px);
            box-shadow: 0 10px 35px rgba(229,9,20,0.6);
            color: #fff;
        }

        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.06);
            color: #ccc;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
            color: #fff;
        }

        /* ===== HERO STATS ===== */
        .hero-stats {
            display: flex;
            gap: 3rem;
            justify-content: center;
            margin-top: 3.5rem;
            flex-wrap: wrap;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
        }

        .hero-stat-label {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .hero-stat-divider {
            width: 1px;
            background: #2a2a2a;
        }

        /* ===== FEATURES ===== */
        .features {
            padding: 5rem 2rem;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }

        .features-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .features-header h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #fff;
        }

        .features-header p {
            color: #999;
            font-size: 1.05rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            perspective: 1000px;
        }

        .feature-card {
            background: rgba(28,28,28,0.6);
            border: 1px solid #2a2a2a;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, border-color 0.4s ease;
            text-align: center;
            transform-style: preserve-3d;
        }

        .feature-card:hover {
            border-color: #E50914;
            transform: translateY(-10px) rotateX(2deg) rotateY(2deg);
            box-shadow: -10px 20px 30px rgba(0,0,0,0.8);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: rgba(229,9,20,0.12);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
            color: #E50914;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), text-shadow 0.4s, background 0.3s;
            transform: translateZ(0) scale(1);
            backface-visibility: hidden;
            will-change: transform;
        }

        .feature-card:hover .feature-icon {
            background: rgba(229,9,20,0.2);
            transform: translateZ(60px) scale(1.2);
            text-shadow: 0 15px 10px rgba(0,0,0,0.5);
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #fff;
        }

        .feature-card p {
            color: #999;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* ===== SECURITY SECTION ===== */
        .security-section {
            padding: 5rem 2rem;
            background: linear-gradient(180deg, transparent 0%, rgba(229,9,20,0.03) 50%, transparent 100%);
        }

        .security-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 4rem;
            flex-wrap: wrap;
        }

        .security-info {
            flex: 1;
            min-width: 280px;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0,184,148,0.1);
            border: 1px solid rgba(0,184,148,0.2);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #00b894;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.2rem;
        }

        .security-info h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }

        .security-info p {
            color: #888;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .security-features {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .security-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #bbb;
            font-size: 0.9rem;
            list-style: none;
        }

        .security-features li i {
            color: #00b894;
            font-size: 1.1rem;
        }

        .security-visual {
            flex: 1;
            min-width: 280px;
            display: flex;
            justify-content: center;
        }

        .security-shield {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(229,9,20,0.08) 0%, transparent 70%);
            border: 1px solid rgba(229,9,20,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: #E50914;
            animation: pulse 3s infinite;
            position: relative;
        }

        .security-shield::after {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 1px solid rgba(229,9,20,0.05);
            animation: pulse 3s infinite 0.5s;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            padding: 5rem 2rem;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .cta-section h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #fff;
        }

        .cta-section p {
            color: #888;
            font-size: 1.05rem;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        /* ===== FOOTER ===== */
        .footer {
            border-top: 1px solid #1a1a1a;
            padding: 3rem 5% 2rem;
        }

        .footer-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-brand img {
            height: 32px;
            filter: brightness(0) invert(1);
            margin-bottom: 1rem;
        }

        .footer-brand p {
            color: #666;
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .footer-col h4 {
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-col a {
            display: block;
            color: #666;
            font-size: 0.85rem;
            text-decoration: none;
            margin-bottom: 0.6rem;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #E50914;
        }

        .footer-bottom {
            max-width: 1100px;
            margin: 0 auto;
            padding-top: 1.5rem;
            border-top: 1px solid #1a1a1a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom p {
            color: #555;
            font-size: 0.8rem;
        }

        .footer-bottom strong {
            color: #E50914;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
        }

        .footer-social a {
            color: #555;
            font-size: 1.2rem;
            transition: color 0.2s;
        }

        .footer-social a:hover {
            color: #E50914;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes rgbText {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes floatGlow {
            0%, 100% { filter: brightness(0) invert(1) drop-shadow(0 0 20px rgba(255,255,255,0.15)); }
            50% { filter: brightness(0) invert(1) drop-shadow(0 0 40px rgba(229,9,20,0.3)); }
        }

        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .navbar-links, .navbar-actions { display: none; }
            .mobile-menu-toggle { display: block; }

            .hero-title { font-size: 2.8rem; letter-spacing: -1px; }
            .hero-subtitle { font-size: 1rem; }
            .hero-description { font-size: 0.9rem; }
            .hero-logo { height: 70px; }
            .hero-stats { gap: 1.5rem; }
            .hero-stat-value { font-size: 1.5rem; }
            .hero-stat-divider { display: none; }

            .btn-primary-custom, .btn-secondary-custom { padding: 12px 28px; font-size: 0.9rem; }
            .features { padding: 3rem 1.5rem; }
            .features-grid { grid-template-columns: 1fr; }
            .security-inner { flex-direction: column-reverse; gap: 2rem; }
            .security-shield { width: 140px; height: 140px; font-size: 3.5rem; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }

        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar-landing">
        <a href="/" class="navbar-brand">
            <span>OSWA Inv</span>
        </a>

        <div class="navbar-links">
            <a href="#inicio">Inicio</a>
            <a href="#modulos">Módulos</a>
            <a href="#seguridad">Seguridad</a>
        </div>

        <div class="navbar-actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/inventario') }}" class="btn-register-nav">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-register-nav">
                            <i class="bi bi-person-plus"></i> Registrarse
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="bi bi-list"></i>
        </button>
    </nav>

    <!-- HERO -->
    <section class="hero" id="inicio">
        <div class="hero-content">
            <div class="hero-logo" style="height:80px;"></div>

            <div class="hero-badge">
                <span class="badge-dot"></span>
                Sistema de Gestión de Inventario
            </div>

            <h1 class="hero-title">
                <span class="highlight">OSWA</span> Inv
            </h1>

            <p class="hero-subtitle">
                Control total de inventario para tu empresa
            </p>

            <p class="hero-description">
                Gestión inteligente de productos, proveedores, requisiciones y más.
                Auditoría en tiempo real con seguridad criptográfica.
            </p>

            <div class="hero-actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/inventario') }}" class="btn-primary-custom">
                            <i class="bi bi-speedometer2"></i> Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary-custom">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-secondary-custom">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value" id="statProductos">0</div>
                    <div class="hero-stat-label">Productos</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-value" id="statProveedores">0</div>
                    <div class="hero-stat-label">Proveedores</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-value" id="statUsuarios">0</div>
                    <div class="hero-stat-label">Usuarios</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-value" id="statMisiones">0</div>
                    <div class="hero-stat-label">Misiones</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="features" id="modulos">
        <div class="features-header">
            <h2>¿Qué puedes hacer?</h2>
            <p>Herramientas diseñadas para la gestión eficiente de tu inventario</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-box-seam-fill"></i></div>
                <h3>Catálogo</h3>
                <p>Administra productos con códigos de barra, stock, precios, alertas de inventario bajo y edición masiva.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-upc-scan"></i></div>
                <h3>Escáner</h3>
                <p>Escanea códigos de barra y obtén información automática desde OpenFoodFacts. Ideal para recepción de mercancía.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-truck"></i></div>
                <h3>Proveedores</h3>
                <p>Gestiona tus proveedores, datos de contacto y controla el abastecimiento de productos por cada uno.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-file-earmark-text"></i></div>
                <h3>Requisiciones</h3>
                <p>Solicita productos de forma interna con flujo de aprobación integrado. Aprobación, rechazo y seguimiento.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-flag-fill"></i></div>
                <h3>Misiones</h3>
                <p>Asigna y da seguimiento a tareas del equipo. Sistema de experiencia (XP), niveles y fechas de vencimiento.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <h3>Reportes</h3>
                <p>Visualiza tendencias de salidas, distribución por categorías y exporta reportes PDF detallados del inventario.</p>
            </div>
        </div>
    </section>

    <!-- SECURITY -->
    <section class="security-section" id="seguridad">
        <div class="security-inner">
            <div class="security-info">
                <div class="security-badge">
                    <i class="bi bi-shield-fill-check"></i> Seguridad
                </div>
                <h2>Auditoría y seguridad integradas</h2>
                <p>
                    Cada movimiento en el inventario queda registrado con firma criptográfica.
                    Sabrás exactamente quién, cuándo y qué se modificó.
                </p>
                <ul class="security-features">
                    <li><i class="bi bi-check-circle-fill"></i> Trazabilidad completa de movimientos</li>
                    <li><i class="bi bi-check-circle-fill"></i> Hash criptográfico por operación</li>
                    <li><i class="bi bi-check-circle-fill"></i> Bitácora de accesos al sistema</li>
                    <li><i class="bi bi-check-circle-fill"></i> Roles y permisos (Admin / Empleado)</li>
                    <li><i class="bi bi-check-circle-fill"></i> Respaldos automáticos de base de datos</li>
                </ul>
            </div>
            <div class="security-visual">
                <div class="security-shield">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2>¿Listo para empezar?</h2>
        <p>Únete al sistema de gestión de inventario. Controla, audita y optimiza tus recursos.</p>
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/inventario') }}" class="btn-primary-custom">
                    <i class="bi bi-speedometer2"></i> Ir al Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-primary-custom">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </a>
            @endauth
        @endif
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <p>OSWA Inv es un sistema de gestión de inventario diseñado para optimizar, asegurar y registrar cada movimiento en el almacén.</p>
            </div>
            <div class="footer-col">
                <h4>Plataforma</h4>
                <a href="{{ route('login') }}">Iniciar Sesión</a>
                <a href="#modulos">Módulos</a>
                <a href="#seguridad">Seguridad</a>
            </div>
            <div class="footer-col">
                <h4>Recursos</h4>
                <a href="#">Documentación</a>
                <a href="#">Soporte</a>
                <a href="#">Reportar Error</a>
            </div>

        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} <strong>OSWA Inv</strong>. Todos los derechos reservados.</p>
            <div class="footer-social">
                <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                <a href="#" title="Correo"><i class="bi bi-envelope-fill"></i></a>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            // Simple mobile menu toggle
        }

        // Animated counters
        document.addEventListener('DOMContentLoaded', function() {
            const stats = {
                statProductos: {{ $totalProductos ?? 42 }},
                statProveedores: {{ $totalProveedores ?? 8 }},
                statUsuarios: {{ $totalUsuarios ?? 15 }},
                statMisiones: {{ $totalMisiones ?? 23 }},
            };

            Object.entries(stats).forEach(([id, target]) => {
                const el = document.getElementById(id);
                if (!el) return;
                let current = 0;
                const step = Math.max(1, Math.floor(target / 40));
                const interval = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }
                    el.textContent = current;
                }, 30);
            });
        });
    </script>
</body>
</html>
