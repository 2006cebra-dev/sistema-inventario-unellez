<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mapa de Sucursales - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @include('partials.navbar')
    <style>
        :root { --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b; }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--bg-main); color: #e5e5e5; min-height: 100vh; }
        #mapa-sucursales { height: 70vh; width: 100%; border-radius: 16px; border: 1px solid #2a2a2a; }
        .gm-style .gm-style-iw-c { background: #1c1c1c !important; color: #fff !important; border-radius: 12px !important; }
        .gm-style .gm-style-iw-d { color: #ccc !important; max-height: 300px !important; overflow-y: auto !important; }
        .gm-style .gm-style-iw-tc::after { background: #1c1c1c !important; }
        .sucursal-card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 0.8rem 1rem; cursor: pointer; transition: all .3s; }
        .sucursal-card:hover { border-color: var(--n-red); transform: translateY(-2px); }
        .sucursal-card.active { border-color: #00b894; background: rgba(0,184,148,0.08); }
        .sucursal-card .badge-count { background: rgba(229,9,20,0.15); color: var(--n-red); font-size: 0.65rem; padding: 2px 8px; border-radius: 10px; }
        .search-box { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 10px; padding: 0.5rem 1rem; color: #fff; width: 100%; outline: none; }
        .search-box:focus { border-color: var(--n-red); }
        .route-line-info { font-size: 0.7rem; color: #888; padding: 2px 0; border-bottom: 1px solid #1f1f1f; display: flex; justify-content: space-between; }
        .main-content { padding-top: 80px; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="text-white fw-bold mb-0"><i class="bi bi-geo-alt-fill me-2" style="color:var(--n-red);"></i>Mapa de Sucursales</h4>
                <div style="color:#666;font-size:0.8rem;margin-top:4px;">{{ count($sucursales) }} sucursales · {{ collect($transferCount)->sum() }} transferencias realizadas</div>
            </div>
            <div class="d-flex gap-2" style="font-size:0.75rem;flex-wrap:wrap;align-items:center;">
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E50914;margin-right:4px;"></span> Sede Principal</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#00b894;margin-right:4px;"></span> Con transferencias</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#555;margin-right:4px;"></span> Sin actividad</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ffc107;margin-right:4px;"></span> En Camino</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#00b894;margin-right:4px;"></span> Llegó</span>
                <button onclick="abrirScannerQr()" style="background:rgba(229,9,20,0.1);border:1px solid rgba(229,9,20,0.3);color:var(--n-red);border-radius:8px;padding:4px 12px;font-size:0.7rem;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(229,9,20,0.2)'" onmouseout="this.style.background='rgba(229,9,20,0.1)'"><i class="bi bi-upc-scan me-1"></i>Escanear Guía</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-9">
                <div id="mapa-sucursales"></div>
            </div>
            <div class="col-lg-3">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1rem;max-height:70vh;display:flex;flex-direction:column;">
                    <input type="text" class="search-box mb-2" id="filtroSucursal" placeholder="Buscar sucursal..." oninput="filtrarSucursales(this.value)">
                    <div style="font-size:0.65rem;color:#555;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:flex;justify-content:space-between;">
                        <span>Sucursal</span><span style="text-align:right;">Envíos <span style="color:#ffc107;"><i class="bi bi-truck"></i></span> <span style="color:#00b894;"><i class="bi bi-check-circle"></i></span></span>
                    </div>
                    <div style="overflow-y:auto;flex:1;" id="listaSucursales">
                        @foreach($sucursales as $nombre => $data)
                        @php $count = $transferCount[$nombre] ?? 0; @endphp
                        <div class="sucursal-card mb-1" data-name="{{ $nombre }}" data-count="{{ $count }}" onclick="centrarMapa('{{ $nombre }}', {{ $data['lat'] }}, {{ $data['lng'] }})">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div>
                                    <div style="font-weight:600;color:#fff;font-size:0.85rem;">{{ $nombre }}</div>
                                    <div style="color:#666;font-size:0.7rem;">
                                        @if($data['dist'] > 0)
                                            <i class="bi bi-signpost-2 me-1"></i>{{ number_format($data['dist']) }} km
                                        @else
                                            <span style="color:var(--n-red);"><i class="bi bi-building me-1"></i>Sede Principal</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($count > 0)
                                    <span class="badge-count">{{ $count }} envíos</span>
                                    <div style="font-size:0.55rem;margin-top:2px;display:flex;gap:4px;justify-content:flex-end;">
                                        @php
                                            $transfers = collect($transfersPorSucursal[$nombre] ?? []);
                                            $enCamino = $transfers->where('estado', 'en_camino')->count();
                                            $llegados = $transfers->where('estado', 'llegado')->count();
                                        @endphp
                                        @if($enCamino > 0)
                                        <span style="color:#ffc107;"><i class="bi bi-truck"></i> {{ $enCamino }}</span>
                                        @endif
                                        @if($llegados > 0)
                                        <span style="color:#00b894;"><i class="bi bi-check-circle"></i> {{ $llegados }}</span>
                                        @endif
                                    </div>
                                    @else
                                    <span style="color:#444;font-size:0.65rem;">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Original: Productos más transferidos + Últimas transferencias -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1.2rem;">
                    <h5 style="font-size:0.95rem;font-weight:600;color:#fff;margin-bottom:1rem;"><i class="bi bi-arrow-left-right me-2" style="color:var(--n-red);"></i>Más transferidos</h5>
                    @php $top5 = $productoMasTransferido; @endphp
                    @if($top5->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        @foreach($top5 as $codigo => $total)
                        @php $p = $productos->firstWhere('codigo', $codigo); @endphp
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 10px;background:#1a1a1a;border-radius:8px;">
                            <span style="color:#fff;font-size:0.85rem;">{{ $p->nombre ?? $codigo }}</span>
                            <span style="color:#00b894;font-weight:600;font-size:0.85rem;">{{ $total }} uds</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="color:#555;font-size:0.85rem;text-align:center;padding:1rem;">Aún no hay transferencias</div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1.2rem;">
                    <h5 style="font-size:0.95rem;font-weight:600;color:#fff;margin-bottom:1rem;"><i class="bi bi-clock-history me-2" style="color:var(--n-red);"></i>Últimas transferencias</h5>
                    <div style="max-height:200px;overflow-y:auto;">
                        @php $ultimas = \App\Models\Movimiento::where('motivo', 'like', 'Transferencia a %')->latest()->take(10)->get(); @endphp
                        @forelse($ultimas as $m)
                        <div class="route-line-info">
                            <span>{{ str_replace('Transferencia a ', '→ ', $m->motivo) }}</span>
                            <span style="display:flex;align-items:center;gap:4px;">
                                @if($m->estado === 'llegado')
                                <span style="background:rgba(0,184,148,0.15);color:#00b894;padding:0 6px;border-radius:8px;font-size:0.55rem;font-weight:600;">Llegó</span>
                                @else
                                <span style="background:rgba(255,193,7,0.15);color:#ffc107;padding:0 6px;border-radius:8px;font-size:0.55rem;font-weight:600;">Envío</span>
                                @endif
                                {{ $m->cantidad }} uds · {{ $m->created_at->format('d/m') }}
                            </span>
                        </div>
                        @empty
                        <div style="color:#555;text-align:center;padding:1rem;">Sin movimientos</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuevo: Cronograma de Salidas (timeline completo abajo) -->
        <div class="row mt-4">
            <div class="col-12">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1.2rem;">
                    <h5 style="font-size:0.95rem;font-weight:600;color:#fff;margin-bottom:1rem;"><i class="bi bi-mailbox me-2" style="color:var(--n-red);"></i>Cronograma de Salidas</h5>
                    <div style="max-height:300px;overflow-y:auto;">
                        @php
                            $lineaTiempo = \App\Models\Movimiento::where('motivo', 'like', 'Transferencia a %')
                                ->latest()->take(30)->get();
                        @endphp
                        @forelse($lineaTiempo as $m)
                        @php
                            $destino = str_replace('Transferencia a ', '', $m->motivo);
                            $demorado = $m->estado === 'en_camino' && $m->created_at->diffInDays(now()) >= 7;
                            $diasTranscurridos = $m->created_at->diffInDays(now());
                        @endphp
                        <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid #1f1f1f;align-items:center;">
                            <div style="width:24px;display:flex;flex-direction:column;align-items:center;flex-shrink:0;">
                                <div style="width:8px;height:8px;border-radius:50%;
                                    @if($m->estado === 'llegado') background:#00b894;
                                    @elseif($demorado) background:#E50914;box-shadow:0 0 6px #E50914;
                                    @else background:#ffc107;
                                    @endif"></div>
                                <div style="width:2px;height:30px;background:#2a2a2a;"></div>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.8rem;color:#fff;font-weight:600;">
                                    <span style="color:#888;">→</span> {{ $destino }}
                                </div>
                                <div style="font-size:0.65rem;color:#666;">
                                    {{ $m->cantidad }} uds · {{ $m->created_at->format('d/m/Y H:i') }}
                                    @if($demorado)
                                    <span style="color:#E50914;font-weight:600;"> · {{ $diasTranscurridos }} días</span>
                                    @endif
                                </div>
                            </div>
                            <div style="flex-shrink:0;">
                                @if($m->estado === 'llegado')
                                <span style="background:rgba(0,184,148,0.15);color:#00b894;padding:2px 10px;border-radius:10px;font-size:0.6rem;font-weight:600;"><i class="bi bi-check-circle"></i> Llegó</span>
                                @elseif($demorado)
                                <span style="background:rgba(229,9,20,0.15);color:#E50914;padding:2px 10px;border-radius:10px;font-size:0.6rem;font-weight:600;animation:pulse 1.5s infinite;"><i class="bi bi-exclamation-triangle"></i> Demorado</span>
                                @else
                                <span style="background:rgba(255,193,7,0.15);color:#ffc107;padding:2px 10px;border-radius:10px;font-size:0.6rem;font-weight:600;"><i class="bi bi-truck"></i> Envío</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div style="color:#555;text-align:center;padding:1rem;">Sin movimientos registrados</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Escáner QR -->
<div id="qrScannerOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;flex-direction:column;" onclick="if(event.target===this)cerrarScannerQr()">
    <div style="background:#1a1a1a;border-radius:16px;padding:1.5rem;max-width:420px;width:90%;border:1px solid #2a2a2a;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h6 style="color:#fff;font-weight:700;margin:0;"><i class="bi bi-upc-scan me-2" style="color:var(--n-red);"></i>Escanear Guía</h6>
            <button onclick="cerrarScannerQr()" style="background:none;border:none;color:#888;font-size:1.2rem;cursor:pointer;"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="qrScannerContainer" style="width:100%;min-height:200px;"></div>
        <div style="margin-top:12px;display:flex;flex-direction:column;gap:8px;">
            <div style="border-top:1px solid #2a2a2a;padding-top:12px;text-align:center;">
                <label for="scanImageInput" style="background:rgba(255,255,255,0.05);border:1px dashed #444;border-radius:10px;padding:10px;display:flex;align-items:center;justify-content:center;gap:8px;color:#888;font-size:0.8rem;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.borderColor='#E50914';this.style.color='#fff'" onmouseout="this.style.borderColor='#444';this.style.color='#888'">
                    <i class="bi bi-image" style="font-size:1.2rem;"></i> Subir foto de la guía
                    <input type="file" id="scanImageInput" accept="image/*" style="display:none;" onchange="escanearImagen(this)">
                </label>
            </div>
        </div>
        <div id="qrResultado" style="display:none;margin-top:1rem;padding:0.8rem;background:#0f0f0f;border-radius:8px;border:1px solid #2a2a2a;color:#ccc;font-size:0.8rem;"></div>
    </div>
</div>
@include('partials.mobile-bottom-nav')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnxMWZA56z9F_4RsHWVEnx2wWnvilMA0Q&callback=initMapa&loading=async" defer></script>
<script>
let map, markers = [], infowindow, lines = [];
let qrScanner = null;
const DIAS_DEMORA = 7;

window.initMapa = function() {
    const el = document.getElementById('mapa-sucursales');
    if (!el) return;

    map = new google.maps.Map(el, {
        center: { lat: 8.6, lng: -67.5 }, zoom: 6.7,
        styles: [
            { elementType: 'geometry', stylers: [{ color: '#141414' }] },
            { elementType: 'labels.text.stroke', stylers: [{ color: '#141414' }] },
            { elementType: 'labels.text.fill', stylers: [{ color: '#888' }] },
            { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#2a2a2a' }] },
            { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0d1b2a' }] },
            { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#1c1c1c' }] },
        ]
    });
    infowindow = new google.maps.InfoWindow({ maxWidth: 320 });

    const sucursales = @json($sucursales);
    const transferCount = @json($transferCount);
    const transfersData = @json($transfersPorSucursal);
    const ahora = new Date();
    const principal = sucursales['Barinas'];

    // Detectar demoras por sucursal (envíos en camino > 7 días)
    const demoras = {};
    Object.entries(transfersData).forEach(([nombre, transfers]) => {
        const demorados = transfers.filter(t => {
            if (t.estado !== 'en_camino') return false;
            const creado = new Date(t.created_at);
            const diffDias = (ahora - creado) / (1000 * 60 * 60 * 24);
            return diffDias >= DIAS_DEMORA;
        });
        if (demorados.length > 0) demoras[nombre] = demorados.length;
    });

    Object.entries(sucursales).forEach(([nombre, data]) => {
        const envios = transferCount[nombre] || 0;
        const esPrincipal = data.dist === 0;
        const tieneEnvios = envios > 0;
        const tieneDemora = demoras[nombre] > 0;

        let fillColor = '#555';
        if (esPrincipal) fillColor = '#E50914';
        else if (tieneDemora) fillColor = '#E50914';
        else if (tieneEnvios) fillColor = '#00b894';

        const markerScale = esPrincipal ? 14 : (tieneDemora ? 14 : (tieneEnvios ? 12 : 8));
        const marker = new google.maps.Marker({
            position: { lat: data.lat, lng: data.lng },
            map, title: nombre,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: markerScale,
                fillColor, fillOpacity: 0.9,
                strokeColor: tieneDemora ? '#E50914' : '#fff',
                strokeWeight: tieneDemora ? 3 : 2,
            }
        });

        // Animación de pulso para markers con demora
        if (tieneDemora) {
            let pulse = true;
            setInterval(() => {
                marker.setIcon({
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: markerScale,
                    fillColor,
                    fillOpacity: pulse ? 0.5 : 0.9,
                    strokeColor: '#E50914',
                    strokeWeight: 3,
                });
                pulse = !pulse;
            }, 1000);
        }

        marker.addListener('click', () => {
            const transfers = transfersData[nombre] || [];
            const ultimos = transfers.slice(0, 8);

            const tieneDemoraLocal = demoras[nombre] > 0;
            let prodHtml = ultimos.length
                ? ultimos.map(t => {
                    const fecha = new Date(t.created_at).toLocaleDateString('es-ES');
                    const demorado = t.estado === 'en_camino' && (ahora - new Date(t.created_at)) / (1000*60*60*24) >= DIAS_DEMORA;
                    const badge = t.estado === 'llegado'
                        ? '<span style="background:rgba(0,184,148,0.15);color:#00b894;padding:1px 8px;border-radius:10px;font-size:0.6rem;font-weight:600;">Llegó</span>'
                        : demorado
                        ? '<span style="background:rgba(229,9,20,0.15);color:#E50914;padding:1px 8px;border-radius:10px;font-size:0.6rem;font-weight:600;"><i class="bi bi-exclamation-triangle"></i> ' + Math.floor((ahora - new Date(t.created_at)) / (1000*60*60*24)) + 'd</span>'
                        : '<span style="background:rgba(255,193,7,0.15);color:#ffc107;padding:1px 8px;border-radius:10px;font-size:0.6rem;font-weight:600;">En Camino</span>';
                    return '<div class="route-line-info"><span>' + t.codigo_producto + ' × ' + t.cantidad + '</span><span style="display:flex;align-items:center;gap:4px;">' + badge + '<span style="color:#555;">' + fecha + '</span></span></div>';
                }).join('')
                : '<div style="color:#555;font-size:0.8rem;padding:4px 0;">Sin transferencias recibidas</div>';

            let confirmBtn = '';
            if (!esPrincipal && ultimos.some(t => t.estado === 'en_camino')) {
                const enCamino = ultimos.filter(t => t.estado === 'en_camino');
                confirmBtn = '<div style="margin-top:8px;display:flex;flex-direction:column;gap:4px;">' +
                    enCamino.map(t => {
                        const demorado = (ahora - new Date(t.created_at)) / (1000*60*60*24) >= DIAS_DEMORA;
                        return '<button onclick="confirmarLlegada(' + t.id + ',\'' + t.codigo_producto + '\')" style="background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.3);color:#00b894;border-radius:8px;padding:4px 10px;font-size:0.7rem;cursor:pointer;transition:all 0.2s;' + (demorado ? 'border-color:#E50914;color:#E50914;background:rgba(229,9,20,0.1);animation:pulse 1.5s infinite;' : '') + '" onmouseover="this.style.background=\'rgba(0,184,148,0.2)\'" onmouseout="this.style.background=\'rgba(0,184,148,0.1)\'"><i class="bi bi-check-circle me-1"></i>Llegó ' + t.codigo_producto + ' × ' + t.cantidad + (demorado ? ' ⚠' : '') + '</button>';
                    }).join('') +
                    '</div>';
            }

            const demoraWarning = tieneDemoraLocal ? '<div style="margin-top:8px;padding:6px 10px;background:rgba(229,9,20,0.1);border:1px solid rgba(229,9,20,0.3);border-radius:8px;font-size:0.65rem;color:#E50914;"><i class="bi bi-exclamation-triangle me-1"></i>' + demoras[nombre] + ' envío(s) demorado(s) (+' + DIAS_DEMORA + ' días)</div>' : '';

            infowindow.setContent(
                '<div class="info-window" style="max-width:280px;">' +
                    '<h6 style="color:' + (esPrincipal ? '#E50914' : (tieneDemoraLocal ? '#E50914' : '#00b894')) + ';margin-bottom:4px;font-weight:700;">' + nombre + (tieneDemoraLocal ? ' <span style="font-size:0.6rem;background:rgba(229,9,20,0.15);color:#E50914;padding:2px 6px;border-radius:6px;">⚠</span>' : '') + '</h6>' +
                    (esPrincipal
                        ? '<div style="font-size:0.75rem;color:#E50914;margin-bottom:8px;"><i class="bi bi-building me-1"></i>Sede Principal</div>'
                        : '<div style="font-size:0.75rem;color:#888;margin-bottom:8px;"><i class="bi bi-signpost-2 me-1"></i>' + data.dist.toLocaleString() + ' km · ' + envios + ' envíos recibidos</div>') +
                    demoraWarning +
                    '<hr style="border-color:#2a2a2a;margin:6px 0;">' +
                    '<div style="font-size:0.7rem;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Últimas transferencias</div>' +
                    prodHtml +
                    confirmBtn +
                '</div>'
            );
            infowindow.open(map, marker);
        });

        markers.push(marker);

        if (principal && nombre !== 'Barinas' && tieneEnvios) {
            const line = new google.maps.Polyline({
                path: [
                    { lat: principal.lat, lng: principal.lng },
                    { lat: data.lat, lng: data.lng }
                ],
                geodesic: true,
                strokeColor: '#00b894',
                strokeOpacity: 0.2 + Math.min(envios / 10, 0.6),
                strokeWeight: 1 + Math.min(envios, 4),
                map: map
            });
            lines.push(line);
        }
    });
};

window.centrarMapa = function(nombre, lat, lng) {
    map.setCenter({ lat, lng });
    map.setZoom(10);
    const marker = markers.find(m => m.getTitle() === nombre);
    if (marker) google.maps.event.trigger(marker, 'click');
    document.querySelectorAll('.sucursal-card').forEach(c => c.classList.remove('active'));
    const card = document.querySelector('.sucursal-card[data-name="' + nombre + '"]');
    if (card) card.classList.add('active');
};

function filtrarSucursales(valor) {
    const q = valor.toLowerCase();
    document.querySelectorAll('.sucursal-card').forEach(c => {
        c.style.display = c.getAttribute('data-name').toLowerCase().includes(q) ? '' : 'none';
    });
}

window.confirmarLlegada = async function(id, codigo) {
    try {
        const res = await fetch('/transferencias/confirmar-llegada/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });
        const data = await res.json();
        if (data.success) {
            if (infowindow) infowindow.close();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch(e) {
        alert('Error de conexión');
    }
};

// --- Escáner QR ---
window.abrirScannerQr = function() {
    document.getElementById('qrScannerOverlay').style.display = 'flex';
    if (qrScanner) { qrScanner.clear(); qrScanner = null; }
    qrScanner = new Html5QrcodeScanner("qrScannerContainer", { fps: 10, qrbox: { width: 250, height: 150 } }, false);
    qrScanner.render(onQrScanSuccess, onQrScanError);
};

window.cerrarScannerQr = function() {
    document.getElementById('qrScannerOverlay').style.display = 'none';
    if (qrScanner) { qrScanner.clear(); qrScanner = null; }
};

function onQrScanSuccess(decodedText) {
    if (qrScanner) { qrScanner.pause(); }
    const resultDiv = document.getElementById('qrResultado');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verificando...';

    // Intentar como QR de guía (JSON con ids)
    try {
        const data = JSON.parse(decodedText);
        if (data.ids && Array.isArray(data.ids)) {
            fetch('/transferencias/confirmar-por-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ data: decodedText })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    resultDiv.innerHTML = '<span style="color:#00b894;"><i class="bi bi-check-circle-fill me-1"></i>' + res.message + '</span>';
                    setTimeout(() => { cerrarScannerQr(); location.reload(); }, 1500);
                } else {
                    resultDiv.innerHTML = '<span style="color:#E50914;"><i class="bi bi-x-circle-fill me-1"></i>' + res.message + '</span>';
                    setTimeout(() => { if (qrScanner) qrScanner.resume(); resultDiv.style.display = 'none'; }, 2000);
                }
            });
            return;
        }
    } catch(e) {}

    // No es QR de guía → buscar como código de barras de producto
    fetch('/transferencias/buscar-por-codigo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ codigo: decodedText })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.transferencias.length > 0) {
            let html = '<div style="color:#00b894;margin-bottom:8px;"><i class="bi bi-check-circle-fill me-1"></i>Producto: ' + data.producto + '</div>';
            html += '<div style="display:flex;flex-direction:column;gap:4px;">';
            data.transferencias.forEach(t => {
                html += '<button onclick="confirmarLlegada(' + t.id + ',\'' + t.codigo_producto + '\')" style="background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.3);color:#00b894;border-radius:8px;padding:6px 10px;font-size:0.75rem;cursor:pointer;text-align:left;transition:all 0.2s;" onmouseover="this.style.background=\'rgba(0,184,148,0.2)\'" onmouseout="this.style.background=\'rgba(0,184,148,0.1)\'"><i class="bi bi-check-circle me-1"></i>Llegó → ' + t.sucursal + ' × ' + t.cantidad + ' uds (' + t.fecha + ')</button>';
            });
            html += '</div>';
            resultDiv.innerHTML = html;
            setTimeout(() => { if (qrScanner) qrScanner.resume(); resultDiv.style.display = 'none'; }, 8000);
        } else {
            resultDiv.innerHTML = '<span style="color:#ffc107;"><i class="bi bi-info-circle me-1"></i>Código ' + decodedText + ' — sin transferencias en camino</span>';
            setTimeout(() => { if (qrScanner) qrScanner.resume(); resultDiv.style.display = 'none'; }, 2500);
        }
    })
    .catch(() => {
        resultDiv.innerHTML = '<span style="color:#E50914;"><i class="bi bi-x-circle-fill me-1"></i>Error al buscar producto</span>';
        setTimeout(() => { if (qrScanner) qrScanner.resume(); resultDiv.style.display = 'none'; }, 2000);
    });
}

function onQrScanError(err) {
    // ignorar errores de scan continuo
}

// Escanear código desde imagen subida
window.escanearImagen = function(input) {
    const file = input.files[0];
    if (!file) return;
    const resultDiv = document.getElementById('qrResultado');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analizando imagen...';

    // Crear elemento temporal fuera de pantalla para el scan
    const tempId = '_qr_temp_scan_' + Date.now();
    const tempEl = document.createElement('div');
    tempEl.id = tempId;
    tempEl.style.position = 'fixed';
    tempEl.style.left = '-9999px';
    tempEl.style.top = '0';
    document.body.appendChild(tempEl);

    try {
        const tempScanner = new Html5Qrcode(tempId);
        tempScanner.scanFile(file, false)
            .then(decodedText => {
                tempScanner.clear();
                document.body.removeChild(tempEl);
                onQrScanSuccess(decodedText);
            })
            .catch(() => {
                // Intentar scanFileV2 si está disponible
                if (typeof tempScanner.scanFileV2 === 'function') {
                    return tempScanner.scanFileV2(file)
                        .then(result => {
                            tempScanner.clear();
                            document.body.removeChild(tempEl);
                            onQrScanSuccess(result.decodedText);
                        });
                }
                throw new Error('No code found');
            })
            .catch(err => {
                tempScanner.clear();
                document.body.removeChild(tempEl);
                resultDiv.innerHTML = '<span style="color:#ffc107;"><i class="bi bi-info-circle me-1"></i>No se encontró código QR/barras en la imagen</span>';
                setTimeout(() => { resultDiv.style.display = 'none'; input.value = ''; }, 3000);
            });
    } catch(e) {
        if (document.getElementById(tempId)) document.body.removeChild(tempEl);
        resultDiv.innerHTML = '<span style="color:#ffc107;"><i class="bi bi-info-circle me-1"></i>Error al procesar imagen</span>';
        setTimeout(() => { resultDiv.style.display = 'none'; input.value = ''; }, 3000);
    }
};
</script>
</body>
</html>
