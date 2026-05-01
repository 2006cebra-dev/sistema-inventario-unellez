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

        /* SPLASH SCREEN FINAL */
        .splash-screen-container {
            position: fixed;
            top: 0; left: 0; 
            width: 100vw; height: 100vh;
            background: #000000 !important;
            z-index: 999999 !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out;
        }
        .splash-screen-container.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        .unellez-logo {
            width: 150px;
            filter: brightness(0) invert(1);
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.9; }
        }
        .progress-wrapper {
            width: 300px;
            text-align: center;
        }
        .progress {
            height: 4px !important;
            background-color: #1a1a1a !important;
            border-radius: 0 !important;
            overflow: hidden;
            margin-bottom: 10px;
        }
        #splash-bar {
            background-color: #ff0000 !important;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.7);
            transition: width 0.1s linear;
        }
        .splash-text-sub, #splash-quote, #splash-percentage {
            font-family: 'Courier New', Courier, monospace !important;
            letter-spacing: 1px;
        }
        #splash-quote {
            color: #ffffff;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        #splash-percentage {
            color: #ffffff;
            font-size: 0.75rem;
            margin-top: 6px;
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
<body>
    
<!-- SPLASH SCREEN MINIMALISTA -->
<div id="splash-screen" class="splash-screen-container">
    <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="unellez-logo">
    <div class="progress-wrapper">
        <div class="progress">
            <div id="splash-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
        </div>
        <div id="splash-quote" class="splash-text-sub">Iniciando sistema...</div>
        <div id="splash-percentage">0%</div>
    </div>
</div>
    
    <div id="login-card-container">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="logo-auth-clean">
            <h1>OSWA Inv</h1>
            <p>Gestión de Inventario</p>
        </div>
        
        <form id="formLogin" method="POST" action="{{ route('login') }}" class="text-start">
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const splash = document.getElementById('splash-screen');
            const loginContainer = document.getElementById('login-card-container');
            const loginForm = document.getElementById('formLogin');

            function ejecutarAnimacionCarga(isLogin = false) {
                let progreso = 0;
                const bar = document.getElementById('splash-bar');
                const quote = document.getElementById('splash-quote');
                const percentage = document.getElementById('splash-percentage');

                const frasesIngenieria = [
                    "Iniciando módulos de estructuras discretas...",
                    "Sincronizando bases de datos relacionales...",
                    "Cargando protocolos de auditoría SHA-256...",
                    "Estableciendo handshake de seguridad...",
                    "Compilando entorno de gestión OSWA...",
                    "Optimizando recursos del sistema..."
                ];

                if(isLogin && loginContainer) {
                    loginContainer.style.opacity = '0';
                    loginContainer.style.pointerEvents = 'none';
                    loginContainer.style.transition = 'opacity 0.3s ease';
                }

                splash.classList.remove('fade-out');
                splash.style.display = 'flex';

                const interval = setInterval(() => {
                    progreso += Math.floor(Math.random() * 10) + 2;

                    if(progreso % 20 === 0) {
                        quote.innerText = frasesIngenieria[Math.floor(Math.random() * frasesIngenieria.length)];
                    }

                    if (progreso >= 100) {
                        progreso = 100;
                        clearInterval(interval);
                        bar.style.width = '100%';
                        percentage.innerText = '100%';

                        if(!isLogin) {
                            setTimeout(() => {
                                splash.classList.add('fade-out');
                            }, 500);
                        }
                    } else {
                        bar.style.width = progreso + '%';
                        percentage.innerText = progreso + '%';
                    }
                }, 50);
            }

            ejecutarAnimacionCarga(false);

            if(loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    ejecutarAnimacionCarga(true);

                    setTimeout(() => {
                        loginForm.submit();
                    }, 1000);
                });
            }
        });
    </script>
</body>
</html>
