@extends('layout')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <h4>Registrar Nuevo Producto</h4>
                <form action="/productos/guardar" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Código de Barras</label>
                        <input type="text" name="codigo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Inicial</label>
                        <input type="number" name="stock" class="form-control" value="0">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Guardar Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection