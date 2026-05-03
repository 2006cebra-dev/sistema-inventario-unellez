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
            font-size: 1.15rem;
            font-weight: 400;
            color: var(--text-bright-muted);
            max-width: 600px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.9);
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

        /* CARDS */
        .feature-card { background: var(--bg-card); border: 1px solid rgba(255,255,255,0.05); border-radius: 15px; padding: 2rem; transition: 0.3s; height: 100%; }
        .feature-card:hover { transform: translateY(-5px); border-color: rgba(229, 9, 20, 0.5); box-shadow: 0 10px 30px rgba(0,0,0,0.8); }
        .feature-icon { font-size: 2rem; color: var(--accent); }

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
                <img src="{{ asset('img/logo-unellez.png') }}" alt="Logo" height="40" class="me-2" style="filter: brightness(0) invert(1);">
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
                <img src="{{ asset('img/logo-unellez.png') }}" alt="Logo" height="50" class="mb-3" style="filter: brightness(0) invert(1);">
                <h2 class="display-5 fw-bold text-white">Ingeniería Aplicada al Inventario</h2>
                <p class="text-oswa-muted fs-5">Diseñado para optimizar, asegurar y registrar cada movimiento en el almacén.</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6 reveal reveal-left">
                    <div class="feature-card d-flex align-items-start">
                        <i class="bi bi-box-seam feature-icon me-4"></i>
                        <div>
                            <h4 class="fw-bold mb-2 text-white">Catálogo Inteligente</h4>
                            <p class="text-oswa-muted mb-0">Control exacto de stock con un sistema de semáforo visual que alerta sobre vencimientos en tiempo real.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 reveal reveal-right">
                    <div class="feature-card d-flex align-items-start">
                        <i class="bi bi-buildings feature-icon me-4" style="color: #FFD700;"></i>
                        <div>
                            <h4 class="fw-bold mb-2 text-white">Red de Proveedores</h4>
                            <p class="text-oswa-muted mb-0">Directorio corporativo vinculado al inventario, permitiendo emitir órdenes de abastecimiento directo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 reveal reveal-left">
                    <div class="feature-card d-flex align-items-start">
                        <i class="bi bi-shield-lock feature-icon me-4" style="color: #00BFFF;"></i>
                        <div>
                            <h4 class="fw-bold mb-2 text-white">Auditoría Criptográfica</h4>
                            <p class="text-oswa-muted mb-0">Registro inmutable de movimientos protegidos con firma digital (SHA-256) contra alteraciones.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 reveal reveal-right">
                    <div class="feature-card d-flex align-items-start">
                        <i class="bi bi-file-earmark-pdf feature-icon me-4" style="color: #32CD32;"></i>
                        <div>
                            <h4 class="fw-bold mb-2 text-white">Reportes Automatizados</h4>
                            <p class="text-oswa-muted mb-0">Generación instantánea de reportes en PDF listos para imprimir, firmar y entregar a la gerencia.</p>
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
                    <p class="text-oswa-muted fs-5 mb-4">
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

    <!-- PREGUNTAS FRECUENTES -->
    <section id="faq" class="py-5" style="background-color: #080808;">
        <div class="container reveal">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold text-white">Preguntas Frecuentes</h2>
                <p class="text-oswa-muted">Resolvemos tus dudas sobre OSWA Inv</p>
            </div>

            <div class="accordion" id="accordionFAQ" style="max-width: 800px; margin: 0 auto;">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Cómo funciona la seguridad SHA-256?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body">
                            Cada vez que se registra una entrada o salida, el sistema genera una firma matemática única. Si alguien altera los datos directamente en la base de datos, el sistema lo detecta instantáneamente y marca el registro como "ALTERADO".
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Puedo tener múltiples usuarios gestionando?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body">
                            Sí. OSWA Inv cuenta con un sistema de perfiles estilo Netflix, donde puedes crear diferentes usuarios, cada uno con sus propios permisos y registros de auditoría.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <span class="text-danger fw-bold">5to Semestre - UNELLEZ</span><br>
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
                        <h3 class="fw-bold text-white mb-1">Yorgelis Blanco</h3>
                        <p class="text-oswa-muted mb-4">
                            Estudiante de Ingeniería Informática<br>
                            <span class="text-danger fw-bold">5to Semestre - UNELLEZ</span><br>
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

    <!-- FOOTER PROFESIONAL -->
    <footer class="py-4" style="background-color: #000; border-top: 1px solid #222;">
        <div class="container text-center reveal">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="Logo" height="40" class="mb-3" style="filter: brightness(0) invert(1); opacity: 0.7;">
            <p class="text-oswa-muted mb-1 fs-6">
                &copy; {{ date('Y') }} <strong>OSWA Inv</strong>. Todos los derechos reservados.
            </p>
            <p class="text-oswa-muted mb-0" style="font-size: 0.85rem;">
                Diseñado y desarrollado con excelencia por <strong class="text-white">Carlos Braca</strong> y <strong class="text-white">Yorgelis Blanco</strong>.
            </p>
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
            const reveals = document.querySelectorAll(".reveal");
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
                    reply = "¡Claro! En la sección de abajo encontrarás los botones de WhatsApp para hablar directamente con Carlos Braca o Yorgelis Blanco, los creadores del sistema.";
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
