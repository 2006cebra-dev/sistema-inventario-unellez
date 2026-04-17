@extends('layout')

@section('contenido')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(45deg, #4e73df, #224abe); border-radius: 15px;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Total Productos</p>
                    <h2 class="fw-bold mb-0">{{ $totalProductos }}</h2>
                </div>
                <i class="bi bi-box-seam fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(45deg, #1cc88a, #13855c); border-radius: 15px;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Stock en Depósito</p>
                    <h2 id="total-unidades-card" class="fw-bold mb-0">{{ $totalUnidades }}</h2>
                </div>
                <i class="bi bi-truck fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(45deg, #e74a3b, #be2617); border-radius: 15px;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Alertas Stock Bajo</p>
                    <h2 class="fw-bold mb-0">{{ $bajoStock }}</h2>
                </div>
                <i class="bi bi-exclamation-triangle fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 align-items-center">
    <div class="col-md-5">
        <div class="input-group shadow-sm border rounded-pill overflow-hidden bg-white">
            <span class="input-group-text bg-white border-0 px-3"><i class="bi bi-search text-primary"></i></span>
            <input type="text" id="buscador" class="form-control border-0 p-3" placeholder="Buscar por nombre, código, marca o categoría...">
        </div>
    </div>
    <div class="col-md-7 text-end">
        <a href="/historial" class="btn btn-dark shadow-sm rounded-pill px-4 py-2">
            <i class="bi bi-clock-history"></i> Historial
        </a>
        <a href="/productos/excel" class="btn btn-success shadow-sm rounded-pill px-4 py-2 ms-2">
            <i class="bi bi-file-earmark-spreadsheet"></i> Excel
        </a>
        <a href="/productos/pdf" target="_blank" class="btn btn-outline-danger shadow-sm rounded-pill px-4 py-2 ms-2">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>
        <a href="/productos/respaldo" class="btn btn-warning shadow-sm rounded-pill px-4 py-2 ms-2 text-white">
            <i class="bi bi-shield-lock-fill"></i> Respaldar DB
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-muted text-uppercase small mb-0">
                    <i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Distribución por Categoría
                </h6>
            </div>
            <div style="height: 250px;">
                <canvas id="graficaCategorias"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-list-stars text-primary me-2"></i> Inventario Actual</h5>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm">Alto Barinas, Venezuela</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaProductos">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Producto</th>
                    <th>Categoría/Marca</th>
                    <th class="text-center">Vencimiento</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $p)
                @php
                    $hoy = \Carbon\Carbon::now();
                    $vence = $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento) : null;
                    $esCritico = $vence && $vence->diffInDays($hoy, false) >= -7 && $vence->isFuture();
                    $vencido = $vence && $vence->isPast();
                    
                    $filaColor = '';
                    if($p->stock <= 0) $filaColor = 'table-danger';
                    elseif($p->stock <= 5) $filaColor = 'table-warning';

                    $cat = strtolower($p->categoria ?? 'general');
                    $badgeColor = 'bg-primary-subtle text-primary';
                    if ($cat == 'alimentos') $badgeColor = 'bg-success-subtle text-success';
                    elseif ($cat == 'bebidas') $badgeColor = 'bg-info-subtle text-info';
                    elseif ($cat == 'limpieza') $badgeColor = 'bg-warning-subtle text-warning';
                    elseif ($cat == 'higiene') $badgeColor = 'bg-secondary-subtle text-secondary';
                @endphp
                <tr id="fila-{{ $p->id }}" class="{{ $filaColor }}">
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ $p->imagen ?? '/img/no-photo.png' }}" 
                                 onerror="this.src='/img/no-photo.png';"
                                 class="rounded-3 me-3 shadow-sm border" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark">{{ $p->nombre }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $p->codigo }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeColor }} rounded-pill mb-1">{{ ucfirst($p->categoria ?? 'General') }}</span><br>
                        <small class="text-muted">{{ $p->marca ?? 'S/M' }}</small>
                    </td>
                    <td class="text-center">
                        @if($vence)
                            <span class="{{ $vencido ? 'text-danger fw-bold' : ($esCritico ? 'text-warning fw-bold' : '') }}">
                                {{ $vence->format('d/m/Y') }}
                                @if($vencido) <i class="bi bi-x-circle-fill"></i> @elseif($esCritico) <i class="bi bi-exclamation-triangle-fill"></i> @endif
                            </span>
                        @else
                            <span class="text-muted small">---</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex align-items-center bg-white border rounded-pill p-1 shadow-sm">
                            <button onclick="ajustarStockAjax({{ $p->id }}, 'restar')" class="btn btn-sm btn-light rounded-circle text-danger p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;"><i class="bi bi-dash"></i></button>
                            <span id="stock-val-{{ $p->id }}" class="px-3 fw-bold {{ $p->stock <= 5 ? 'text-danger' : '' }}">{{ $p->stock }}</span>
                            <button onclick="ajustarStockAjax({{ $p->id }}, 'sumar')" class="btn btn-sm btn-light rounded-circle text-success p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;"><i class="bi bi-plus"></i></button>
                        </div>
                    </td>
                    <td class="text-center pe-4">
                        <div class="btn-group shadow-sm" style="border-radius: 50px; overflow: hidden;">
                            <a href="/productos/editar/{{ $p->id }}" class="btn btn-sm btn-primary px-3">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="confirmarEliminar('{{ $p->codigo }}', '{{ $p->nombre }}')" class="btn btn-sm btn-danger px-3">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // 📊 GRÁFICA
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('graficaCategorias').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsCat) !!},
                datasets: [{
                    label: 'Cantidad de Productos',
                    data: {!! json_encode($dataCat) !!},
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.7)', 
                        'rgba(28, 200, 138, 0.7)', 
                        'rgba(54, 185, 204, 0.7)', 
                        'rgba(246, 194, 62, 0.7)', 
                        'rgba(231, 74, 59, 0.7)'
                    ],
                    borderColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 1,
                    borderRadius: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });

    // ⚡ AJAX
    function ajustarStockAjax(id, accion) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        fetch(`/productos/ajustar/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ accion: accion })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const stockSpan = document.getElementById(`stock-val-${id}`);
                const fila = document.getElementById(`fila-${id}`);
                const nuevoStock = data.nuevo_stock;
                stockSpan.innerText = nuevoStock;
                
                stockSpan.classList.remove('text-danger');
                fila.classList.remove('table-warning', 'table-danger');
                if(nuevoStock <= 0) {
                    stockSpan.classList.add('text-danger');
                    fila.classList.add('table-danger');
                } else if(nuevoStock <= 5) {
                    stockSpan.classList.add('text-danger');
                    fila.classList.add('table-warning');
                }

                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 1200, timerProgressBar: true
                });
                Toast.fire({ icon: accion === 'sumar' ? 'success' : 'info', title: accion === 'sumar' ? '+1 Unidad' : '-1 Unidad' });
            }
        });
    }

    // 🔍 BUSCADOR
    document.getElementById('buscador').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tablaProductos tbody tr');
        filas.forEach(fila => {
            fila.style.display = fila.innerText.toLowerCase().includes(filtro) ? '' : 'none';
        });
    });

    // 🗑️ ELIMINAR
    function confirmarEliminar(codigo, nombre) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: `Vas a borrar: ${nombre}. No se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/productos/eliminar/" + codigo;
            }
        })
    }
</script>

<style>
    tr { transition: background-color 0.3s ease; }
    .table-warning { background-color: #fff9e6 !important; }
    .table-danger { background-color: #ffe6e6 !important; }
    .btn-group .btn { border: none; }
    .bi-plus, .bi-dash { font-weight: bold; font-size: 1.2rem; }
</style>
@endsection