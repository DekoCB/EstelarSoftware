<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'created_by', 'cash_movement_id',
        'periodo', 'tipo', 'concepto', 'monto', 'moneda',
        'metodo_pago', 'estado', 'fecha_pago', 'notas',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto'      => 'decimal:2',
    ];

    const TIPOS = [
        'sueldo'    => 'Sueldo',
        'honorario' => 'Honorario / RxH',
        'comision'  => 'Comisión',
        'bono'      => 'Bono',
        'adelanto'  => 'Adelanto',
        'otro'      => 'Otro',
    ];

    const METODOS_PAGO = [
        'efectivo'      => 'Efectivo',
        'transferencia' => 'Transferencia',
        'yape'          => 'Yape',
        'plin'          => 'Plin',
        'tarjeta'       => 'Tarjeta',
        'cheque'        => 'Cheque',
        'otro'          => 'Otro',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function metodoPagoLabel(): string
    {
        return self::METODOS_PAGO[$this->metodo_pago ?? ''] ?? '—';
    }

    public function periodoFormateado(): string
    {
        [$anio, $mes] = explode('-', $this->periodo);
        $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        return ($meses[(int)$mes] ?? $mes) . ' ' . $anio;
    }

    public function tipoBadgeClass(): string
    {
        return match ($this->tipo) {
            'sueldo'    => 'bg-sky-500/15 text-sky-400',
            'honorario' => 'bg-violet-500/15 text-violet-400',
            'comision'  => 'bg-emerald-500/15 text-emerald-400',
            'bono'      => 'bg-amber-500/15 text-amber-400',
            'adelanto'  => 'bg-orange-500/15 text-orange-400',
            default     => 'bg-slate-500/15 text-slate-400',
        };
    }

    // ── Relaciones ────────────────────────────────────────────────────
    public function user(): BelongsTo         { return $this->belongsTo(User::class); }
    public function createdBy(): BelongsTo    { return $this->belongsTo(User::class, 'created_by'); }
    public function cashMovement(): BelongsTo { return $this->belongsTo(CashMovement::class); }

    // ── Scopes ────────────────────────────────────────────────────────
    public function scopePeriodo($q, string $periodo) { return $q->where('periodo', $periodo); }
    public function scopePendientes($q)  { return $q->where('estado', 'pendiente'); }
    public function scopePagados($q)     { return $q->where('estado', 'pagado'); }
}
