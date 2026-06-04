@extends('layouts.app')
@section('title', 'Mapa de Sucursales')
@push('styles')
<style>
    #mapa-sucursales { height: 70vh; width: 100%; border-radius: 16px; border: 1px solid #2a2a2a; }
    .gm-style .gm-style-iw-c { background: #1c1c1c !important; color: #fff !important; border-radius: 12px !important; }
    .gm-style .gm-style-iw-d { color: #ccc !important; }
    .gm-style .gm-style-iw-tc::after { background: #1c1c1c !important; }
    .info-window { max-width: 260px; }
    .info-window h6 { color: #E50914; margin-bottom: 6px; font-weight: 700; }
    .info-window .prod-item { display: flex; justify-content: space-between; padding: 2px 0; font-size: 0.8rem; border-bottom: 1px solid #2a2a2a; }
    .sucursal-card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 1rem; cursor: pointer; transition: all .3s; }
    .sucursal-card:hover { border-color: #E50914; transform: translateY(-2px); }
    .sucursal-card.active { border-color: #E50914; background: rgba(229,9,20,0.08); }
</style>
@endpush
@section('content')
@include('partials.navbar')
<main class="main-content">
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="text-white fw-bold mb-0"><i class="bi bi-geo-alt-fill me-2" style="color:var(--accent-primary);"></i>Mapa de Sucursales</h4>
            <div style="color:#666;font-size:0.85rem;"><i class="bi bi-info-circle me-1"></i>{{ count($sucursales) }} sucursales activas</div>
        </div>

        <div class="row g-4">
            <div class="col-lg-9">
                <div id="mapa-sucursales"></div>
            </div>
            <div class="col-lg-3">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1rem;max-height:70vh;overflow-y:auto;">
                    <div style="color:#888;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Sucursales</div>
                    <div class="d-flex flex-column gap-2" id="listaSucursales">
                        @foreach($sucursales as $nombre => $data)
                        <div class="sucursal-card" data-name="{{ $nombre }}" onclick="centrarMapa('{{ $nombre }}', {{ $data['lat'] }}, {{ $data['lng'] }})">
                            <div style="font-weight:600;color:#fff;font-size:0.9rem;">{{ $nombre }}</div>
                            <div style="color:#666;font-size:0.75rem;"><i class="bi bi-people me-1"></i>Stock total: <span id="stock-{{ Str::slug($nombre) }}">—</span></div>
                            @if($data['dist'] > 0)
                            <div style="color:#555;font-size:0.7rem;">{{ number_format($data['dist']) }} km de Caracas</div>
                            @else
                            <div style="color:#E50914;font-size:0.7rem;"><i class="bi bi-building me-1"></i>Sede Principal</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div style="background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:1.2rem;">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="text-white mb-0" style="font-size:1rem;"><i class="bi bi-box-seam me-2" style="color:var(--accent-primary);"></i>Productos con Bajo Stock a Nivel Nacional</h5>
                        <span style="color:#888;font-size:0.75rem;">{{ $productos->where('stock', '<=', 'stock_minimo')->count() }} alertas</span>
                    </div>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                            <thead>
                                <tr style="border-bottom:1px solid #2b2b2b;color:#666;text-transform:uppercase;font-size:0.7rem;letter-spacing:0.5px;">
                                    <th style="padding:0.6rem;text-align:left;">Producto</th>
                                    <th style="padding:0.6rem;text-align:center;">Stock</th>
                                    <th style="padding:0.6rem;text-align:center;">Mínimo</th>
                                    <th style="padding:0.6rem;text-align:right;">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos->where('stock', '<=', 'stock_minimo') as $p)
                                <tr style="border-bottom:1px solid #1f1f1f;">
                                    <td style="padding:0.6rem;color:#fff;">{{ $p->nombre }}</td>
                                    <td style="padding:0.6rem;text-align:center;color:{{ $p->stock == 0 ? '#E50914' : '#ffc107' }};">{{ $p->stock }}</td>
                                    <td style="padding:0.6rem;text-align:center;color:#666;">{{ $p->stock_minimo }}</td>
                                    <td style="padding:0.6rem;text-align:right;color:#00b894;">${{ number_format($p->precio, 2) }}</td>
                                </tr>
                                @endforeach
                                @if($productos->where('stock', '<=', 'stock_minimo')->count() == 0)
                                <tr><td colspan="4" style="padding:1rem;text-align:center;color:#555;">Sin alertas de stock</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@include('partials.mobile-bottom-nav')
@endsection
@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnxMWZA56z9F_4RsHWVEnx2wWnvilMA0Q&callback=initMapa&loading=async" defer></script>
<script>
let map, markers = [], infowindow;

window.initMapa = function() {
    map = new google.maps.Map(document.getElementById('mapa-sucursales'), {
        center: { lat: 9.0, lng: -66.5 },
        zoom: 6.5,
        styles: [
            { elementType: 'geometry', stylers: [{ color: '#141414' }] },
            { elementType: 'labels.text.stroke', stylers: [{ color: '#141414' }] },
            { elementType: 'labels.text.fill', stylers: [{ color: '#888' }] },
            { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#2a2a2a' }] },
            { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0d1b2a' }] },
            { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#1c1c1c' }] },
        ]
    });
    infowindow = new google.maps.InfoWindow({ maxWidth: 300 });

    const sucursales = @json($sucursales);
    const productos = @json($productos);

    const stockGlobal = {};
    productos.forEach(p => { stockGlobal[p.codigo] = p.stock; });

    Object.entries(sucursales).forEach(([nombre, data]) => {
        const stockSimulado = Math.floor(Math.random() * (data.dist > 0 ? 80 : 200)) + 10;
        const marker = new google.maps.Marker({
            position: { lat: data.lat, lng: data.lng },
            map, title: nombre,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: data.dist === 0 ? 14 : 10,
                fillColor: data.dist === 0 ? '#E50914' : (stockSimulado < 30 ? '#ffc107' : '#00b894'),
                fillOpacity: 0.9,
                strokeColor: '#fff',
                strokeWeight: 2,
            }
        });
        marker.addListener('click', () => {
            const prods = productos.filter(p => p.stock < p.stock_minimo).slice(0, 5);
            let prodHtml = prods.length ? prods.map(p =>
                `<div class="prod-item"><span>${p.nombre}</span><span style="color:${p.stock === 0 ? '#E50914' : '#ffc107'}">${p.stock} uds</span></div>`
            ).join('') : '<div style="color:#555;font-size:0.8rem;">Stock saludable</div>';

            infowindow.setContent(`
                <div class="info-window">
                    <h6>${nombre}</h6>
                    <div style="font-size:0.8rem;color:#888;margin-bottom:8px;">
                        ${data.dist === 0 ? '📍 Sede Principal' : `📦 ${number_format(data.dist)} km de Caracas`}
                    </div>
                    <div style="font-size:0.85rem;color:#00b894;margin-bottom:8px;">
                        <i class="bi bi-box-seam"></i> Stock local estimado: <strong>${stockSimulado}</strong> uds
                    </div>
                    <div style="font-size:0.7rem;color:#E50914;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Alertas de stock</div>
                    ${prodHtml}
                </div>
            `);
            infowindow.open(map, marker);
        });
        markers.push(marker);

        const slug = '{{ Str::slug('') }}' + nombre.replace(/\s+/g, '-').toLowerCase();
        const el = document.querySelector(`[data-name="${nombre}"] [id^="stock-"]`);
        if (el) el.textContent = stockSimulado + ' uds';
    });
};

window.centrarMapa = function(nombre, lat, lng) {
    map.setCenter({ lat, lng });
    map.setZoom(10);
    const marker = markers.find(m => m.getTitle() === nombre);
    if (marker) { google.maps.event.trigger(marker, 'click'); }
    document.querySelectorAll('.sucursal-card').forEach(c => c.classList.remove('active'));
    const card = document.querySelector(`.sucursal-card[data-name="${nombre}"]`);
    if (card) card.classList.add('active');
};

function number_format(x) { return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
</script>
@endpush
