<?php

namespace App\Http\Requests\Leads;

use App\Support\LeadResponseOptions;
use Illuminate\Validation\Rule;

class LeadRules
{
    public static function rules(): array
    {
        return [
            'nombre'    => ['nullable', 'string', 'max:150'],
            'lugar'     => ['nullable', 'string', 'max:150'],
            'telefono'  => ['nullable', 'string', 'regex:/^\+51\s?\d{3}\s?\d{3}\s?\d{3}$/'],

            'respuesta_rapida'      => ['nullable', Rule::in(array_keys(LeadResponseOptions::opciones()))],
            'respuesta_comentario'  => ['nullable', 'string', 'max:1000'],
            'respuesta_fecha'       => [
                Rule::requiredIf(fn () => request()->filled('respuesta_rapida')
                    && (LeadResponseOptions::requiere(request('respuesta_rapida'), 'fecha'))),
                'nullable', 'date',
            ],
            'respuesta_hora'        => [
                Rule::requiredIf(fn () => request()->filled('respuesta_rapida')
                    && (LeadResponseOptions::requiere(request('respuesta_rapida'), 'hora'))),
                'nullable', 'date_format:H:i',
            ],
            'sistema_interes'       => [
                Rule::requiredIf(fn () => request()->filled('respuesta_rapida')
                    && (LeadResponseOptions::requiere(request('respuesta_rapida'), 'sistema'))),
                'nullable', 'string', 'max:200',
            ],
        ];
    }
}
