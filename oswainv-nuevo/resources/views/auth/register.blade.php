<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #E50914;
            --bg-main: #050505;
            --bg-card: #121212;
            --input-bg: #1e1e1e;
        }
        
        body {
            background-color: var(--bg-main);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #fff;
            overflow: hidden;
        }

        .bg-video-full {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; object-fit: cover; z-index: -2;
        }
        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: -1;
        }

        .auth-container {
            display: flex; width: 1200px; max-width: 95vw; height: 85vh; min-height: 700px;
            background: rgba(18, 18, 18, 0.95); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            border-radius: 20px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.9);
            border: 1px solid rgba(255,255,255,0.05); z-index: 10;
            opacity: 0; animation: aparecerTarjeta 0.6s ease-out forwards;
        }

        .auth-left {
            flex: 1; background-position: center; background-size: cover; background-repeat: no-repeat;
            background-image: url('{{ asset("img/fondo-login.jpg") }}');
            position: relative; display: flex; flex-direction: column; align-items: center;
            justify-content: center; text-align: center; border-right: 1px solid rgba(255,255,255,0.05);
        }
        .auth-left-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.3), rgba(18,18,18,0.95)); z-index: 1;
        }
        .auth-left-content { position: relative; z-index: 2; padding: 3rem; }
        
        .auth-title-brand { font-size: 3rem; font-weight: 800; letter-spacing: 6px; color: #fff; margin-bottom: 0.5rem; }
        .auth-subtitle-brand { font-size: 1rem; letter-spacing: 3px; color: #aaa; text-transform: uppercase; }

        .auth-right {
            flex: 1; padding: 3rem 4rem; display: flex; flex-direction: column; justify-content: center; background: transparent;
        }

        .form-control {
            background-color: var(--input-bg); border: 1px solid #333; color: #fff; border-radius: 8px;
            transition: all 0.3s; box-shadow: none;
        }
        .form-control:focus { background-color: #252525; border-color: var(--accent); box-shadow: none; color: #fff; }
        .form-control::placeholder { color: #666; }

        .btn-auth:hover { background-color: #b20710 !important; border-color: #b20710 !important; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(229, 9, 20, 0.4); }
        .hover-white:hover { color: #fff !important; }
        .hover-red:hover { color: var(--accent) !important; text-decoration: underline !important; }

        .mobile-brand { display: none; }

        @media (max-width: 992px) {
            body { overflow-y: auto; height: auto; align-items: flex-start; padding: 0; }
            .auth-container { flex-direction: column; height: auto; min-height: 100vh; max-width: 100vw; width: 100%; border-radius: 0; border: none; overflow-y: auto; animation: none; opacity: 1; background: rgba(18,18,18,0.95); }
            .auth-left { display: none; }
            .auth-right { padding: 2rem 1.5rem; flex: none; width: 100%; }
            .auth-header h2 { font-size: 1.4rem; }
            .auth-header p { font-size: 0.85rem; }
            .form-control { font-size: 16px; padding: 10px 14px; }
            .btn-auth { padding: 12px; font-size: 1rem; }
            .mobile-brand { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 1.5rem; }
            .mobile-brand img { height: 48px; filter: brightness(0) invert(1) drop-shadow(0 0 12px rgba(255,255,255,0.2)); }
            .mobile-brand .brand-name { font-size: 1.6rem; font-weight: 800; background: linear-gradient(90deg,#E50914,#ff6b6b,#B20710,#E50914); background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 2px; }
            .anim-up-1, .anim-up-2, .anim-up-3, .anim-up-4, .anim-up-5, .anim-up-6, .anim-up-7, .anim-up-8, .anim-up-9 { opacity: 1 !important; animation: none !important; }
            .anim-left-1, .anim-left-2, .anim-left-3 { opacity: 1 !important; animation: none !important; }
        }

        @keyframes aparecerTarjeta { 0% { opacity: 0; transform: scale(0.97); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes deslizarIzquierda { 0% { opacity: 0; transform: translateX(50px); } 100% { opacity: 1; transform: translateX(0); } }
        @keyframes deslizarArriba { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }

        .anim-left-1 { opacity: 0; animation: deslizarIzquierda 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s forwards; }
        .anim-left-2 { opacity: 0; animation: deslizarIzquierda 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.4s forwards; }
        .anim-left-3 { opacity: 0; animation: deslizarIzquierda 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.6s forwards; }

        .anim-up-1 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.3s forwards; }
        .anim-up-2 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.5s forwards; }
        .anim-up-3 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.7s forwards; }
        .anim-up-4 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.9s forwards; }
        .anim-up-5 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 1.1s forwards; }
        .anim-up-6 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 1.3s forwards; }
        .anim-up-7 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 1.5s forwards; }
        .anim-up-8 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 1.7s forwards; }
        .anim-up-9 { opacity: 0; animation: deslizarArriba 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 1.9s forwards; }
    </style>
</head>
<body>

    <video autoplay muted loop playsinline class="bg-video-full">
        <source src="{{ asset('img/video-fondo.mp4') }}" type="video/mp4">
    </video>
    <div class="bg-overlay"></div>

    <div class="auth-container">
        
        <div class="auth-left">
            <div class="auth-left-overlay"></div>
            <div class="auth-left-content">
                <div class="auth-logo-icon anim-left-1">
                    <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 110px; filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255,255,255,0.3)); margin-bottom: 15px;">
                </div>
                <div class="auth-title-brand anim-left-2">OSWA INV</div>
                <div class="auth-subtitle-brand anim-left-3">Sistema de Gestión Exclusivo</div>
            </div>
        </div>

        <div class="auth-right">
            
            <div class="mobile-brand">
                <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ">
                <span class="brand-name">OSWA INV</span>
            </div>
            
            <div class="auth-header text-center mb-3 anim-up-1">
                <h2 class="fw-bold text-white mb-1" style="font-size: 1.7rem;">Crear Cuenta</h2>
                <p class="text-secondary" style="font-size: 0.85rem;">Registre sus datos para acceder al sistema</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="w-100 mx-auto" style="max-width: 400px;">
                @csrf
                
                <div class="form-group mb-2 anim-up-2">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Nombre Real</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Ej. Yorgelys Blanco" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-2 anim-up-3">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Apodo / Nick</label>
                    <input type="text" id="nick" name="nick" class="form-control" placeholder="Ej. yorgelys23" value="{{ old('nick') }}">
                    @error('nick')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-2 anim-up-4">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@unellez.edu.ve" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-2 anim-up-5">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                    @error('password')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3 anim-up-6">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita contraseña" required>
                </div>

                <button type="submit" class="btn btn-danger btn-auth fw-bold w-100 mb-3 py-2 anim-up-7" style="background-color: #E50914; border-color: #E50914; font-size: 1rem; border-radius: 8px; letter-spacing: 0.5px;">
                    <i class="bi bi-person-plus me-2"></i> Registrarse
                </button>

                <div class="auth-footer text-center text-secondary mb-4 anim-up-8" style="font-size: 0.95rem;">
                    ¿Ya tiene cuenta? <a href="{{ route('login') }}" class="text-white fw-bold text-decoration-none hover-red">Iniciar Sesión</a>
                </div>
            </form>

            <div class="auth-copyright text-center mt-auto pt-4 border-top border-secondary border-opacity-25 w-100 mx-auto anim-up-9" style="font-size: 0.75rem; color: #666; max-width: 420px;">
                &copy; <script>document.write(new Date().getFullYear())</script> <strong class="text-white">OSWA Inv</strong>. Todos los derechos reservados.<br>
                Desarrollado con <i class="bi bi-code-slash text-secondary mx-1"></i> y <i class="bi bi-heart-fill text-danger mx-1"></i> por <strong class="text-white">Carlos Braca & Yorgelys Blanco</strong><br>
                <span class="mt-1 d-block">Ingeniería en Informática — V Semestre | <strong class="text-white" style="letter-spacing: 0.5px;">UNELLEZ</strong></span>
            </div>
        </div>
    </div>
</body>
</html>
