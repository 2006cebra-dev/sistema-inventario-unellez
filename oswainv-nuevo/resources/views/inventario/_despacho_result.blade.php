<div style="text-align:left;color:#ccc;font-size:0.9rem;">
    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;">
        <span style="color:#888;">Productos</span>
        <span style="color:#fff;font-weight:600;">{{ count($resultados) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;">
        <span style="color:#888;">Unidades</span>
        <span style="color:#ffd700;font-weight:600;">{{ $total }}</span>
    </div>
    @if($motivo)
    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;">
        <span style="color:#888;">Motivo</span>
        <span style="color:#E50914;font-weight:600;">{{ $motivo }}</span>
    </div>
    @endif
    @if($sucursal)
    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2a2a2a;">
        <span style="color:#888;">Destino</span>
        <span style="color:#00b894;font-weight:600;">{{ $sucursal }}</span>
    </div>
    @endif
</div>
