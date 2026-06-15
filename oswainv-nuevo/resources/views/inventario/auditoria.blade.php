<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Auditoría Kardex - OSWA Inv</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg-dark: #141414; --bg-card: #181818; --bg-input: #333333;
            --border-color: #2b2b2b; --text-primary: #ffffff; --text-secondary: #b3b3b3;
            --accent-primary: #E50914; --accent-success: #00b894;
            --accent-danger: #e74c3c; --accent-warning: #fdcb6e; --accent-info: #0984e3;
        }
        [data-theme="light"] {
            --bg-dark: #f5f6f8; --bg-card: #ffffff; --bg-input: #e9ecef;
            --border-color: #dee2e6; --text-primary: #212529; --text-secondary: #6c757d;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-primary); margin: 0; transition: all 0.3s ease; }
        
        .audit-table { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
        .audit-table thead { background: #222; }
        .audit-table th { padding: 1rem 1.5rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.8rem; }
        .audit-table td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
        .audit-table tbody tr { background: var(--bg-card); transition: background 0.2s ease; }
        .audit-table tbody tr:hover { background: #222; }
        
        body[data-theme="dark"] .audit-table,
        body[data-theme="dark"] .audit-table th,
        body[data-theme="dark"] .audit-table td,
        body[data-theme="dark"] .audit-table thead th,
        body[data-theme="dark"] .audit-table tbody tr {
            background-color: #181818 !important;
            color: #ffffff !important;
            border-color: #2b2b2b !important;
        }
        body[data-theme="dark"] .audit-table thead th {
            background-color: #222 !important;
            color: #b3b3b3 !important;
        }
        body[data-theme="dark"] .audit-table tbody tr:hover {
            background-color: #222 !important;
        }

        .type-entrada { background: rgba(0,184,148,0.15); color: var(--accent-success); padding: 4px 10px; border-radius: 4px; font-weight: 600; }
        .type-salida { background: rgba(231,76,60,0.15); color: var(--accent-danger); padding: 4px 10px; border-radius: 4px; font-weight: 600; }

        .firma-valid { 
            background: rgba(0,184,148,0.2); color: #10b981; padding: 6px 14px; border-radius: 4px; font-weight: 600; font-size: 0.85rem;
            border: 1px solid #10b981;
            box-shadow: 0 0 12px rgba(16,185,129,0.3);
            display: inline-flex; align-items: center; gap: 6px;
        }
        .firma-invalid { 
            background: rgba(239,68,68,0.25); color: #ef4444; padding: 6px 14px; border-radius: 4px; font-weight: 700; font-size: 0.85rem;
            border: 1px solid #ef4444;
            animation: pulseAlert 1s infinite;
            box-shadow: 0 0 15px rgba(239,68,68,0.5);
            display: inline-flex; align-items: center; gap: 6px;
        }
        @keyframes pulseAlert {
            0%, 100% { box-shadow: 0 0 10px rgba(239,68,68,0.4); transform: scale(1); }
            50% { box-shadow: 0 0 25px rgba(239,68,68,0.8); transform: scale(1.02); }
        }
        
        .firma-hash { font-family: monospace; font-size: 0.75rem; color: var(--text-secondary); background: var(--bg-input); padding: 4px 8px; border-radius: 4px; }
        
        .user-badge { background: var(--bg-input); padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; }

        .professional-footer {
            text-align: center;
            padding: 1.5rem 4%;
            margin-top: 2rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-dark);
            color: var(--text-secondary);
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .professional-footer span.highlight {
            color: var(--text-primary);
            font-weight: 600;
        }
        .professional-footer .heart-icon {
            color: var(--accent-danger);
            animation: heartbeat 1.5s infinite;
            display: inline-block;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</head>
<body data-theme="dark">
    
    @include('partials.navbar')
    
    <main class="main-content">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-50">
            <div class="d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-file-earmark-text fs-4"></i>
                </div>
                <h2 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">Auditoría de Movimiento</h2>
            </div>
            
            <button onclick="Swal.fire('Próximamente', 'Estamos preparando el código del controlador para exportar a Excel', 'info')" class="btn btn-success d-flex align-items-center gap-2" style="font-weight: 600; padding: 8px 16px;">
                <i class="bi bi-file-earmark-excel fs-5"></i> Exportar CSV
            </button>
        </div>
        
        <div class="table-responsive">
            <div class="audit-table">
                <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                        <th>Firma SHA-256</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimientos as $mov)
                        @php
                            // 1. RECALCULAR HASH EN TIEMPO REAL CON NOMBRES EXACTOS
                            $cadenaActual = $mov->id . $mov->codigo_producto . $mov->tipo . $mov->cantidad . $mov->motivo . $mov->usuario_accion;
                            $hashEnTiempoReal = hash('sha256', $cadenaActual);

                            // 2. SISTEMA DE SEMÁFORO DE INTEGRIDAD (Usando firma_hash)
                            if (empty($mov->firma_hash)) {
                                $estado = 'Antiguo';
                                $claseBadge = 'border-secondary text-secondary';
                                $icono = 'bi-clock-history';
                                $textoFirma = 'SIN FIRMA';
                            } elseif ($mov->firma_hash === $hashEnTiempoReal) {
                                $estado = 'Seguro';
                                $claseBadge = 'border-success text-success';
                                $icono = 'bi-shield-check';
                                $textoFirma = substr($mov->firma_hash, 0, 15) . '...';
                            } else {
                                $estado = 'Alterado';
                                $claseBadge = 'border-danger text-danger bg-danger bg-opacity-10 fw-bold';
                                $icono = 'bi-shield-exclamation';
                                $textoFirma = 'HASH INVÁLIDO';
                            }
                        @endphp

                        <tr>
                            <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div>{{ $mov->codigo_producto }}</div>
                                <small style="color: var(--text-secondary);">{{ $mov->producto?->nombre ?? 'Sin producto' }}</small>
                            </td>
                            <td>
                                <span class="{{ $mov->tipo === 'Entrada' ? 'type-entrada' : 'type-salida' }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td><strong>{{ $mov->cantidad }}</strong></td>
                            <td>{{ $mov->motivo }}</td>
                            <td><span class="user-badge">{{ $mov->usuario_accion }}</span></td>
                            <td class="text-secondary align-middle" style="font-family: monospace; font-size: 0.85rem; letter-spacing: 1px;">
                                @if($estado == 'Alterado')
                                    <span class="text-danger fw-bold">{{ $textoFirma }}</span>
                                @else
                                    {{ $textoFirma }}
                                @endif
                            </td>
                            <td class="align-middle">
                                <button class="btn btn-sm {{ $claseBadge }} d-flex align-items-center bg-transparent" style="cursor: default; border-radius: 6px;">
                                    <i class="bi {{ $icono }} me-2"></i> {{ $estado }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 p-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px;">
            <h6><i class="bi bi-shield-lock me-2"></i>Seguridad de Datos</h6>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Cada movimiento genera una firma digital SHA-256 que incluye: código del producto + tipo + cantidad + fecha/hora.
                Si alguien intenta modificar el historial, la firma ya no coincidirá y el sistema mostrará "Alterada".
            </p>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Antifraud: Check for altered records
            const alteredRows = document.querySelectorAll('.firma-invalid');
            if (alteredRows.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ ALERTA CRÍTICA - POSIBLE FRAUDE',
                    html: `<p style="color:#ff6b6b;font-size:1.1rem;">Se detectaron <strong>${alteredRows.length} registro(s)</strong> alterado(s) en la Base de Datos.</p><p style="color:var(--text-secondary);">La firma SHA-256 no coincide. Existe posibilidad de manipulación directa en la BD.</p>`,
                    background: 'var(--bg-card)',
                    color: 'var(--text-primary)',
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'Investigar',
                    allowOutsideClick: false,
                    backdrop: `
                        rgba(231,76,60,0.3)
                        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Ctext y='50' x='50' font-size='50' text-anchor='middle'%3E⚠️%3C/text%3E%3C/svg%3E")
                        center no-repeat
                    `
                });
            }
        });
    </script>

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
</body>
</html>