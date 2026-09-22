<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    const ESTADOS = ['borrador', 'enviado', 'aceptado', 'rechazado', 'facturado'];

    const ESTADO_LABELS = [
        'borrador'  => 'Borrador',
        'enviado'   => 'Enviado',
        'aceptado'  => 'Aceptado',
        'rechazado' => 'Rechazado',
        'facturado' => 'Facturado',
    ];

    const UNIDADES = ['servicio', 'hora', 'día', 'mes', 'unidad', 'licencia'];

    protected $fillable = [
        'numero', 'client_id', 'project_id', 'status',
        'fecha_emision', 'fecha_vencimiento', 'moneda', 'tipo_cambio',
        'subtotal', 'igv', 'total', 'incluye_igv',
        'notas', 'terminos', 'created_by', 'sent_at', 'accepted_at',
    ];

    protected $casts = [
        'fecha_emision'     => 'date',
        'fecha_vencimiento' => 'date',
        'incluye_igv'       => 'boolean',
        'sent_at'           => 'datetime',
        'accepted_at'       => 'datetime',
        'subtotal'          => 'decimal:2',
        'igv'               => 'decimal:2',
        'total'             => 'decimal:2',
    ];

    // ── Relaciones ───────────────────────────────────────────────────
    public function client(): BelongsTo    { return $this->belongsTo(Client::class); }
    public function project(): BelongsTo   { return $this->belongsTo(Project::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany       { return $this->hasMany(QuoteItem::class)->orderBy('orden'); }
    public function payments(): HasMany    { return $this->hasMany(QuotePayment::class)->orderBy('orden'); }
    public function invoices(): HasMany    { return $this->hasMany(Invoice::class); }

    // ── Helpers de cobros ─────────────────────────────────────────────
    public function montoCobrado(): float
    {
        return (float) $this->payments()->where('estado', 'pagada')->sum('monto');
    }

    public function montoPendiente(): float
    {
        return (float) $this->total - $this->montoCobrado();
    }

    public function tienePlanCobros(): bool
    {
        return $this->payments()->exists();
    }

    public function porcentajeCobrado(): int
    {
        return $this->total > 0
            ? (int) round($this->montoCobrado() / (float) $this->total * 100)
            : 0;
    }

    public function generarPlanDefault(): void
    {
        if ($this->tienePlanCobros()) return;

        $cuotas = [
            ['nombre' => 'Anticipo',     'porcentaje' => 40, 'orden' => 1],
            ['nombre' => '2da cuota',    'porcentaje' => 30, 'orden' => 2],
            ['nombre' => 'Cuota final',  'porcentaje' => 30, 'orden' => 3],
        ];

        foreach ($cuotas as $cuota) {
            $this->payments()->create([
                'nombre'     => $cuota['nombre'],
                'porcentaje' => $cuota['porcentaje'],
                'monto'      => round((float) $this->total * $cuota['porcentaje'] / 100, 2),
                'orden'      => $cuota['orden'],
                'estado'     => 'pendiente',
            ]);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────
    public function statusLabel(): string
    {
        return self::ESTADO_LABELS[$this->status] ?? $this->status;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'borrador'  => 'bg-slate-700/60 text-slate-400',
            'enviado'   => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'aceptado'  => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'rechazado' => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            'facturado' => 'bg-violet-500/10 text-violet-400 ring-1 ring-violet-500/20',
            default     => 'bg-slate-700/60 text-slate-400',
        };
    }

    public function monedaSimbolo(): string
    {
        return $this->moneda === 'USD' ? '$' : 'S/';
    }

    public function estaVencida(): bool
    {
        return $this->fecha_vencimiento
            && $this->fecha_vencimiento->isPast()
            && !in_array($this->status, ['aceptado', 'facturado', 'rechazado']);
    }

    public static function generarNumero(): string
    {
        $year  = now()->year;
        $count = static::withTrashed()->whereYear('created_at', $year)->count() + 1;
        return 'COT-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ── Scopes ───────────────────────────────────────────────────────
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('numero', 'like', "%{$term}%")
              ->orWhereHas('client', fn($c) => $c->where('razon_social', 'like', "%{$term}%"));
        });
    }
}
