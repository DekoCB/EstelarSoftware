<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    const TIPO_FACTURA = '01';
    const TIPO_BOLETA  = '03';

    const ESTADOS_SUNAT = [
        'borrador', 'pendiente', 'enviando', 'aceptado', 'rechazado', 'anulado', 'error',
    ];

    const ESTADO_LABELS = [
        'borrador'  => 'Borrador',
        'pendiente' => 'Pendiente',
        'enviando'  => 'Enviando',
        'aceptado'  => 'Aceptado',
        'rechazado' => 'Rechazado',
        'anulado'   => 'Anulado',
        'error'     => 'Error',
    ];

    // Mapeo tipo_documento ERP → código SUNAT
    const DOC_CODES = [
        'RUC'       => '6',
        'DNI'       => '1',
        'CE'        => '4',
        'PASAPORTE' => '7',
    ];

    // Mapeo unidades ERP → códigos SUNAT
    const UNIDAD_SUNAT = [
        'servicio' => 'ZZ',
        'hora'     => 'HUR',
        'día'      => 'DIA',
        'mes'      => 'MON',
        'unidad'   => 'NIU',
        'licencia' => 'NIU',
    ];

    protected $fillable = [
        'tipo_comprobante', 'serie', 'correlativo', 'numero_completo',
        'sunat_doc_id', 'client_id', 'quote_id',
        'fecha_emision', 'fecha_vencimiento', 'moneda',
        'subtotal', 'igv', 'total',
        'estado_sunat', 'sunat_mensaje', 'notas',
        'created_by', 'emitido_at',
    ];

    protected $casts = [
        'fecha_emision'     => 'date',
        'fecha_vencimiento' => 'date',
        'emitido_at'        => 'datetime',
        'subtotal'          => 'decimal:2',
        'igv'               => 'decimal:2',
        'total'             => 'decimal:2',
    ];

    // ── Relaciones ───────────────────────────────────────────────────
    public function client(): BelongsTo    { return $this->belongsTo(Client::class); }
    public function quote(): BelongsTo     { return $this->belongsTo(Quote::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany       { return $this->hasMany(InvoiceItem::class)->orderBy('orden'); }

    // ── Helpers ──────────────────────────────────────────────────────
    public function esFactura(): bool { return $this->tipo_comprobante === self::TIPO_FACTURA; }
    public function esBoleta(): bool  { return $this->tipo_comprobante === self::TIPO_BOLETA; }

    public function tipoLabel(): string
    {
        return $this->esFactura() ? 'Factura' : 'Boleta';
    }

    public function estadoLabel(): string
    {
        return self::ESTADO_LABELS[$this->estado_sunat] ?? $this->estado_sunat;
    }

    public function estadoBadgeClass(): string
    {
        return match ($this->estado_sunat) {
            'borrador'  => 'bg-slate-700/60 text-slate-400',
            'pendiente' => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
            'enviando'  => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'aceptado'  => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'rechazado' => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            'anulado'   => 'bg-slate-600/60 text-slate-500',
            'error'     => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            default     => 'bg-slate-700/60 text-slate-400',
        };
    }

    public function monedaSimbolo(): string
    {
        return $this->moneda === 'USD' ? '$' : 'S/';
    }

    public function puedeEmitirse(): bool
    {
        return in_array($this->estado_sunat, ['borrador', 'pendiente', 'error'])
            && $this->sunat_doc_id !== null;
    }

    public function puedeBorrarse(): bool
    {
        // Solo si nunca fue registrado en la API SUNAT (sin correlativo asignado).
        // Un comprobante con sunat_doc_id ya tiene número en SUNAT y debe anularse
        // con Nota de Crédito, no eliminarse.
        return is_null($this->sunat_doc_id) && $this->estado_sunat === 'borrador';
    }

    // ── Scope ────────────────────────────────────────────────────────
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('numero_completo', 'like', "%{$term}%")
              ->orWhereHas('client', fn($c) => $c->where('razon_social', 'like', "%{$term}%"));
        });
    }
}
