@extends('layout')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 mi-fondo">
                <h5 class="fw-bold mb-0 text-dark mi-texto">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i> Registrar Nuevo Producto
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="/productos/guardar" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Código de Barras</label>
                            <input type="text" name="codigo" class="form-control form-control-lg" required placeholder="Ej. 759100...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" required placeholder="Ej. Harina PAN">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Stock Inicial</label>
                            <input type="number" name="stock" class="form-control form-control-lg" value="0" min="0">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Precio Unitario ($)</label>
                            <div class="input-group input-group-lg shadow-sm rounded overflow-hidden">
                                <span class="input-group-text bg-success text-white border-success"><i class="bi bi-currency-dollar"></i></span>
                                <input type="number" name="precio" class="form-control border-success" step="0.01" min="0" value="0.00" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Categoría</label>
                            <select name="categoria" class="form-select form-control-lg">
                                <option value="Alimentos">Alimentos</option>
                                <option value="Bebidas">Bebidas</option>
                                <option value="Limpieza">Limpieza</option>
                                <option value="Higiene">Higiene Personal</option>
                                <option value="General" selected>General</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Foto del Producto (Opcional)</label>
                            <input type="file" name="foto" class="form-control form-control-lg" accept="image/*">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="/" class="btn btn-light me-2 mi-boton px-4 rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-save me-2"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection