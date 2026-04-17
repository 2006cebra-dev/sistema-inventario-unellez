@extends('layout')

@section('contenido')
<div class="card shadow-sm border-0 mx-auto mt-4" style="border-radius: 20px; max-width: 900px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h3 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square"></i> Editar Producto</h3>
                <p class="text-muted small mb-0">Gestión de inventario - UNELLEZ Barinas</p>
            </div>
            <a href="/" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                <i class="bi bi-arrow-left"></i> Volver al Panel
            </a>
        </div>

        <form action="/productos/actualizar/{{ $producto->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4 text-center mb-4 border-end">
                    <label class="form-label fw-bold d-block text-uppercase small text-muted">Imagen del Producto</label>
                    
                    <div class="position-relative d-inline-block mb-3">
                        <img id="preview" src="{{ $producto->imagen ?? '/img/no-photo.png' }}" 
                             onerror="this.src='/img/no-photo.png';"
                             class="img-thumbnail rounded-4 shadow-sm" 
                             style="width: 220px; height: 220px; object-fit: cover; border: 3px solid #f8fafc;">
                        
                        <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                    </div>

                    <div class="px-2">
                        <input type="file" name="foto" id="fotoInput" class="form-control form-control-sm rounded-pill mb-2" accept="image/*">
                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                            Sube una foto nueva para actualizar la imagen actual.
                        </small>
                    </div>
                </div>

                <div class="col-md-8 ps-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Código de Barras (Único)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-pill"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" class="form-control bg-light fw-bold border-start-0 rounded-end-pill" value="{{ $producto->codigo }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control rounded-3" value="{{ $producto->nombre }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Marca</label>
                            <input type="text" name="marca" class="form-control rounded-3" value="{{ $producto->marca }}" placeholder="Ej: Polar, Mavesa...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Categoría</label>
                            <select name="categoria" class="form-select rounded-3">
                                <option value="General" {{ ($producto->categoria ?? '') == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Alimentos" {{ ($producto->categoria ?? '') == 'Alimentos' ? 'selected' : '' }}>Alimentos</option>
                                <option value="Bebidas" {{ ($producto->categoria ?? '') == 'Bebidas' ? 'selected' : '' }}>Bebidas</option>
                                <option value="Limpieza" {{ ($producto->categoria ?? '') == 'Limpieza' ? 'selected' : '' }}>Limpieza</option>
                                <option value="Higiene" {{ ($producto->categoria ?? '') == 'Higiene' ? 'selected' : '' }}>Higiene Personal</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Fecha de Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control rounded-3" value="{{ $producto->fecha_vencimiento }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Descripción / Detalles</label>
                        <textarea name="descripcion" class="form-control rounded-3" rows="2" placeholder="Detalles adicionales...">{{ $producto->descripcion }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Stock en Depósito</label>
                        <div class="input-group border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light text-muted border-0"><i class="bi bi-boxes"></i></span>
                            <input type="number" name="stock" class="form-control bg-light text-muted border-0 px-3 fw-bold" value="{{ $producto->stock }}" readonly>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle"></i> Por seguridad, el stock solo se modifica desde el panel principal usando los botones rápidos (+ / -).
                        </small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="text-end">
                <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow">
                    <i class="bi bi-check-circle-fill me-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // MAGIA: Vista previa de la foto en tiempo real
    document.getElementById('fotoInput').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
        }
    }
</script>
@endsection