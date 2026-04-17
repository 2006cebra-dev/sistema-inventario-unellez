@extends('layout')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary"></i> Historial de Movimientos</h3>
    <a href="/" class="btn btn-outline-secondary btn-modern shadow-sm">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="ps-4 py-3">Fecha y Hora</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $m)
                <tr>
                    <td class="ps-4 text-muted small">
                        {{ date('d/m/Y h:i A', strtotime($m->created_at)) }}
                    </td>
                    <td>
                        <div class="fw-bold">{{ $m->producto_nombre }}</div>
                        <small class="text-muted">Cod: {{ $m->codigo_producto }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $m->tipo == 'Entrada' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 rounded-pill">
                            {{ $m->tipo }}
                        </span>
                    </td>
                    <td class="fw-bold text-center">{{ $m->cantidad }}</td>
                    <td class="text-muted italic small">{{ $m->motivo }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection