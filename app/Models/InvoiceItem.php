<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'descripcion', 'unidad_sunat',
        'cantidad', 'precio_unitario', 'tipo_afectacion', 'igv_porcentaje',
        'subtotal', 'igv', 'total', 'orden',
    ];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}
