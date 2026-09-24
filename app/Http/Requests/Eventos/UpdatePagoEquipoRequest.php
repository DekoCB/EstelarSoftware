<?php

namespace App\Http\Requests\Eventos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePagoEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('eventos.editar');
    }

    public function rules(): array
    {
        return [
            'estado_pago' => ['required', Rule::in(['pagado', 'rechazado'])],
        ];
    }
}
