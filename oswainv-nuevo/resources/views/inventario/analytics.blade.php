<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analytics - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('partials.navbar')
    <style>
        :root { --bg: #0f0f0f; --card: #1a1a1a; --n-red: #E50914; --n-border: #2a2a2a; --text: #e5e5e5; }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; }
        .main-content { padding-top: 80px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1.5rem; animation: fadeUp 0.5s ease; transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-4px); border-color: var(--n-red); }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }
        .stat-card:nth-child(4) { animation-delay: 0.3s; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-icon { font-size: 1.8rem; margin-bottom: 8px; }
        .stat-value { font-size: 1.8rem; font-weight: 800; font-family: 'Consolas', monospace; }
        .stat-label { font-size: 0.7rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .chart-card { background: var(--card); border: 1px solid var(--n-border); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .chart-card canvas { max-height: 280px; }
        .rank-item { display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1rem; background: var(--card); border: 1px solid var(--n-border); border-radius: 12px; margin-bottom: 6px; transition: all 0.3s; }
        .rank-item:hover { border-color: var(--n-red); transform: translateX(4px); }
        .rank-num { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0; background: rgba(229,9,20,0.12); color: var(--n-red); }
        .rank-num.gold { background: rgba(255,215,0,0.15); color: #ffd700; }
        .rank-num.silver { background: rgba(192,192,192,0.15); color: #c0c0c0; }
        .rank-num.bronze { background: rgba(205,127,50,0.15); color: #cd7f32; }
        .rank-info { flex: 1; min-width: 0; }
        .rank-name { font-weight: 600; color: #fff; font-size: 0.9rem; }
        .rank-meta { font-size: 0.75rem; color: #666; }
        .rank-value { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; background: rgba(229,9,20,0.12); color: var(--n-red); }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container-fluid px-4 py-4" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="text-white fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2" style="color:var(--n-red);"></i>Analytics</h4>
                <div style="color:#666;font-size:0.8rem;margin-top:4px;">Rentabilidad, rotación y rendimiento del inventario</div>
            </div>
            <div style="font-size:0.75rem;color:#555;">
                <i class="bi bi-currency-dollar me-1"></i>Tasa BCV: <strong>$ {{ number_format($tasaBcv, 2) }}</strong>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color:var(--n-red);"><i class="bi bi-boxes"></i></div>
                <div class="stat-value" style="color:var(--n-red);">{{ $totalProductos }}</div>
                <div class="stat-label">Total Productos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color:#ffc107;"><i class="bi bi-box"></i></div>
                <div class="stat-value" style="color:#ffc107;">{{ $totalStock }}</div>
                <div class="stat-label">Stock Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color:#00b894;"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value" style="color:#00b894;">$ {{ number_format($totalGananciaPotencial, 2) }}</div>
                <div class="stat-label">Ganancia Potencial Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color:#0984e3;"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-value" style="color:#0984e3;">Bs {{ number_format($totalGananciaPotencial * $tasaBcv, 2) }}</div>
                <div class="stat-label">Ganancia Potencial Bs</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6 class="text-white fw-bold mb-3"><i class="bi bi-trophy-fill me-2" style="color:#ffd700;"></i>Top 5 Más Rentables</h6>
                    @forelse($topRentables as $i => $p)
                    <div class="rank-item">
                        <div class="rank-num {{ $i == 0 ? 'gold' : ($i == 1 ? 'silver' : ($i == 2 ? 'bronze' : '')) }}">{{ $i + 1 }}</div>
                        <div class="rank-info">
                            <div class="rank-name">{{ $p->nombre }}</div>
                            <div class="rank-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · Margen: {{ $p->margen }}%</div>
                        </div>
                        <div class="rank-value">$ {{ number_format($p->ganancia, 2) }}</div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:2rem;color:#555;">No hay datos de rentabilidad (faltan precios de costo)</div>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6 class="text-white fw-bold mb-3"><i class="bi bi-arrow-up-right-circle-fill me-2" style="color:var(--n-red);"></i>Top 5 Más Vendidos</h6>
                    @forelse($topVendidos as $i => $item)
                    <div class="rank-item">
                        <div class="rank-num {{ $i == 0 ? 'gold' : ($i == 1 ? 'silver' : ($i == 2 ? 'bronze' : '')) }}">{{ $i + 1 }}</div>
                        <div class="rank-info">
                            <div class="rank-name">{{ $item['producto']->nombre }}</div>
                            <div class="rank-meta"><i class="bi bi-upc-scan me-1"></i>{{ $item['producto']->codigo }}</div>
                        </div>
                        <div class="rank-value">{{ $item['total_salidas'] }} uds</div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:2rem;color:#555;">No hay movimientos de salida registrados</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6 class="text-white fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2" style="color:#0984e3;"></i>Ganancias por Categoría</h6>
                    <canvas id="chartCategorias"></canvas>
                    <div class="mt-3">
                        @foreach($gananciasPorCategoria as $cat)
                        <div class="d-flex justify-content-between align-items-center py-1" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <span style="font-size:0.85rem;color:#ccc;">{{ $cat->categoria }}</span>
                            <span style="font-size:0.85rem;font-weight:600;color:#00b894;">$ {{ number_format($cat->ganancia_total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6 class="text-white fw-bold mb-3"><i class="bi bi-clock-history me-2" style="color:#ffc107;"></i>Baja Rotación (60 días sin mover)</h6>
                    @forelse($bajaRotacion as $i => $p)
                    <div class="rank-item">
                        <div class="rank-num">{{ $i + 1 }}</div>
                        <div class="rank-info">
                            <div class="rank-name">{{ $p->nombre }}</div>
                            <div class="rank-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · Stock: {{ $p->stock }} {{ $p->unidad_medida ?? 'uds' }}</div>
                        </div>
                        <div class="rank-value" style="background:rgba(253,203,110,0.12);color:#ffc107;">$ {{ number_format($p->stock * ($p->precio_costo ?: $p->precio), 2) }}</div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:2rem;color:#555;"><i class="bi bi-check-circle-fill" style="font-size:2rem;color:#00b894;display:block;margin-bottom:8px;"></i>Todos los productos han tenido movimiento</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="chart-card">
                    <h6 class="text-white fw-bold mb-3"><i class="bi bi-arrow-down-right-circle-fill me-2" style="color:#e74c3c;"></i>Productos con Pérdida</h6>
                    <div class="row g-2">
                    @forelse($perdidas as $i => $p)
                        <div class="col-lg-4 col-md-6">
                            <div class="rank-item">
                                <div class="rank-num" style="background:rgba(231,76,60,0.15);color:#e74c3c;">{{ $i + 1 }}</div>
                                <div class="rank-info">
                                    <div class="rank-name">{{ $p->nombre }}</div>
                                    <div class="rank-meta"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo }} · Costo: ${{ number_format($p->precio_costo, 2) }} / Venta: ${{ number_format($p->precio, 2) }}</div>
                                </div>
                                <div class="rank-value" style="background:rgba(231,76,60,0.12);color:#e74c3c;">-$ {{ number_format(abs($p->ganancia), 2) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12" style="text-align:center;padding:2rem;color:#555;"><i class="bi bi-emoji-smile-fill" style="font-size:2rem;color:#00b894;display:block;margin-bottom:8px;"></i>No hay productos con pérdida</div>
                    @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.mobile-bottom-nav')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const ctx = document.getElementById('chartCategorias');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($labelsCategorias) !!},
            datasets: [{
                data: {!! json_encode($dataCategorias) !!},
                backgroundColor: ['#E50914','#ffc107','#00b894','#0984e3','#e17055','#a29bfe','#fd79a8','#00cec9','#636e72','#fdcb6e'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#ccc', font: { size: 11 }, padding: 16 } }
            }
        }
    });
}
</script>
</body>
</html>