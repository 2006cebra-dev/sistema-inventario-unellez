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
        }
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.9)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1920') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: var(--bg-card);
            border: none;
            border-radius: 4px;
            box-shadow: 0 0 60px rgba(0,0,0,0.7);
            padding: 3.5rem 2.5rem;
            max-width: 420px;
            width: 100%;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo h1 {
            color: var(--accent-primary);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .login-logo p {
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
        .form-check-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        .form-check-input {
            background-color: var(--bg-input);
            border-color: #555;
            border-radius: 2px;
        }
        .form-check-input:checked {
            background-color: var(--accent-primary);
            border-color: var(--accent-primary);
        }
        .btn-primary {
            background: var(--accent-primary);
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s ease;
        }
        .btn-primary:hover {
            background: #c10711;
            transform: none;
            box-shadow: none;
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
        .register-link {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .register-link a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link a:hover {
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
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" class="logo-auth-clean">
            <h1>OSWA Inv</h1>
            <p>Gestión de Inventario</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
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
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Recordarme
                    </label>
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-door-open me-2"></i>Iniciar Sesión
                </button>
            </div>
            
            @if (Route::has('password.request'))
                <div class="text-center mt-3">
                    <a class="btn-link" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
        </form>
        
        <div class="divider"></div>
        
        <div class="register-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
        </div>
    </div>
</body>
</html>