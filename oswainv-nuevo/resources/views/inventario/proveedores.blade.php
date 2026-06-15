<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Proveedores - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-main: #121212; --bg-card: #1c1c1c; --n-red: #E50914; --n-border: #2b2b2b;
            --bg-dark: #121212; --bg-input: #2a2a2a; --border-color: #2b2b2b;
            --text-primary: #e5e5e5; --text-secondary: #a3a3a3; --accent-primary: #E50914;
            --accent-success: #00b894; --accent-danger: #e74c3c; --accent-warning: #fdcb6e;
            --topbar-height: 68px;
        }
        * { font-family: 'Inter', sans-serif; }
        body, html { overflow-x: hidden !important; max-width: 100vw; }
        body { background-color: var(--bg-main) !important; color: #e5e5e5 !important; margin: 0; }
        
        .card { background-color: var(--bg-card) !important; border: 1px solid var(--n-border) !important; border-radius: 12px !important; transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; }
        .card:hover { transform: translateY(-5px) scale(1.02); border-color: var(--n-red) !important; box-shadow: 0 10px 20px rgba(0,0,0,0.5); z-index: 5; }

        .modal-content { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--n-border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--n-border); }
        .modal-footer { border-top: 1px solid var(--n-border); }
        .form-control { background: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; }
        .form-control:focus { background: #333; border-color: var(--accent-primary); color: #e5e5e5; box-shadow: 0 0 0 0.25rem rgba(229,9,20,0.25); }
        .form-label { color: var(--text-secondary); }
        .form-select { background-color: var(--bg-input); border: 1px solid var(--n-border); color: var(--text-primary); border-radius: 8px; }
        .form-select:focus { background-color: #333; border-color: var(--accent-primary); color: #e5e5e5; }
        
        .modal-content-premium { background-color: #1c1c1c !important; border-radius: 16px !important; box-shadow: 0 20px 60px rgba(0,0,0,0.8) !important; }
        .oswa-input-solid { background-color: #2a2a2a !important; border: 1px solid #333 !important; color: #fff !important; border-radius: 8px !important; }
        .oswa-input-solid:focus { background-color: #333 !important; border-color: #E50914 !important; box-shadow: 0 0 0 0.2rem rgba(229,9,20,0.25) !important; }

        @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .animate-page-enter { animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.1s; }
        
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; border-left: 1px solid #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #B20710; border-radius: 10px; border: 2px solid #0a0a0a; }
        ::-webkit-scrollbar-thumb:hover { background: #E50914; }
    </style>
</head>
<body data-theme="dark">
    
    @include('partials.navbar')
    
    
    <!-- CONTENIDO PRINCIPAL DE PROVEEDORES -->
    <main class="main-content">
        <div class="d-flex flex-column flex-md-row align-items-md-center mb-4 pb-3 border-bottom border-secondary border-opacity-50 gap-3 animate-page-enter">
            <h2 class="text-white m-0 d-flex align-items-center gap-2">
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-2 text-warning d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-buildings fs-4"></i>
                </div>
                Directorio de Proveedores
            </h2>
            
            <!-- El ms-md-auto empuja todo este bloque hacia la derecha -->
            <div class="d-flex gap-2 ms-md-auto">
                <button class="btn btn-outline-light text-white border-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#modalOrdenesCompra">
                    <i class="bi bi-receipt"></i> Historial de Compras
                </button>
                <button class="btn btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalNuevoProveedor" onclick="limpiarModalProveedor()">
                    <i class="bi bi-plus-lg"></i> Registrar Proveedor
                </button>
            </div>
        </div>

        <div class="row animate-page-enter delay-1" id="contenedor-proveedores">
            @forelse($proveedores as $proveedor)
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 provider-card" style="background: var(--bg-card); border: 1px solid var(--n-border); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.4); transition: all 0.3s ease;">
                        
                        <div class="card-body p-4">
                            <!-- CABECERA: Logo, Nombre y RIF -->
                            <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <!-- Logo de la Empresa (CORREGIDO) -->
                                <div class="me-3 flex-shrink-0">
                                    @if($proveedor->logo)
                                        <img src="{{ asset('storage/' . $proveedor->logo) }}" alt="{{ $proveedor->nombre }}" style="width: 65px; height: 65px; object-fit: cover; border-radius: 12px; border: 2px solid #333;" onerror="this.onerror=null;this.parentElement.innerHTML='<div style=\'width:65px;height:65px;background:linear-gradient(135deg,#2b2b2b,#1a1a1a);border-radius:12px;display:flex;align-items:center;justify-content:center;border:2px solid #333;color:#ffc107;font-size:1.8rem;font-weight:bold;\'>{{ strtoupper(substr($proveedor->nombre, 0, 1)) }}</div>'">
                                    @else
                                        <!-- Placeholder si no hay logo -->
                                        <div style="width: 65px; height: 65px; background: linear-gradient(135deg, #2b2b2b, #1a1a1a); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 2px solid #333; color: var(--accent-warning); font-size: 1.8rem; font-weight: bold; box-shadow: inset 0 0 10px rgba(0,0,0,0.5);">
                                            {{ strtoupper(substr($proveedor->nombre, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Info Principal -->
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h5 class="text-white fw-bold mb-1 text-truncate" title="{{ $proveedor->nombre }}">{{ $proveedor->nombre }}</h5>
                                    <span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ccc; font-family: monospace; letter-spacing: 0.5px;">
                                        <i class="bi bi-upc-scan me-1"></i> {{ $proveedor->rif }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- DATOS DE CONTACTO -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2 text-secondary">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(9, 132, 227, 0.1); color: #0984e3; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>
                                    <span class="text-truncate" style="font-size: 0.95rem;">{{ $proveedor->contacto ?: 'Sin contacto registrado' }}</span>
                                </div>
                                <div class="d-flex align-items-center text-secondary">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0, 184, 148, 0.1); color: #00b894; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <span style="font-size: 0.95rem; font-family: monospace;">{{ $proveedor->telefono ?: 'Sin teléfono' }}</span>
                                </div>
                            </div>

                            <!-- BOTÓN CATÁLOGO -->
                            <button class="btn w-100 d-flex justify-content-between align-items-center" onclick="verProductosProveedor({{ json_encode($proveedor) }})" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px 16px; border-radius: 8px; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                <span><i class="bi bi-box-seam text-info me-2"></i> Ver Catálogo</span>
                                <span class="badge rounded-pill" style="background: var(--accent-primary);">{{ $proveedor->productos ? $proveedor->productos->count() : 0 }}</span>
                            </button>
                        </div>
                        
                        <!-- FOOTER ACCIONES (Admin) -->
                        @if(Auth::check() && Auth::user()->tienePermiso('gestionar_proveedores'))
                        <div class="card-footer bg-transparent p-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex justify-content-between align-items-center">
                                
                                <!-- Botón Abastecer -->
                                <button class="btn btn-sm fw-bold d-flex align-items-center gap-2" onclick="abastecerProveedor({{ json_encode($proveedor) }}, {{ json_encode($proveedor->productos ?? []) }})" style="background: rgba(0, 184, 148, 0.15); color: #00b894; border: 1px solid rgba(0, 184, 148, 0.3); border-radius: 8px; padding: 6px 14px; transition: all 0.2s;" onmouseover="this.style.background='rgba(0, 184, 148, 0.25)'" onmouseout="this.style.background='rgba(0, 184, 148, 0.15)'">
                                    <i class="bi bi-cart-plus fs-5"></i> Abastecer
                                </button>
                                
                                <!-- Botones Editar / Eliminar -->
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm d-flex align-items-center justify-content-center" title="Editar" onclick="editarProveedor({{ json_encode($proveedor) }})" style="width: 36px; height: 36px; background: rgba(253, 203, 110, 0.1); color: #fdcb6e; border: 1px solid rgba(253, 203, 110, 0.2); border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='rgba(253, 203, 110, 0.2)'" onmouseout="this.style.background='rgba(253, 203, 110, 0.1)'">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm d-flex align-items-center justify-content-center" title="Eliminar" onclick="eliminarProveedor({{ $proveedor->id }})" style="width: 36px; height: 36px; background: rgba(229, 9, 20, 0.1); color: #E50914; border: 1px solid rgba(229, 9, 20, 0.2); border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='rgba(229, 9, 20, 0.2)'" onmouseout="this.style.background='rgba(229, 9, 20, 0.1)'">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-dark rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; border: 1px solid #333;">
                        <i class="bi bi-buildings text-secondary" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                    <h5 class="text-white mt-2">Directorio Vacío</h5>
                    <p class="text-muted">Aún no hay proveedores registrados en el sistema.</p>
                </div>
            @endforelse
        </div>
    <!-- ============================================================== -->
    <!-- MODAL: HISTORIAL DE COMPRAS                                    -->
    <!-- ============================================================== -->
    <div class="modal fade" id="modalOrdenesCompra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid #333; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.6);">
                <div class="modal-header border-secondary" style="background: rgba(0,0,0,0.2);">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-receipt text-info me-2"></i> Historial de Adquisiciones</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-dark table-hover align-middle m-0" style="background: transparent;">
                            <thead class="text-secondary sticky-top" style="font-size: 0.8rem; text-transform: uppercase; background: var(--bg-card); box-shadow: 0 2px 5px rgba(0,0,0,0.5);">
                                <tr>
                                    <th class="ps-4 py-3">Fecha</th>
                                    <th class="py-3">Detalle de la Compra</th>
                                    <th class="py-3">Cant. Total</th>
                                    <th class="pe-4 py-3">Responsable</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Consulta directa a la tabla de auditoría para filtrar compras --}}
                                @php
                                    $historialCompras = \App\Models\Movimiento::where('motivo', 'LIKE', 'COMPRA a%')
                                                        ->orderBy('created_at', 'desc')
                                                        ->get();
                                @endphp
                                
                                @forelse($historialCompras as $compra)
                                <tr style="border-bottom: 1px solid #222;">
                                    <td class="ps-4 text-light">
                                        {{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}<br>
                                        <small class="text-secondary">{{ \Carbon\Carbon::parse($compra->created_at)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="text-info" style="font-size: 0.9rem;">{{ $compra->motivo }}</span><br>
                                        <small class="text-secondary">Código ref: {{ $compra->codigo_producto }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success px-2 py-1">
                                            + {{ $compra->cantidad }}
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <span class="badge bg-secondary bg-opacity-25 text-light px-2 py-1">
                                            <i class="bi bi-person-fill"></i> {{ $compra->usuario_accion }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-5">
                                        <i class="bi bi-cart-x fs-1 d-block mb-3 text-muted"></i>
                                        Aún no se han registrado órdenes de compra de mercancía.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-secondary" style="background: rgba(0,0,0,0.2);">
                    <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->

    </main>

<!-- FOOTER GLOBAL OSWA INV -->
<footer class="professional-footer mt-5 pt-4 pb-4" style="text-align: center; border-top: 1px solid #2b2b2b; color: #a3a3a3; font-size: 0.85rem; background-color: transparent;">
    <div class="mb-2">
        &copy; <script>document.write(new Date().getFullYear())</script> <strong class="text-white">OSWA Inv</strong>. Todos los derechos reservados.
    </div>
    <div class="mb-2">
        Desarrollado con <i class="bi bi-code-slash text-secondary"></i> y <i class="bi bi-heart-fill text-danger"></i> por <span class="text-white fw-bold">Carlos Braca & Yorgelys Blanco</span>
    </div>
    
    <div class="d-flex align-items-center justify-content-center gap-3 mt-3" style="font-size: 0.85rem;">
        <span style="color: #888888;">Ingeniería en Informática — V Semestre</span>
        
        <div style="width: 1px; height: 16px; background-color: #444444;"></div>
        
        <div class="d-flex align-items-center gap-2">
            <strong class="text-white" style="letter-spacing: 1px;">OSWA Inv</strong>
        </div>
    </div>
</footer>

    <!-- =======================================================
         MODALES VITALES DEL MÓDULO DE PROVEEDORES
         ======================================================= -->

    <!-- MODAL: REGISTRAR / EDITAR PROVEEDOR -->
    <div class="modal fade" id="modalNuevoProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-building-add text-warning me-2"></i> Registrar Proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formProveedor" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="prov-id">
                        
                        <div class="row">
                            <!-- Columna Izquierda: Logo -->
                            <div class="col-md-4 mb-4 mb-md-0 d-flex flex-column align-items-center justify-content-start">
                                <label class="form-label text-secondary w-100 text-center fw-bold"><i class="bi bi-image me-1"></i> Logo de la Empresa</label>
                                <div class="position-relative mt-2" style="width: 160px; height: 160px; border: 2px dashed #444; border-radius: 50%; overflow: hidden; background: #1a1a1a; cursor: pointer; transition: border-color 0.3s;" onclick="document.getElementById('provLogo').click()" onmouseover="this.style.borderColor='#E50914'" onmouseout="this.style.borderColor='#444'">
                                    <img id="logoPreview" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <div id="logoPlaceholder" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="bi bi-building text-secondary" style="font-size: 3rem; margin-bottom: 5px;"></i>
                                        <span style="font-size: 0.75rem; text-align: center; padding: 0 10px;">Subir Logo<br>(Opcional)</span>
                                    </div>
                                </div>
                                <input type="file" id="provLogo" name="logo" class="d-none" accept="image/*" onchange="previewProvLogo(event)">
                            </div>

                            <!-- Columna Derecha: Datos Principales -->
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label text-secondary">Nombre de la Empresa</label>
                                        <input type="text" name="nombre" id="provNombre" class="form-control bg-dark text-white border-secondary" placeholder="Ej. Alimentos Polar C.A." required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary">RIF</label>
                                        <input type="text" name="rif" id="provRif" class="form-control bg-dark text-white border-secondary" placeholder="Ej. J-12345678-9" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary">Teléfono</label>
                                        <input type="text" name="telefono" id="provTelefono" class="form-control bg-dark text-white border-secondary" placeholder="Ej. 0414-1234567">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary">Persona de Contacto</label>
                                    <input type="text" name="contacto" id="provContacto" class="form-control bg-dark text-white border-secondary" placeholder="Nombre del vendedor">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 mt-2">
                            <label class="form-label text-secondary">Dirección Física</label>
                            <textarea name="direccion" id="provDireccion" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Ubicación del galpón o distribuidora..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarProveedor" class="btn btn-warning text-dark fw-bold"><i class="bi bi-save me-1"></i> Guardar Registro</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: CATÁLOGO DEL PROVEEDOR -->
    <div class="modal fade" id="modalCatalogoProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold" id="tituloCatalogoProv"><i class="bi bi-box-seam text-info me-2"></i> Catálogo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-secondary" style="font-size: 0.85rem;">
                                    <th>CÓDIGO</th>
                                    <th>PRODUCTO</th>
                                    <th>STOCK</th>
                                    <th>PRECIO VENTA</th>
                                    <th>COSTO</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductosProv"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ABASTECER (Orden de Compra) -->
    <div class="modal fade" id="modalAbastecer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid #333;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-cart-check text-success"></i> Registrar Orden de Compra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('compras.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="proveedor_id" id="proveedor_id_compra">
                        
                        <div class="mb-3">
                            <label class="form-label text-light">Seleccionar Producto a Ingresar</label>
                            <div class="input-group">
                                <select class="form-select bg-dark text-white border-secondary" name="producto_id" id="selectProductoCompra" required>
                                    <option value="">Seleccione el producto...</option>
                                </select>
                                <a href="#" class="text-danger fw-bold text-decoration-none ms-2" data-bs-toggle="modal" data-bs-target="#modalNuevoProductoRapido" onclick="event.preventDefault();"><i class="bi bi-plus-circle"></i> Nuevo</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Cantidad (Lote)</label>
                                <input type="number" class="form-control bg-dark text-white border-secondary" name="cantidad" required min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Costo Total ($)</label>
                                <input type="number" step="0.01" class="form-control bg-dark text-white border-secondary" name="costo_total" required>
                            </div>
                        </div>

                        <div class="mb-4 p-3 rounded" style="background: rgba(253, 203, 110, 0.08); border: 1px solid rgba(253, 203, 110, 0.3);">
                            <label class="form-label text-warning fw-bold"><i class="bi bi-calendar-x"></i> Fecha de Vencimiento del Lote</label>
                            <input type="date" class="form-control bg-dark text-white border-warning" name="fecha_vencimiento" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Requerido por control de calidad para alertas de caducidad.</small>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success fw-bold">Procesar Compra</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: NUEVO PRODUCTO (Transferido desde Catálogo) -->
    <div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title text-white fw-bold"><i class="bi bi-box-seam text-danger me-2"></i> Registrar Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formNuevoProducto" enctype="multipart/form-data" action="{{ route('guardar.producto') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-4 mb-md-0 d-flex flex-column align-items-center justify-content-start">
                                <label class="form-label text-secondary w-100 text-center fw-bold"><i class="bi bi-camera me-1"></i> Fotografía</label>
                                <div class="position-relative mt-2" style="width: 180px; height: 180px; border: 2px dashed #444; border-radius: 16px; overflow: hidden; background: #1a1a1a; cursor: pointer;" onclick="document.getElementById('prodImagenNuevo').click()">
                                    <img id="imgPreviewNuevo" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <div id="imgPlaceholderNuevo" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="bi bi-cloud-arrow-up fs-1 mb-2 text-secondary"></i>
                                        <span style="font-size: 0.8rem; text-align: center; padding: 0 10px;">Clic para subir<br>imagen (JPG/PNG)</span>
                                    </div>
                                </div>
                                <input type="file" id="prodImagenNuevo" name="imagen" class="d-none" accept="image/*" onchange="previewImageNuevo(event)">
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label text-secondary">Nombre del Producto</label>
                                    <input type="text" id="prodNombreNuevo" name="nombre" class="form-control bg-dark text-white border-secondary" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Código</label>
                                        <input type="text" id="prodCodigoNuevo" name="codigo" class="form-control bg-dark text-white border-secondary">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Marca</label>
                                        <input type="text" id="prodMarcaNuevo" name="marca" class="form-control bg-dark text-white border-secondary">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Precio ($)</label>
                                        <input type="number" step="0.01" id="prodPrecioNuevo" name="precio" class="form-control bg-dark text-white border-secondary" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-secondary">Stock Inicial</label>
                                        <input type="number" id="prodStockNuevo" name="stock" class="form-control bg-dark text-white border-secondary" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Categoría</label>
                                <input type="text" id="prodCategoriaNuevo" name="categoria" class="form-control bg-dark text-white border-secondary">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Vencimiento (Opcional)</label>
                                <input type="date" id="prodVencimientoNuevo" name="fecha_vencimiento" class="form-control bg-dark text-white border-secondary">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label text-secondary"><i class="bi bi-buildings me-1"></i> Proveedor Asignado (Opcional)</label>
                                <select id="prodProveedorNuevo" name="proveedor_id" class="form-select bg-dark text-white border-secondary">
                                    <option value="">-- Sin proveedor asignado --</option>
                                    @foreach(\App\Models\Proveedor::all() as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarNuevoProducto" class="btn btn-danger fw-bold"><i class="bi bi-save me-1"></i> Guardar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS DE FUNCIONALIDAD DE PROVEEDORES -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Guardar o Actualizar Proveedor (CON IMAGEN Y ALERTAS VIP)
            const btnGuardar = document.getElementById('btnGuardarProveedor');
            if(btnGuardar) {
                btnGuardar.addEventListener('click', function() {
                    const form = document.getElementById('formProveedor');
                    const formData = new FormData(form); 
                    const id = document.getElementById('prov-id').value;
                    const url = id ? `/proveedores/${id}/actualizar` : "{{ route('proveedores.store') }}";
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    
                    // Efecto de carga en el botón
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando...';
                    this.disabled = true;

                    fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData 
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        
                        if(data.success) {
                            let mModal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoProveedor'));
                            if(mModal) mModal.hide();
                            mostrarToast('Proveedor ' + (id ? 'actualizado' : 'registrado') + ' con éxito', 'bi bi-building-check');
                            setTimeout(() => window.location.reload(), 800);
                        } else {
                            mostrarToast(data.message || 'Error al guardar el proveedor.', 'bi bi-exclamation-triangle-fill');
                        }
                    })
                    .catch(err => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        mostrarToast('Error de conexión con el servidor', 'bi bi-exclamation-triangle-fill');
                    });
                });
            }
        });

        // Función para previsualizar el logo
        function previewProvLogo(event) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
                document.getElementById('logoPreview').style.display = 'block';
                document.getElementById('logoPlaceholder').style.display = 'none';
            };
            if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
        }

        // Limpiar el modal antes de registrar uno nuevo
        function limpiarModalProveedor() {
            document.getElementById('formProveedor').reset();
            document.getElementById('prov-id').value = '';
            document.querySelector('#modalNuevoProveedor .modal-title').innerHTML = '<i class="bi bi-building-add text-warning me-2"></i> Registrar Proveedor';
            
            document.getElementById('logoPreview').src = '';
            document.getElementById('logoPreview').style.display = 'none';
            document.getElementById('logoPlaceholder').style.display = 'flex';
        }

        // Rellenar el modal con los datos para editar
        function editarProveedor(proveedor) {
            document.getElementById('prov-id').value = proveedor.id;
            document.querySelector('#provNombre').value = proveedor.nombre;
            document.querySelector('#provRif').value = proveedor.rif;
            document.querySelector('#provContacto').value = proveedor.contacto || '';
            document.querySelector('#provTelefono').value = proveedor.telefono || '';
            document.querySelector('#provDireccion').value = proveedor.direccion || '';
            
            // Rellenar el logo si existe en la BD apuntando correctamente a la ruta
            if (proveedor.logo) {
                document.getElementById('logoPreview').src = "{{ asset('storage') }}/" + proveedor.logo;
                document.getElementById('logoPreview').style.display = 'block';
                document.getElementById('logoPlaceholder').style.display = 'none';
            } else {
                document.getElementById('logoPreview').src = '';
                document.getElementById('logoPreview').style.display = 'none';
                document.getElementById('logoPlaceholder').style.display = 'flex';
            }

            document.querySelector('#modalNuevoProveedor .modal-title').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i> Editar Proveedor';
            new bootstrap.Modal(document.getElementById('modalNuevoProveedor')).show();
        }

        // Eliminar Proveedor con confirmación
        function eliminarProveedor(id) {
            Swal.fire({
                title: '¿Eliminar Proveedor?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E50914',
                cancelButtonColor: '#444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No',
                background: '#141414',
                color: '#fff',
                position: 'top-end',
                toast: true
            }).then((result) => {
                if (!result.isConfirmed) return;
                fetch(`/proveedores/${id}/eliminar`, {
                    method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                }).then(res => res.json()).then(data => {
                    if(data.success) {
                        mostrarToast('Proveedor eliminado', 'bi bi-building-slash');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        mostrarToast(data.message || 'Error al eliminar.', 'bi bi-exclamation-triangle-fill');
                    }
                });
            });
        }

        // Mostrar los productos asignados a ese proveedor
        function verProductosProveedor(proveedor) {
            document.getElementById('tituloCatalogoProv').innerHTML = `<i class="bi bi-box-seam text-info me-2"></i> Catálogo de ${proveedor.nombre}`;
            const tbody = document.getElementById('tablaProductosProv');
            tbody.innerHTML = '';
            
            if (!proveedor.productos || proveedor.productos.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-4">No hay productos asignados a este proveedor.</td></tr>';
            } else {
                proveedor.productos.forEach(prod => {
                    const costo = prod.precio_costo ? '<span style="color:#fdcb6e;">$' + parseFloat(prod.precio_costo).toFixed(2) + '</span>' : '<span style="color:#444;">—</span>';
                    const stockBajo = prod.stock <= (prod.stock_minimo || 5);
                    tbody.innerHTML += `
                        <tr style="border-bottom: 1px solid #222;">
                            <td class="text-secondary">${prod.codigo || 'N/A'}</td>
                            <td class="text-white fw-bold">${prod.nombre}</td>
                            <td><span class="badge ${stockBajo ? 'bg-danger' : 'bg-success'} bg-opacity-25 px-2 py-1" style="color: ${stockBajo ? '#e74c3c' : '#00b894'}">${prod.stock}</span></td>
                            <td class="text-success">$${parseFloat(prod.precio).toFixed(2)}</td>
                            <td style="color:#fdcb6e;">${costo}</td>
                        </tr>
                    `;
                });
            }
            new bootstrap.Modal(document.getElementById('modalCatalogoProveedor')).show();
        }

        // Abrir modal de abastecimiento
        function abastecerProveedor(proveedor, productos) {
            document.getElementById('proveedor_id_compra').value = proveedor.id;
            document.getElementById('proveedor_id_rapido').value = proveedor.id;
            
            const select = document.querySelector('#modalAbastecer select[name="producto_id"]');
            select.innerHTML = '<option value="">Seleccione el producto...</option>';
            
            if(productos && productos.length > 0) {
                productos.forEach(function(prod) {
                    const option = document.createElement('option');
                    option.value = prod.id;
                    option.textContent = prod.codigo + ' - ' + prod.nombre;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML += '<option value="" disabled>No hay productos asociados a este proveedor</option>';
            }
            
            new bootstrap.Modal(document.getElementById('modalAbastecer')).show();
        }

        function previewImageNuevo(event) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreviewNuevo').src = e.target.result;
                document.getElementById('imgPreviewNuevo').style.display = 'block';
                document.getElementById('imgPlaceholderNuevo').style.display = 'none';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btnGuardarNP = document.getElementById('btnGuardarNuevoProducto');
            if(btnGuardarNP) {
                btnGuardarNP.addEventListener('click', function() {
                    const form = document.getElementById('formNuevoProducto');
                    const formData = new FormData(form);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch('{{ route("guardar.producto") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(async response => {
                        if (!response.ok) throw await response.json();
                        return response.json();
                    })
                    .then(data => {
                        if(data.success) {
                            mostrarToast('Producto guardado correctamente', 'bi bi-box-seam');
                            bootstrap.Modal.getInstance(document.getElementById('modalNuevoProducto')).hide();
                            setTimeout(() => location.reload(), 800);
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        let msg = error.message || 'Revisa los campos e intenta de nuevo.';
                        mostrarToast(msg, 'bi bi-exclamation-triangle-fill');
                    });
                });
            }
        });
        
        function toggleSidebar() {
            document.getElementById('topbarNav').classList.toggle('show');
        }

        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            if(arrow) arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                const menu = document.getElementById('userDropdownMenu');
                if(menu) menu.style.display = 'none';
            }
        });

        const inputSearch = document.getElementById('topbarSearchInput');
        if(inputSearch) {
            inputSearch.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                document.querySelectorAll('.provider-card').forEach(card => {
                    const name = card.querySelector('.user-name') ? card.querySelector('.user-name').textContent.toLowerCase() : card.textContent.toLowerCase();
                    card.closest('.col-12').style.display = name.includes(query) ? '' : 'none';
                });
            });
        }

    </script>

<!-- MODAL: CREAR PRODUCTO RÁPIDO DESDE PROVEEDORES -->
<div class="modal fade" id="modalNuevoProductoRapido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-box-seam text-warning me-2"></i>Registrar Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
<!-- Asegúrate de que la ruta 'productos.store' sea la correcta en tu sistema -->
<form id="formProductoRapido" action="{{ route('guardar.producto') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="proveedor_id" id="proveedor_id_rapido">
    <div class="modal-body p-4">
        
        <!-- Nombre del Producto -->
        <div class="mb-3">
            <label class="text-warning small fw-bold mb-1">Nombre del Producto</label>
            <input type="text" class="form-control oswa-input-solid" name="nombre" required placeholder="Ej. Harina Pan 1Kg">
        </div>
        
        <!-- Código y Precio -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="text-secondary small fw-bold mb-1">Código de Barras</label>
                <input type="text" class="form-control oswa-input-solid" name="codigo" required>
            </div>
            <div class="col-6">
                <label class="text-success small fw-bold mb-1">Precio Venta ($)</label>
                <input type="number" step="0.01" class="form-control oswa-input-solid" name="precio" required>
            </div>
        </div>
        
        <!-- Categoría y Stock -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="text-secondary small fw-bold mb-1">Categoría</label>
                <input type="text" class="form-control oswa-input-solid" name="categoria" required placeholder="Ej. Víveres">
            </div>
            <div class="col-6">
                <label class="text-secondary small fw-bold mb-1">Stock Inicial</label>
                <input type="number" class="form-control oswa-input-solid" name="stock" value="0" required>
            </div>
        </div>

        <!-- NUEVOS CAMPOS: Foto y Vencimiento -->
        <div class="row g-2">
            <div class="col-6">
                <label class="text-info small fw-bold mb-1"><i class="bi bi-calendar-event me-1"></i>Vencimiento</label>
                <input type="date" class="form-control oswa-input-solid" name="fecha_vencimiento">
            </div>
            <div class="col-6">
                <label class="text-primary small fw-bold mb-1"><i class="bi bi-camera me-1"></i>Fotografía</label>
                <input type="file" class="form-control oswa-input-solid" name="imagen" accept="image/*">
            </div>
        </div>

    </div>
    <div class="modal-footer border-secondary text-end p-3">
        <button type="button" class="btn btn-dark me-2" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold px-4">Guardar en Catálogo</button>
    </div>
</form>
        </div>
    </div>
</div>

<script>
    document.getElementById('formProductoRapido').addEventListener('submit', function(e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let btnSubmit = form.querySelector('button[type="submit"]');
        
        let originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.status === 422) {
                return response.json().then(data => { throw data; });
            }
            if (response.ok || response.redirected) {
                mostrarToast('Producto registrado correctamente', 'bi bi-box-seam');
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch(error => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;

            if(error.errors) {
                let mensajes = Object.values(error.errors).flat().join('<br>');
                mostrarToast(mensajes, 'bi bi-exclamation-triangle-fill');
            } else {
                mostrarToast('Error de conexión con el servidor.', 'bi bi-exclamation-triangle-fill');
            }
        });
    });
</script>
</body>
</html>