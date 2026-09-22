<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadCotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('leads.editar');
    }

    public function rules(): array
    {
        return [
            'quiere_cotizacion' => ['required', 'boolean'],
            'ruc_dni' => [
                Rule::requiredIf(fn () => $this->boolean('quiere_cotizacion')),
                'nullable', 'regex:/^(\d{8}|\d{11})$/',
            ],
            'reunion_realizada'  => ['required', 'boolean'],
            'cotizacion_enviada' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ruc_dni' => 'RUC / DNI',
        ];
    }
}
