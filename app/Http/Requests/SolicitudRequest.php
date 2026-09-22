<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('solicitudes.crear');
    }

    public function rules(): array
    {
        return [
            'tipo'        => ['required', 'in:dinero,objeto'],
            'titulo'      => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'monto'       => ['nullable', 'numeric', 'min:0.01', 'max:99999'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required'        => 'Selecciona el tipo de solicitud.',
            'titulo.required'      => 'El título es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'monto.numeric'        => 'El monto debe ser un número.',
        ];
    }
}
