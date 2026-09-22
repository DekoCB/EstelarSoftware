<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

class StoreReunionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('leads.editar');
    }

    public function rules(): array
    {
        return [
            'fecha_hora' => ['required', 'date'],
            'nota'       => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_hora' => 'fecha y hora',
            'nota'       => 'nota',
        ];
    }
}
