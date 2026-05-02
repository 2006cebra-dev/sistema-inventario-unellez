<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nueva Requisición - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-main: #121212;
            --bg-card: #1c1c1c;
            --n-red: #E50914;
            --n-border: #2b2b2b;
            --bg-dark: #121212;
            --bg-input: #2a2a2a;
            --border-color: #2b2b2b;
            --text-primary: #e5e5e5;
            --text-secondary: #a3a3a3;
            --accent-primary: #E50914;
            --accent-success: #00b894;
            --accent-danger: #e74c3c;
            --accent-warning: #fdcb6e;
            --accent-info: #0984e3;
            --topbar-height: 68px;
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }

        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height);
            background: linear-gradient(to bottom, rgba(18,18,18,0.85) 0%, rgba(18,18,18,0) 100%);
            backdrop-filter: blur(10px);
            border: none !important;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4%; z-index: 999;
        }
        .topbar-left { display: flex; align-items: center; gap: 2rem; }
        .topbar-logo { white-space: nowrap; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .topbar-logo .logo-text {
            background: linear-gradient(90deg, #E50914, #ff6b6b, #B20710, #E50914);
            background-size: 300% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: rgbText 4s ease infinite;
        }
        @keyframes rgbText { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .logo-nav-unellez {
            height: 35px; filter: brightness(0) invert(1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; margin-right: 10px;
        }
        .logo-nav-unellez:hover { transform: scale(1.2); filter: brightness(0) invert(1) drop-shadow(0 0 8px rgba(255, 255, 255, 0.8)); }

        .topbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .topbar-nav a { color: #b3b3b3; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease; position: relative; padding: 4px 0; }
        .topbar-nav a:hover, .topbar-nav a.active { color: #ffffff; }
        .topbar-nav a.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: var(--accent-primary); border-radius: 1px; }

        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .theme-toggle { background: none; border: none; color: #b3b3b3; font-size: 1.2rem; cursor: pointer; transition: color 0.2s; }
        .theme-toggle:hover { color: #fff; }

        .user-dropdown { position: relative; cursor: pointer; }
        .user-avatar { width: 32px; height: 32px; border-radius: 4px; background: #E50914; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem; }
        .dropdown-menu-netflix { position: absolute; top: calc(100% + 10px); right: 0; width: 260px; background: rgba(20, 20, 20, 0.95); backdrop-filter: blur(10px); border: 1px solid #333; border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); z-index: 9999; display: none; overflow: hidden; }
        .dropdown-menu-netflix.show { display: block; }
        .dropdown-header { padding: 15px; border-bottom: 1px solid #333; }
        .dd-name { color: #fff; font-weight: bold; }
        .dd-email { color: #888; font-size: 0.8rem; }
        .dd-role { color: #E50914; font-size: 0.7rem; font-weight: bold; margin-top: 4px; text-transform: uppercase; }
        .dd-item { width: 100%; display: flex; align-items: center; gap: 10px; padding: 12px 15px; background: none; border: none; color: #ccc; font-size: 0.85rem; text-decoration: none; cursor: pointer; transition: background 0.2s; }
        .dd-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .dd-item.text-danger:hover { background: rgba(229,9,20,0.15); color: #E50914; }

        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }

        .req-container { max-width: 800px; margin: 0 auto; }
        .req-header { text-align: center; margin-bottom: 2rem; }
        .req-header h2 { font-size: 1.8rem; font-weight: 700; color: #fff; }
        .req-header p { color: #888; }

        .req-card {
            background: var(--bg-card);
            border: 1px solid var(--n-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        .req-card:hover { border-color: rgba(229,9,20,0.3); }

        .req-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid var(--n-border); }
        .req-item:last-child { border-bottom: none; }
        .req-item-info { flex: 1; }
        .req-item-name { font-weight: 600; color: #fff; }
        .req-item-stock { font-size: 0.8rem; color: #888; }
        .req-item-qty { display: flex; align-items: center; gap: 0.5rem; }
        .qty-btn {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid #444; background: #2a2a2a;
            color: #fff; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
        }
        .qty-btn:hover { background: #E50914; border-color: #E50914; }
        .qty-input {
            width: 60px; text-align: center; background: #2a2a2a; border: 1px solid #444; border-radius: 6px;
            color: #fff; padding: 6px; font-size: 0.9rem;
        }
        .qty-input:focus { outline: none; border-color: #E50914; }

        .req-search { position: relative; margin-bottom: 1rem; }
        .req-search input {
            width: 100%; padding: 12px 16px 12px 42px; background: #2a2a2a; border: 1px solid #444;
            border-radius: 8px; color: #fff; font-size: 0.9rem;
        }
        .req-search input::placeholder { color: #666; }
        .req-search input:focus { outline: none; border-color: #E50914; }
        .req-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #666; }

        .btn-submit-req {
            background: linear-gradient(135deg, #E50914, #ff6b6b);
            color: #fff; border: none; padding: 12px 30px; border-radius: 8px;
            font-weight: 600; font-size: 1rem; cursor: pointer; width: 100%;
            transition: all 0.3s;
        }
        .btn-submit-req:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(229,9,20,0.4); }
        .btn-submit-req:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

        .req-empty { text-align: center; padding: 3rem; color: #666; }
        .req-empty i { font-size: 3rem; margin-bottom: 1rem; display: block; }

        .footer-req { text-align: center; padding: 2rem 0; color: #555; font-size: 0.8rem; }
    </style>
</head>
<body>
    <nav class="topbar">
        <div class="topbar-left">
            <div class="topbar-logo">
                <img src="{{ asset('img/logo-unellez.png') }}" class="logo-nav-unellez" alt="Logo">
                <span class="logo-text">OSWA Inv</span>
            </div>
        </div>

        <div class="topbar-nav">
            <a href="{{ route('inventario') }}">Dashboard</a>
            <a href="{{ route('catalogo') }}">Catálogo</a>
            <a href="{{ route('requisiciones.crear') }}" class="active">Requisición</a>
        </div>

        <div class="topbar-right">
            <button class="theme-toggle" onclick="toggleTheme()" title="Modo claro/oscuro"><i class="bi bi-moon-fill"></i></button>
            <div class="user-dropdown" id="userDropdown">
                <div class="d-flex align-items-center gap-2" onclick="toggleUserDropdown()">
                    @if(auth()->user()?->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #333;">
                    @else
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <i class="bi bi-caret-down-fill" style="color:#888;font-size:0.7rem;"></i>
                </div>
                <div class="dropdown-menu-netflix" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dd-name">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                        <div class="dd-email">{{ auth()->user()?->email ?? 'Sin correo' }}</div>
                        <div class="dd-role">{{ auth()->user()?->rol ?? 'empleado' }}</div>
                    </div>
                    <a href="{{ route('inventario') }}" class="dd-item"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 w-100">
                        @csrf
                        <button type="submit" class="dd-item text-danger w-100 text-start" style="background: none; border: none; cursor: pointer; padding: 12px 15px;">
                            <i class="bi bi-box-arrow-right"></i> Cambiar Usuario / Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <main class="main-content">
        <div class="req-container">
            <div class="req-header">
                <h2><i class="bi bi-cart-plus" style="color: #E50914;"></i> Nueva Requisición</h2>
                <p>Selecciona los productos y cantidades que necesitas</p>
            </div>

            <div class="req-search">
                <i class="bi bi-search"></i>
                <input type="text" id="searchProductos" placeholder="Buscar producto por nombre...">
            </div>

            <div class="req-card" id="listaProductos">
                @forelse($productos as $prod)
                <div class="req-item" data-nombre="{{ strtolower($prod->nombre) }}">
                    <div class="req-item-info">
                        <div class="req-item-name">{{ $prod->nombre }}</div>
                        <div class="req-item-stock">Stock disponible: <strong style="color: #00b894;">{{ $prod->stock }}</strong> unidades</div>
                    </div>
                    <div class="req-item-qty">
                        <button class="qty-btn" onclick="cambiarCantidad(this, -1)">−</button>
                        <input type="number" class="qty-input" name="productos[{{ $prod->id }}]" value="0" min="0" max="{{ $prod->stock }}" data-max="{{ $prod->stock }}">
                        <button class="qty-btn" onclick="cambiarCantidad(this, 1)">+</button>
                    </div>
                </div>
                @empty
                <div class="req-empty">
                    <i class="bi bi-box-seam"></i>
                    <p>No hay productos disponibles para solicitar.</p>
                </div>
                @endforelse
            </div>

            <button class="btn-submit-req" id="btnEnviar" onclick="enviarRequisicion()" disabled>
                <i class="bi bi-send-fill me-2"></i> Enviar Requisición
            </button>
        </div>
    </main>

    <footer class="footer-req">
        &copy; <script>document.write(new Date().getFullYear())</script> OSWA Inv — Sistema de Inventario
    </footer>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function toggleUserDropdown() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }

        function cambiarCantidad(btn, delta) {
            const input = btn.parentElement.querySelector('.qty-input');
            let val = parseInt(input.value) || 0;
            const max = parseInt(input.dataset.max);
            val = Math.max(0, Math.min(val + delta, max));
            input.value = val;
            actualizarBotonEnviar();
        }

        function actualizarBotonEnviar() {
            const inputs = document.querySelectorAll('.qty-input');
            let total = 0;
            inputs.forEach(inp => total += parseInt(inp.value) || 0);
            document.getElementById('btnEnviar').disabled = total === 0;
        }

        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', function() {
                let val = parseInt(this.value) || 0;
                const max = parseInt(this.dataset.max);
                if (val < 0) this.value = 0;
                if (val > max) this.value = max;
                actualizarBotonEnviar();
            });
        });

        document.getElementById('searchProductos').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('.req-item').forEach(item => {
                const nombre = item.dataset.nombre;
                item.style.display = nombre.includes(filtro) ? 'flex' : 'none';
            });
        });

        function enviarRequisicion() {
            const inputs = document.querySelectorAll('.qty-input');
            const requisiciones = [];
            inputs.forEach(inp => {
                const cant = parseInt(inp.value) || 0;
                if (cant > 0) {
                    const prodId = inp.name.match(/\[(\d+)\]/)[1];
                    requisiciones.push({ producto_id: prodId, cantidad: cant });
                }
            });

            if (requisiciones.length === 0) return;

            Swal.fire({
                title: 'Enviando Requisición...',
                text: `Solicitando ${requisiciones.length} producto(s)`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const enviarSiguiente = (index) => {
                if (index >= requisiciones.length) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Requisición Enviada!',
                        text: 'Tu solicitud ha sido registrada correctamente.',
                        confirmButtonColor: '#E50914'
                    }).then(() => { window.location.href = '{{ route("catalogo") }}'; });
                    return;
                }

                fetch('{{ route("requisiciones.solicitar") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(requisiciones[index])
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        enviarSiguiente(index + 1);
                    } else {
                        Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                    }
                }).catch(err => {
                    Swal.fire('Error', 'Error de conexión', 'error');
                });
            };

            enviarSiguiente(0);
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                document.getElementById('userDropdownMenu').classList.remove('show');
            }
        });
    </script>
</body>
</html>
