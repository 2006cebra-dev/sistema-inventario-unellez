<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Requisición - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b;
            --bg-dark: #121212; --bg-input: #2a2a2a; --border-color: #2b2b2b;
            --text-primary: #e5e5e5; --text-secondary: #a3a3a3; --accent-primary: #E50914;
            --accent-success: #00b894; --accent-danger: #e74c3c; --accent-warning: #fdcb6e;
            --topbar-height: 68px;
        }
        [data-theme="light"] {
            --bg-dark: #121212; --bg-card: #1c1c1c; --bg-input: #2a2a2a;
            --border-color: #2b2b2b; --text-primary: #e5e5e5; --text-secondary: #a3a3a3;
        }
        * { font-family: 'Inter', sans-serif; }
        body, html { overflow-x: hidden !important; max-width: 100vw; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }

        .main-content { padding-top: calc(var(--topbar-height) + 2rem); padding-left: 4%; padding-right: 4%; padding-bottom: 2rem; min-height: 100vh; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #B20710; border-radius: 10px; border: 2px solid #0a0a0a; }
        ::-webkit-scrollbar-thumb:hover { background: #E50914; }

        .professional-footer { text-align: center; padding: 1.5rem 4%; margin-top: 2rem; border-top: 1px solid var(--border-color); color: var(--text-secondary); font-size: 0.85rem; }
        .professional-footer span.highlight { color: var(--text-primary); font-weight: 600; }
        .professional-footer .heart-icon { color: var(--accent-danger); animation: heartbeat 1.5s infinite; display: inline-block; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
    </style>
</head>
<body data-theme="dark">

@include('partials.navbar')

<main class="main-content container-fluid pb-5" style="padding-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white fw-bold mb-0"><i class="bi bi-cart-check text-warning me-2"></i>Mi Requisición</h2>
            <p class="text-secondary mt-1 mb-0">Confirma las cantidades de los productos que vas a solicitar</p>
        </div>
        <button class="btn btn-outline-danger" onclick="vaciarCarrito()"><i class="bi bi-trash me-2"></i>Vaciar Lista</button>
    </div>

    <form action="{{ route('requisiciones.solicitar') }}" method="POST" id="formRequisicion">
        @csrf
        <div class="card bg-transparent border-0">
            <div class="card-body p-4 rounded-4" style="background-color: #141414; border: 1px solid #2b2b2b; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">

                <div id="contenedor-carrito">
                </div>

                <div id="carrito-vacio" class="text-center py-5 d-none">
                    <i class="bi bi-cart-x text-secondary" style="font-size: 4rem;"></i>
                    <h4 class="text-white mt-3">Tu lista está vacía</h4>
                    <p class="text-secondary">Vuelve al catálogo para seleccionar los productos que necesitas.</p>
                    <a href="{{ route('catalogo') }}" class="btn btn-warning mt-3 fw-bold"><i class="bi bi-arrow-left me-2"></i>Volver al Catálogo</a>
                </div>

                <div class="border-top border-secondary pt-4 mt-4 text-end" id="footer-carrito">
                    <button type="submit" class="btn btn-warning px-5 py-3 fw-bold fs-5" style="border-radius: 8px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="bi bi-send-fill me-2"></i>Enviar Solicitud al Administrador
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<!-- FOOTER GLOBAL OSWA INV -->
<footer class="professional-footer mt-5 pt-4 pb-4" style="text-align: center; border-top: 1px solid #2b2b2b; color: #a3a3a3; font-size: 0.85rem; background-color: transparent;">
    <div class="mb-2">
        &copy; <script>document.write(new Date().getFullYear())</script> <strong class="text-white">OSWA Inv</strong>. Todos los derechos reservados.
    </div>
    <div class="mb-2">
        Desarrollado con <i class="bi bi-code-slash text-secondary"></i> y <i class="bi bi-heart-fill text-danger"></i> por <span class="text-white fw-bold">Carlos Braca & Yorgelys Blanco</span>
    </div>
    <div class="d-flex align-items-center justify-content-center gap-3 mt-3" style="font-size: 0.85rem;">
        <span style="color: #888888;">Ingeniería en Informática — V Semestre</span>
        <div style="width: 1px; height: 16px; background-color: #444444;"></div>
        <div class="d-flex align-items-center gap-2">
            <strong class="text-white" style="letter-spacing: 1px;">OSWA Inv</strong>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Configurar el estilo Netflix de los Toasts
        window.Toast = (typeof Swal !== 'undefined') ? Swal.mixin({
            toast: true, 
            position: 'top-end', 
            showConfirmButton: false, 
            timer: 3000, 
            timerProgressBar: true, 
            background: '#141414', 
            color: '#fff',
            customClass: { popup: 'border border-secondary shadow-lg' }
        }) : null;
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        renderizarCarrito();
    });

    function renderizarCarrito() {
        let carrito = JSON.parse(localStorage.getItem('oswa_carrito')) || [];
        const contenedor = document.getElementById('contenedor-carrito');
        const vacio = document.getElementById('carrito-vacio');
        const footer = document.getElementById('footer-carrito');

        contenedor.innerHTML = '';

        if(carrito.length === 0) {
            vacio.classList.remove('d-none');
            footer.classList.add('d-none');
            return;
        }

        vacio.classList.add('d-none');
        footer.classList.remove('d-none');

        carrito.forEach((item, index) => {
            contenedor.innerHTML += `
                <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-dark border border-secondary rounded-3" style="box-shadow: inset 0 0 10px rgba(0,0,0,0.2);">
                    <div>
                        <h5 class="text-white mb-1 fw-bold">${item.nombre}</h5>
                        <span class="badge bg-secondary">Stock actual: ${item.stock}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 140px; border: 1px solid #444; border-radius: 6px; overflow: hidden;">
                            <button type="button" class="btn btn-dark text-white border-0" onclick="cambiarCantidad(${index}, -1)">-</button>
                            <input type="number" name="productos[${item.id}]" class="form-control bg-transparent text-white text-center border-0" value="${item.cantidad}" readonly style="box-shadow: none;">
                            <button type="button" class="btn btn-dark text-white border-0" onclick="cambiarCantidad(${index}, 1)">+</button>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarItem(${index})" title="Quitar producto">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            `;
        });
    }

    function cambiarCantidad(index, delta) {
        let carrito = JSON.parse(localStorage.getItem('oswa_carrito')) || [];
        let nuevaCantidad = carrito[index].cantidad + delta;
        if(nuevaCantidad > 0) {
            carrito[index].cantidad = nuevaCantidad;
            localStorage.setItem('oswa_carrito', JSON.stringify(carrito));
            renderizarCarrito();
        }
    }

    function eliminarItem(index) {
        let carrito = JSON.parse(localStorage.getItem('oswa_carrito')) || [];
        carrito.splice(index, 1);
        localStorage.setItem('oswa_carrito', JSON.stringify(carrito));
        renderizarCarrito();
    }

    function vaciarCarrito() {
        localStorage.removeItem('oswa_carrito');
        renderizarCarrito();
    }

    document.getElementById('formRequisicion')?.addEventListener('submit', function() {
        setTimeout(() => { localStorage.removeItem('oswa_carrito'); }, 500);
    });
</script>

</body>
</html>
