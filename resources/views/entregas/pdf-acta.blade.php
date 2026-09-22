<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
    .page { padding: 40px 48px; }

    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #0ea5e9; }
    .company-name { font-size: 18px; font-weight: 700; color: #0f172a; }
    .company-sub  { font-size: 9px; color: #64748b; margin-top: 2px; }
    .company-info { font-size: 9px; color: #94a3b8; margin-top: 6px; line-height: 1.6; }
    .doc-badge    { text-align: right; }
    .doc-title    { font-size: 22px; font-weight: 800; color: #0ea5e9; letter-spacing: -0.5px; }
    .doc-num      { font-size: 11px; font-weight: 600; color: #334155; font-family: monospace; margin-top: 2px; }
    .doc-tipo     { display: inline-block; margin-top: 6px; padding: 3px 10px; border-radius: 20px;
                    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
                    background: #e0f2fe; color: #0284c7; }

    .info-grid { display: flex; gap: 16px; margin-bottom: 24px; }
    .info-box  { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; }
    .info-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 3px; }
    .info-value { font-size: 11px; font-weight: 600; color: #0f172a; }
    .info-sub   { font-size: 9px; color: #64748b; margin-top: 2px; }

    .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
                     color: #64748b; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

    .items-list { margin-bottom: 24px; }
    .item-row   { display: flex; align-items: flex-start; gap: 8px; padding: 6px 0; border-bottom: 1px dashed #f1f5f9; }
    .item-check { width: 14px; height: 14px; border: 1.5px solid #0ea5e9; border-radius: 3px;
                  display: flex; align-items: center; justify-content: center; margin-top: 1px; flex-shrink: 0; }
    .item-check-inner { width: 8px; height: 8px; background: #0ea5e9; border-radius: 1px; }
    .item-text  { font-size: 11px; color: #334155; }

    .obs-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; margin-bottom: 24px; }
    .obs-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #d97706; margin-bottom: 4px; }
    .obs-text  { font-size: 11px; color: #92400e; }

    .firma-section { margin-top: 32px; }
    .firma-grid { display: flex; gap: 40px; }
    .firma-box  { flex: 1; text-align: center; }
    .firma-line { border-top: 1px solid #334155; margin: 40px 20px 8px; }
    .firma-nombre { font-size: 11px; font-weight: 700; color: #0f172a; }
    .firma-cargo  { font-size: 9px; color: #64748b; }
    .firma-dni    { font-size: 9px; color: #94a3b8; font-family: monospace; }

    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e2e8f0;
              text-align: center; font-size: 9px; color: #94a3b8; }

    .badge-firmado   { background: #d1fae5; color: #059669; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: 700; }
    .badge-borrador  { background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: 700; }
    .badge-observado { background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: 700; }
</style>
</head>
<body>
<div class="page">

    {{-- Encabezado --}}
    <div class="header">
        <div>
            <div class="company-name">{{ $config?->razon_social ?? 'Estelar Software Empresarial' }}</div>
            <div class="company-sub">Software a medida · Huancayo, Perú</div>
            <div class="company-info">
                RUC: {{ $config?->ruc ?? '—' }}<br>
                {{ $config?->direccion ?? '' }}<br>
                {{ $config?->email ?? '' }} · {{ $config?->telefono ?? '' }}
            </div>
        </div>
        <div class="doc-badge">
            <div class="doc-title">ACTA DE ENTREGA</div>
            <div class="doc-num">N° {{ str_pad($entrega->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div>
                <span class="doc-tipo">{{ $entrega->tipoLabel() }}</span>
            </div>
            <div style="margin-top:6px;">
                @if($entrega->estado === 'firmado')
                <span class="badge-firmado">FIRMADO</span>
                @elseif($entrega->estado === 'observado')
                <span class="badge-observado">OBSERVADO</span>
                @else
                <span class="badge-borrador">BORRADOR</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Proyecto</div>
            <div class="info-value">{{ $entrega->project->name }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $entrega->client->razon_social }}</div>
            @if($entrega->client->ruc)
            <div class="info-sub">RUC: {{ $entrega->client->ruc }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-label">Fecha de entrega</div>
            <div class="info-value" style="font-family: monospace;">{{ $entrega->fecha_entrega->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Título --}}
    <div style="margin-bottom: 20px;">
        <div class="section-title">Descripción</div>
        <p style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">{{ $entrega->titulo }}</p>
        @if($entrega->descripcion)
        <p style="font-size: 11px; color: #475569; line-height: 1.6;">{{ $entrega->descripcion }}</p>
        @endif
    </div>

    {{-- Ítems entregados --}}
    @if($entrega->items_entregados && count(array_filter($entrega->items_entregados)))
    <div class="items-list">
        <div class="section-title">Ítems entregados</div>
        @foreach($entrega->items_entregados as $item)
        @if($item)
        <div class="item-row">
            <div class="item-check"><div class="item-check-inner"></div></div>
            <div class="item-text">{{ $item }}</div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Observaciones --}}
    @if($entrega->observaciones)
    <div class="obs-box">
        <div class="obs-title">Observaciones</div>
        <div class="obs-text">{{ $entrega->observaciones }}</div>
    </div>
    @endif

    {{-- Firmas --}}
    <div class="firma-section">
        <div class="section-title">Conformidad</div>
        <div class="firma-grid">
            <div class="firma-box">
                <div class="firma-line"></div>
                <div class="firma-nombre">{{ $config?->razon_social ?? 'Estelar Software Empresarial' }}</div>
                <div class="firma-cargo">Proveedor del servicio</div>
                <div class="firma-dni">RUC: {{ $config?->ruc ?? '—' }}</div>
            </div>
            <div class="firma-box">
                <div class="firma-line"></div>
                @if($entrega->firma_cliente)
                <div class="firma-nombre">{{ $entrega->firma_cliente }}</div>
                @if($entrega->cargo_firmante)
                <div class="firma-cargo">{{ $entrega->cargo_firmante }}</div>
                @endif
                @if($entrega->dni_firmante)
                <div class="firma-dni">DNI: {{ $entrega->dni_firmante }}</div>
                @endif
                @else
                <div class="firma-nombre">{{ $entrega->client->razon_social }}</div>
                <div class="firma-cargo">Cliente</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Acta generada el {{ now()->format('d/m/Y H:i') }} · {{ $config?->razon_social ?? 'Estelar Software Empresarial' }} · Huancayo, Perú
    </div>

</div>
</body>
</html>
