<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #E50914;
            --bg-main: #050505;
            --bg-card: #121212;
            --text-main: #ffffff;
            --text-bright-muted: #d4d4d4;
        }

        body { background-color: var(--bg-main); color: var(--text-main); font-family: 'Inter', sans-serif; overflow-x: hidden; scroll-behavior: smooth; }

        .text-oswa-muted { color: var(--text-bright-muted) !important; line-height: 1.6; }

        /* NAVBAR INICIAL (Transparente) */
        .navbar-oswa {
            background: transparent;
            border-bottom: 1px solid transparent;
            transition: all 0.4s ease-in-out;
            padding: 20px 0;
        }

        /* ESTADO AL BAJAR EL SCROLL (Cristal) */
        .navbar-oswa.scrolled {
            background: rgba(5, 5, 5, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 0;
        }
        .btn-outline-oswa { color: #fff; border: 1px solid var(--accent); transition: 0.3s; }
        .btn-outline-oswa:hover { background: var(--accent); color: #fff; box-shadow: 0 0 15px rgba(229, 9, 20, 0.5); }
        .btn-oswa { background: var(--accent); color: #fff; border: none; transition: 0.3s; }
        .btn-oswa:hover { background: #b20710; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4); color: #fff; }

        /* HERO SECTION CON FONDO ANIMADO */
        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background-color: #000;
        }

        .hero-bg-container {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
        }

        .hero-bg-image {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            animation: kenBurns 20s infinite alternate ease-in-out;
            will-change: transform;
        }

        .hero-bg-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(
                to bottom,
                rgba(5, 5, 5, 0.7) 0%,
                rgba(5, 5, 5, 0.4) 50%,
                rgba(5, 5, 5, 0.9) 100%
            );
            z-index: 2;
        }

        /* REFINAMIENTO DE TEXTO HERO */
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 4.2rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -2px;
            color: #fff;
            text-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }

        .hero-accent {
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 4px;
            font-size: 0.9em;
            display: block;
            margin-top: 10px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 400;
            color: #ddd;
            max-width: 650px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.9);
            line-height: 1.7;
        }

        @keyframes kenBurns {
            0% {
                transform: scale(1) translate(0, 0);
            }
            100% {
                transform: scale(1.15) translate(-1%, -1%);
            }
        }

        /* ANIMACIONES */
        .reveal { opacity: 0; transform: translateY(60px); transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }
        .reveal-left { transform: translateX(-60px); }
        .reveal-right { transform: translateX(60px); }
        .reveal-left.active, .reveal-right.active { transform: translateX(0); }

        /* STAGGERED REVEAL */
        .reveal-stagger { opacity: 0; transform: translateY(40px) scale(0.95); transition: all 0.6s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .reveal-stagger.active { opacity: 1; transform: translateY(0) scale(1); }

        /* TECH BADGE 3D */
        .tech-badge { display: flex; align-items: center; gap: 12px; padding: 14px 24px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, border-color 0.4s ease; transform-style: preserve-3d; cursor: default; }
        .tech-badge:hover { transform: translateY(-6px) rotateX(3deg) rotateY(3deg); border-color: rgba(229,9,20,0.3); box-shadow: -8px 15px 25px rgba(0,0,0,0.6); }
        .tech-badge i { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); transform: translateZ(0) scale(1); backface-visibility: hidden; will-change: transform; }
        .tech-badge:hover i { transform: translateZ(40px) scale(1.15); }

        /* CARDS */
        .feature-card:hover { transform: translateY(-10px) rotateX(2deg) rotateY(2deg); border-color: rgba(229, 9, 20, 0.5); box-shadow: -10px 20px 30px rgba(0,0,0,0.8); }
        .feature-icon { font-size: 2rem; color: var(--accent); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), text-shadow 0.4s; transform: translateZ(0) scale(1); backface-visibility: hidden; will-change: transform; display: inline-block; }
        .feature-card:hover .feature-icon { transform: translateZ(60px) scale(1.2); text-shadow: 0 15px 10px rgba(0,0,0,0.5); }
        .feature-card h5 { font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem; color: #fff; letter-spacing: -0.3px; }
        .feature-card p { color: #ddd !important; font-size: 0.95rem; line-height: 1.8 !important; }

        /* ACCORDION FAQ OSCURO */
        .accordion-item { background-color: var(--bg-card); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px !important; margin-bottom: 1rem; overflow: hidden; }
        .accordion-button { background-color: var(--bg-card); color: white; font-weight: bold; box-shadow: none !important; }
        .accordion-button:not(.collapsed) { background-color: rgba(229, 9, 20, 0.1); color: var(--accent); }
        .accordion-button::after { filter: invert(1); }
        .accordion-body { color: var(--text-bright-muted); }

        /* CHATBOT */
        .btn-chatbot { position: fixed; bottom: 30px; right: 30px; background: var(--accent); color: white; border: none; border-radius: 50%; width: 60px; height: 60px; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 20px rgba(229, 9, 20, 0.5); cursor: pointer; z-index: 1000; transition: 0.3s; }
        .btn-chatbot:hover { transform: scale(1.1); background: #b20710; }
        .modal-content { background-color: var(--bg-card); border: 1px solid #333; color: white; }
        .modal-header { border-bottom: 1px solid #333; }
        .modal-footer { border-top: 1px solid #333; }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
        #chat-body { max-height: 350px; overflow-y: auto; scrollbar-width: thin; }
    </style>
</head>
<body>

    <!-- NAVBAR COMPLETO -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-oswa fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="fw-bold" style="letter-spacing: 2px;">OSWA <span class="text-danger">INV</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link text-white fw-semibold" href="#descubre">Características</a></li>
                    <li class="nav-item"><a class="nav-link text-white fw-semibold" href="#seguridad">Seguridad</a></li>
                    <li class="nav-item"><a class="nav-link text-white fw-semibold" href="#faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link text-white fw-semibold" href="#contacto">Soporte</a></li>
                </ul>
                <div class="d-flex gap-3 mt-3 mt-lg-0">
                    <a href="{{ route('login') }}" class="btn btn-outline-oswa px-4 rounded-pill">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-oswa px-4 rounded-pill">Registrarse</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION CON FONDO ANIMADO -->
    <section class="hero-section text-center">
        <!-- Contenedor del fondo con animación -->
        <div class="hero-bg-container">
            <div class="hero-bg-image" style="background-image: url('{{ asset('img/hero-bg.jpg') }}');"></div>
            <div class="hero-bg-overlay"></div>
        </div>

        <div class="container position-relative reveal active" style="z-index: 3;">
            <!-- Título más refinado -->
            <h1 class="hero-title mb-4">
                El Control Total de tu <br> 
                <span class="hero-accent">Inventario</span>
            </h1>
            
            <!-- Subtítulo con mejor espaciado -->
            <p class="hero-subtitle mb-5 mx-auto">
                Un sistema de gestión exclusivo, seguro y diseñado para maximizar la eficiencia de tus operaciones. Tecnología de punta al alcance de tu empresa.
            </p>
            <div class="d-flex justify-content-center gap-4">
                <a href="{{ route('login') }}" class="btn btn-oswa btn-lg px-5 py-3 rounded-pill fw-bold">Comenzar Ahora</a>
                <a href="#descubre" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold">Saber Más</a>
            </div>
        </div>
    </section>

    <!-- 4 CARACTERÍSTICAS -->
    <section id="descubre" class="py-5" style="margin-top: 2rem;">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <h2 class="display-5 fw-bold text-white">Ingeniería Aplicada al Inventario</h2>
                <p class="text-oswa-muted fs-5" style="color: #bbb !important;">Diseñado para optimizar, asegurar y registrar cada movimiento en el almacén.</p>
            </div>

            <div class="row g-4 mt-4" style="max-width: 1200px; margin: 0 auto; perspective: 1000px;">
    
    <!-- TARJETA 1: AUDITORÍA -->
    <div class="col-md-6 reveal-stagger" style="transition-delay:0.05s;">
        <div class="feature-card p-4">
            <div class="d-flex gap-3">
                <div><i class="bi bi-shield-lock feature-icon text-info"></i></div>
                <div>
                    <h5>Auditoría Criptográfica (SHA-256)</h5>
                    <p>
                        <strong class="text-white">¿Qué hace?</strong> Blinda el historial de transacciones.<br>
                        <strong class="text-white" style="display:inline-block;margin-top:6px;">¿Cómo funciona?</strong> Cada vez que un producto entra o sale, el sistema genera una firma digital única e inmutable. Si la base de datos es manipulada manualmente para robar o alterar datos, el sistema rompe la cadena y marca el registro como "ALTERADO".
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA 2: ESCÁNER -->
    <div class="col-md-6 reveal-stagger" style="transition-delay:0.1s;">
        <div class="feature-card p-4">
            <div class="d-flex gap-3">
                <div><i class="bi bi-upc-scan feature-icon text-danger"></i></div>
                <div>
                    <h5>Módulo de Despacho (Escáner)</h5>
                    <p>
                        <strong class="text-white">¿Qué hace?</strong> Agiliza la búsqueda y el despacho de productos.<br>
                        <strong class="text-white" style="display:inline-block;margin-top:6px;">¿Cómo funciona?</strong> Utiliza la cámara de cualquier celular, tablet o laptop para leer códigos de barras en tiempo real mediante tecnología HTML5-QRCode, sin necesidad de instalar aplicaciones externas ni comprar pistolas lectoras costosas.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA 3: CATÁLOGO Y SEMÁFORO -->
    <div class="col-md-6 reveal-stagger" style="transition-delay:0.15s;">
        <div class="feature-card p-4">
            <div class="d-flex gap-3">
                <div><i class="bi bi-box-seam feature-icon text-warning"></i></div>
                <div>
                    <h5>Catálogo Inteligente y Alertas</h5>
                    <p>
                        <strong class="text-white">¿Qué hace?</strong> Previene la escasez y pérdida de mercancía.<br>
                        <strong class="text-white" style="display:inline-block;margin-top:6px;">¿Cómo funciona?</strong> Analiza el inventario constantemente y utiliza un algoritmo de semáforo visual para alertar al administrador cuando un producto llega a niveles críticos (Bajo Stock) o cuando está a 30 días o menos de su fecha de vencimiento.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA 4: REQUISICIONES -->
    <div class="col-md-6 reveal-stagger" style="transition-delay:0.2s;">
        <div class="feature-card p-4">
            <div class="d-flex gap-3">
                <div><i class="bi bi-cart-check feature-icon text-success"></i></div>
                <div>
                    <h5>Sistema de Requisiciones</h5>
                    <p>
                        <strong class="text-white">¿Qué hace?</strong> Controla quién saca material del almacén.<br>
                        <strong class="text-white" style="display:inline-block;margin-top:6px;">¿Cómo funciona?</strong> Separa los permisos por roles. Un "Empleado" no puede descontar stock directamente, sino que envía una solicitud digital. El "Administrador" recibe la petición en su panel y la aprueba o rechaza con un solo clic.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA 5: SENSOR OFFLINE -->
    <div class="col-md-6 reveal-stagger" style="transition-delay:0.25s;">
        <div class="feature-card p-4">
            <div class="d-flex gap-3">
                <div><i class="bi bi-wifi-off feature-icon text-primary"></i></div>
                <div>
                    <h5>Sensor de Conexión en Tiempo Real</h5>
                    <p>
                        <strong class="text-white">¿Qué hace?</strong> Evita fallos y pérdida de datos por mal internet.<br>
                        <strong class="text-white" style="display:inline-block;margin-top:6px;">¿Cómo funciona?</strong> Un script monitorea la red milisegundo a milisegundo. Si detecta caída de red, activa una alerta visual roja y bloquea preventivamente los botones de "Guardar" para impedir que una transacción quede a medias en el servidor.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA 6: REPORTES Y PROVEEDORES -->
    <div class="col-md-6 reveal-stagger" style="transition-delay:0.3s;">
        <div class="feature-card p-4">
            <div class="d-flex gap-3">
                <div><i class="bi bi-file-earmark-pdf feature-icon text-secondary"></i></div>
                <div>
                    <h5>Reportes y Cierre Diario automatizado</h5>
                    <p>
                        <strong class="text-white">¿Qué hace?</strong> Elimina el papeleo manual al final del día.<br>
                        <strong class="text-white" style="display:inline-block;margin-top:6px;">¿Cómo funciona?</strong> Recopila todas las entradas, salidas, alertas y operaciones realizadas en la jornada, cruzándolas con la base de proveedores, y genera instantáneamente un documento PDF estructurado listo para la gerencia.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
        </div>
    </section>

    <!-- INTEGRIDAD Y CONFIANZA -->
    <section id="seguridad" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 reveal reveal-left mb-5 mb-lg-0">
                    <h2 class="display-4 fw-bold text-white mb-4">Integridad y Confianza Absoluta</h2>
                    <p class="fs-5 mb-4" style="color: #bbb; line-height: 1.7;">
                        A diferencia de otros sistemas, OSWA Inv cuenta con un registro inmutable. Cada operación es firmada criptográficamente para asegurar que nadie manipule la base de datos por detrás.
                    </p>
                    <ul class="list-unstyled text-oswa-muted fs-5">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-danger me-2"></i> Auditoría completa de movimientos.</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-danger me-2"></i> Control multi-usuario y perfiles.</li>
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Reportes exportables.</li>
                    </ul>
                </div>
                <div class="col-lg-6 reveal reveal-right">
                    <div class="p-1 rounded-4" style="background: linear-gradient(45deg, #E50914, #333);">
                        <img src="{{ asset('img/fondo-login.jpg') }}" alt="Sistema" class="img-fluid rounded-4" style="box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN TECNOLOGÍAS -->
    <section class="py-5" style="background: linear-gradient(180deg, transparent 0%, rgba(229,9,20,0.02) 50%, transparent 100%);">
        <div class="container reveal">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold text-white">Tecnologías Utilizadas</h2>
                <p class="text-oswa-muted">Stack moderno y seguro para garantizar rendimiento y confiabilidad</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-3" style="max-width: 850px; margin: 0 auto; perspective: 1200px;">
                <div class="tech-badge reveal-stagger" style="transition-delay:0.05s;">
                    <i class="bi bi-layers fs-4" style="color:#E50914;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">Laravel</div>
                        <div style="font-size:0.7rem;color:#666;">Framework PHP</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.1s;">
                    <i class="bi bi-code-slash fs-4" style="color:#8892bf;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">PHP 8.x</div>
                        <div style="font-size:0.7rem;color:#666;">Backend</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.15s;">
                    <i class="bi bi-database fs-4" style="color:#f29111;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">MySQL</div>
                        <div style="font-size:0.7rem;color:#666;">Base de Datos</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.2s;">
                    <i class="bi bi-filetype-js fs-4" style="color:#f7df1e;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">JavaScript</div>
                        <div style="font-size:0.7rem;color:#666;">Vanilla + ES6</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.25s;">
                    <i class="bi bi-upc-scan fs-4" style="color:#0984e3;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">HTML5-QRCode</div>
                        <div style="font-size:0.7rem;color:#666;">Escáner web</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.3s;">
                    <i class="bi bi-filetype-html fs-4" style="color:#e34f26;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">Bootstrap 5</div>
                        <div style="font-size:0.7rem;color:#666;">UI Framework</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.35s;">
                    <i class="bi bi-shield-check fs-4" style="color:#00b894;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">SHA-256</div>
                        <div style="font-size:0.7rem;color:#666;">Criptografía</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.4s;">
                    <i class="bi bi-filetype-pdf fs-4" style="color:#e74c3c;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">DomPDF</div>
                        <div style="font-size:0.7rem;color:#666;">Reportes PDF</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.45s;">
                    <i class="bi bi-bar-chart-fill fs-4" style="color:#ffc107;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">Chart.js</div>
                        <div style="font-size:0.7rem;color:#666;">Gráficas</div>
                    </div>
                </div>
                <div class="tech-badge reveal-stagger" style="transition-delay:0.5s;">
                    <i class="bi bi-bell-fill fs-4" style="color:#E50914;"></i>
                    <div>
                        <div class="fw-bold text-white" style="font-size:0.9rem;">SweetAlert2</div>
                        <div style="font-size:0.7rem;color:#666;">Notificaciones</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN PREGUNTAS FRECUENTES -->
    <section id="faq" class="py-5" style="background-color: #080808;">
        <div class="container reveal">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold text-white">Preguntas Frecuentes</h2>
                <p class="text-oswa-muted">Resolvemos tus dudas sobre el funcionamiento de OSWA Inv</p>
            </div>

            <div class="accordion accordion-flush bg-transparent" id="faqAccordion" style="max-width: 800px; margin: 0 auto;">
                
                <!-- PREGUNTA 1 -->
                <div class="accordion-item bg-transparent border-0 mb-3">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed bg-dark text-white border border-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="box-shadow: none;">
                            ¿Cómo funciona la seguridad y auditoría con SHA-256?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary bg-dark border border-top-0 border-secondary rounded-bottom mt-1" style="background-color: #141414 !important;">
                            Cada vez que se registra una entrada o salida, el sistema genera una firma matemática única. Si alguien altera los datos directamente en la base de datos de forma externa, el sistema lo detecta instantáneamente y marca el registro como "ALTERADO".
                        </div>
                    </div>
                </div>

                <!-- PREGUNTA 2 -->
                <div class="accordion-item bg-transparent border-0 mb-3">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-dark text-white border border-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="box-shadow: none;">
                            ¿Puedo tener múltiples usuarios gestionando el sistema?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary bg-dark border border-top-0 border-secondary rounded-bottom mt-1" style="background-color: #141414 !important;">
                            Sí, OSWA Inv cuenta con un sistema avanzado de roles y permisos. Puedes asignar diferentes perfiles (como Administrador o Empleado), garantizando que cada persona tenga los accesos adecuados y manteniendo un registro de auditoría exacto de quién realizó cada acción.
                        </div>
                    </div>
                </div>

                <!-- PREGUNTA 3 -->
                <div class="accordion-item bg-transparent border-0 mb-3">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed bg-dark text-white border border-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="box-shadow: none;">
                            ¿Necesito comprar un lector de códigos de barras físico?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary bg-dark border border-top-0 border-secondary rounded-bottom mt-1" style="background-color: #141414 !important;">
                            No es estrictamente necesario. OSWA Inv incluye un módulo de escaneo inteligente que te permite utilizar la cámara de tu teléfono celular, tablet o computadora para leer códigos de barras, aunque también es 100% compatible con lectores USB tradicionales.
                        </div>
                    </div>
                </div>

                <!-- PREGUNTA 4 -->
                <div class="accordion-item bg-transparent border-0 mb-3">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed bg-dark text-white border border-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="box-shadow: none;">
                            ¿Qué sucede si pierdo la conexión a Internet?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary bg-dark border border-top-0 border-secondary rounded-bottom mt-1" style="background-color: #141414 !important;">
                            El sistema integra sensores de conexión en tiempo real. Si detecta que estás "offline", la interfaz te alertará visualmente y bloqueará temporalmente el guardado de datos críticos para prevenir pérdida de información o inconsistencias en el inventario.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- FIN SECCIÓN PREGUNTAS FRECUENTES -->

<!-- SECCIÓN CONTACTO ACTUALIZADA -->
    <section id="contacto" class="py-5">
        <div class="container reveal">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-white">Soporte Técnico Especializado</h2>
                <p class="text-oswa-muted fs-5">Hable directamente con los desarrolladores del proyecto.</p>
            </div>
            
            <div class="row justify-content-center g-4">
                <!-- CARLOS BRACA -->
                <div class="col-md-5 reveal reveal-left">
                    <div class="feature-card text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1">Carlos Braca</h3>
                        <p class="text-oswa-muted mb-4">
                            Estudiante de Ingeniería Informática<br>
                            <span class="text-danger fw-bold">5to Semestre</span><br>
                            <small>Backend & Seguridad SHA-256</small>
                        </p>
                        <a href="https://wa.me/584122266083" target="_blank" class="btn btn-success rounded-pill px-4 py-2 fw-bold" style="background-color: #25D366; border: none;">
                            <i class="bi bi-whatsapp me-2"></i> Escríbeme
                        </a>
                    </div>
                </div>

                <!-- YORGELIS BLANCO -->
                <div class="col-md-5 reveal reveal-right">
                    <div class="feature-card text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1">Yorgelys Blanco</h3>
                        <p class="text-oswa-muted mb-4">
                            Estudiante de Ingeniería Informática<br>
                            <span class="text-danger fw-bold">5to Semestre</span><br>
                            <small>Frontend & Diseño UI/UX</small>
                        </p>
                        <a href="https://wa.me/584145207044" target="_blank" class="btn btn-success rounded-pill px-4 py-2 fw-bold" style="background-color: #25D366; border: none;">
                            <i class="bi bi-whatsapp me-2"></i> Escríbeme
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
            <strong class="text-white" style="letter-spacing: 1px;">OSWA Inv</strong>
        </div>
    </div>
</footer>

    <!-- BOTÓN FLOTANTE CHATBOT -->
    <button class="btn-chatbot" data-bs-toggle="modal" data-bs-target="#botModal" title="Asistente Virtual">
        <i class="bi bi-robot"></i>
    </button>

    <!-- MODAL DEL CHATBOT INTERACTIVO -->
    <div class="modal fade" id="botModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-robot text-danger me-2 fs-4"></i> Asistente OSWA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Cuerpo del Chat -->
                <div class="modal-body p-3" id="chat-body">
                    <div class="mb-3 p-2 rounded bg-dark border border-secondary text-white me-4">
                        <i class="bi bi-robot text-danger me-2"></i> ¡Hola! Soy el asistente virtual. ¿Qué duda tienes sobre el sistema?
                    </div>
                </div>

                <!-- Botones de preguntas comunes del usuario -->
                <div class="px-3 pb-2" id="quick-replies">
                    <button class="btn btn-sm btn-outline-secondary text-white mb-1 chat-quick-btn" data-ask="celular">¿Funciona en mi celular?</button>
                    <button class="btn btn-sm btn-outline-secondary text-white mb-1 chat-quick-btn" data-ask="clave">¿Qué hago si olvido mi clave?</button>
                    <button class="btn btn-sm btn-outline-secondary text-white mb-1 chat-quick-btn" data-ask="soporte">¿Dan soporte técnico?</button>
                </div>

                <!-- Input del Chat -->
                <div class="modal-footer p-2">
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control bg-dark border-secondary text-white" placeholder="Escribe aquí..." autocomplete="off">
                        <button type="button" id="btn-send" class="btn btn-danger"><i class="bi bi-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lógica del Navbar Transparente/Cristal
            const navbar = document.querySelector('.navbar-oswa');
            const handleScroll = () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            };
            window.addEventListener('scroll', handleScroll);
            handleScroll();

            // Animaciones de Scroll
            const reveals = document.querySelectorAll(".reveal, .reveal-stagger");
            const revealOnScroll = () => {
                const windowHeight = window.innerHeight;
                const elementVisible = 100;
                reveals.forEach((reveal) => {
                    if (reveal.getBoundingClientRect().top < windowHeight - elementVisible) {
                        reveal.classList.add("active");
                    }
                });
            };
            window.addEventListener("scroll", revealOnScroll);
            revealOnScroll();

            // --- CEREBRO DEL CHATBOT ---
            const chatBody = document.getElementById('chat-body');
            const chatInput = document.getElementById('chat-input');
            const btnSend = document.getElementById('btn-send');
            const quickBtns = document.querySelectorAll('.chat-quick-btn');

            function appendMessage(sender, text) {
                const msgDiv = document.createElement('div');
                msgDiv.classList.add('mb-3', 'p-2', 'rounded');
                if(sender === 'user') {
                    msgDiv.classList.add('bg-secondary', 'text-white', 'ms-4');
                    msgDiv.style.textAlign = 'right';
                    msgDiv.innerHTML = text;
                } else {
                    msgDiv.classList.add('bg-dark', 'text-white', 'border', 'border-secondary', 'me-4');
                    msgDiv.innerHTML = `<i class="bi bi-robot text-danger me-2"></i> ${text}`;
                }
                chatBody.appendChild(msgDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            function botReply(question) {
                const q = question.toLowerCase();
                let reply = "No tengo la respuesta a eso. Te recomiendo ir a la sección de 'Soporte' y escribirnos directo por WhatsApp.";

                if(q.includes('celular') || q.includes('telefono') || q.includes('móvil')) {
                    reply = "¡Sí! OSWA Inv es 100% responsivo. Puedes abrirlo desde tu teléfono, tablet o computadora y se adaptará perfectamente a tu pantalla.";
                } else if(q.includes('clave') || q.includes('contraseña') || q.includes('olvide')) {
                    reply = "Si olvidaste tu clave, ve a 'Entrar' y haz clic en '¿Olvidaste tu contraseña?'. El sistema te enviará un correo para recuperarla de forma segura.";
                } else if(q.includes('soporte') || q.includes('ayuda') || q.includes('contacto')) {
                    reply = "¡Claro! En la sección de abajo encontrarás los botones de WhatsApp para hablar directamente con Carlos Braca o Yorgelys Blanco, los creadores del sistema.";
                } else if(q.includes('hola') || q.includes('buenas')) {
                    reply = "¡Hola! Estoy aquí para ayudarte a entender cómo funciona OSWA Inv.";
                }

                setTimeout(() => appendMessage('bot', reply), 500);
            }

            btnSend.addEventListener('click', function() {
                const text = chatInput.value.trim();
                if(text !== "") {
                    appendMessage('user', text);
                    chatInput.value = '';
                    botReply(text);
                }
            });

            chatInput.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') btnSend.click();
            });

            quickBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    appendMessage('user', this.innerText);
                    botReply(this.getAttribute('data-ask'));
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
