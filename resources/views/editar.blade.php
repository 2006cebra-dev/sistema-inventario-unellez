@extends('layout')

@section('contenido')

@php
    $isAdmin = Auth::check() && Auth::user()->role === 'admin';
    $inputCol = $isAdmin ? 'col-md-4' : 'col-md-6';
    
    // 🧠 LÓGICA INTELIGENTE DE FOTO Y DESCRIPCIÓN
    $esImagenApi = isset($producto->descripcion) && str_contains($producto->descripcion, 'http') && !str_contains($producto->descripcion, 'flaticon');
    $fotoMostrar = $esImagenApi ? $producto->descripcion : ($producto->imagen ?? 'https://cdn-icons-png.flaticon.com/512/1174/1174466.png');
    $descMostrar = $esImagenApi ? '' : $producto->descripcion;
@endphp

<div class="card shadow border-0 mx-auto mt-4 mb-5" style="border-radius: 20px; max-width: 900px; background-color: #1e293b;">
    
    <div class="card-header border-bottom border-secondary py-4" style="background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 20px 20px 0 0;">
        <div class="d-flex justify-content-between align-items-center px-2">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-25 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-pencil-square text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-white">Editar Producto</h4>
                    <small class="text-secondary">Modifica los detalles del inventario</small>
                </div>
            </div>
            <a href="/" class="btn btn-outline-light rounded-pill px-4 py-2 shadow-sm transition-all">
                <i class="bi bi-arrow-left me-1"></i> Volver al Panel
            </a>
        </div>
    </div>

    <div class="card-body p-4 p-md-5 text-white">
        <form action="/productos/actualizar/{{ $producto->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-4 text-center mb-4 border-end border-secondary border-opacity-50">
                    <label class="form-label fw-bold d-block text-uppercase small text-secondary mb-3">Fotografía</label>
                    
                    <div class="position-relative d-inline-block mb-3 bg-white p-2 rounded-4 shadow" style="border: 1px solid #334155;">
                        <img id="preview" src="{{ $fotoMostrar }}" 
                             onerror="this.src='https://cdn-icons-png.flaticon.com/512/1174/1174466.png';"
                             class="rounded-4" 
                             style="width: 200px; height: 200px; object-fit: contain;">
                        
                        @if($esImagenApi)
                            <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success border border-light shadow-sm px-2 py-1" style="font-size: 0.7rem;">
                                <i class="bi bi-globe"></i> API Global
                            </div>
                        @else
                            <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-lg" style="transform: translate(20%, 20%);">
                                <i class="bi bi-camera-fill"></i>
                            </div>
                        @endif
                    </div>

                    <div class="px-3 mt-3">
                        @if($esImagenApi)
                            <input type="hidden" name="descripcion" value="{{ $producto->descripcion }}">
                            <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 text-success small py-2 rounded-3">
                                <i class="bi bi-shield-check me-1"></i> Imagen protegida por la base de datos mundial.
                            </div>
                        @else
                            <input type="file" name="foto" id="fotoInput" class="form-control form-control-sm bg-dark text-white border-secondary rounded-pill mb-2" accept="image/*">
                            <small class="text-secondary d-block" style="font-size: 0.75rem;">Sube una foto nueva para actualizar.</small>
                        @endif
                    </div>
                </div>

                <div class="col-md-8 ps-md-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Código de Barras</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-dark border-secondary text-primary rounded-start-3"><i class="bi bi-upc-scan fs-5"></i></span>
                            <input type="text" class="form-control bg-dark border-secondary text-white fw-bold fs-5 rounded-end-3" value="{{ $producto->codigo }}" readonly title="El código es único y no se edita">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control bg-dark border-secondary text-white rounded-3 py-2 px-3 focus-ring" value="{{ $producto->nombre }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Marca</label>
                            <input type="text" name="marca" class="form-control bg-dark border-secondary text-white rounded-3 py-2 px-3 focus-ring" value="{{ $producto->marca }}" placeholder="Ej: Genérico...">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="{{ $inputCol }} mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Categoría</label>
                            <select name="categoria" class="form-select bg-dark border-secondary text-white rounded-3 py-2 focus-ring">
                                <option value="General" {{ ($producto->categoria ?? '') == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Alimentos" {{ ($producto->categoria ?? '') == 'Alimentos' ? 'selected' : '' }}>Alimentos</option>
                                <option value="Bebidas" {{ ($producto->categoria ?? '') == 'Bebidas' ? 'selected' : '' }}>Bebidas</option>
                                <option value="Limpieza" {{ ($producto->categoria ?? '') == 'Limpieza' ? 'selected' : '' }}>Limpieza</option>
                                <option value="Higiene" {{ ($producto->categoria ?? '') == 'Higiene' ? 'selected' : '' }}>Higiene</option>
                                @if($esImagenApi)
                                    <option value="{{ $producto->categoria }}" selected>{{ $producto->categoria }}</option>
                                @endif
                            </select>
                        </div>
                        
                        @if($isAdmin)
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Precio Unitario ($)</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-success border-success text-white"><i class="bi bi-currency-dollar"></i></span>
                                <input type="number" name="precio" class="form-control bg-dark border-success text-white fw-bold py-2 focus-ring" step="0.01" min="0" value="{{ $producto->precio ?? '0.00' }}" required>
                            </div>
                        </div>
                        @endif

                        <div class="{{ $inputCol }}">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control bg-dark border-secondary text-white rounded-3 py-2 focus-ring" value="{{ $producto->fecha_vencimiento }}">
                        </div>
                    </div>

                    @if(!$esImagenApi)
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Descripción / Detalles</label>
                        <textarea name="descripcion" class="form-control bg-dark border-secondary text-white rounded-3 py-2 px-3 focus-ring" rows="3" placeholder="Detalles adicionales...">{{ $descMostrar }}</textarea>
                    </div>
                    @endif

                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-4 border-top border-secondary border-opacity-50">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg transition-all" style="font-size: 1.1rem;">
                    <i class="bi bi-save2-fill me-2"></i> Actualizar Producto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Solo activamos esto si existe el input de foto (no es API)
    const fotoInput = document.getElementById('fotoInput');
    if(fotoInput) {
        fotoInput.onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                document.getElementById('preview').src = URL.createObjectURL(file);
            }
        }
    }
</script>

<style>
    /* Estilos para embellecer los inputs y botones */
    .focus-ring:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4) !important;
    }
</style>
@endsection