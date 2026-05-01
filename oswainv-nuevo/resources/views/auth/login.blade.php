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
            --bg-dark: #000000;
            --bg-card: rgba(0, 0, 0, 0.75);
            --bg-input: #333333;
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --accent-primary: #E50914;
            --accent-danger: #e74c3c;
        }
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #000000;
        }
        .auth-wrapper {
            min-height: 100vh;
            background: linear-gradient(to bottom, rgba(0,0,0,0.80) 0%, rgba(0,0,0,0.95) 100%),
                        url('{{ asset('img/refrigeracion_centros_datos.jpg') }}') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .auth-card {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 60px 68px 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            border: none;
        }
        .auth-logo { text-align: center; margin-bottom: 2rem; }
        .auth-logo h1 { color: var(--accent-primary); font-size: 2rem; font-weight: 700; letter-spacing: 2px; }
        .auth-logo p { color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.5rem; }
        .logo-auth-clean {
            height: 80px; width: auto;
            filter: brightness(0) invert(1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: block; margin: 0 auto 20px auto; cursor: pointer;
        }
        .logo-auth-clean:hover {
            transform: scale(1.1);
            filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255, 255, 255, 0.7));
        }
        .auth-label { color: #b3b3b3; font-size: 0.9rem; font-weight: 400; margin-bottom: 0.5rem; }
        .auth-input {
            background: #333333 !important;
            border: 1px solid #333333 !important;
            color: #ffffff !important;
            border-radius: 4px;
            padding: 16px 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .auth-input:focus {
            background: #454545 !important;
            border-color: #E50914 !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .auth-input::placeholder { color: #777; }
        .auth-input.is-invalid { border-color: var(--accent-danger) !important; }
        .form-check-input { background-color: var(--bg-input); border-color: rgba(255,255,255,0.2); }
        .form-check-input:checked { background-color: var(--accent-primary); border-color: var(--accent-primary); }
        .btn-auth {
            background-color: #E50914;
            color: white;
            font-weight: bold;
            padding: 16px;
            border-radius: 4px;
            border: none;
            width: 100%;
            margin-top: 24px;
            font-size: 1rem;
            transition: background-color 0.2s, transform 0.1s;
        }
        .btn-auth:hover {
            background-color: #f40612;
            transform: scale(1.02);
            color: white;
        }
        .auth-links { color: #b3b3b3; font-size: 0.9rem; }
        .auth-links a { color: #ffffff; text-decoration: none; font-weight: 500; }
        .auth-links a:hover { text-decoration: underline; }
        .divider { height: 1px; background: #444; margin: 2rem 0; }
        .invalid-feedback { color: var(--accent-primary); font-size: 0.825rem; margin-top: 0.25rem; }
        .auth-footer {
            text-align: center; padding: 1.5rem 4%; margin-top: 2rem;
            color: var(--text-secondary); font-size: 0.85rem;
        }
        .auth-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .auth-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #B20710, #E50914); border-radius: 10px; }

        /* SPLASH SCREEN */
        .splash-screen-container {
            position: fixed; top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: #000000 !important;
            z-index: 999999 !important;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out;
        }
        .splash-screen-container.fade-out { opacity: 0; visibility: hidden; }
        .unellez-logo {
            width: 150px; filter: brightness(0) invert(1);
            margin-bottom: 2rem; animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.9; }
        }
        .progress-wrapper { width: 300px; text-align: center; }
        .progress {
            height: 4px !important; background-color: #1a1a1a !important;
            border-radius: 0 !important; overflow: hidden; margin-bottom: 10px;
        }
        #splash-bar {
            background-color: #ff0000 !important;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.7);
            transition: width 0.1s linear;
        }
        #splash-quote, #splash-percentage {
            font-family: 'Courier New', Courier, monospace !important;
            letter-spacing: 1px;
        }
        #splash-quote { color: #ffffff; font-size: 0.85rem; text-transform: uppercase; }
        #splash-percentage { color: #ffffff; font-size: 0.75rem; margin-top: 6px; }

        @media (max-width: 480px) {
            .auth-card { padding: 40px 24px 30px; margin: 0 16px; }
        }
    </style>
</head>
<body>

<!-- SPLASH SCREEN -->
<div id="splash-screen" class="splash-screen-container">
    <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="unellez-logo">
    <div class="progress-wrapper">
        <div class="progress">
            <div id="splash-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
        </div>
        <div id="splash-quote">Iniciando sistema...</div>
        <div id="splash-percentage">0%</div>
    </div>
</div>

<div class="auth-wrapper" id="authWrapper">
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
                    <label for="email" class="auth-label">Correo Electrónico</label>
                    <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="correo@ejemplo.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="auth-label">Contraseña</label>
                    <input id="password" type="password" class="form-control auth-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: #b3b3b3;">Recordarme</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn-auth"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</button>
                </div>

                <div class="auth-links mt-3" style="font-size: 0.85rem;">
                    <a href="#" style="color: #b3b3b3;">¿Olvidaste tu contraseña?</a>
                </div>
            </form>

            <div class="divider"></div>

            <div class="auth-links" style="text-align: center;">
                ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-white fw-bold">Regístrate</a>
            </div>
        </div>

        <footer class="auth-footer">
            <div class="mb-1">&copy; <script>document.write(new Date().getFullYear())</script> <strong>OSWA Inv</strong>. Todos los derechos reservados.</div>
            <div>Desarrollado con <i class="bi bi-code-slash" style="color:#0d6efd;"></i> y <i class="bi bi-heart-fill heart-icon"></i> por <span class="highlight">Carlos Braca & Yorgelis Blanco</span></div>
            <div class="mt-2 d-flex align-items-center justify-content-center" style="font-size: 0.75rem; opacity: 0.8;">
                <span>Ingeniería en Informática — V Semestre |</span>
                <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 18px; margin-left: 8px; margin-right: 4px; filter: brightness(0) invert(1);">
                <strong style="letter-spacing: 0.5px;">UNELLEZ</strong>
            </div>
        </footer>
    </div>
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

            if (isLogin && loginContainer) {
                loginContainer.style.opacity = '0';
                loginContainer.style.pointerEvents = 'none';
                loginContainer.style.transition = 'opacity 0.3s ease';
            }

            splash.classList.remove('fade-out');
            splash.style.display = 'flex';

            const interval = setInterval(() => {
                progreso += Math.floor(Math.random() * 10) + 2;
                if (progreso % 20 === 0) {
                    quote.innerText = frasesIngenieria[Math.floor(Math.random() * frasesIngenieria.length)];
                }
                if (progreso >= 100) {
                    progreso = 100;
                    clearInterval(interval);
                    bar.style.width = '100%';
                    percentage.innerText = '100%';
                    if (!isLogin) {
                        setTimeout(() => { splash.classList.add('fade-out'); }, 500);
                    }
                } else {
                    bar.style.width = progreso + '%';
                    percentage.innerText = progreso + '%';
                }
            }, 50);
        }

        ejecutarAnimacionCarga(false);

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                ejecutarAnimacionCarga(true);
                setTimeout(() => { loginForm.submit(); }, 1000);
            });
        }
    });
</script>
</body>
</html>
