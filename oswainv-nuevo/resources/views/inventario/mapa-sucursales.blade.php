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
            <div class="d-flex gap-2" style="font-size:0.75rem;">
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E50914;margin-right:4px;"></span> Sede Principal</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#00b894;margin-right:4px;"></span> Con transferencias</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#555;margin-right:4px;"></span> Sin actividad</span>
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
                        <span>Sucursal</span><span>Transfers</span>
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

        <div class="row mt-4">
            <div class="col-md-6">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1.2rem;">
                    <h5 style="font-size:0.95rem;font-weight:600;color:#fff;margin-bottom:1rem;"><i class="bi bi-arrow-left-right me-2" style="color:var(--n-red);"></i>Productos más transferidos</h5>
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
                    <div style="color:#555;font-size:0.85rem;text-align:center;padding:1rem;">Aún no hay transferencias registradas</div>
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
                            <span style="color:#555;">{{ $m->cantidad }} uds · {{ $m->created_at->format('d/m') }}</span>
                        </div>
                        @empty
                        <div style="color:#555;text-align:center;padding:1rem;">Sin movimientos</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.mobile-bottom-nav')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnxMWZA56z9F_4RsHWVEnx2wWnvilMA0Q&callback=initMapa&loading=async" defer></script>
<script>
let map, markers = [], infowindow, lines = [];

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
    const principal = sucursales['Barinas'];

    Object.entries(sucursales).forEach(([nombre, data]) => {
        const envios = transferCount[nombre] || 0;
        const esPrincipal = data.dist === 0;
        const tieneEnvios = envios > 0;

        let fillColor = '#555';
        if (esPrincipal) fillColor = '#E50914';
        else if (tieneEnvios) fillColor = '#00b894';

        const marker = new google.maps.Marker({
            position: { lat: data.lat, lng: data.lng },
            map, title: nombre,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: esPrincipal ? 14 : (tieneEnvios ? 12 : 8),
                fillColor, fillOpacity: 0.9,
                strokeColor: '#fff', strokeWeight: 2,
            }
        });

        marker.addListener('click', () => {
            const transfers = transfersData[nombre] || [];
            const ultimos = transfers.slice(0, 8);

            let prodHtml = ultimos.length
                ? ultimos.map(t => {
                    const fecha = new Date(t.created_at).toLocaleDateString('es-ES');
                    return '<div class="route-line-info"><span>' + t.codigo_producto + ' × ' + t.cantidad + '</span><span style="color:#555;">' + fecha + '</span></div>';
                }).join('')
                : '<div style="color:#555;font-size:0.8rem;padding:4px 0;">Sin transferencias recibidas</div>';

            infowindow.setContent(
                '<div class="info-window" style="max-width:280px;">' +
                    '<h6 style="color:' + (esPrincipal ? '#E50914' : '#00b894') + ';margin-bottom:4px;font-weight:700;">' + nombre + '</h6>' +
                    (esPrincipal
                        ? '<div style="font-size:0.75rem;color:#E50914;margin-bottom:8px;"><i class="bi bi-building me-1"></i>Sede Principal</div>'
                        : '<div style="font-size:0.75rem;color:#888;margin-bottom:8px;"><i class="bi bi-signpost-2 me-1"></i>' + data.dist.toLocaleString() + ' km · ' + envios + ' envíos recibidos</div>') +
                    '<hr style="border-color:#2a2a2a;margin:6px 0;">' +
                    '<div style="font-size:0.7rem;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Últimas transferencias</div>' +
                    prodHtml +
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
</script>
</body>
</html>
