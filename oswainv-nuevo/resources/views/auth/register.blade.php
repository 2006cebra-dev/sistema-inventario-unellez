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
            --accent: #E50914; /* Rojo OSWA Inv */
            --bg-main: #0a0a0a;
            --bg-card: #141414;
            --input-bg: #1f1f1f;
        }
        
        body {
            background-color: var(--bg-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #fff;
            padding: 20px 0;
        }

        .auth-container {
            display: flex;
            width: 1000px;
            max-width: 95%;
            min-height: 650px; /* Un poco más alto para los campos extra */
            background: var(--bg-card);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0,0,0,0.8);
            border: 1px solid #222;
        }

        /* 1. EL VIDEO DE AFUERA */
        .bg-video-full {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            object-fit: cover;
            z-index: -2;
        }
        
        /* Filtro oscuro para el video de afuera */
        .bg-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.6); /* Ajusta si lo quieres más claro o más oscuro */
            z-index: -1;
        }

        /* 2. LA TARJETA COMPLETA */
        .auth-container {
            display: flex;
            width: 1000px;
            max-width: 95%;
            min-height: 650px;
            background: rgba(20, 20, 20, 0.95); /* Casi negro sólido */
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0,0,0,0.8);
            border: 1px solid #222;
            z-index: 10;
            position: relative;
        }

        /* 3. MITAD IZQUIERDA (CON TU FOTO STATIC) */
        .auth-left {
            flex: 1;
            background: url('{{ asset("img/fondo-login.jpg") }}') center/cover;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        
        /* Capa para oscurecer tu foto y resaltar el logo de la Unellez */
        .auth-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.9));
            z-index: 1;
        }

        .auth-left-content {
            position: relative;
            z-index: 2;
            padding: 2rem;
        }

        /* 4. MITAD DERECHA (FORMULARIO) */
        .auth-right {
            flex: 1;
            padding: 3rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: transparent;
        }

        /* Lado Derecho: Formulario (Fondo más sólido para leer bien) */
        .auth-right {
            flex: 1;
            padding: 3rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(20, 20, 20, 0.95);
        }

        .auth-logo-icon { font-size: 3.5rem; color: var(--accent); margin-bottom: 1rem; }
        .auth-title-brand { font-size: 2.2rem; font-weight: 700; letter-spacing: 5px; margin-bottom: 0.5rem; color: #fff; }
        .auth-subtitle-brand { font-size: 0.85rem; letter-spacing: 3px; color: #aaa; text-transform: uppercase; }

        .auth-right {
            flex: 1;
            padding: 3rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-header { margin-bottom: 2rem; }
        .auth-header h2 { font-weight: 600; font-size: 1.8rem; margin-bottom: 0.5rem; }
        .auth-header p { color: #888; font-size: 0.9rem; }

        .form-group { margin-bottom: 1.2rem; }
        .form-label {
            font-size: 0.75rem; color: var(--accent); text-transform: uppercase;
            letter-spacing: 1.5px; font-weight: 600; margin-bottom: 0.5rem; display: block;
        }

        .form-control {
            background-color: var(--input-bg); border: 1px solid #333; color: #fff;
            border-radius: 8px; padding: 0.8rem 1.2rem; font-size: 0.95rem;
            transition: all 0.3s; box-shadow: none;
        }

        .form-control:focus { background-color: #252525; border-color: var(--accent); box-shadow: none; color: #fff; }
        .form-control::placeholder { color: #555; }

        .auth-link { color: var(--accent); text-decoration: none; transition: color 0.2s; }
        .auth-link:hover { color: #ff4757; text-decoration: underline; }

        .btn-auth {
            background-color: var(--accent); color: #fff; border: none; border-radius: 8px;
            padding: 1rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
            width: 100%; transition: all 0.3s; cursor: pointer; margin-top: 1rem;
        }

        .btn-auth:hover { background-color: #b20710; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4); }

        .auth-footer { margin-top: 2rem; text-align: center; font-size: 0.85rem; color: #888; }
        .auth-copyright { margin-top: auto; text-align: center; font-size: 0.75rem; color: #555; padding-top: 1rem; }

        @media (max-width: 900px) {
            .auth-container { flex-direction: column; height: auto; border-radius: 0; width: 100%; }
            .auth-left { flex: none; height: 200px; }
            .auth-right { padding: 2rem; }
        }

        /* =========================================
           ANIMACIONES DE ENTRADA (ESTILO CINE)
           ========================================= */
           
        /* 1. Animación base: Deslizar hacia la izquierda */
        @keyframes deslizarHaciaIzquierda {
            0% {
                opacity: 0;
                transform: translateX(60px); /* Empieza 60px a la derecha */
            }
            100% {
                opacity: 1;
                transform: translateX(0); /* Termina en su posición original */
            }
        }

        /* 2. Animación de la tarjeta principal (Aparecer suave) */
        @keyframes aparecerTarjeta {
            0% { opacity: 0; transform: scale(0.98); }
            100% { opacity: 1; transform: scale(1); }
        }

        .auth-container {
            /* Mantenemos lo que ya tenías y le sumamos la animación */
            animation: aparecerTarjeta 0.6s ease-out forwards;
        }

        /* 3. Aplicar animación en CASCADA a los elementos de la izquierda */
        
        .auth-logo-icon {
            opacity: 0; /* Inicia oculto */
            /* Se ejecuta a los 0.2 segundos */
            animation: deslizarHaciaIzquierda 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s forwards;
        }

        .auth-title-brand {
            opacity: 0; /* Inicia oculto */
            /* Se ejecuta a los 0.4 segundos */
            animation: deslizarHaciaIzquierda 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.4s forwards;
        }

        .auth-subtitle-brand {
            opacity: 0; /* Inicia oculto */
            /* Se ejecuta a los 0.6 segundos */
            animation: deslizarHaciaIzquierda 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.6s forwards;
        }
    </style>
</head>
<body>
    <!-- 1. VIDEO DE FONDO GIGANTE -->
    <video autoplay muted loop playsinline class="bg-video-full">
        <!-- Carlos: Asegúrate de llamar a tu video 'video-fondo.mp4' para no confundirlo con la foto -->
        <source src="{{ asset('img/video-fondo.mp4') }}" type="video/mp4">
    </video>
    <!-- Capa oscura para que el video no encandile -->
    <div class="bg-overlay"></div>

    <!-- 2. LA TARJETA PRINCIPAL -->
    <div class="auth-container">
        
        <!-- MITAD IZQUIERDA: LLEVA LA FOTO POR CSS -->
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="auth-logo-icon">
                    <img src="{{ asset('img/logo-unellez.png') }}" alt="UNELLEZ" style="height: 90px; filter: brightness(0) invert(1) drop-shadow(0 0 10px rgba(255,255,255,0.2)); margin-bottom: 10px;">
                </div>
                <div class="auth-title-brand">OSWA INV</div>
                <div class="auth-subtitle-brand">Sistema de Gestión Exclusivo</div>
            </div>
        </div>

        <!-- MITAD DERECHA: FORMULARIO -->
        <div class="auth-right">
            <div class="auth-header">
                <h2>Crear Cuenta</h2>
                <p>Registre sus datos para acceder al sistema</p>
            </div>

            <!-- Formulario apuntando a la ruta de registro de Laravel -->
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="name">Nombre Completo</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Ej. Yorgelis Blanco" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@unellez.edu.ve" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fila para las contraseñas -->
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                        @error('password')
                            <span class="text-danger" style="font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="form-label" for="password_confirmation">Confirmar</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita contraseña" required>
                    </div>
                </div>

                <button type="submit" class="btn-auth">Registrarse</button>
            </form>

            <div class="auth-footer">
                <span style="color: #666;">¿Ya tiene cuenta?</span> <a href="{{ route('login') }}" class="auth-link fw-bold">Iniciar Sesión</a>
            </div>

            <div class="auth-copyright">
                &copy; {{ date('Y') }} OSWA Inv — Carlos Braca & Yorgelis Blanco
            </div>
        </div>
    </div>

</body>
</html>
