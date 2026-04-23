@extends('layout')

@section('contenido')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $isAdmin = Auth::check() && Auth::user()->role == 'admin';
    $colSize = $isAdmin ? 'col-md-3' : 'col-md-4';
@endphp

<div class="row g-4 mb-4">
    <div class="{{ $colSize }}">
        <div class="card border-0 shadow p-4 text-white rounded-4" style="background: linear-gradient(135deg, #4e73df, #224abe);">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Total Productos</p>
                    <h2 class="fw-bold mb-0" id="total-productos-card">{{ $totalProductos ?? 0 }}</h2>
                </div>
                <i class="bi bi-box-seam fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="{{ $colSize }}">
        <div class="card border-0 shadow p-4 text-white rounded-4" style="background: linear-gradient(135deg, #1cc88a, #13855c);">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Stock Total</p>
                    <h2 id="total-unidades-card" class="fw-bold mb-0">{{ $totalUnidades ?? 0 }}</h2>
                </div>
                <i class="bi bi-truck fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="{{ $colSize }}">
        <div class="card border-0 shadow p-4 text-white rounded-4" style="background: linear-gradient(135deg, #e74a3b, #be2617);">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Alertas (Bajo)</p>
                    <h2 id="alertas-stock-card" class="fw-bold mb-0">{{ $bajoStock ?? 0 }}</h2>
                </div>
                <i class="bi bi-exclamation-triangle fs-1 opacity-25"></i>
            </div>
        </div>
    </div>

    @if($isAdmin)
    <div class="col-md-3">
        <div class="card border-0 shadow p-4 text-dark rounded-4" style="background: linear-gradient(135deg, #f6c23e, #dda20a);">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase">Capital Invertido</p>
                    <h2 class="fw-bold mb-0">$<span id="capital-invertido-card">{{ number_format($capitalDolares ?? 0, 2) }}</span></h2>
                    <small class="fw-bold opacity-75">
                        ≈ <span id="capital-bs-card">{{ number_format($capitalBolivares ?? 0, 2) }}</span> Bs.
                    </small>
                </div>
                <i class="bi bi-currency-exchange fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row mb-4 align-items-center">
    <div class="col-md-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-boxes text-primary"></i> Panel de Admin 
            <span class="badge bg-success-subtle text-success ms-2 border border-success-subtle" style="font-size: 0.8rem;">
                <i class="bi bi-graph-up-arrow"></i> BCV: {{ number_format($tasaDolar ?? 36.50, 2) }}
            </span>
        </h5>
    </div>
    
    <div class="col-md-5">
        <div class="input-group shadow-sm border-primary caja-ia">
            <span class="input-group-text bg-transparent border-0"><i class="bi bi-robot text-primary fs-5"></i></span>
            <input type="text" id="ia-input" class="form-control bg-transparent border-0 py-2 input-ia" 
                   placeholder="Pregunta a OSWA-Bot... (Ej: ¿Qué se vence?)">
            <button class="btn btn-primary px-4" onclick="ejecutarIA()"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>

    <div class="col-md-4 text-end">
        <a href="/historial" class="btn btn-dark shadow-sm rounded-pill px-3 py-2"><i class="bi bi-clock-history"></i> Historial</a>
        <a href="/productos/excel" class="btn btn-success shadow-sm rounded-pill px-3 py-2 ms-1"><i class="bi bi-file-earmark-spreadsheet"></i></a>
        @if($isAdmin)
        <a href="/respaldar-db" class="btn btn-warning shadow-sm rounded-pill px-3 py-2 ms-1 text-dark fw-bold"><i class="bi bi-shield-lock-fill"></i></a>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-list-stars text-primary me-2"></i> Inventario Actual</h5>
        <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill shadow-sm">Alto Barinas</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaInteligente">
            <thead class="text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Producto</th>
                    <th>Categoría/Marca</th>
                    <th class="text-center">Precio ($)</th>
                    <th class="text-center">Vencimiento</th>
                    <th class="text-center">Stock / Predicción</th>
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
                    
                    $claseColor = '';
                    $efectoLatido = '';
                    if($p->stock <= 0) {
                        $claseColor = 'alerta-critica';
                    } elseif($p->stock <= 5) {
                        $claseColor = 'alerta-baja';
                        $efectoLatido = 'latido-alerta';
                    }

                    $cat = strtolower($p->categoria ?? 'general');
                    $badgeColor = 'bg-primary-subtle text-primary';
                    if (str_contains($cat, 'alimento') || str_contains($cat, 'comida')) $badgeColor = 'bg-success-subtle text-success';
                    elseif (str_contains($cat, 'bebida')) $badgeColor = 'bg-info-subtle text-info';
                    elseif (str_contains($cat, 'limpieza')) $badgeColor = 'bg-warning-subtle text-warning';
                    
                    $fotoReal = (isset($p->descripcion) && str_contains($p->descripcion, 'http') && !str_contains($p->descripcion, 'flaticon')) ? $p->descripcion : null;
                @endphp
                <tr id="fila-{{ $p->id }}" class="{{ $claseColor }}">
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($fotoReal)
                                <img src="{{ $fotoReal }}" 
                                     onerror="this.outerHTML='<div class=\'rounded-3 me-3 shadow-sm border d-flex align-items-center justify-content-center\' style=\'width: 50px; height: 50px; background-color: #1e293b;\'><i class=\'bi bi-box-seam text-secondary fs-4\'></i></div>'" 
                                     class="rounded-3 me-3 shadow-sm border bg-white" 
                                     style="width: 50px; height: 50px; object-fit: contain; padding: 2px;">
                            @else
                                <div class="rounded-3 me-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1e293b;">
                                    <i class="bi bi-box-seam text-secondary fs-4"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold" style="font-size: 1.05rem;">{{ $p->nombre }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $p->codigo }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeColor }} rounded-pill mb-1">{{ ucfirst($p->categoria ?? 'General') }}</span><br>
                        <small class="text-muted">{{ $p->marca ?? 'S/M' }}</small>
                    </td>
                    <td class="text-center fw-bold text-success">
                        ${{ number_format($p->precio ?? 0, 2) }}
                    </td>
                    <td class="text-center">
                        @if($vence)
                            <span class="{{ $vencido ? 'text-danger fw-bold' : ($esCritico ? 'text-warning fw-bold' : '') }}">{{ $vence->format('d/m/Y') }}</span>
                        @else
                            <span class="text-muted small">---</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex align-items-center border rounded-pill p-1 shadow-sm caja-stock {{ $efectoLatido }}" id="contenedor-stock-{{ $p->id }}">
                            <button onclick="ajustarStockAjax({{ $p->id }}, 'restar')" class="btn btn-sm btn-light rounded-circle text-danger p-0 d-flex align-items-center justify-content-center btn-accion-stock"><i class="bi bi-dash"></i></button>
                            <span id="stock-val-{{ $p->id }}" class="px-3 fw-bold {{ $p->stock <= 5 ? 'text-danger' : '' }}">{{ $p->stock }}</span>
                            <button onclick="ajustarStockAjax({{ $p->id }}, 'sumar')" class="btn btn-sm btn-light rounded-circle text-success p-0 d-flex align-items-center justify-content-center btn-accion-stock"><i class="bi bi-plus"></i></button>
                        </div>
                    </td>
                    <td class="text-center pe-4">
                        <div class="btn-group shadow-sm" id="btn-group-{{ $p->id }}" style="border-radius: 50px; overflow: hidden;">
                            
                            <button onclick="abrirModalTransferencia({{ $p->id }}, '{{ $p->nombre }}', {{ $p->stock }})" class="btn btn-sm btn-warning px-3 text-dark fw-bold" title="Transferir a otra sucursal">
                                <i class="bi bi-truck"></i>
                            </button>

                            @if($isAdmin && $p->stock <= 5)
                                <a href="/orden-compra/{{ $p->id }}" id="pdf-btn-{{ $p->id }}" target="_blank" class="btn btn-sm btn-info px-3 text-white" title="Generar Orden B2B">
                                    <i class="bi bi-cart-plus-fill"></i>
                                </a>
                            @endif

                            <a href="/productos/editar/{{ $p->id }}" class="btn btn-sm btn-primary px-3"><i class="bi bi-pencil"></i></a>
                            
                            @if($isAdmin)
                            <button onclick="confirmarEliminar('{{ $p->codigo }}', '{{ $p->nombre }}')" class="btn btn-sm btn-danger px-3"><i class="bi bi-trash"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
            <h6 class="fw-bold text-muted text-uppercase small mb-3 text-center">Distribución por Categorías</h6>
            <div style="height: 250px; position: relative;">
                <canvas id="graficaCategorias"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
            <h6 class="fw-bold text-muted text-uppercase small mb-3 text-center">Salud del Stock</h6>
            <div style="height: 250px; position: relative;">
                <canvas id="graficaStock"></canvas>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false,
            customClass: { popup: 'swal-adaptable' }
        });
    });
