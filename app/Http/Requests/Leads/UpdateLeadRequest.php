<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('leads.editar');
    }

    public function rules(): array
    {
        return LeadRules::rules();
    }

    public function attributes(): array
    {
        return [
            'nombre'               => 'nombre',
            'lugar'                => 'lugar',
            'telefono'             => 'teléfono',
            'respuesta_rapida'     => 'respuesta',
            'respuesta_comentario' => 'comentario',
            'respuesta_fecha'      => 'fecha',
            'respuesta_hora'       => 'hora',
            'sistema_interes'      => 'sistema de interés',
        ];
    }
}
