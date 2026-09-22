<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotePayment extends Model
{
    protected $fillable = [
        'quote_id', 'invoice_id', 'cash_movement_id',
        'nombre', 'porcentaje', 'monto',
        'fecha_vencimiento', 'fecha_pago', 'estado',
        'metodo_pago', 'notas', 'orden',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago'        => 'datetime',
        'monto'             => 'decimal:2',
        'porcentaje'        => 'decimal:2',
    ];

    public function quote(): BelongsTo   { return $this->belongsTo(Quote::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }

    public function estadoBadgeClass(): string
    {
        return match ($this->estado) {
            'pagada'   => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'vencida'  => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            'pendiente'=> 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
            default    => 'bg-slate-700/60 text-slate-400',
        };
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            'pagada'    => 'Pagado',
            'vencida'   => 'Vencida',
            'pendiente' => 'Pendiente',
            default     => $this->estado,
        };
    }

    public function estaVencida(): bool
    {
        return $this->estado === 'pendiente'
            && $this->fecha_vencimiento
            && $this->fecha_vencimiento->isPast();
    }
}
