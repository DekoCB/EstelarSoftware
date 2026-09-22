<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tipo', 'concepto', 'descripcion', 'monto', 'moneda',
        'metodo_pago', 'referencia', 'fecha', 'categoria',
        'invoice_id', 'quote_id', 'client_id', 'user_id',
        'comprobante_path', 'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    const CATEGORIAS_INGRESO = [
        'cobro_cliente'   => 'Cobro a cliente',
        'anticipo_cliente' => 'Anticipo de cliente',
        'otro_ingreso'    => 'Otro ingreso',
    ];

    const CATEGORIAS_EGRESO = [
        'pago_proveedor' => 'Pago a proveedor',
        'planilla'       => 'Planilla / sueldos',
        'servicios'      => 'Servicios (luz, internet, etc.)',
        'equipos'        => 'Equipos / hardware',
        'impuestos'      => 'Impuestos / tributos',
        'otro_egreso'    => 'Otro egreso',
    ];

    const METODOS_PAGO = [
        'efectivo'      => 'Efectivo',
        'transferencia' => 'Transferencia bancaria',
        'yape'          => 'Yape',
        'plin'          => 'Plin',
        'tarjeta'       => 'Tarjeta',
        'cheque'        => 'Cheque',
        'otro'          => 'Otro',
    ];

    public function categoriaLabel(): string
    {
        $all = array_merge(self::CATEGORIAS_INGRESO, self::CATEGORIAS_EGRESO);
        return $all[$this->categoria] ?? $this->categoria;
    }

    public function metodoPagoLabel(): string
    {
        return self::METODOS_PAGO[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function montoFormateado(): string
    {
        return number_format($this->monto, 2);
    }

    // ── Relaciones ────────────────────────────────────────────────────
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function quote(): BelongsTo  { return $this->belongsTo(Quote::class); }

    // ── Scopes ────────────────────────────────────────────────────────
    public function scopeIngresos($q)  { return $q->where('tipo', 'ingreso'); }
    public function scopeEgresos($q)   { return $q->where('tipo', 'egreso'); }
    public function scopeDelMes($q, $year, $month) {
        return $q->whereYear('fecha', $year)->whereMonth('fecha', $month);
    }
}
