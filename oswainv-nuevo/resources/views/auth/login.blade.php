<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #141414;
            --bg-card: rgba(0,0,0,0.75);
            --bg-input: #333333;
            --border-color: #2b2b2b;
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --accent-primary: #E50914;
            --accent-success: #10b981;
            --accent-danger: #e74c3c;
        }
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.9)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1920') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: var(--bg-card);
            border: none;
            border-radius: 4px;
            box-shadow: 0 0 60px rgba(0,0,0,0.7);
            padding: 3.5rem 2.5rem;
            max-width: 480px;
            width: 100%;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo h1 {
            color: var(--accent-primary);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .auth-logo p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .form-label {
            color: var(--text-secondary);
            font-weight: 400;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .form-control {
            background: var(--bg-input);
            border: none;
            color: var(--text-primary);
            padding: 1rem 1rem;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: background 0.2s ease;
        }
        .form-control:focus {
            background: #444;
            border: none;
            color: var(--text-primary);
            box-shadow: none;
            outline: 1px solid #666;
        }
        .form-control::placeholder {
            color: #777;
        }
        .form-check-input {
            background-color: var(--bg-input);
            border-color: rgba(255,255,255,0.2);
        }
        .form-check-input:checked {
            background-color: var(--accent-primary);
            border-color: var(--accent-primary);
        }
        .btn-primary-custom {
            background: var(--accent-primary);
            color: #fff;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s ease;
        }
        .btn-primary-custom:hover {
            background: #c10711;
            color: #fff;
        }
        .btn-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }
        .btn-link:hover {
            color: #fff;
        }
        .invalid-feedback {
            color: var(--accent-primary);
            font-size: 0.825rem;
            margin-top: 0.25rem;
        }
        .divider {
            height: 1px;
            background: #444;
            margin: 2rem 0;
        }
        .login-link {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .login-link a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .logo-auth-clean {
            height: 80px;
            width: auto;
            filter: brightness(0) invert(1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
            margin: 0 auto 20px auto;
            cursor: pointer;
        }
        .logo-auth-clean:hover {
            transform: scale(1.1);
            filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255, 255, 255, 0.7));
        }
        .auth-footer {
            text-align: center;
            padding: 1.5rem 4%;
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        .auth-footer span.highlight {
            color: var(--text-primary);
            font-weight: 600;
        }
        .auth-footer .heart-icon {
            color: var(--accent-danger);
            animation: heartbeat 1.5s infinite;
            display: inline-block;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* ESTILOS DEL OVERLAY DE LOGIN */
        .cinematic-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: #050505; z-index: 99999;
            display: flex; justify-content: center; align-items: center;
            opacity: 0; transition: opacity 0.4s ease;
            pointer-events: none; /* EVITA QUE BLOQUEE LA PANTALLA ANTES DE TIEMPO */
        }
        .cinematic-overlay.active { 
            opacity: 1; 
            pointer-events: all; /* BLOQUEA INTERACCIÓN CUANDO ESTÁ ACTIVO */
        }

        .cinematic-content { text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }

        .fase-secuencia { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .fase-secuencia.fade-out { opacity: 0; transform: scale(0.9); filter: blur(5px); }
        .fase-secuencia.fade-in { animation: textExplosion 0.8s forwards; }

        /* EFECTO DE RAYO ELÉCTRICO PARA LOS TÍTULOS */
        .fase-secuencia.fade-in .quote-highlight {
            animation: rayoElectrico 1.2s ease-out forwards !important;
        }

        /* Keyframes del cortocircuito eléctrico */
        @keyframes rayoElectrico {
            0% { opacity: 0; text-shadow: none; filter: brightness(1); }
            8% { opacity: 1; text-shadow: 0 0 40px #ffffff, 0 0 100px #ffffff, 0 0 150px #E50914; transform: scale(1.05) skewX(-5deg); filter: brightness(2.5); }
            12% { opacity: 0; text-shadow: none; transform: scale(1) skewX(0); filter: brightness(1); }
            16% { opacity: 1; text-shadow: 0 0 60px #ffffff, 0 0 120px #E50914; transform: scale(1.02) skewX(3deg); filter: brightness(1.8); }
            20% { opacity: 0; }
            24% { opacity: 1; text-shadow: 0 0 40px #E50914; filter: brightness(1.2); }
            28% { opacity: 0; }
            35% { opacity: 1; text-shadow: 0 0 30px #E50914; }
            100% { opacity: 1; transform: scale(1) skewX(0); text-shadow: 0 0 20px rgba(229, 9, 20, 0.8), 0 0 40px rgba(229, 9, 20, 0.4); filter: brightness(1); }
        }

        .intro-logo { width: 160px; filter: brightness(0) invert(1); animation: pulseGlow 2s infinite alternate; }
        .loading-text { color: var(--text-secondary, #b3b3b3); font-size: 1.2rem; letter-spacing: 3px; font-weight: 600; }

        .quote-text { color: #a0a0a0; font-size: 1.5rem; font-weight: 300; letter-spacing: 2px; }
        .quote-highlight { 
            color: #ffffff; font-size: 3.5rem; font-weight: 800; letter-spacing: 4px;
            text-shadow: 0 0 20px rgba(229, 9, 20, 0.8), 0 0 40px rgba(229, 9, 20, 0.4); 
        }

        @keyframes pulseGlow {
            0% { filter: brightness(0) invert(1) drop-shadow(0 0 5px rgba(255,255,255,0.2)); transform: scale(0.95); }
            100% { filter: brightness(0) invert(1) drop-shadow(0 0 25px rgba(255,255,255,0.8)); transform: scale(1.05); }
        }
        @keyframes textExplosion {
            0% { opacity: 0; transform: scale(0.8); filter: blur(10px); }
            100% { opacity: 1; transform: scale(1); filter: blur(0); }
        }

        /* Cursor de terminal hacker */
        .cursor-blink {
            display: inline-block;
            width: 10px;
            height: 1.2rem;
            background-color: #E50914;
            margin-left: 5px;
            animation: blink 1s step-end infinite;
            vertical-align: text-bottom;
        }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body>
    
<!-- OVERLAY DE CARGA CINEMÁTICA (LOGIN) -->
<div id="login-cinematic-overlay" class="cinematic-overlay d-none">
    <div class="cinematic-content" style="width: 80%; max-width: 600px;">
        
        <!-- Fase 1: Logo, Bienvenida y Barra tipo Videojuego -->
        <div id="fase-1" class="fase-secuencia text-center w-100">
            <img src="{{ asset('img/logo-unellez.png') }}" class="intro-logo mb-3" alt="UNELLEZ" style="width: 120px;">
            
            <h3 id="texto-bienvenida" class="text-white fw-bold mb-4" style="letter-spacing: 3px; font-size: 1.6rem; text-transform: uppercase; min-height: 2.5rem;">
                <!-- Se llenará con JS -->
            </h3>
            
            <div class="d-flex justify-content-between align-items-end mb-1 px-2">
                <span style="color: var(--text-secondary); font-family: monospace; font-size: 0.9rem;">CARGANDO RECURSOS DEL SISTEMA</span>
                <span id="porcentaje-carga" class="text-danger fw-bold" style="font-family: monospace; font-size: 1.5rem; text-shadow: 0 0 10px rgba(229,9,20,0.5);">0%</span>
            </div>
            
            <!-- Barra de Progreso -->
            <div class="progress" style="height: 6px; background-color: #222; border-radius: 0; box-shadow: 0 0 15px rgba(0,0,0,0.8) inset;">
                <div id="barra-progreso" class="progress-bar bg-danger" role="progressbar" style="width: 0%; transition: width 0.1s linear; box-shadow: 0 0 10px rgba(229,9,20,0.8);"></div>
            </div>
        </div>

        <!-- Fase 2: Frase 1 -->
        <div id="fase-2" class="fase-secuencia text-center d-none">
            <h2 class="quote-text">Cada línea de código cuenta...</h2>
            <h1 class="quote-highlight mt-2" style="font-size: 2.5rem; letter-spacing: 3px;">CADA DATO IMPORTA</h1>
        </div>

        <!-- Fase 3: Frase 2 -->
        <div id="fase-3" class="fase-secuencia text-center d-none">
            <h2 class="quote-text">Optimizando el presente...</h2>
            <h1 class="quote-highlight mt-2" style="font-size: 2.5rem; letter-spacing: 3px;">ASEGURANDO EL MAÑANA</h1>
        </div>

        <!-- Fase 4: Frase Final Épica -->
        <div id="fase-4" class="fase-secuencia text-center d-none">
            <h2 class="quote-text">La ingeniería no es solo código...</h2>
            <h1 class="quote-highlight mt-2">ES DISEÑAR EL FUTURO</h1>
        </div>
        
    </div>
</div>
    
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="logo-auth-clean">
            <h1>OSWA Inv</h1>
            <p>Gestión de Inventario</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}" class="text-start">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="correo@ejemplo.com">
                
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" style="background-color: var(--bg-input); border-color: rgba(255,255,255,0.2);">
                <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: var(--text-secondary);">Recordarme</label>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary-custom w-100 mb-3"><i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión</button>
            </div>

            <div style="font-size: 0.85rem;"><a href="#" class="text-decoration-none" style="color: var(--text-secondary);">¿Olvidaste tu contraseña?</a></div>
        </form>
        
        <div class="divider"></div>
        
        <div class="login-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-white text-decoration-none fw-bold">Regístrate</a>
        </div>
    </div>

    <footer class="auth-footer">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.querySelector('form'); 
            
            if(loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Detenemos el envío
                    
                    const overlay = document.getElementById('login-cinematic-overlay');
                    const fase1 = document.getElementById('fase-1');
                    const fase2 = document.getElementById('fase-2');
                    const fase3 = document.getElementById('fase-3');
                    const fase4 = document.getElementById('fase-4');
                    const btnSubmit = loginForm.querySelector('button[type="submit"]');
                    
                    // --- LÓGICA DE BIENVENIDA CON SUSPENSO Y HACK DE NOMBRES ---
                    const inputUsuario = document.querySelector('input[name="email"]') || document.querySelector('input[name="username"]');
                    let nombreUsuario = "AGENTE"; // Nombre por defecto
                    
                    if(inputUsuario && inputUsuario.value) {
                        const prefijo = inputUsuario.value.split('@')[0].toLowerCase();
                        
                        // DICCIONARIO VIP PARA LA PRESENTACIÓN (Agrega aquí tus correos)
                        const usuariosVIP = {
                            '2006cebra': 'CARLOS',
                            'yorgelis123': 'YORGELIS',
                            'admin': 'ADMINISTRADOR'
                        };
                        
                        // Si el correo está en la lista VIP, usa el nombre real, sino usa el correo
                        nombreUsuario = usuariosVIP[prefijo] || prefijo.toUpperCase();
                    }

                    const textoFinal = `BIENVENIDO, ${nombreUsuario}... A OSWA INV`;
                    const h3Bienvenida = document.getElementById('texto-bienvenida');
                    h3Bienvenida.innerHTML = '<span class="cursor-blink"></span>'; // Cursor inicial
                    
                    // Efecto Máquina de Escribir (Suspenso)
                    let charIndex = 0;
                    const typingInterval = setInterval(() => {
                        if (charIndex < textoFinal.length) {
                            const currentText = textoFinal.substring(0, charIndex + 1);
                            h3Bienvenida.innerHTML = currentText + '<span class="cursor-blink"></span>';
                            charIndex++;
                        } else {
                            clearInterval(typingInterval);
                        }
                    }, 60); // 60ms por letra

                    if(btnSubmit) btnSubmit.disabled = true;
                    
                    // Quitar el d-none y arrancar
                    overlay.classList.remove('d-none');
                    setTimeout(() => overlay.classList.add('active'), 50);
                    
                    try {
                        const cinematicSound = new Audio('{{ asset("sounds/intro.mp3") }}');
                        cinematicSound.volume = 0.9;
                        cinematicSound.play();
                    } catch (err) {
                        console.log("Audio bloqueado", err);
                    }
                    
                    // --- LÓGICA DE LA BARRA DE CARGA (VIDEOJUEGO) ---
                    let progreso = 0;
                    const textoPorcentaje = document.getElementById('porcentaje-carga');
                    const barraProgreso = document.getElementById('barra-progreso');
                    
                    // El contador subirá aleatoriamente hasta llegar a 100% justo antes de cambiar de fase (3s)
                    const intervaloCarga = setInterval(() => {
                        // Sube entre 1% y 3% cada 60ms (promedio 2% -> 100% en ~3s)
                        progreso += Math.floor(Math.random() * 3) + 1; 
                        if(progreso >= 100) {
                            progreso = 100;
                            clearInterval(intervaloCarga);
                        }
                        textoPorcentaje.innerText = progreso + '%';
                        barraProgreso.style.width = progreso + '%';
                    }, 60);

                    // --- LÍNEA DE TIEMPO (15 SEGUNDOS TOTALES) ---

                    // Minuto 0:03 - Sale Logo y Barra, entra Frase 1
                    setTimeout(() => {
                        fase1.classList.add('fade-out');
                        setTimeout(() => {
                            fase1.classList.add('d-none');
                            fase2.classList.remove('d-none');
                            fase2.classList.add('fade-in');
                        }, 500);
                    }, 3000);

                    // Minuto 0:06.5 - Sale Frase 1, entra Frase 2
                    setTimeout(() => {
                        fase2.classList.replace('fade-in', 'fade-out');
                        setTimeout(() => {
                            fase2.classList.add('d-none');
                            fase3.classList.remove('d-none');
                            fase3.classList.add('fade-in');
                        }, 500);
                    }, 6500);

                    // Minuto 0:10 - Sale Frase 2, entra Frase Final Épica
                    setTimeout(() => {
                        fase3.classList.replace('fade-in', 'fade-out');
                        setTimeout(() => {
                            fase3.classList.add('d-none');
                            fase4.classList.remove('d-none');
                            fase4.classList.add('fade-in');
                        }, 500);
                    }, 10000);

                    // Minuto 0:14.5 - Se oscurece todo antes de entrar al sistema
                    setTimeout(() => {
                        fase4.classList.replace('fade-in', 'fade-out');
                    }, 14000);

                    // Minuto 0:15 - ¡Entramos al Dashboard!
                    setTimeout(() => {
                        loginForm.submit();
                    }, 15000);
                });
            }
        });
    </script>
</body>
</html>
