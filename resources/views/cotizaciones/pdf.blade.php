<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

    .page { padding: 40px 48px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #0ea5e9; }
    .company-name { font-size: 20px; font-weight: 700; color: #0f172a; }
    .company-sub  { font-size: 10px; color: #64748b; margin-top: 2px; }
    .company-info { font-size: 9px; color: #94a3b8; margin-top: 8px; line-height: 1.6; }

    .quote-badge { text-align: right; }
    .quote-title { font-size: 24px; font-weight: 800; color: #0ea5e9; letter-spacing: -0.5px; }
    .quote-num   { font-size: 13px; font-weight: 600; color: #334155; margin-top: 2px; font-family: monospace; }
    .quote-status { display: inline-block; margin-top: 6px; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-borrador  { background: #f1f5f9; color: #64748b; }
    .status-enviado   { background: #e0f2fe; color: #0284c7; }
    .status-aceptado  { background: #d1fae5; color: #059669; }
    .status-rechazado { background: #fee2e2; color: #dc2626; }
    .status-facturado { background: #ede9fe; color: #7c3aed; }

    /* Info section */
    .info-grid { display: flex; gap: 20px; margin-bottom: 28px; }
    .info-box  { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; }
    .info-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; }
    .info-value { font-size: 11px; font-weight: 600; color: #1e293b; line-height: 1.4; }
    .info-sub   { font-size: 9px; color: #64748b; margin-top: 2px; }

    /* Items table */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .items-table thead tr { background: #0f172a; }
    .items-table thead th { padding: 10px 12px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; }
    .items-table thead th.right { text-align: right; }
    .items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    .items-table tbody tr:nth-child(even) { background: #fafafa; }
    .items-table tbody td { padding: 10px 12px; font-size: 10px; color: #334155; vertical-align: top; }
    .items-table tbody td.right { text-align: right; font-family: monospace; }
    .item-desc  { font-weight: 600; color: #1e293b; }
    .item-unit  { font-size: 9px; color: #94a3b8; margin-top: 2px; }

    /* Totals */
    .totals { width: 240px; margin-left: auto; }
    .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 10px; color: #64748b; border-bottom: 1px solid #f1f5f9; }
    .totals-row.total { padding: 10px 0; border-top: 2px solid #0ea5e9; border-bottom: none; font-size: 14px; font-weight: 800; color: #0ea5e9; }
    .totals-row .label { }
    .totals-row .value { font-family: monospace; font-weight: 600; }

    /* Notes */
    .notes-section { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 20px; }
    .notes-box { flex: 1; }
    .notes-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; }
    .notes-text  { font-size: 9px; color: #64748b; line-height: 1.7; white-space: pre-wrap; }

    /* Footer */
    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 8px; color: #cbd5e1; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div>
            @if($config?->logo_documentos && file_exists(storage_path('app/public/' . $config->logo_documentos)))
            <img src="{{ storage_path('app/public/' . $config->logo_documentos) }}"
                 style="height:48px; margin-bottom:8px;" alt="logo">
            @else
            <div class="company-name">{{ $config?->razon_social ?? config('app.name') }}</div>
            @endif
            @if($config?->nombre_comercial)
            <div class="company-sub">{{ $config->nombre_comercial }}</div>
            @endif
            <div class="company-info">
                @if($config?->ruc) RUC: {{ $config->ruc }}<br>@endif
                @if($config?->direccion) {{ $config->direccion }}<br>@endif
                @if($config?->email) {{ $config->email }}<br>@endif
                @if($config?->telefono) Tel: {{ $config->telefono }}@endif
            </div>
        </div>
        <div class="quote-badge">
            <div class="quote-title">COTIZACIÓN</div>
            <div class="quote-num">{{ $cotizacion->numero }}</div>
            <div class="quote-status status-{{ $cotizacion->status }}">{{ $cotizacion->statusLabel() }}</div>
        </div>
    </div>

    {{-- Info: cliente + fechas --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $cotizacion->client->razon_social }}</div>
            @if($cotizacion->client->nombre_comercial)
            <div class="info-sub">{{ $cotizacion->client->nombre_comercial }}</div>
            @endif
            @if($cotizacion->client->numero_documento)
            <div class="info-sub">RUC/DNI: {{ $cotizacion->client->numero_documento }}</div>
            @endif
            @if($cotizacion->client->direccion)
            <div class="info-sub">{{ $cotizacion->client->direccion }}</div>
            @endif
        </div>
        <div class="info-box" style="flex:0 0 180px;">
            <div class="info-label">Fecha de emisión</div>
            <div class="info-value">{{ $cotizacion->fecha_emision->format('d/m/Y') }}</div>
            @if($cotizacion->fecha_vencimiento)
            <div class="info-label" style="margin-top:10px;">Válida hasta</div>
            <div class="info-value">{{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}</div>
            @endif
            <div class="info-label" style="margin-top:10px;">Moneda</div>
            <div class="info-value">{{ $cotizacion->moneda }} — {{ $cotizacion->monedaSimbolo() }}</div>
        </div>
        @if($cotizacion->project)
        <div class="info-box" style="flex:0 0 160px;">
            <div class="info-label">Proyecto</div>
            <div class="info-value">{{ $cotizacion->project->name }}</div>
        </div>
        @endif
    </div>

    {{-- Tabla de ítems --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:42%">Descripción</th>
                <th class="right" style="width:10%">Cant.</th>
                <th style="width:10%">Unidad</th>
                <th class="right" style="width:14%">Precio Unit.</th>
                <th class="right" style="width:8%">Dto %</th>
                <th class="right" style="width:16%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->items as $item)
            <tr>
                <td><div class="item-desc">{{ $item->descripcion }}</div></td>
                <td class="right">{{ number_format($item->cantidad, 2) }}</td>
                <td>{{ $item->unidad }}</td>
                <td class="right">{{ $cotizacion->monedaSimbolo() }} {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="right">{{ $item->descuento > 0 ? $item->descuento.'%' : '—' }}</td>
                <td class="right" style="font-weight:600; color:#1e293b;">{{ $cotizacion->monedaSimbolo() }} {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="totals">
        <div class="totals-row">
            <span class="label">Subtotal</span>
            <span class="value">{{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->subtotal, 2) }}</span>
        </div>
        @if($cotizacion->igv > 0)
        <div class="totals-row">
            <span class="label">IGV ({{ $config?->igv_porcentaje ?? 18 }}%)</span>
            <span class="value">{{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->igv, 2) }}</span>
        </div>
        @endif
        <div class="totals-row total">
            <span class="label">TOTAL</span>
            <span class="value">{{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->total, 2) }}</span>
        </div>
    </div>

    {{-- Notas y términos --}}
    @if($cotizacion->notas || $cotizacion->terminos)
    <div class="notes-section">
        @if($cotizacion->notas)
        <div class="notes-box">
            <div class="notes-label">Notas</div>
            <div class="notes-text">{{ $cotizacion->notas }}</div>
        </div>
        @endif
        @if($cotizacion->terminos)
        <div class="notes-box">
            <div class="notes-label">Términos y condiciones</div>
            <div class="notes-text">{{ $cotizacion->terminos }}</div>
        </div>
        @endif
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        {{ $config?->razon_social ?? config('app.name') }} — Documento generado el {{ now()->format('d/m/Y H:i') }}
        @if($config?->web) · {{ $config->web }}@endif
    </div>

</div>
</body>
</html>
