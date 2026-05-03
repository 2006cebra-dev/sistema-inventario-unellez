<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escáner - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #141414; color: #ffffff; margin: 0; padding: 0; }
        
        .topbar {
            background: rgba(0,0,0,0.9);
            padding: 16px 4%;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #222;
            backdrop-filter: blur(10px);
        }
        .back-btn {
            color: #b3b3b3;
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .back-btn:hover { color: #E50914; }
        
        .scanner-wrapper {
            max-width: 640px;
            margin: 2rem auto;
            padding: 0 4%;
        }
        
        .scanner-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .scanner-header h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .scanner-header p {
            color: #b3b3b3;
            font-size: 0.9rem;
        }
        
        .scanner-card {
            background: #1c1c1c;
            border: 1px solid #2b2b2b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.6);
        }
        
        .scanner-viewer {
            position: relative;
            background: #000000;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #E50914;
        }
        
        #reader { width: 100%; border: none !important;}
        #reader video { border-radius: 0; }
        
        .scanner-viewer::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 150px;
            border: 2px solid rgba(229,9,20,0.6);
            border-radius: 12px;
            pointer-events: none;
            z-index: 10;
        }
        .scanner-viewer::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #E50914, transparent);
            animation: scanLine 2s ease-in-out infinite;
            pointer-events: none;
            z-index: 11;
        }
        @keyframes scanLine {
            0%, 100% { transform: translate(-50%, calc(-50% - 60px)); opacity: 0.3; }
            50% { transform: translate(-50%, calc(-50% + 60px)); opacity: 1; }
        }
        
        .scanner-footer {
            padding: 1.5rem;
            background: #141414;
        }
        
        .manual-input-box { margin-top: 1rem; }
        .form-control {
            background: #2a2a2a;
            border: 1px solid #333;
            color: #ffffff;
            padding: 14px 16px;
            text-align: center;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: #333;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(229,9,20,0.3);
            border-color: #E50914;
            outline: none;
        }
        .form-control::placeholder { color: #777; }
        
        .hint-text { color: #777; font-size: 0.85rem; margin-top: 1rem; text-align: center; }
        
        #status-indicator { display: flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); font-size: 0.75rem; font-weight: 500; }
        #status-indicator .status-dot { width: 8px; height: 8px; border-radius: 50%; transition: background 0.3s ease; }
        #status-indicator .status-text { color: #ccc; transition: color 0.3s ease; }
        #status-indicator.online .status-dot { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        #status-indicator.offline .status-dot { background: #e74c3c; box-shadow: 0 0 6px rgba(231,76,60,0.6); }
        #status-indicator.offline .status-text { color: #e74c3c; }
        
        .offline-banner { display: none; background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 1.5rem; text-align: center; }
        .offline-banner.show { display: block; }
        .offline-banner i { color: #e74c3c; margin-right: 8px; }
        .offline-banner span { color: #e74c3c; font-size: 0.85rem; font-weight: 500; }
    </style>
</head>
<body>

    <div class="topbar">
        <a href="{{ route('catalogo') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
        <div id="status-indicator" class="online" style="margin-left: auto;">
            <span class="status-dot"></span>
            <span class="status-text" id="statusText">En línea</span>
        </div>
    </div>

    <div class="scanner-wrapper">
        <div class="offline-banner" id="offlineBanner">
            <i class="bi bi-wifi-off"></i>
            <span>Modo offline — Solo se buscarán productos ya registrados en la base de datos local.</span>
        </div>
        
        <div class="scanner-header">
            <h3><i class="bi bi-upc-scan me-2" style="color: #E50914;"></i>Escáner de Inventario</h3>
            <p>Apunta la cámara al código de barras o ingresa el código manualmente</p>
        </div>
        
        <div class="scanner-card">
            <div class="scanner-viewer">
                <div id="reader"></div>
            </div>
            
            <div class="scanner-footer">
                <div class="manual-input-box text-center">
                    <input type="text" id="barcodeInput" class="form-control" placeholder="Escribe el código y presiona Enter..." onkeypress="if(event.key==='Enter') procesarBarcode()">
                    <p class="hint-text"><i class="bi bi-keyboard me-1"></i>O ingresa manualmente y presiona Enter</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let html5QrCode;

        // ARRANCAR CÁMARA AUTOMÁTICAMENTE
        document.addEventListener("DOMContentLoaded", function() {
            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 150 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, (codigo) => {
                if (html5QrCode.getState() === 2) { html5QrCode.pause(true); } 
                document.getElementById('barcodeInput').value = codigo;
                procesarBarcode();
            }).catch(err => {
                console.error("Error de cámara:", err);
                document.getElementById('reader').innerHTML = `
                    <div style="padding: 40px 20px; text-align: center; color: #E50914;">
                        <i class="bi bi-camera-video-off" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-white">La cámara está bloqueada o en uso por otra app.</p>
                    </div>`;
            });
            
            function updateStatusIndicator() {
                const indicator = document.getElementById('status-indicator');
                const statusText = document.getElementById('statusText');
                const offlineBanner = document.getElementById('offlineBanner');
                if (navigator.onLine) {
                    indicator.className = 'online';
                    statusText.textContent = 'En línea';
                    offlineBanner.classList.remove('show');
                } else {
                    indicator.className = 'offline';
                    statusText.textContent = 'Sin conexión';
                    offlineBanner.classList.add('show');
                }
            }
            window.addEventListener('online', updateStatusIndicator);
            window.addEventListener('offline', updateStatusIndicator);
            updateStatusIndicator();
        });

        async function procesarBarcode() {
            const codigo = document.getElementById('barcodeInput').value.trim();
            if (!codigo) return;

            if (html5QrCode && html5QrCode.getState() === 2) { html5QrCode.pause(true); }

            Swal.fire({ title: 'Buscando...', text: 'Verificando base de datos...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }, background: '#1c1c1c', color: '#fff' });

            try {
                // 1. Buscar en tu base de datos (Laravel)
                const response = await fetch('{{ route("escanear.producto") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ codigo: codigo })
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '+1 ' + data.producto.nombre,
                        text: 'Stock actualizado a: ' + data.nuevo_stock,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#1c1c1c', color: '#fff'
                    }).then(() => {
                        document.getElementById('barcodeInput').value = '';
                        if (html5QrCode && html5QrCode.getState() === 3) html5QrCode.resume();
                    });
                } else {
                    if (!navigator.onLine) {
                        Swal.fire({
                            icon: 'warning', title: 'Sin conexión',
                            text: 'No se puede buscar en internet. Conéctate para registrar nuevos productos.',
                            confirmButtonText: 'Entendido', confirmButtonColor: '#E50914', background: '#1c1c1c', color: '#fff'
                        }).then(() => {
                            document.getElementById('barcodeInput').value = '';
                            if (html5QrCode && html5QrCode.getState() === 3) html5QrCode.resume();
                        });
                        return;
                    }
                    
                    // 2. Buscar en API Mundial (OpenFoodFacts)
                    Swal.fire({ title: 'Código no registrado', text: 'Buscando información global en internet...', icon: 'info', showConfirmButton: false, background: '#1c1c1c', color: '#fff' });
                    
                    const apiMundial = await fetch(`https://world.openfoodfacts.org/api/v0/product/${codigo}.json`);
                    const dataMundial = await apiMundial.json();

                    if (dataMundial.status === 1 && dataMundial.product) {
                        let nombreInternet = dataMundial.product.product_name || dataMundial.product.generic_name || "Producto Nuevo";
                        let imagenInternet = dataMundial.product.image_front_url || dataMundial.product.image_url || "";

                        Swal.fire({
                            html: `
                                <div style="text-align:center; padding:10px;">
                                    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(0,184,148,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                        <i class="bi bi-cloud-check-fill" style="font-size: 40px; color: #00b894;"></i>
                                    </div>
                                    <h3 style="color:#ffffff; margin:10px 0 5px; font-weight:700;">¡Encontrado en Internet!</h3>
                                    <p style="color:#b3b3b3; margin:0 0 15px; font-size:1.1rem; font-weight: 500;">${nombreInternet}</p>
                                    ${imagenInternet ? `<img src="${imagenInternet}" style="width:150px; height:150px; object-fit:cover; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.5); margin:10px auto; display:block;">` : `<i class="bi bi-box-seam" style="font-size:80px; color:#444;"></i>`}
                                    <p style="color:#777; margin-top: 15px; font-size: 0.85rem;">Enviaremos estos datos al catálogo para que agregues el precio.</p>
                                </div>
                            `,
                            background: '#1c1c1c',
                            showConfirmButton: true,
                            confirmButtonText: '<i class="bi bi-arrow-right-circle me-1"></i> Ir al Catálogo',
                            confirmButtonColor: '#E50914',
                            allowOutsideClick: false
                        }).then(() => {
                            if (html5QrCode) { html5QrCode.stop().catch(()=>{}); }
                            // AQUI LA MAGIA: REDIRIGIMOS AL CATALOGO CON LOS DATOS
                            window.location.href = `{{ route('catalogo') }}?nuevo_codigo=${codigo}&nuevo_nombre=${encodeURIComponent(nombreInternet)}&nueva_imagen=${encodeURIComponent(imagenInternet)}`;
                        });
                    } else {
                        // El producto no existe ni en internet
                        Swal.fire({
                            icon: 'question',
                            title: 'Producto no reconocido',
                            text: 'El código de barras no existe en nuestra base de datos ni en internet. Vamos a registrarlo manualmente.',
                            confirmButtonText: 'Registrar a mano',
                            confirmButtonColor: '#E50914',
                            background: '#1c1c1c', color: '#fff'
                        }).then(() => {
                            if (html5QrCode) { html5QrCode.stop().catch(()=>{}); }
                            // REDIRIGIMOS AL CATALOGO SOLO CON EL CÓDIGO
                            window.location.href = `{{ route('catalogo') }}?nuevo_codigo=${codigo}`;
                        });
                    }
                }
            } catch (error) {
                console.error(error);
                Swal.fire({title: 'Error', text: 'Problema de conexión', icon: 'error', background: '#1c1c1c', color: '#fff'}).then(() => {
                    if (html5QrCode && html5QrCode.getState() === 3) html5QrCode.resume();
                });
            }
        }
    </script>
</body>
</html>