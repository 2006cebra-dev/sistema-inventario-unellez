<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - OSWA Inv</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --accent: #E50914; --bg-main: #050505; --bg-card: #121212; --input-bg: #1e1e1e; }

        * { box-sizing: border-box; }

        body {
            background: var(--bg-main);
            font-family: 'Inter', sans-serif;
            height: 100vh; display: flex; align-items: center; justify-content: center;
            margin: 0; color: #fff; overflow: hidden;
        }

        .bg-animated {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -2;
            background: radial-gradient(ellipse at 20% 50%, #1a0000 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 50%, #0d0d0d 0%, transparent 50%),
                        linear-gradient(135deg, #050505 0%, #111 50%, #0a0a0a 100%);
        }
        .bg-animated::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(2px 2px at 20% 30%, rgba(229,9,20,0.15), transparent),
                        radial-gradient(2px 2px at 80% 70%, rgba(229,9,20,0.1), transparent);
        }

        .auth-container {
            display: flex; width: 1000px; max-width: 92vw; height: 80vh; min-height: 620px;
            background: rgba(18, 18, 18, 0.92);
            border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            border: 1px solid rgba(255,255,255,0.05);
            opacity: 0; animation: fadeIn 0.5s ease-out forwards;
        }

        .auth-left {
            flex: 1; position: relative; display: flex; flex-direction: column;
            align-items: center; justify-content: center; text-align: center;
            border-right: 1px solid rgba(255,255,255,0.05);
            background-image: url('{{ asset("img/fondo-login.jpg") }}');
            background-position: center; background-size: cover; background-repeat: no-repeat;
        }
        .auth-left-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.3), rgba(18,18,18,0.92));
            z-index: 0;
        }
        .auth-left-content { position: relative; z-index: 1; padding: 3rem; }
        .auth-left-content { position: relative; z-index: 1; padding: 3rem; }
        .auth-title-brand { font-size: 2.8rem; font-weight: 800; letter-spacing: 5px; color: #fff; margin-bottom: 0.5rem; }
        .auth-subtitle-brand { font-size: 0.95rem; letter-spacing: 2px; color: #999; text-transform: uppercase; }

        .auth-right {
            flex: 1; padding: 2.5rem 3.5rem; display: flex; flex-direction: column;
            justify-content: center; background: transparent;
        }

        .form-control {
            background-color: var(--input-bg); border: 1px solid #333; color: #fff;
            border-radius: 8px; transition: border-color 0.3s; box-shadow: none;
        }
        .form-control:focus { background-color: #252525; border-color: var(--accent); box-shadow: none; color: #fff; }
        .form-control::placeholder { color: #666; }

        .btn-auth { transition: transform 0.2s, box-shadow 0.2s; }
        .btn-auth:hover {
            background-color: #b20710 !important; border-color: #b20710 !important;
            transform: translateY(-1px); box-shadow: 0 6px 18px rgba(229, 9, 20, 0.3);
        }
        .hover-white:hover { color: #fff !important; }
        .hover-red:hover { color: var(--accent) !important; text-decoration: underline !important; }

        .mobile-brand { display: none; }

        @media (max-width: 992px) {
            body { overflow-y: auto; height: auto; align-items: flex-start; padding: 0; }
            .auth-container { flex-direction: column; height: auto; min-height: 100vh; max-width: 100vw; width: 100%; border-radius: 0; border: none; animation: none; opacity: 1; }
            .auth-left { display: none; }
            .auth-right { padding: 2rem 1.5rem; flex: none; width: 100%; }
            .auth-header h2 { font-size: 1.4rem; }
            .auth-header p { font-size: 0.85rem; }
            .form-control { font-size: 16px; padding: 10px 14px; }
            .btn-auth { padding: 12px; font-size: 1rem; }
            .mobile-brand { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 1.5rem; }
            .mobile-brand .brand-name { font-size: 1.6rem; font-weight: 800; background: linear-gradient(90deg,#E50914,#ff6b6b,#B20710,#E50914); background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 2px; }
            [class*="anim-"] { opacity: 1 !important; animation: none !important; }
        }

        @keyframes fadeIn { to { opacity: 1; } }
        @keyframes slideUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes slideLeft { to { opacity: 1; transform: translateX(0); } }

        .anim-l { opacity: 0; transform: translateX(30px); animation: slideLeft 0.6s ease-out forwards; }
        .anim-l2 { opacity: 0; transform: translateX(30px); animation: slideLeft 0.6s ease-out 0.15s forwards; }

        .anim-1 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.1s forwards; }
        .anim-2 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.2s forwards; }
        .anim-3 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.3s forwards; }
        .anim-4 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.4s forwards; }
        .anim-5 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.5s forwards; }
        .anim-6 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.6s forwards; }
        .anim-7 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.7s forwards; }
        .anim-8 { opacity: 0; transform: translateY(20px); animation: slideUp 0.5s ease-out 0.8s forwards; }
    </style>
</head>
<body>

    <div class="bg-animated"></div>

    <div class="auth-container">

        <div class="auth-left">
            <div class="auth-left-overlay"></div>
            <div class="auth-left-content">
                <div class="auth-title-brand anim-l">OSWA INV</div>
                <div class="auth-subtitle-brand anim-l2">Sistema de Gestión Exclusivo</div>
            </div>
        </div>

        <div class="auth-right">

            <div class="mobile-brand">
                <span class="brand-name">OSWA INV</span>
            </div>

            <div class="auth-header text-center mb-3 anim-1">
                <h2 class="fw-bold text-white mb-1" style="font-size: 1.7rem;">Crear Cuenta</h2>
                <p class="text-secondary" style="font-size: 0.85rem;">Registre sus datos para acceder al sistema</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="w-100 mx-auto" style="max-width: 400px;">
                @csrf

                <div class="form-group mb-2 anim-2">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Nombre Real</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Ej. Yorgelys Blanco" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-2 anim-3">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Apodo / Nick</label>
                    <input type="text" id="nick" name="nick" class="form-control" placeholder="Ej. yorgelys23" value="{{ old('nick') }}">
                    @error('nick')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-2 anim-4">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-2 anim-5">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                    @error('password')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3 anim-6">
                    <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita contraseña" required>
                </div>

                <button type="submit" class="btn btn-danger btn-auth fw-bold w-100 mb-3 py-2 anim-7" style="background-color: #E50914; border-color: #E50914; font-size: 1rem; border-radius: 8px; letter-spacing: 0.5px;">
                    <i class="bi bi-person-plus me-2"></i> Registrarse
                </button>

                <div class="auth-footer text-center text-secondary mb-4 anim-8" style="font-size: 0.95rem;">
                    ¿Ya tiene cuenta? <a href="{{ route('login') }}" class="text-white fw-bold text-decoration-none hover-red">Iniciar Sesión</a>
                </div>
            </form>

            <div class="auth-copyright text-center mt-auto pt-4 border-top border-secondary border-opacity-25 w-100 mx-auto anim-8" style="font-size: 0.75rem; color: #666; max-width: 420px;">
                &copy; <script>document.write(new Date().getFullYear())</script> <strong class="text-white">OSWA Inv</strong>. Todos los derechos reservados.<br>
                Desarrollado con <i class="bi bi-code-slash text-secondary mx-1"></i> y <i class="bi bi-heart-fill text-danger mx-1"></i> por <strong class="text-white">Carlos Braca & Yorgelys Blanco</strong><br>
                <span class="mt-1 d-block">Ingeniería en Informática — V Semestre</span>
            </div>
        </div>
    </div>
</body>
</html>