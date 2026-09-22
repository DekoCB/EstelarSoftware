<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id', 'descripcion', 'cantidad', 'unidad',
        'precio_unitario', 'descuento', 'subtotal', 'orden',
    ];

    public function quote(): BelongsTo { return $this->belongsTo(Quote::class); }
}
