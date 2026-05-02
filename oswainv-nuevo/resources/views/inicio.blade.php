<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSWA Inv — Sistema de Gestión de Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
            --topbar-height: 68px;
        }
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5; }

        /* Navbar Glassmorphism */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height);
            background: linear-gradient(to bottom, rgba(18,18,18,0.90) 0%, rgba(18,18,18,0) 100%) !important;
            backdrop-filter: blur(10px);
            border: none !important;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999;
        }
        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .topbar-logo { white-space: nowrap; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .topbar-logo .logo-text {
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
        }
        @keyframes rgbText { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .logo-nav-unellez {
            height: 35px; filter: brightness(0) invert(1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; margin-right: 10px;
        }
        .logo-nav-unellez:hover {
            transform: scale(1.2);
            filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
        }
        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; }
        .topbar-nav a:hover { color: #ffffff; }
        .topbar-right { display: flex; align-items: center; gap: 1rem; }

        .btn-netflix-red {
            background: var(--n-red) !important; color: #fff !important; border: none !important;
            font-weight: 600; padding: 10px 24px; border-radius: 4px;
            box-shadow: 0 4px 15px rgba(229,9,20,0.4); transition: all 0.3s ease;
            text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-netflix-red:hover {
            background: #b8070f !important; transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(229,9,20,0.6); color: #fff !important;
        }

        @keyframes textGlowRed {
            0% { text-shadow: 0 0 5px rgba(229, 9, 20, 0.2); }
            50% { text-shadow: 0 0 15px rgba(229, 9, 20, 0.7); }
            100% { text-shadow: 0 0 5px rgba(229, 9, 20, 0.2); }
        }
        .text-animated-red {
            color: #E50914 !important;
            animation: textGlowRed 2.5s infinite ease-in-out;
            font-weight: 900;
        }

        .subtitle-premium {
            font-family: 'Inter', 'Roboto', sans-serif !important;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 5px;
            color: #E50914 !important;
            text-transform: uppercase;
        }

        .btn-dark-glass {
            background-color: rgba(25, 25, 25, 0.6) !important;
            color: #e5e5e5 !important;
            border: 1px solid #333 !important;
            padding: 12px 28px;
            border-radius: 4px;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-dark-glass:hover {
            background-color: rgba(40, 40, 40, 0.9) !important;
            color: #fff !important;
            border-color: #E50914 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        @media (max-width: 768px) {
            .topbar-nav { display: none !important; }
            .topbar-logo .logo-text { display: none !important; }
        }

        /* Hero Banner */
        .hero-banner {
            position: relative; min-height: 100vh;
            display: flex; align-items: center; justify-content: center; text-align: center;
            background: linear-gradient(177deg, rgba(18,18,18,0.97) 0%, rgba(18,18,18,0.85) 40%, transparent 80%),
                        url('{{ asset('img/refrigeracion_centros_datos.jpg') }}') center/cover no-repeat;
            overflow: hidden;
        }
        .hero-banner::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 300px;
            background: linear-gradient(to top, var(--bg-main) 0%, transparent 100%);
            z-index: 1;
        }
        .hero-banner .container { position: relative; z-index: 2; }

        /* Scroll Reveal */
        .reveal { opacity: 0; transform: translateY(60px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* Stat Cards */
        .stat-card {
            background: var(--bg-card) !important;
            border: 1px solid var(--n-border) !important;
            border-radius: 15px !important;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative; overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-8px) scale(1.05);
            border-color: #E50914 !important;
            box-shadow: 0 15px 30px rgba(0,0,0,0.6);
        }

        /* Sección Características */
        .section-title {
            font-size: 2rem; font-weight: 700; text-align: center; margin-bottom: 0.5rem;
        }
        .section-subtitle {
            text-align: center; color: var(--text-secondary); font-family: 'Consolas', monospace;
            font-size: 0.9rem; margin-bottom: 1rem;
        }

        /* Sección Estadísticas */
        .stats-strip {
            background: var(--bg-card); border: 1px solid var(--n-border);
            border-radius: 15px; padding: 2rem;
        }
        .stat-number {
            font-family: 'Consolas', monospace; font-size: 2.5rem; font-weight: 800;
            color: var(--n-red);
        }
        .stat-label { color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }

        /* Sección Contacto */
        .contact-card {
            background: var(--bg-card); border: 1px solid var(--n-border);
            border-radius: 15px; padding: 2.5rem;
        }

        /* Footer */
        .landing-footer {
            text-align: center; padding: 2rem 4%; border-top: 1px solid var(--n-border);
            color: var(--text-secondary); font-size: 0.85rem;
        }
        .landing-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .landing-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; }

        /* =========================================
           ADAPTACIÓN PARA MÓVILES (Pantallas < 768px)
           ========================================= */
        @media (max-width: 768px) {
            
            /* 1. Reparación del Chatbot */
            .oswa-bot-window {
                width: 90vw !important;
                right: 5vw !important;
                bottom: 100px !important;
                height: 65vh !important;
            }

            /* 2. Reparación del Hero Banner (Letras gigantes) */
            .hero-banner h1.display-3 {
                font-size: 2.2rem !important;
            }
            
            .subtitle-hero-clear {
                font-size: 1rem !important;
                line-height: 1.4 !important;
                padding: 0 15px !important;
            }

            /* Achicar el logo de la UNELLEZ en móvil */
            .hero-banner img.logo-white-glow {
                height: 35px !important; 
            }

            /* 3. Reparación de Botones del Navbar y Hero (Que no se salgan de la pantalla) */
            .hero-banner .d-flex.gap-3 {
                flex-direction: column !important;
                gap: 15px !important;
                width: 100%;
                padding: 0 20px;
            }
            
            .hero-banner .d-flex.gap-3 .btn {
                width: 100% !important;
            }

            /* Si los botones de "Iniciar Sesión" y "Registrar" del Navbar se desbordan: */
            .navbar .d-flex.gap-3 {
                flex-direction: column !important;
                width: 100% !important;
                margin-top: 15px;
            }
            .navbar .d-flex.gap-3 .btn {
                width: 100% !important;
                text-align: center;
            }
        }

        /* Chatbot OSWA-Bot */
        .bot-fab { position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; border-radius: 50%; background: #E50914; color: white; border: none; font-size: 1.8rem; box-shadow: 0 8px 25px rgba(229,9,20,0.5); z-index: 9999; cursor: pointer; transition: transform 0.3s; display: flex; align-items: center; justify-content: center; }
        .bot-fab:hover { transform: scale(1.1); }
        .floating-bot-window { position: fixed; bottom: 90px; right: 20px; width: 340px; height: 450px; background: #1c1c1c; border: 1px solid #2b2b2b; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.6); z-index: 9998; display: flex; flex-direction: column; opacity: 0; pointer-events: none; transform: translateY(20px); transition: all 0.3s ease; overflow: hidden; }
        .floating-bot-window.show { opacity: 1; pointer-events: all; transform: translateY(0); }
        .bot-header { background: #141414; padding: 15px; color: white; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2b2b2b; }
        .bot-header button { background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; }
        .bot-chat-history { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; background: #121212; }
        .bot-chat-history::-webkit-scrollbar { width: 6px; }
        .bot-chat-history::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }
        .chat-bubble { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 0.85rem; line-height: 1.4; animation: fadeIn 0.3s ease; }
        .user-bubble { align-self: flex-end; background: #E50914; color: white; border-bottom-right-radius: 4px; }
        .bot-bubble { align-self: flex-start; background: #2b2b2b; color: white; border-bottom-left-radius: 4px; }
        .oswa-quick-replies-container { display: flex; gap: 8px; padding: 10px 15px; overflow-x: auto; white-space: nowrap; background: transparent; width: 100%; box-sizing: border-box; }
        .oswa-quick-replies-container::-webkit-scrollbar { height: 5px; }
        .oswa-quick-replies-container::-webkit-scrollbar-track { background: #1c1c1c; border-radius: 10px; }
        .oswa-quick-replies-container::-webkit-scrollbar-thumb { background: #E50914; border-radius: 10px; }
        .bot-chip { flex-shrink: 0; background: transparent; border: 1px solid #E50914; color: #E50914; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease-in-out; white-space: nowrap; }
        .bot-chip:hover { background: #E50914; color: #ffffff; }
        .bot-input-area { padding: 10px; background: #1c1c1c; display: flex; gap: 8px; }
        .bot-input-area input { flex: 1; padding: 10px 15px; background: #2a2a2a; border: none; border-radius: 20px; color: #e5e5e5; outline: none; font-size: 0.85rem; }
        .bot-input-area button { width: 40px; height: 40px; border-radius: 50%; background: #E50914; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) {
            .floating-bot-window { width: 90vw !important; right: 5vw !important; bottom: 100px !important; height: 65vh !important; }
        }
    </style>
</head>
<body>

    <nav class="topbar">
        <div class="topbar-left">
            <img src="{{ asset('img/logo-unellez.png') }}" class="logo-nav-unellez" alt="Logo">
            <span class="fw-bold" style="color: #E50914 !important; font-size: 1.25rem;">OSWA Inv</span>
        </div>
        <div class="topbar-nav">
            <a href="#inicio">Inicio</a>
            <a href="#caracteristicas">Características</a>
            <a href="#estadisticas">Estadísticas</a>
            <a href="#contacto">Contacto</a>
        </div>
        <div class="topbar-right">
            <div class="d-flex gap-3 align-items-center">
                <a href="{{ route('register') }}" class="btn btn-outline-light fw-bold px-4 py-2" style="border-radius: 4px; transition: all 0.3s ease;">
                    Registrarse
                </a>
                <a href="{{ route('login') }}" class="btn fw-bold px-4 py-2" style="background-color: #E50914; color: white; border-radius: 4px; border: none; transition: transform 0.2s ease;">
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section id="inicio" class="hero-banner">
        <div class="container reveal">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="me-3" style="height: 45px; filter: drop-shadow(0px 0px 8px rgba(229, 9, 20, 0.3));">
                <span class="subtitle-premium">Sistema de Inventario</span>
            </div>
            <h1 class="display-3 fw-bold text-white mb-4">Control de Inventario <span style="color: #E50914;">Nivel Enterprise</span></h1>
            <p class="lead text-secondary mb-5" style="font-family: 'Consolas', monospace; font-size: 1rem; max-width: 600px; margin: 0 auto;">Auditoría criptográfica, gestión en tiempo real y predicción inteligente de stock. Diseñado para ingeniería.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('login') }}" class="btn-netflix-red" style="padding: 14px 36px; font-size: 1.05rem;"><i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema</a>
                <a href="#caracteristicas" class="btn-dark-glass"><i class="bi bi-chevron-down me-1"></i>Descubrir Más</a>
            </div>
        </div>
    </section>

    <!-- Características -->
    <section id="caracteristicas" class="py-5">
        <div class="container">
            <div class="reveal">
                <h2 class="section-title text-white">Módulos del Sistema</h2>
                <p class="section-subtitle">Cada componente diseñado para control total y precisión.</p>
            </div>
            <div class="row mt-5 g-4">
                <div class="col-md-4 reveal">
                    <div class="card stat-card text-center h-100">
                        <i class="bi bi-box-seam text-danger display-4 mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">Catálogo Dinámico</h4>
                        <p class="text-secondary" style="font-family: 'Consolas', monospace; font-size: 0.85rem;">Control absoluto de entradas, salidas y stock en tiempo real con semáforo de alertas.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="card stat-card text-center h-100">
                        <i class="bi bi-shield-lock-fill text-info display-4 mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">Auditoría Criptográfica</h4>
                        <p class="text-secondary" style="font-family: 'Consolas', monospace; font-size: 0.85rem;">Registro inmutable de acciones con sellado SHA-256 para transparencia y trazabilidad.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="card stat-card text-center h-100">
                        <i class="bi bi-buildings-fill text-warning display-4 mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">Gestión de Proveedores</h4>
                        <p class="text-secondary" style="font-family: 'Consolas', monospace; font-size: 0.85rem;">Directorio ERP con vinculación directa a productos y botón de abastecimiento rápido.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="card stat-card text-center h-100">
                        <i class="bi bi-graph-up-arrow text-success display-4 mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">Predicción de Stock</h4>
                        <p class="text-secondary" style="font-family: 'Consolas', monospace; font-size: 0.85rem;">Cálculo inteligente de días estimados restantes basado en el histórico de consumo mensual.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="card stat-card text-center h-100">
                        <i class="bi bi-file-earmark-pdf-fill text-danger display-4 mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">Reportes y Respaldo</h4>
                        <p class="text-secondary" style="font-family: 'Consolas', monospace; font-size: 0.85rem;">Exportación a PDF y respaldo completo de base de datos con un solo clic.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="card stat-card text-center h-100">
                        <i class="bi bi-people-fill text-primary display-4 mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">Gestión de Usuarios</h4>
                        <p class="text-secondary" style="font-family: 'Consolas', monospace; font-size: 0.85rem;">Roles de administrador y empleado con control de acceso y registro de auditoría.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas -->
    <section id="estadisticas" class="py-5">
        <div class="container">
            <div class="reveal">
                <h2 class="section-title text-white">En Números</h2>
                <p class="section-subtitle">Datos que respaldan la eficiencia del sistema.</p>
            </div>
            <div class="row mt-5 g-4">
                <div class="col-md-3 col-6 reveal">
                    <div class="stats-strip text-center">
                        <div class="stat-number">5+</div>
                        <div class="stat-label">Módulos Activos</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 reveal">
                    <div class="stats-strip text-center">
                        <div class="stat-number">SHA-256</div>
                        <div class="stat-label">Encriptación</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 reveal">
                    <div class="stats-strip text-center">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Disponibilidad</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 reveal">
                    <div class="stats-strip text-center">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Código Propio</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 reveal">
                    <div class="contact-card text-center">
                        <i class="bi bi-envelope-paper-heart display-4 text-danger mb-3"></i>
                        <h2 class="text-white fw-bold mb-3">¿Interesado en el Proyecto?</h2>
                        <p class="text-secondary mb-4" style="font-family: 'Consolas', monospace; font-size: 0.9rem;">OSWA Inv fue desarrollado por estudiantes de Ingeniería en Informática de la UNELLEZ para optimizar la gestión de inventario con tecnología moderna.</p>
                        <div class="d-flex justify-content-center gap-4 flex-wrap">
                            <div class="d-flex align-items-center gap-2 text-secondary">
                                <i class="bi bi-mortarboard-fill text-danger"></i>
                                <span style="font-size: 0.85rem;">UNELLEZ</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-secondary">
                                <i class="bi bi-laptop-fill text-danger"></i>
                                <span style="font-size: 0.85rem;">Ing. Informática</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-secondary">
                                <i class="bi bi-bookmark-star-fill text-danger"></i>
                                <span style="font-size: 0.85rem;">V Semestre</span>
                            </div>
                        </div>
                        <div class="mt-5 d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                            <a href="{{ route('login') }}" class="btn fw-bold px-4 py-2" style="background-color: #E50914; color: white; border-radius: 6px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                <i class="bi bi-rocket-takeoff me-2"></i> Probar Sistema
                            </a>
                            <a href="https://wa.me/584122266083" target="_blank" class="btn fw-bold px-4 py-2" style="background-color: #25D366; color: white; border-radius: 6px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                <i class="bi bi-whatsapp me-2"></i> Contactar a Carlos
                            </a>
                            <a href="https://wa.me/584145207044" target="_blank" class="btn fw-bold px-4 py-2" style="background-color: #25D366; color: white; border-radius: 6px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                <i class="bi bi-whatsapp me-2"></i> Contactar a Yorgelis
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="mb-1">&copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.</div>
        <div>Desarrollado con <i class="bi bi-code-slash" style="color:#0d6efd;"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span></div>
        <div class="mt-2 d-flex align-items-center justify-content-center" style="font-size: 0.75rem; opacity: 0.8;">
            <span>Ingeniería en Informática — V Semestre |</span>
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 18px; margin-left: 8px; margin-right: 4px; filter: brightness(0) invert(1);">
            <strong style="letter-spacing: 0.5px;">UNELLEZ</strong>
        </div>
    </footer>

    <!-- OSWA-Bot Chatbot -->
    <button class="bot-fab" onclick="toggleBotWindow()"><i class="bi bi-robot"></i></button>
    <div class="floating-bot-window" id="botWindow">
        <div class="bot-header"><span><i class="bi bi-robot me-2"></i> OSWA-Bot IA</span><button onclick="toggleBotWindow()"><i class="bi bi-x-lg"></i></button></div>
        <div class="bot-chat-history" id="botChatHistory">
            <div class="chat-bubble bot-bubble">¡Epa! Soy el asistente virtual de OSWA Inv. ¿Tienes alguna duda sobre el sistema?</div>
        </div>
        <div class="oswa-quick-replies-container">
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('🤔 ¿Qué es OSWA Inv?')">🤔 ¿Qué es OSWA Inv?</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('✨ Características')">✨ Características</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('🔐 ¿Es seguro?')">🔐 ¿Es seguro?</button>
            <button type="button" class="bot-chip" onclick="enviarOpcionRapida('📞 Contactar Creadores')">📞 Contactar Creadores</button>
        </div>
        <div class="bot-input-area">
            <input type="text" id="botInput" placeholder="Pregúntame algo..." onkeypress="if(event.key==='Enter') enviarBot()">
            <button onclick="enviarBot()"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { entry.target.classList.add('active'); }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });

        // Chatbot functions
        function toggleBotWindow() {
            document.getElementById('botWindow').classList.toggle('show');
        }

        function enviarOpcionRapida(texto) {
            const inputBot = document.getElementById('botInput');
            if (inputBot) {
                inputBot.value = texto;
                enviarBot();
            }
        }

        function enviarBot() {
            const input = document.getElementById('botInput');
            const pregunta = input.value.trim();
            if (!pregunta) return;
            const chatHistory = document.getElementById('botChatHistory');
            chatHistory.innerHTML += `<div class="chat-bubble user-bubble">${pregunta}</div>`;
            input.value = '';
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // FAQ Interception
            if (pregunta.includes('¿Qué es OSWA Inv?')) {
                setTimeout(() => {
                    const html = '<b>OSWA Inv</b> es un sistema de control de inventario nivel Enterprise, diseñado por estudiantes de ingeniería para ofrecer gestión en tiempo real, predicción de stock y máxima seguridad. 🚀';
                    chatHistory.innerHTML += `<div class="chat-bubble bot-bubble">${html}</div>`;
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }, 500);
                return;
            } else if (pregunta.includes('Características')) {
                setTimeout(() => {
                    const html = 'Nuestras características principales incluyen:<br><br>📦 Gestión de catálogo en tiempo real.<br>🤝 Administración de proveedores.<br>📊 Reportes automatizados de stock.<br>⚡ Interfaz ultrarrápida y modo oscuro.';
                    chatHistory.innerHTML += `<div class="chat-bubble bot-bubble">${html}</div>`;
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }, 500);
                return;
            } else if (pregunta.includes('¿Es seguro?')) {
                setTimeout(() => {
                    const html = '¡Totalmente! 🔐 Contamos con auditoría criptográfica, protección de rutas y autenticación robusta para garantizar que la data de tu inventario nunca sea vulnerada.';
                    chatHistory.innerHTML += `<div class="chat-bubble bot-bubble">${html}</div>`;
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }, 500);
                return;
            } else if (pregunta.includes('Contactar Creadores')) {
                setTimeout(() => {
                    const numeroCarlos = "584122266083";
                    const mensajeBase = "Hola Carlos, vengo de la página de inicio de OSWA Inv y me interesa el sistema.";
                    const enlaceCarlos = `https://wa.me/${numeroCarlos}?text=${encodeURIComponent(mensajeBase)}`;
                    const html = `¿Quieres implementar OSWA Inv en tu negocio o tienes dudas técnicas? Escríbele directamente a uno de sus desarrolladores:<br><br>
                    <a href="${enlaceCarlos}" target="_blank" style="display:flex; align-items:center; justify-content:center; gap:8px; padding:10px 15px; background:#25D366; color:#fff; text-decoration:none; border-radius:4px; font-size:0.9rem; font-weight:bold; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="bi bi-whatsapp"></i> Hablar con Carlos
                    </a>`;
                    chatHistory.innerHTML += `<div class="chat-bubble bot-bubble">${html}</div>`;
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }, 500);
                return;
            }

            // Default response for unmatched queries
            setTimeout(() => {
                const html = 'Interesante pregunta. Para información más detallada, te sugiero explorar las secciones de la página o <a href="{{ route("login") }}" style="color:#E50914;">ingresar al sistema</a> para descubrir todas las funciones.';
                chatHistory.innerHTML += `<div class="chat-bubble bot-bubble">${html}</div>`;
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }, 500);
        }
    </script>
</body>
</html>
