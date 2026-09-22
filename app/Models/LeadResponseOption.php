<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadResponseOption extends Model
{
    protected $fillable = [
        'clave', 'label', 'color', 'requiere_fecha', 'requiere_hora',
        'requiere_sistema', 'comentario_auto', 'orden',
    ];

    protected $casts = [
        'requiere_fecha'   => 'boolean',
        'requiere_hora'    => 'boolean',
        'requiere_sistema' => 'boolean',
    ];
}
