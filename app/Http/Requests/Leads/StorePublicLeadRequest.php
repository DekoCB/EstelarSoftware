<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'          => ['required', 'string', 'max:150'],
            'empresa'         => ['nullable', 'string', 'max:150'],
            'email'           => ['nullable', 'email', 'max:150'],
            'telefono'        => ['nullable', 'string', 'max:20'],
            'sistema_interes' => ['nullable', 'string', 'max:200'],
            'mensaje'         => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'          => 'nombre',
            'empresa'         => 'empresa',
            'telefono'        => 'teléfono',
            'sistema_interes' => 'área a automatizar',
        ];
    }
}
