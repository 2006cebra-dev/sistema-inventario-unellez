<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSWA-INV | Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0f172a;
            --primary: #3b82f6;
            --accent: #10b981;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #f8fafc;
        }

        /* 🪄 FONDOS ANIMADOS (ORBES DE LUZ) */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 10s infinite ease-in-out alternate;
            z-index: 0;
        }
        .orb-1 {
            width: 400px; height: 400px;
            background: #3b82f6;
            top: -10%; left: -10%;
        }
        .orb-2 {
            width: 300px; height: 300px;
            background: #8b5cf6;
            bottom: -10%; right: -5%;
            animation-delay: -5s;
        }
        .orb-3 {
            width: 250px; height: 250px;
            background: #10b981;
            bottom: 20%; left: 20%;
            animation-delay: -2s;
            opacity: 0.2;
        }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }

        /* 🪟 TARJETA DE CRISTAL (GLASSMORPHISM) */
        .glass-login {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            z-index: 1;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ✍️ INPUTS MODERNOS */
        .form-floating .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 12px;
        }
        .form-floating .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            color: white;
        }
        .form-floating label { color: #94a3b8; }
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary);
            background: transparent;
        }

        /* 🔘 BOTÓN NEÓN */
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* LOGO */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-right: 10px;
            filter: drop-shadow(0 0 10px rgba(59,130,246,0.5));
        }
        .logo-text {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(to right, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="glass-login">
        
        <div class="logo-container">
            <i class="bi bi-boxes logo-icon"></i>
            <span class="logo-text">OSWA-INV</span>
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold text-white">Bienvenido de nuevo</h5>
            <p class="text-muted small">Ingresa tus credenciales para acceder al panel de control y auditoría.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-25 border-0 text-danger border-start border-danger border-4 rounded-3 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" value="{{ old('email') }}" required autofocus>
                <label for="email"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                <label for="password"><i class="bi bi-lock me-2"></i>Contraseña Segura</label>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 small">
                <div class="form-check">
                    <input class="form-check-input bg-transparent border-secondary" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted" for="remember">
                        Recordarme
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary text-decoration-none">¿Olvidaste tu clave?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3 text-uppercase">
                Ingresar al Sistema <i class="bi bi-arrow-right ms-2"></i>
            </button>

            <div class="text-center mt-4">
                <p class="text-muted small mb-0">Protegido con Criptografía SHA-256 <i class="bi bi-shield-check text-success"></i></p>
            </div>
        </form>
    </div>

</body>
</html>