<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAV - Escáner Inteligente</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; }
        .scanner-container { max-width: 500px; margin: 30px auto; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        #reader { width: 100% !important; border: none !important; border-radius: 15px; overflow: hidden;}
        #reader__scan_region { background: #000; }
        .swal2-input-custom { width: 80%; padding: 10px; margin-top: 10px; border-radius: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <div class="container text-center">
        <div class="scanner-container p-3 border">
            <h3 class="fw-bold text-primary mb-3"><i class="bi bi-upc-scan"></i> Escáner de Inventario</h3>
            <p class="text-muted small">Apunta al código de barras para registrar entrada</p>
            
            <div id="reader" class="shadow-sm"></div>

            <div class="mt-4 pb-2">
                <a href="/" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-house-door"></i> Volver al Panel
                </a>
            </div>
        </div>
        <p class="text-muted small fw-bold">Barinas, Venezuela - UNELLEZ</p>
    </div>

    <script>
        // 🔊 Función para hacer el sonido de "Beep" de supermercado
        function playBeep() {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime); // Frecuencia del pitido
            osc.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.1); // Duración rápida
        }

        let isScanning = true; // Control para evitar lecturas dobles

        function onScanSuccess(decodedText) {
            if(!isScanning) return;
            isScanning = false; // Bloqueamos temporalmente
            
            playBeep(); // Suena el beep
            html5QrcodeScanner.pause(true); // Congela la cámara

            // Mostramos que está cargando
            Swal.fire({
                title: 'Buscando...',
                text: 'Conectando con la base de datos',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            // Enviamos el código al controlador
            fetch('/registrar-movimiento', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ codigo_barras: decodedText.trim() })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // PRODUCTO EXISTE O SE ENCONTRÓ EN LA API
                    Swal.fire({
                        title: data.nombre,
                        html: `
                            <div class="text-center">
                                <img src="${data.foto || '/img/no-photo.png'}" class="rounded mb-3 shadow-sm border" style="width: 140px; height: 140px; object-fit: cover;">
                                <p class="mb-1"><b>Marca:</b> ${data.marca}</p>
                                <div class="display-5 fw-bold text-success mb-2">📦 ${data.stock}</div>
                                <div class="badge bg-success-subtle text-success p-2 px-3 rounded-pill">${data.mensaje}</div>
                            </div>
                        `,
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => reanudarEscaner()); // Al terminar, reanuda la cámara sin recargar
                } 
                else if (data.status === 'not_found') {
                    // PRODUCTO NUEVO (MANUAL)
                    Swal.fire({
                        title: '📦 ¡Producto Desconocido!',
                        text: 'El código ' + decodedText + ' no existe. Regístralo:',
                        html: `
                            <input id="swal-nombre" class="swal2-input" placeholder="Nombre (Ej: Harina PAN)">
                            <input id="swal-marca" class="swal2-input" placeholder="Marca (Ej: Polar)">
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar Producto',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#198754',
                        preConfirm: () => {
                            const nombre = document.getElementById('swal-nombre').value;
                            const marca = document.getElementById('swal-marca').value;
                            if (!nombre) { Swal.showValidationMessage('El nombre es obligatorio'); }
                            return { nombre: nombre, marca: marca };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            registrarManualDesdeMovil(decodedText, result.value.nombre, result.value.marca);
                        } else {
                            reanudarEscaner();
                        }
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Problema de conexión', 'error').then(() => reanudarEscaner());
            });
        }

        // Función para guardar manual
        function registrarManualDesdeMovil(codigo, nombre, marca) {
            fetch('/productos/guardar-rapido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ codigo: codigo.trim(), nombre: nombre, marca: marca })
            })
            .then(res => res.json())
            .then(() => {
                Swal.fire({
                    icon: 'success', title: '¡Listo!', text: 'Producto guardado en inventario',
                    timer: 2000, showConfirmButton: false
                }).then(() => reanudarEscaner());
            });
        }

        // Reactivar la cámara
        function reanudarEscaner() {
            html5QrcodeScanner.resume();
            isScanning = true;
        }

        // Configuración de la cámara
        let config = { 
            fps: 10, 
            qrbox: { width: 250, height: 200 },
            aspectRatio: 1.0 
        };
        let html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>