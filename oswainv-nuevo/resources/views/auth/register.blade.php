<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - OSWA Inv</title>
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
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.9)), url('https://images.unsplash.com/photo-1553413077-190dd305871c?w=1920') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: var(--bg-card);
            border: none;
            border-radius: 4px;
            box-shadow: 0 0 60px rgba(0,0,0,0.7);
            padding: 3.5rem 2.5rem;
            max-width: 480px;
            width: 100%;
        }
        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-logo h1 {
            color: var(--accent-primary);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .register-logo p {
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
        .btn-primary {
            background: var(--accent-primary);
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s ease;
            width: 100%;
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
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-logo">
            <h1><i class="bi bi-box-seam"></i> OSWA Inv</h1>
            <p>Crear Cuenta Nueva</p>
        </div>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Juan Pérez">
                
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="correo@ejemplo.com">
                
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                </button>
            </div>
        </form>
        
        <div class="divider"></div>
        
        <div class="login-link">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia Sesión</a>
        </div>
    </div>
</body>
</html>