</script>
@endif

<script>
    window.chartStock = null;

    $(document).ready(function() {
        $('#tablaInteligente').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "pageLength": 5, "lengthMenu": [5, 10, 25, 50], "order": [[ 0, "asc" ]]
        });

        document.getElementById('ia-input').addEventListener("keypress", function(e) {
            if (e.key === "Enter") ejecutarIA();
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        try {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.color = '#9ca3af';

            let labelsCategorias = {!! json_encode($labelsCat ?? []) !!};
            let dataCategorias = {!! json_encode($dataCat ?? []) !!};
            let normales = {{ ($totalProductos ?? 0) - ($bajoStock ?? 0) }};
            let bajos = {{ $bajoStock ?? 0 }};

            if(!labelsCategorias || labelsCategorias.length === 0) { labelsCategorias = ['Sin registros']; dataCategorias = [1]; }
            if(normales === 0 && bajos === 0) { normales = 1; }

            new Chart(document.getElementById('graficaCategorias'), {
                type: 'doughnut',
                data: {
                    labels: labelsCategorias,
                    datasets: [{ data: dataCategorias, backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6c757d'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            window.chartStock = new Chart(document.getElementById('graficaStock'), {
                type: 'pie',
                data: {
                    labels: ['Saludable', 'Crítico / Agotado'],
                    datasets: [{ data: [normales, bajos], backgroundColor: ['#1cc88a', '#e74a3b'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        } catch (error) {
            console.error("Error gráficas:", error);
        }
    });

    function ejecutarIA() {
        const orden = document.getElementById('ia-input').value;
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        
        if (!orden) return;
        if (!tokenMeta) { Swal.fire('Error', 'Falta el token CSRF en layout.blade.php', 'error'); return; }

        Swal.fire({ title: 'OSWA-Bot pensando...', text: 'Analizando tu inventario...', didOpen: () => { Swal.showLoading() } });

        fetch('/ia/comando', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tokenMeta.content },
            body: JSON.stringify({ orden: orden })
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (data.status === 'success') {
                let html = `<p class='text-start mb-2'>${data.msg}</p>`;
                if (data.data && data.data.length > 0) {
                    html += `<ul class='list-group list-group-flush text-start small border rounded shadow-sm'>`;
                    data.data.forEach(p => { 
                        html += `<li class='list-group-item bg-transparent border-bottom' style='color: inherit;'><b>${p.nombre}</b> - Quedan: ${p.stock}</li>`; 
                    });
                    html += `</ul>`;
                }
                Swal.fire({ 
                    title: '🤖 OSWA-Bot:', html: html, confirmButtonColor: '#3b82f6', customClass: { popup: 'swal-adaptable' }
                });
                document.getElementById('ia-input').value = "";
            } else {
                Swal.fire({title: 'Ups...', text: data.msg, icon: 'warning', customClass: { popup: 'swal-adaptable' }});
            }
        });
    }

    // 🧠 MÁGIA FEFO INCLUIDA
    function ajustarStockAjax(id, accion, forzarFefo = false) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        let bodyData = { accion: accion };
        if (forzarFefo) bodyData.forzar_fefo = true;

        fetch(`/productos/ajustar/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify(bodyData)
        })
        .then(res => res.json())
        .then(data => {
            
            // 🚨 ALERTA DEL PROTOCOLO FEFO
            if (data.status === 'fefo_warning') {
                Swal.fire({
                    title: '⚠️ Protocolo FEFO Activo',
                    text: data.msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981', 
                    cancelButtonColor: '#ef4444', 
                    confirmButtonText: 'Entendido (Buscar viejo)',
                    cancelButtonText: 'Ignorar y sacar este',
                    customClass: { popup: 'swal-adaptable' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const filaVieja = document.getElementById(`fila-${data.id_sugerido}`);
                        if(filaVieja) {
                            filaVieja.style.border = "3px solid #10b981";
                            filaVieja.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // El usuario asume el riesgo
                        ajustarStockAjax(id, accion, true);
                    }
                });
                return; // Frenamos aquí para no restar el stock
            }

            if (data.status === 'success') {
                const stockSpan = document.getElementById(`stock-val-${id}`);
                const fila = document.getElementById(`fila-${id}`);
                const contenedorStock = document.getElementById(`contenedor-stock-${id}`);
                const btnGroup = document.getElementById(`btn-group-${id}`);
                const nuevoStock = data.nuevo_stock;
                
                stockSpan.innerText = nuevoStock;
                stockSpan.classList.remove('text-danger');
                fila.classList.remove('alerta-baja', 'alerta-critica');
                contenedorStock.classList.remove('latido-alerta');
                
                let pdfBtn = document.getElementById(`pdf-btn-${id}`);
                if(nuevoStock <= 5) {
                    stockSpan.classList.add('text-danger'); 
                    fila.classList.add(nuevoStock <= 0 ? 'alerta-critica' : 'alerta-baja'); 
                    if(nuevoStock > 0) contenedorStock.classList.add('latido-alerta');
                    
                    if(!pdfBtn && {{ $isAdmin ? 'true' : 'false' }}) {
                        let newBtn = document.createElement('a');
                        newBtn.href = `/orden-compra/${id}`;
                        newBtn.id = `pdf-btn-${id}`;
                        newBtn.target = '_blank';
                        newBtn.className = 'btn btn-sm btn-info px-3 text-white';
                        newBtn.title = 'Generar Orden B2B';
                        newBtn.innerHTML = '<i class="bi bi-cart-plus-fill"></i>';
                        btnGroup.insertBefore(newBtn, btnGroup.childNodes[2]); 
                    }
                } else {
                    if(pdfBtn) pdfBtn.remove();
                }

                if(data.total_productos !== undefined) document.getElementById('total-productos-card').innerText = data.total_productos;
                if(data.total_unidades !== undefined) document.getElementById('total-unidades-card').innerText = data.total_unidades;
                if(data.bajo_stock !== undefined) document.getElementById('alertas-stock-card').innerText = data.bajo_stock;
                const capUSD = document.getElementById('capital-invertido-card');
                if (capUSD && data.capital_invertido) capUSD.innerText = data.capital_invertido;

                if(window.chartStock && typeof window.chartStock.update === 'function') {
                    let normales = data.total_productos - data.bajo_stock;
                    window.chartStock.data.datasets[0].data = [normales, data.bajo_stock];
                    window.chartStock.update();
                }

                const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
                Toast.fire({ icon: accion === 'sumar' ? 'success' : 'info', title: accion === 'sumar' ? '+1 Unidad Registrada' : '-1 Unidad Descontada', customClass: { popup: 'swal-adaptable' } });
            }
        });
    }

    // 🚚 FUNCIÓN DEL CAMIÓN CON RUTAS NACIONALES
    function abrirModalTransferencia(id, nombre, maxStock) {
        if(maxStock <= 0) {
            Swal.fire('Sin Stock', 'No hay unidades para transferir.', 'error');
            return;
        }

        Swal.fire({
            title: `🚚 Transferir ${nombre}`,
            html: `
                <div class="text-start mt-3">
                    <label class="form-label text-muted small fw-bold">Sucursal Destino (Grafo)</label>
                    <select id="swal-destino" class="form-select bg-dark text-white border-secondary mb-3 shadow-sm">
                        <optgroup label="Occidente">
                            <option value="socopo">Socopó (Barinas)</option>
                            <option value="merida">Mérida</option>
                            <option value="tachira">San Cristóbal (Táchira)</option>
                            <option value="zulia">Maracaibo (Zulia)</option>
                            <option value="trujillo">Trujillo</option>
                            <option value="lara">Barquisimeto (Lara)</option>
                            <option value="portuguesa">Guanare (Portuguesa)</option>
                        </optgroup>
                        <optgroup label="Centro">
                            <option value="caracas">Caracas (Distrito Capital)</option>
                            <option value="carabobo">Valencia (Carabobo)</option>
                            <option value="aragua">Maracay (Aragua)</option>
                            <option value="la_guaira">La Guaira</option>
                            <option value="cojedes">San Carlos (Cojedes)</option>
                        </optgroup>
                        <optgroup label="Oriente">
                            <option value="anzoategui">Barcelona (Anzoátegui)</option>
                            <option value="monagas">Maturín (Monagas)</option>
                            <option value="sucre">Cumaná (Sucre)</option>
                            <option value="nueva_esparta">Margarita (Nueva Esparta)</option>
                        </optgroup>
                        <optgroup label="Sur / Llanos">
                            <option value="bolivar">Ciudad Bolívar (Bolívar)</option>
                            <option value="amazonas">Puerto Ayacucho (Amazonas)</option>
                            <option value="apure">San Fernando (Apure)</option>
                            <option value="guarico">San Juan (Guárico)</option>
                        </optgroup>
                    </select>
                    <label class="form-label text-muted small fw-bold">Cantidad a enviar (Max: ${maxStock})</label>
                    <input type="number" id="swal-cantidad" class="form-control bg-dark text-white border-secondary" min="1" max="${maxStock}" value="1">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Procesar Envío',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'swal-adaptable' },
            preConfirm: () => {
                return {
                    destino: document.getElementById('swal-destino').value,
                    cantidad: document.getElementById('swal-cantidad').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                Swal.showLoading();

                fetch(`/productos/transferir/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify(result.value)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Enviado!',
                            html: `${data.msg}<br><br><small class="text-muted">Distancia: ${data.distancia} Km | Flete: $${data.costo_flete}</small>`,
                            customClass: { popup: 'swal-adaptable' }
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.msg, 'error');
                    }
                });
            }
        });
    }

    function confirmarEliminar(codigo, nombre) {
        Swal.fire({
            title: '¿Eliminar?', text: `Vas a borrar: ${nombre}.`, icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Sí, borrar',
            customClass: { popup: 'swal-adaptable' }
        }).then((result) => { if (result.isConfirmed) { window.location.href = "/productos/eliminar/" + codigo; } })
    }
</script>

<style>
    /* 🎨 COLORES Y ANIMACIONES */
    tr { transition: background-color 0.3s ease; }
    tr.alerta-baja td { background-color: rgba(255, 193, 7, 0.1) !important; }
    tr.alerta-critica td { background-color: rgba(220, 53, 69, 0.1) !important; }
    
    .caja-ia { border-radius: 50px; overflow: hidden; border: 2px solid #3b82f6; transition: box-shadow 0.3s ease; }
    .caja-ia:focus-within { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important; }
    
    .input-ia { color: inherit !important; background-color: transparent !important; }
    .input-ia:focus { box-shadow: none; background-color: transparent !important; }
    .input-ia::placeholder { color: inherit !important; opacity: 0.6; }

    .caja-stock { background-color: transparent !important; }
    .btn-accion-stock { width: 28px; height: 28px; background: rgba(128, 128, 128, 0.1) !important; border: none; }
    .btn-accion-stock:hover { background: rgba(128, 128, 128, 0.2) !important; }

    @keyframes pulso {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .latido-alerta { animation: pulso 2s infinite; border-color: rgba(220, 53, 69, 0.6) !important; }

    @media (prefers-color-scheme: dark) {
        .swal-adaptable { background-color: #1e293b !important; color: #f8fafc !important; }
    }
</style>
@endsection