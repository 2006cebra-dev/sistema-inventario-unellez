<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Despacho Rápido - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root { --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b; }
        * { font-family: 'Inter', sans-serif; }
        body, html { overflow-x: hidden !important; max-width: 100vw; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #B20710; border-radius: 10px; border: 2px solid #0a0a0a; }
        ::-webkit-scrollbar-thumb:hover { background: #E50914; }

        #reader { border: none !important; background: transparent; padding: 0; }
        #reader video { border-radius: 12px; object-fit: cover; }
        #reader__dashboard_section_csr span { color: #a3a3a3 !important; font-size: 0.9rem; }
        #reader__dashboard_section_swaplink { color: #E50914 !important; text-decoration: none; font-weight: bold; }
        #reader button { background: #E50914 !important; color: white !important; border: none !important; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 10px; margin-bottom: 10px; font-size: 0.9rem; }
        #reader button:hover { background: #ff0f1b !important; transform: translateY(-2px); }
        #reader select { background: #222 !important; color: white !important; border: 1px solid #444 !important; border-radius: 8px; padding: 8px; outline: none; margin-bottom: 12px; max-width: 100%; font-size: 0.9rem; }
        #reader a { color: #E50914 !important; }
        #html5-qrcode-anchor-scan-type-change { color: #E50914 !important; text-decoration: none; }

        .cart-item { display: flex; justify-content: space-between; align-items: center; background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; transition: border-color 0.3s, box-shadow 0.3s; }
        .cart-item:hover { border-color: #E50914; box-shadow: 0 0 20px rgba(229,9,20,0.08); }
        .qty-controls { display: flex; align-items: center; background: #222; border-radius: 8px; border: 1px solid #333; overflow: hidden; }
        .qty-btn { background: none; border: none; color: white; padding: 6px 14px; font-weight: bold; cursor: pointer; transition: background 0.2s; font-size: 1.1rem; }
        .qty-btn:hover { background: rgba(229,9,20,0.2); }
        .qty-input { width: 40px; background: transparent; border: none; color: white; text-align: center; font-weight: bold; outline: none; }

        .scan-box { background: #0d0d0d; border-radius: 12px; overflow: hidden; border: 1px solid #2a2a2a; min-height: 300px; position: relative; display: flex; align-items: center; justify-content: center; transition: border-color 0.3s; }
        .scan-box:focus-within { border-color: #E50914; box-shadow: 0 0 25px rgba(229,9,20,0.1); }

        .panel-card { background: #141414; border: 1px solid #2b2b2b; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }

        .btn-danger-nf { background: #E50914; border: none; border-radius: 8px; font-weight: 700; padding: 14px; transition: all 0.3s; }
        .btn-danger-nf:hover { background: #ff0f1b; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(229,9,20,0.3); }
        .btn-danger-nf:disabled { background: #333; transform: none; box-shadow: none; }

        .input-nf { background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 8px; padding: 12px 16px; transition: border-color 0.3s; }
        .input-nf:focus { border-color: #E50914; background: #222; color: #fff; box-shadow: 0 0 0 3px rgba(229,9,20,0.1); }

        @media (max-width: 768px) {
            .cart-item { flex-direction: column; align-items: flex-start; gap: 12px; }
            .qty-controls { width: 100%; justify-content: space-between; padding: 5px 0; }
            .qty-btn { font-size: 1.2rem; padding: 5px 20px; background: rgba(255,255,255,0.05); border-radius: 6px; }
        }
        @media (max-width: 480px) {
            #reader video { max-height: 250px; }
        }
    </style>
</head>
<body>

    @include('partials.navbar')

<div class="main-content container-fluid pb-5">

    <!-- ENCABEZADO -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3 border-bottom border-secondary border-opacity-50 gap-2">
        <div>
            <h2 class="text-white fw-bold mb-0 d-flex align-items-center gap-2">
                <span class="bg-danger bg-opacity-10 p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-upc-scan fs-5 text-danger"></i>
                </span>
                Módulo de Despacho
            </h2>
            <p class="text-secondary mt-1 mb-0 ms-1">Escanea o ingresa el código para registrar la salida</p>
        </div>
        <span class="badge bg-dark border border-secondary px-3 py-2 text-secondary">
            <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Terminal Activa
        </span>
    </div>

    <div class="row g-4">

        <!-- PANEL IZQUIERDO: CÁMARA + MANUAL -->
        <div class="col-lg-5">
            <div class="panel-card p-4 h-100">
                <div class="mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-camera-video text-danger"></i>
                    <span class="text-white fw-semibold">Escáner de Códigos</span>
                </div>

                <div class="scan-box mb-3">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <div class="mt-3">
                    <label class="text-secondary small fw-bold mb-2">
                        <i class="bi bi-keyboard me-1"></i>Ingreso Manual de Código
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white"><i class="bi bi-upc"></i></span>
                        <input type="text" class="form-control input-nf border-secondary" placeholder="Escriba el código..." id="manualInput">
                        <button class="btn btn-danger fw-bold px-4" type="button" onclick="buscarPorCodigo()">
                            <i class="bi bi-plus-lg me-1"></i>Añadir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL DERECHO: LISTA DE SALIDA -->
        <div class="col-lg-7">
            <div class="panel-card p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary">
                    <h5 class="text-white fw-bold mb-0">
                        <i class="bi bi-cart-dash text-warning me-2"></i>Lista de Salida
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="vaciarCarrito()">
                        <i class="bi bi-trash me-1"></i>Limpiar
                    </button>
                </div>

                <div class="flex-grow-1 overflow-auto pe-1 mb-3" style="min-height: 350px; max-height: 450px;" id="cartList">
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-secondary">
                        <div class="d-flex align-items-center justify-content-center border border-secondary rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-box-seam text-secondary" style="font-size: 2.5rem;"></i>
                        </div>
                        <p class="mt-3 fw-bold text-white mb-1">No hay productos en la lista</p>
                        <span class="small">Escanea productos para agregarlos al despacho</span>
                    </div>
                </div>

                <div class="border-top border-secondary pt-4 mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                        <span class="text-secondary fw-semibold fs-5">Total de Artículos:</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fs-6 fw-bold" id="totalItems">0</span>
                        </div>
                    </div>
                    <button class="btn btn-danger-nf w-100 fs-5" id="btnProcesar" onclick="procesarDespacho()" disabled>
                        <i class="bi bi-check2-circle me-2"></i>Procesar Salida
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

    <script>
        const productosDB = @json($productos);
        let carrito = [];
        let html5QrcodeScanner;

        document.addEventListener('DOMContentLoaded', () => {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 100 } }, false);
            html5QrcodeScanner.render(onScanSuccess);

            document.getElementById('manualInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') buscarPorCodigo();
            });
        });

        function onScanSuccess(decodedText) {
            agregarAlCarrito(decodedText);
            html5QrcodeScanner.pause();
            setTimeout(() => { html5QrcodeScanner.resume(); }, 1500);
        }

        function buscarPorCodigo() {
            const code = document.getElementById('manualInput').value.trim();
            if(code) agregarAlCarrito(code);
            document.getElementById('manualInput').value = '';
            document.getElementById('manualInput').focus();
        }

        function agregarAlCarrito(codigo) {
            const prefijoPais = codigo.substring(0, 3);
            let mensajeBusqueda = 'Verificando base de datos...';
            let tituloBusqueda = 'Buscando...';

            if (prefijoPais === '759') {
                tituloBusqueda = 'Producto Venezolano';
                mensajeBusqueda = 'Detectado prefijo 759. Buscando producto nacional...';
            }

            Swal.fire({ 
                title: tituloBusqueda, 
                text: mensajeBusqueda, 
                allowOutsideClick: false, 
                didOpen: () => { Swal.showLoading() }, 
                background: '#1c1c1c', 
                color: '#fff' 
            });
            const prod = productosDB.find(p => p.codigo === codigo || p.codigo_barras === codigo || p.id == codigo);
            Swal.close();
            
            if (!prod) {
                mostrarToast('Producto no encontrado', 'bi bi-exclamation-triangle-fill');
                return;
            }

            const index = carrito.findIndex(item => item.id === prod.id);
            
            if (index !== -1) {
                if (carrito[index].cantidad < prod.stock) {
                    carrito[index].cantidad++;
                } else {
                    mostrarToast('Stock máximo alcanzado', 'bi bi-exclamation-triangle-fill');
                    return;
                }
            } else {
                carrito.unshift({ id: prod.id, codigo: prod.codigo, nombre: prod.nombre, stock: prod.stock, cantidad: 1 });
            }
            
            reproducirBeep();
            renderizarCarrito();
        }

        function cambiarCantidad(id, delta) {
            const index = carrito.findIndex(item => item.id === id);
            if (index === -1) return;

            const nuevaCant = carrito[index].cantidad + delta;
            
            if (nuevaCant === 0) {
                carrito.splice(index, 1);
            } else if (nuevaCant <= carrito[index].stock) {
                carrito[index].cantidad = nuevaCant;
            } else {
                mostrarToast('Stock máximo', 'bi bi-exclamation-triangle-fill');
            }
            renderizarCarrito();
        }

        function renderizarCarrito() {
            const container = document.getElementById('cartList');
            const totalItemsSpan = document.getElementById('totalItems');
            const btnProcesar = document.getElementById('btnProcesar');
            
            if (carrito.length === 0) {
                container.innerHTML = '<div class="h-100 d-flex flex-column align-items-center justify-content-center text-secondary"><div class="d-flex align-items-center justify-content-center border border-secondary rounded-circle" style="width: 80px; height: 80px;"><i class="bi bi-box-seam text-secondary" style="font-size: 2.5rem;"></i></div><p class="mt-3 fw-bold text-white mb-1">No hay productos en la lista</p><span class="small">Escanea productos para agregarlos al despacho</span></div>';
                totalItemsSpan.innerText = '0';
                btnProcesar.disabled = true;
                return;
            }

            let html = '';
            let totalQty = 0;

            carrito.forEach(item => {
                totalQty += item.cantidad;
                html += `
                    <div class="cart-item">
                        <div>
                            <h6 class="text-white mb-1 fw-bold">${item.nombre}</h6>
                            <div class="d-flex gap-3">
                                <small class="text-secondary font-monospace"><i class="bi bi-upc"></i> ${item.codigo}</small>
                                <small class="text-info"><i class="bi bi-box"></i> Disp: ${item.stock}</small>
                            </div>
                        </div>
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="cambiarCantidad(${item.id}, -1)">−</button>
                            <input type="text" class="qty-input" value="${item.cantidad}" readonly>
                            <button class="qty-btn" onclick="cambiarCantidad(${item.id}, 1)">+</button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            totalItemsSpan.innerText = totalQty;
            btnProcesar.disabled = false;
        }

        function vaciarCarrito() { carrito = []; renderizarCarrito(); }

        function procesarDespacho() {
            if (carrito.length === 0) return;

            Swal.fire({
                title: '¿Confirmar Salida?',
                text: 'Se registrarán estas salidas de forma encriptada.',
                icon: 'warning',
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'No',
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#444',
                background: '#141414',
                color: '#fff',
                position: 'top-end',
                toast: true,
                showConfirmButton: true,
                showCancelButton: true,
                customClass: { popup: 'oswa-confirm-toast' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.getElementById('btnProcesar');
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
                    btn.disabled = true;

                    fetch('{{ route("despacho.procesar") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ items: carrito.map(i => ({ id: i.id, cantidad: i.cantidad })) })
                    }).then(res => res.json()).then(resData => {
                        if (resData.success) {
                            mostrarToast(resData.message, 'bi bi-check-circle-fill');
                            setTimeout(() => { location.reload(); }, 800);
                        } else {
                            mostrarToast(resData.message || 'Error al procesar', 'bi bi-exclamation-triangle-fill');
                            btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Procesar Salida'; btn.disabled = false;
                        }
                    }).catch(() => {
                        mostrarToast('Error de conexión con el servidor', 'bi bi-exclamation-triangle-fill');
                        btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Procesar Salida'; btn.disabled = false;
                    });
                }
            });
        }

        function reproducirBeep() {
            try {
                const context = new (window.AudioContext || window.webkitAudioContext)();
                const osc = context.createOscillator();
                const gain = context.createGain();
                osc.type = 'sine'; osc.frequency.value = 1200;
                gain.gain.setValueAtTime(0.1, context.currentTime);
                osc.connect(gain); gain.connect(context.destination);
                osc.start(); osc.stop(context.currentTime + 0.1);
            } catch (e) {}
        }
    </script>

    <script>
        function checkNetworkStatus() {
            const isOnline = navigator.onLine;
            const btnProcesar = document.getElementById('btnProcesar');

            if (!isOnline) {
                if (btnProcesar) btnProcesar.disabled = true;
            } else {
                if (btnProcesar && typeof carrito !== 'undefined' && carrito.length > 0) btnProcesar.disabled = false;
            }
        }

        window.addEventListener('online', checkNetworkStatus);
        window.addEventListener('offline', checkNetworkStatus);
        document.addEventListener('DOMContentLoaded', checkNetworkStatus);
    </script>
</body>
</html>