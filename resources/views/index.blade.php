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
    <div class="{{ $colSize }} fade-in-up delay-1">
        <div class="card border-0 shadow-lg p-4 text-white rounded-4 card-3d" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase tracking-wider">Total Productos</p>
                    <h2 class="fw-bold mb-0 display-6" id="total-productos-card">{{ $totalProductos ?? 0 }}</h2>
                </div>
                <i class="bi bi-box-seam display-4 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="{{ $colSize }} fade-in-up delay-2">
        <div class="card border-0 shadow-lg p-4 text-white rounded-4 card-3d" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase tracking-wider">Stock Total</p>
                    <h2 id="total-unidades-card" class="fw-bold mb-0 display-6">{{ $totalUnidades ?? 0 }}</h2>
                </div>
                <i class="bi bi-truck display-4 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="{{ $colSize }} fade-in-up delay-3">
        <div class="card border-0 shadow-lg p-4 text-white rounded-4 card-3d" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase tracking-wider">Alertas (Bajo)</p>
                    <h2 id="alertas-stock-card" class="fw-bold mb-0 display-6">{{ $bajoStock ?? 0 }}</h2>
                </div>
                <i class="bi bi-exclamation-triangle display-4 opacity-25"></i>
            </div>
        </div>
    </div>

    @if($isAdmin)
    <div class="col-md-3 fade-in-up delay-4">
        <div class="card border-0 shadow-lg p-4 text-dark rounded-4 card-3d" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 fw-bold small text-uppercase tracking-wider">Capital Invertido</p>
                    <h2 class="fw-bold mb-0" style="font-size: 1.8rem;">$<span id="capital-invertido-card">{{ number_format($capitalDolares ?? 0, 2) }}</span></h2>
                    <small class="fw-bold opacity-75">
                        ≈ <span id="capital-bs-card">{{ number_format($capitalBolivares ?? 0, 2) }}</span> Bs.
                    </small>
                </div>
                <i class="bi bi-currency-exchange display-4 opacity-25"></i>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row mb-4 g-3 align-items-center fade-in-up delay-4">
    <div class="col-md-3">
        <h5 class="fw-bold mb-0 d-flex align-items-center adapt-text">
            <span class="p-2 bg-primary bg-opacity-10 rounded-3 me-2">
                <i class="bi bi-boxes text-primary"></i>
            </span>
            Panel de Admin 
        </h5>
    </div>
    
    <div class="col-md-5">
        <div class="input-group shadow-sm caja-ia-modern">
            <span class="input-group-text border-0 ps-3 bg-transparent"><i class="bi bi-robot text-primary fs-5"></i></span>
            <input type="text" id="ia-input" class="form-control border-0 py-2 input-ia" 
                   placeholder="Pregunta a OSWA-Bot... (Ej: ¿Qué se vence?)">
            <button class="btn btn-primary px-4 shadow-sm btn-primary-gradient" onclick="ejecutarIA()"><i class="bi bi-send-fill text-white"></i></button>
        </div>
    </div>

    <div class="col-md-4 text-end">
        <div class="d-flex gap-2 justify-content-end">
            <a href="/historial" class="btn btn-dark rounded-pill px-4 py-2 text-white shadow-sm fw-bold">
                <i class="bi bi-clock-history me-1 text-white"></i> Historial
            </a>
            <a href="/productos/excel" class="btn btn-success rounded-pill px-3 py-2 text-white shadow-sm fw-bold" title="Exportar Excel">
                <i class="bi bi-file-earmark-spreadsheet text-white"></i>
            </a>
            @if($isAdmin)
            <a href="/respaldar-db" class="btn btn-warning rounded-pill px-3 py-2 text-dark shadow-sm fw-bold" title="Respaldar Base de Datos">
                <i class="bi bi-shield-lock-fill text-dark"></i>
            </a>
            @endif
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm p-4 mb-4 rounded-4 table-container-modern fade-in-up delay-4">
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-10 pb-3 mb-4">
        <h5 class="fw-bold mb-0 adapt-text"><i class="bi bi-list-stars text-primary me-2"></i> Inventario Actual</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-geo-alt-fill me-1"></i> Alto Barinas
            </span>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-graph-up-arrow me-1"></i> BCV: {{ number_format($tasaDolar ?? 36.50, 2) }}
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaInteligente">
            <thead class="small text-uppercase letter-spacing-1 adapt-text opacity-100 fw-bold border-bottom">
                <tr>
                    <th class="border-0">Producto</th>
                    <th class="border-0">Categoría/Marca</th>
                    <th class="border-0 text-center">Precio ($)</th>
                    <th class="border-0 text-center">Vencimiento</th>
                    <th class="border-0 text-center">Stock / Predicción</th>
                    <th class="border-0 text-center pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $p)
                @php
                    $hoy = \Carbon\Carbon::now();
                    $vence = $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento) : null;
                    $esCritico = $vence && $vence->diffInDays($hoy, false) >= -7 && $vence->isFuture();
                    $vencido = $vence && $vence->isPast();
                    
                    // 🚦 LÓGICA DE COLORES DE STOCK INICIAL
                    $claseColorFila = '';
                    $efectoLatido = '';
                    $stockColorText = 'text-success'; 
                    
                    if($p->stock <= 0) { 
                        $claseColorFila = 'alerta-critica-modern'; 
                        $stockColorText = 'text-danger'; 
                    } elseif($p->stock <= 5) { 
                        $claseColorFila = 'alerta-baja-modern'; 
                        $efectoLatido = 'latido-alerta-naranja'; 
                        $stockColorText = 'text-warning'; 
                    }

                    $cat = strtolower($p->categoria ?? 'general');
                    $accentColor = '#3b82f6';
                    if (str_contains($cat, 'alimento') || str_contains($cat, 'comida')) $accentColor = '#10b981';
                    elseif (str_contains($cat, 'bebida')) $accentColor = '#06b6d4';
                    elseif (str_contains($cat, 'limpieza')) $accentColor = '#f59e0b';
                    
                    $fotoReal = (isset($p->descripcion) && str_contains($p->descripcion, 'http') && !str_contains($p->descripcion, 'flaticon')) ? $p->descripcion : null;
                @endphp
                <tr id="fila-{{ $p->id }}" class="{{ $claseColorFila }} product-row shadow-sm">
                    <td>
                        <div class="d-flex align-items-center p-2">
                            <div class="product-img-wrapper me-3">
                                @if($fotoReal)
                                    <img src="{{ $fotoReal }}" 
                                         onerror="this.src='https://cdn-icons-png.flaticon.com/512/1174/1174466.png'" 
                                         class="rounded-3 shadow-sm border bg-white img-fluid" 
                                         style="width: 55px; height: 55px; object-fit: contain; padding: 2px;">
                                @else
                                    <div class="rounded-3 shadow-sm border d-flex align-items-center justify-content-center bg-light" style="width: 55px; height: 55px;">
                                        <i class="bi bi-box-seam text-secondary fs-3"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold adapt-text" style="font-size: 1.05rem;">{{ $p->nombre }}</div>
                                <small class="text-muted tracking-widest">{{ $p->codigo }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-outline" style="border-color: {{ $accentColor }}; color: {{ $accentColor }};">
                            {{ strtoupper($p->categoria ?? 'General') }}
                        </span><br>
                        <small class="text-muted ms-1"><i class="bi bi-tag-fill me-1"></i>{{ $p->marca ?? 'S/M' }}</small>
                    </td>
                    <td class="text-center fw-bold text-success fs-5">
                        ${{ number_format($p->precio ?? 0, 2) }}
                    </td>
                    <td class="text-center">
                        @if($vence)
                            <div class="d-flex flex-column">
                                <span class="{{ $vencido ? 'text-danger fw-bold' : ($esCritico ? 'text-warning fw-bold' : 'adapt-text') }}">
                                    {{ $vence->format('d/m/Y') }}
                                </span>
                                @if($vencido) <small class="text-danger x-small fw-bold">CADUCADO</small> @endif
                            </div>
                        @else
                            <span class="text-muted small">---</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex align-items-center rounded-pill p-1 shadow-sm stock-box {{ $efectoLatido }}" id="contenedor-stock-{{ $p->id }}">
                            <button onclick="ajustarStockAjax({{ $p->id }}, 'restar')" class="btn btn-sm btn-icon rounded-circle text-danger"><i class="bi bi-dash-lg"></i></button>
                            <span id="stock-val-{{ $p->id }}" class="px-3 fw-bold fs-5 {{ $stockColorText }}">{{ $p->stock }}</span>
                            <button onclick="ajustarStockAjax({{ $p->id }}, 'sumar')" class="btn btn-sm btn-icon rounded-circle text-success"><i class="bi bi-plus-lg"></i></button>
                        </div>
                        
                        @if(isset($p->dias_restantes) && $p->dias_restantes !== null)
                            <div class="mt-2 text-info fw-bold" style="font-size: 0.75rem; background: rgba(13, 202, 240, 0.1); padding: 2px 8px; border-radius: 10px; display: inline-block;">
                                <i class="bi bi-graph-down"></i> Dura ~{{ $p->dias_restantes }} días
                            </div>
                        @endif
                    </td>
                    <td class="text-center pe-4">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden" id="btn-group-{{ $p->id }}">
                            
                            <button onclick="abrirModalTransferencia({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->stock }})" class="btn btn-sm btn-warning px-3" title="Transferir a otra sucursal">
                                <i class="bi bi-truck text-dark"></i>
                            </button>

                            @if($isAdmin)
                                @if($p->stock <= 5)
                                <a href="/orden-compra/{{ $p->id }}" id="pdf-btn-{{ $p->id }}" target="_blank" class="btn btn-sm btn-info px-3 text-white" title="Generar Orden B2B">
                                    <i class="bi bi-cart-plus-fill"></i>
                                </a>
                                @endif
                            @endif

                            <a href="/productos/editar/{{ $p->id }}" class="btn btn-sm btn-primary px-3"><i class="bi bi-pencil-square text-white"></i></a>
                            
                            @if($isAdmin)
                            <button onclick="confirmarEliminar('{{ $p->codigo }}', '{{ addslashes($p->nombre) }}')" class="btn btn-sm btn-danger px-3"><i class="bi bi-trash3-fill text-white"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4 mb-4 fade-in-up delay-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 rounded-4 chart-card">
            <h6 class="fw-bold adapt-text text-uppercase small mb-4 text-center tracking-widest opacity-75">Distribución por Categorías</h6>
            <div style="height: 280px;">
                <canvas id="graficaCategorias"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 rounded-4 chart-card">
            <h6 class="fw-bold adapt-text text-uppercase small mb-4 text-center tracking-widest opacity-75">Salud del Stock Global</h6>
            <div style="height: 280px;">
                <canvas id="graficaStock"></canvas>
            </div>
        </div>
    </div>
</div>

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
            
            // CORRECCIÓN MODO CLARO/OSCURO: Detecta el tema
            Chart.defaults.color = document.body.classList.contains('dark-mode') ? '#9ca3af' : '#475569';

            let labelsCategorias = {!! json_encode($labelsCat ?? []) !!};
            let dataCategorias = {!! json_encode($dataCat ?? []) !!};
            let normales = {{ ($totalProductos ?? 0) - ($bajoStock ?? 0) }};
            let bajos = {{ $bajoStock ?? 0 }};

            if(!labelsCategorias || labelsCategorias.length === 0) { labelsCategorias = ['Sin registros']; dataCategorias = [1]; }

            new Chart(document.getElementById('graficaCategorias'), {
                type: 'doughnut',
                data: {
                    labels: labelsCategorias,
                    datasets: [{ data: dataCategorias, backgroundColor: ['#3b82f6', '#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#6366f1'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom' } } }
            });

            window.chartStock = new Chart(document.getElementById('graficaStock'), {
                type: 'pie',
                data: {
                    labels: ['Saludable', 'Stock Crítico'],
                    datasets: [{ data: [normales, bajos], backgroundColor: ['#10b981', '#ef4444'], borderWidth: 2 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        } catch (error) { console.error("Error gráficas:", error); }
    });

    // --- LÓGICA IA (CON SKELETON LOADER PREMIUM) ---
    function ejecutarIA() {
        const orden = document.getElementById('ia-input').value;
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!orden) return;
        
        // Mostrar SKELETON LOADER en vez del icono aburrido girando
        Swal.fire({ 
            title: '🤖 OSWA-Bot procesando...', 
            html: `
                <div class="text-start mt-4 px-3">
                    <div class="skeleton-line w-100"></div>
                    <div class="skeleton-line w-75"></div>
                    <div class="skeleton-line w-50"></div>
                </div>
                <p class="text-muted small mt-3">Consultando red neuronal...</p>
            `, 
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: { popup: 'swal-bot' }
        });

        fetch('/ia/comando', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tokenMeta.content }, 
            body: JSON.stringify({ orden: orden }) 
        })
        .then(res => {
            if (!res.ok) throw new Error("Error del servidor (Ej: Controlador falló)");
            return res.json();
        })
        .then(data => {
            if (data.status === 'success') {
                let mensajeInteligente = data.msg.replace(/text-white/g, 'adapt-text');

                let html = `<div class='text-start mb-3' style='font-size: 1.05rem;'>${mensajeInteligente}</div>`;
                if (data.data && data.data.length > 0) {
                    html += `<ul class='list-group list-group-flush text-start small border rounded shadow-sm mt-2'>`;
                    data.data.forEach(p => { 
                        html += `<li class='list-group-item bg-transparent border-bottom adapt-text'>
                                    <i class="bi bi-box-seam text-primary me-2"></i> <b>${p.nombre}</b> 
                                    <span class="badge bg-primary rounded-pill float-end">Quedan: ${p.stock}</span>
                                 </li>`; 
                    });
                    html += `</ul>`;
                }
                Swal.fire({ 
                    title: '🤖 OSWA-Bot', 
                    html: html, 
                    confirmButtonColor: '#3b82f6', 
                    customClass: { popup: 'swal-bot' } 
                });
                document.getElementById('ia-input').value = "";
            } else { 
                let errorLimpio = data.msg.replace(/<[^>]*>?/gm, '');
                Swal.fire({title: 'No entendí 😅', text: errorLimpio, icon: 'warning', customClass: { popup: 'swal-adaptable' }}); 
            }
        })
        .catch(error => {
            Swal.fire({
                title: '🚨 Error del Sistema',
                text: 'OSWA-Bot no pudo conectarse. Revisa que el servidor de Laravel esté corriendo o que no haya errores de PHP en el controlador.',
                icon: 'error',
                customClass: { popup: 'swal-adaptable' },
                confirmButtonColor: '#ef4444'
            });
        });
    }

    // 🚀 LÓGICA DE STOCK 100% TIEMPO REAL
    function ajustarStockAjax(id, accion, forzarFefo = false) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        let bodyData = { accion: accion };
        if (forzarFefo) bodyData.forzar_fefo = true;
        
        fetch(`/productos/ajustar/${id}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify(bodyData) })
        .then(res => res.json()).then(data => {
            if (data.status === 'fefo_warning') {
                Swal.fire({ title: '⚠️ FEFO', text: data.msg, icon: 'warning', showCancelButton: true, confirmButtonText: 'Buscar viejo', cancelButtonText: 'Forzar', customClass: { popup: 'swal-adaptable' } })
                .then((result) => { if (result.isConfirmed) { const fila = document.getElementById(`fila-${data.id_sugerido}`); if(fila) fila.scrollIntoView({ behavior: 'smooth', block: 'center' }); } else if (result.dismiss === Swal.DismissReason.cancel) { ajustarStockAjax(id, accion, true); } });
                return;
            }
            if (data.status === 'success') { 
                const nuevoStock = data.nuevo_stock;
                const stockSpan = document.getElementById(`stock-val-${id}`);
                const fila = document.getElementById(`fila-${id}`);
                const contenedorStock = document.getElementById(`contenedor-stock-${id}`);
                const btnGroup = document.getElementById(`btn-group-${id}`);
                
                stockSpan.innerText = nuevoStock;
                stockSpan.className = `px-3 fw-bold fs-5 ${nuevoStock <= 0 ? 'text-danger' : (nuevoStock <= 5 ? 'text-warning' : 'text-success')}`;

                fila.classList.remove('alerta-critica-modern', 'alerta-baja-modern');
                contenedorStock.classList.remove('latido-alerta-naranja');

                if (nuevoStock <= 0) {
                    fila.classList.add('alerta-critica-modern');
                } else if (nuevoStock <= 5) {
                    fila.classList.add('alerta-baja-modern');
                    contenedorStock.classList.add('latido-alerta-naranja');
                }

                @if($isAdmin)
                    let pdfBtn = document.getElementById(`pdf-btn-${id}`);
                    if (nuevoStock <= 5 && !pdfBtn) {
                        let newBtn = document.createElement('a');
                        newBtn.href = `/orden-compra/${id}`;
                        newBtn.id = `pdf-btn-${id}`;
                        newBtn.target = '_blank';
                        newBtn.className = 'btn btn-sm btn-info px-3 text-white';
                        newBtn.title = 'Generar Orden B2B';
                        newBtn.innerHTML = '<i class="bi bi-cart-plus-fill"></i>';
                        btnGroup.insertBefore(newBtn, btnGroup.children[1]); 
                    } else if (nuevoStock > 5 && pdfBtn) {
                        pdfBtn.remove();
                    }
                @endif

                if (document.getElementById('total-productos-card')) document.getElementById('total-productos-card').innerText = data.total_productos;
                if (document.getElementById('total-unidades-card')) document.getElementById('total-unidades-card').innerText = data.total_unidades;
                if (document.getElementById('alertas-stock-card')) document.getElementById('alertas-stock-card').innerText = data.bajo_stock;
                if (document.getElementById('capital-invertido-card')) document.getElementById('capital-invertido-card').innerText = data.capital_invertido;

                if(window.chartStock && typeof window.chartStock.update === 'function') {
                    let normales = data.total_productos - data.bajo_stock;
                    window.chartStock.data.datasets[0].data = [normales, data.bajo_stock];
                    window.chartStock.update();
                }

                const isDark = document.body.classList.contains('dark-mode');
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    background: isDark ? '#1e293b' : '#ffffff',
                    color: isDark ? '#ffffff' : '#1e293b'
                });

                Toast.fire({
                    icon: accion === 'sumar' ? 'success' : 'info',
                    title: accion === 'sumar' ? '+1 Añadido' : '-1 Descontado'
                });
            }
        });
    }

    function abrirModalTransferencia(id, nombre, maxStock) {
        if(maxStock <= 0) { Swal.fire({title: 'Sin Stock', text: 'No hay nada que enviar.', icon: 'error', customClass: {popup: 'swal-adaptable'}}); return; }
        
        let selectOpciones = `
            <optgroup label="Región Capital">
                <option value="caracas">Caracas (Distrito Capital)</option>
                <option value="la_guaira">La Guaira</option>
                <option value="miranda">Miranda</option>
            </optgroup>
            <optgroup label="Región Central">
                <option value="aragua">Aragua</option>
                <option value="carabobo">Carabobo</option>
                <option value="cojedes">Cojedes</option>
            </optgroup>
            <optgroup label="Región Centroccidental">
                <option value="falcon">Falcón</option>
                <option value="lara">Lara</option>
                <option value="yaracuy">Yaracuy</option>
            </optgroup>
            <optgroup label="Región de los Llanos">
                <option value="apure">Apure</option>
                <option value="guarico">Guárico</option>
                <option value="portuguesa">Portuguesa</option>
                <option value="valle_la_pascua">Valle de la Pascua</option>
            </optgroup>
            <optgroup label="Región Andina">
                <option value="merida">Mérida</option>
                <option value="tachira">Táchira</option>
                <option value="trujillo">Trujillo</option>
                <option value="socopo">Socopó (Barinas)</option>
            </optgroup>
            <optgroup label="Región Zuliana">
                <option value="zulia">Zulia</option>
                <option value="cabimas">Cabimas</option>
            </optgroup>
            <optgroup label="Región Oriental">
                <option value="anzoategui">Anzoátegui</option>
                <option value="monagas">Monagas</option>
                <option value="sucre">Sucre</option>
            </optgroup>
            <optgroup label="Región Guayana & Insular">
                <option value="amazonas">Amazonas</option>
                <option value="bolivar">Bolívar</option>
                <option value="delta_amacuro">Delta Amacuro</option>
                <option value="nueva_esparta">Nueva Esparta</option>
            </optgroup>
        `;

        Swal.fire({
            title: `<div class="text-start mb-2"><h5 class="fw-bold mb-0 text-primary"><i class="bi bi-truck text-warning fs-3 me-2"></i> Orden de Transferencia</h5><p class="adapt-text fs-6 mt-2 mb-0">${nombre}</p></div>`,
            html: `
                <div class="text-start mt-3">
                    <div class="mb-4">
                        <label class="form-label text-primary fw-bold small mb-1"><i class="bi bi-geo-alt-fill"></i> SUCURSAL DESTINO</label>
                        <select id="swal-destino" class="form-select form-select-lg shadow-sm border-primary swal-custom-input">
                            ${selectOpciones}
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-success fw-bold small mb-1"><i class="bi bi-box-seam-fill"></i> CANTIDAD A ENVIAR (Máx: ${maxStock})</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-success text-white border-success"><i class="bi bi-123"></i></span>
                            <input type="number" id="swal-cantidad" class="form-control border-success fw-bold text-center swal-custom-input" min="1" max="${maxStock}" value="1">
                        </div>
                    </div>
                </div>
            `,
            showCancelButton: true, 
            confirmButtonText: '<i class="bi bi-send-fill me-1"></i> Autorizar Envío', 
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            customClass: { popup: 'swal-adaptable', confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' },
            preConfirm: () => { return { destino: document.getElementById('swal-destino').value, cantidad: document.getElementById('swal-cantidad').value } }
        }).then((result) => {
            if (result.isConfirmed) {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                fetch(`/productos/transferir/${id}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify(result.value) })
                .then(res => res.json()).then(data => { if(data.status === 'success') { Swal.fire({title: '¡Ruta Dijkstra!', text: data.msg, icon: 'success', customClass: {popup: 'swal-adaptable'}}).then(() => window.location.reload()); } else { Swal.fire({title: 'Error', text: data.msg, icon: 'error', customClass: {popup: 'swal-adaptable'}}); } });
            }
        });
    }

    function confirmarEliminar(codigo, nombre) {
        Swal.fire({ title: '¿Eliminar?', text: `Vas a borrar: ${nombre}.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Sí, borrar', customClass: {popup: 'swal-adaptable'} })
        .then((result) => { if (result.isConfirmed) { window.location.href = "/productos/eliminar/" + codigo; } })
    }
</script>

<style>
    /* 🚀 ANIMACIONES DE ENTRADA Y 3D */
    .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .card-3d { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card-3d:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important; }

    /* 👻 SKELETON LOADER PARA EL BOT */
    .skeleton-line { height: 12px; border-radius: 6px; margin-bottom: 12px; animation: pulse-skeleton 1.5s infinite ease-in-out; }
    .skeleton-line.w-50 { width: 50%; }
    .skeleton-line.w-75 { width: 75%; }
    .skeleton-line.w-100 { width: 100%; }
    
    body.dark-mode .skeleton-line { background: #334155; }
    body:not(.dark-mode) .skeleton-line { background: #cbd5e1; }
    @keyframes pulse-skeleton { 0% { opacity: 0.5; } 50% { opacity: 1; } 100% { opacity: 0.5; } }

    /* 🌈 MAGIA RGB GLOBAL */
    body::before {
        content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 4px;
        background: linear-gradient(90deg, #ff007f, #7928ca, #00b4d8, #10b981, #f59e0b, #ff007f);
        background-size: 200% auto; animation: rgbFlow 4s linear infinite; z-index: 9999; box-shadow: 0 0 15px rgba(121, 40, 202, 0.6);
    }
    @keyframes rgbFlow { to { background-position: 200% center; } }

    /* 🎨 REGLAS MAESTRAS DE VISIBILIDAD */
    .adapt-text { transition: color 0.3s ease; }
    
    /* 🌙 MODO OSCURO */
    body.dark-mode .adapt-text { color: #f8fafc !important; }
    body.dark-mode .product-row { background: #1e293b !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    body.dark-mode .table-container-modern { background: rgba(15, 23, 42, 0.4) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    body.dark-mode .chart-card { background: rgba(15, 23, 42, 0.4) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    body.dark-mode .stock-box { background: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; }

    /* MODO CLARO */
    body:not(.dark-mode) .adapt-text { color: #1e293b !important; }
    body:not(.dark-mode) .product-row { background: #ffffff !important; border: 1px solid #e2e8f0 !important; }
    body:not(.dark-mode) .table-container-modern { background: #ffffff !important; border: 1px solid #e2e8f0 !important; }
    body:not(.dark-mode) .chart-card { background: #ffffff !important; border: 1px solid #e2e8f0 !important; }
    body:not(.dark-mode) .stock-box { background: rgba(0, 0, 0, 0.03) !important; border: 1px solid rgba(0, 0, 0, 0.08) !important; }
    
    /* 📊 TABLA */
    #tablaInteligente { border-collapse: separate; border-spacing: 0 12px; background: transparent; }
    .product-row { border-radius: 15px; transition: all 0.3s ease; }
    .product-row:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; }
    .product-row td { border: none !important; padding: 20px 10px; }
    .product-row td:first-child { border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .product-row td:last-child { border-top-right-radius: 15px; border-bottom-right-radius: 15px; }

    /* 🤖 BUSCADOR */
    .caja-ia-modern { border-radius: 50px; overflow: hidden; transition: all 0.3s ease; }
    body.dark-mode .caja-ia-modern { background: rgba(15, 23, 42, 0.8) !important; border: 1px solid rgba(59, 130, 246, 0.5) !important; }
    body.dark-mode .input-ia { background-color: transparent !important; color: white !important; }
    body.dark-mode .input-ia::placeholder { color: rgba(255,255,255,0.5) !important; }
    body.dark-mode .input-group-text i { color: #3b82f6 !important; }
    body.dark-mode .input-group-text { background: transparent !important; border: none !important; }

    body:not(.dark-mode) .caja-ia-modern { background: #ffffff !important; border: 1px solid rgba(59, 130, 246, 0.3) !important; }
    body:not(.dark-mode) .input-ia { background-color: transparent !important; color: #1e293b !important; }
    body:not(.dark-mode) .input-ia::placeholder { color: rgba(0,0,0,0.5) !important; }

    .caja-ia-modern:focus-within { border-color: transparent !important; animation: borderGlow 3s linear infinite; }
    @keyframes borderGlow { 0% { box-shadow: 0 0 12px rgba(255, 0, 127, 0.5); } 50% { box-shadow: 0 0 12px rgba(0, 180, 216, 0.5); } 100% { box-shadow: 0 0 12px rgba(255, 0, 127, 0.5); } }
    
    .btn-primary-gradient { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; border: none; }
    .badge-outline { background: transparent; border: 1.5px solid; padding: 5px 12px; border-radius: 8px; font-size: 0.65rem; font-weight: 800; letter-spacing: 1px; }

    /* ➕ BOTONES DE STOCK MEJORADOS */
    .btn-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: none; transition: all 0.3s ease; }
    .btn-icon.text-danger { background: rgba(239, 68, 68, 0.15) !important; color: #ef4444 !important; }
    .btn-icon.text-danger:hover { background: #ef4444 !important; color: #ffffff !important; box-shadow: 0 0 15px rgba(239, 68, 68, 0.8) !important; transform: scale(1.1); }
    .btn-icon.text-success { background: rgba(16, 185, 129, 0.15) !important; color: #10b981 !important; }
    .btn-icon.text-success:hover { background: #10b981 !important; color: #ffffff !important; box-shadow: 0 0 15px rgba(16, 185, 129, 0.8) !important; transform: scale(1.1); }

    /* 🚨 ALERTAS DE BORDES Y PULSOS */
    .alerta-baja-modern { border-left: 5px solid #f59e0b !important; }
    .alerta-critica-modern { border-left: 5px solid #ef4444 !important; background: rgba(239, 68, 68, 0.1) !important;}

    @keyframes pulso-naranja { 0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); } 70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); } 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); } }
    @keyframes pulso-rojo { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
    .latido-alerta-naranja { animation: pulso-naranja 2s infinite; }

    /* 🤖 SWEETALERT PREMIUM */
    body.dark-mode .swal-adaptable { background-color: #1e293b !important; border: 1px solid rgba(255,255,255,0.1); }
    body.dark-mode .swal-adaptable .swal2-title, body.dark-mode .swal-adaptable .swal2-html-container { color: #f8fafc !important; }
    body.dark-mode .swal-custom-input { background-color: #0f172a !important; color: white !important; }

    body:not(.dark-mode) .swal-adaptable { background-color: #ffffff !important; border: none; }
    body:not(.dark-mode) .swal-adaptable .swal2-title, body:not(.dark-mode) .swal-adaptable .swal2-html-container { color: #1e293b !important; }
    body:not(.dark-mode) .swal-custom-input { background-color: #f8fafc !important; color: #1e293b !important; }
    body:not(.dark-mode) .swal-adaptable .text-white { color: #1e293b !important; }
    body:not(.dark-mode) .swal-adaptable small, body:not(.dark-mode) .swal-adaptable p.small { color: #475569 !important; }

    /* ESTILOS PREMIUM PARA EL BOT */
    body.dark-mode .swal-bot { background: rgba(15, 23, 42, 0.95) !important; border: 1px solid rgba(59, 130, 246, 0.5); backdrop-filter: blur(10px); }
    body.dark-mode .swal-bot .swal2-title { color: #3b82f6 !important; }
    body.dark-mode .swal-bot .swal2-html-container { color: #f8fafc !important; }
    
    body:not(.dark-mode) .swal-bot { background: rgba(255, 255, 255, 0.95) !important; border: 1px solid rgba(59, 130, 246, 0.3); backdrop-filter: blur(10px); }
    body:not(.dark-mode) .swal-bot .swal2-title { color: #1d4ed8 !important; }
    body:not(.dark-mode) .swal-bot .swal2-html-container { color: #1e293b !important; }
</style>
@endsection