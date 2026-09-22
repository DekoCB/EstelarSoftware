<?php

namespace App\Http\Requests\Eventos;

use App\Models\EventLead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('eventos.editar');
    }

    public function rules(): array
    {
        return [
            'tipo_documento'   => ['required', Rule::in(EventLead::TIPOS_DOCUMENTO)],
            'numero_documento' => ['nullable', 'string', 'max:15'],
            'nombres'          => ['required', 'string', 'max:200'],
            'empresa'          => ['nullable', 'string', 'max:200'],
            'rubro'            => ['nullable', 'string', 'max:100'],
            'email'            => ['nullable', 'email', 'max:150'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'direccion'        => ['nullable', 'string', 'max:500'],
            'latitud'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'         => ['nullable', 'numeric', 'between:-180,180'],
            'precision_metros' => ['nullable', 'numeric', 'min:0'],
            'interes'          => ['nullable', 'string', 'max:1000'],
            'estado'           => ['required', Rule::in(EventLead::ESTADOS)],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_documento'   => 'tipo de documento',
            'numero_documento' => 'número de documento',
        ];
    }
}
