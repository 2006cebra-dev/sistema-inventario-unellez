@extends('layout')

@section('contenido')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

@php
    $entradas = $movimientos->where('tipo', 'Entrada')->count();
    $salidas = $movimientos->where('tipo', 'Salida')->count();
    $movimientosHoy = $movimientos->where('created_at', '>=', \Carbon\Carbon::today())->count();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 adapt-text"><i class="bi bi-clock-history text-primary"></i> Auditoría y Kardex</h3>
    <a href="/" class="btn btn-outline-secondary rounded-pill px-3"><i class="bi bi-arrow-left"></i> Volver al Panel</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm p-3 rounded-4 adapt-text" style="border-left: 4px solid #3b82f6 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted small fw-bold text-uppercase">Movimientos Hoy</p>
                    <h3 class="fw-bold mb-0 text-info">{{ $movimientosHoy }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-2 rounded-circle"><i class="bi bi-calendar-day text-primary fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm p-3 rounded-4 adapt-text" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted small fw-bold text-uppercase">Total Entradas</p>
                    <h3 class="fw-bold mb-0 text-success">{{ $entradas }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-2 rounded-circle"><i class="bi bi-box-arrow-in-down text-success fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm p-3 rounded-4 adapt-text" style="border-left: 4px solid #ef4444 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted small fw-bold text-uppercase">Total Salidas</p>
                    <h3 class="fw-bold mb-0 text-danger">{{ $salidas }}</h3>
                </div>
                <div class="bg-danger bg-opacity-10 p-2 rounded-circle"><i class="bi bi-box-arrow-up text-danger fs-4"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card bg-white border-0 shadow-sm p-4 rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tablaKardex">
            <thead class="text-muted small text-uppercase">
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Producto</th>
                    <th>Operación</th>
                    <th class="text-center">Cantidad</th>
                    <th>Motivo / Destino</th>
                    <th>Usuario Responsable</th>
                    <th class="text-center">Integridad</th> </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $m)
                @php
                    $esEntrada = $m->tipo === 'Entrada';
                    $colorFila = $esEntrada ? 'border-success' : 'border-danger';
                    $icono = $esEntrada ? 'bi-plus-circle-fill text-success' : 'bi-dash-circle-fill text-danger';
                    
                    // IA Visual para Motivos
                    $motivoLower = strtolower($m->motivo);
                    $iconoMotivo = 'bi-info-circle text-secondary';
                    if (str_contains($motivoLower, 'escáner')) $iconoMotivo = 'bi-upc-scan text-info';
                    if (str_contains($motivoLower, 'transferencia')) $iconoMotivo = 'bi-truck text-warning';
                    if (str_contains($motivoLower, 'api')) $iconoMotivo = 'bi-globe text-primary';
                    if (str_contains($motivoLower, 'fefo')) $iconoMotivo = 'bi-exclamation-diamond-fill text-danger';
                @endphp
                <tr style="border-left: 3px solid transparent;" class="{{ $colorFila }} fila-hover">
                    <td class="adapt-text">
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($m->created_at)->format('h:i A') }}</small>
                    </td>
                    <td>
                        <div class="fw-bold adapt-text">{{ $m->producto_nombre }}</div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal border border-secondary-subtle">{{ $m->codigo_producto }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $esEntrada ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }} rounded-pill px-3">
                            <i class="bi {{ $icono }} me-1"></i> {{ strtoupper($m->tipo) }}
                        </span>
                    </td>
                    <td class="text-center fs-5 fw-bold {{ $esEntrada ? 'text-success' : 'text-danger' }}">
                        {{ $esEntrada ? '+' : '-' }}{{ $m->cantidad }}
                    </td>
                    <td class="adapt-text">
                        <i class="bi {{ $iconoMotivo }} me-2"></i> {{ $m->motivo }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-1 me-2 border border-secondary-subtle">
                                <i class="bi bi-person-badge text-secondary"></i>
                            </div>
                            <span class="fw-bold adapt-text">{{ $m->usuario_accion }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($m->es_valido === true)
                            <span class="badge bg-success-subtle text-success border border-success" title="Hash SHA-256 Verificado"><i class="bi bi-shield-check-fill"></i> Seguro</span>
                        @elseif($m->es_valido === false)
                            <span class="badge bg-danger text-white pulse-rojo" title="¡ALERTA! Registro alterado en la base de datos"><i class="bi bi-shield-x-fill"></i> FRAUDE</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary" title="Registro antiguo sin firma"><i class="bi bi-shield-minus"></i> Legado</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#tablaKardex').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[ 0, "desc" ]], // Ordenar por fecha más reciente
            "pageLength": 15,
            "lengthMenu": [15, 30, 50, 100],
            "dom": '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel-fill"></i> Excel',
                    className: 'btn btn-sm btn-success shadow-sm mb-3',
                    title: 'Auditoría Kardex - OSWA-INV',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } // Agregado el 6 para exportar Seguridad
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf-fill"></i> PDF',
                    className: 'btn btn-sm btn-danger shadow-sm mb-3 ms-2',
                    title: 'Auditoría Kardex - OSWA-INV',
                    orientation: 'landscape',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } // Agregado el 6
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer-fill"></i> Imprimir',
                    className: 'btn btn-sm btn-secondary shadow-sm mb-3 ms-2',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } // Agregado el 6
                }
            ]
        });
    });
</script>

<style>
    /* Efectos hover universales */
    .fila-hover:hover { background-color: rgba(0, 0, 0, 0.03) !important; transition: 0.2s; }
    .border-success { border-left-color: #10b981 !important; }
    .border-danger { border-left-color: #ef4444 !important; }
    
    /* Variables de texto para Modo Claro (por defecto) */
    .adapt-text { color: #334155; }

    /* MODO OSCURO */
    body.dark-mode .adapt-text { color: #f1f5f9 !important; }
    body.dark-mode .fila-hover:hover { background-color: rgba(255, 255, 255, 0.05) !important; }
    
    body.dark-mode .dataTables_filter input {
        background-color: #0f172a !important;
        color: white !important;
        border: 1px solid #334155 !important;
        border-radius: 8px;
        padding: 5px 10px;
    }
    body.dark-mode .dataTables_length select {
        background-color: #0f172a !important;
        color: white !important;
        border: 1px solid #334155 !important;
        border-radius: 8px;
    }

    /* 🛡️ ANIMACIÓN DE ALERTA DE FRAUDE */
    @keyframes latido-rojo {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    .pulse-rojo {
        animation: latido-rojo 1.5s infinite;
        border: 1px solid #ef4444;
    }
</style>

@endsection