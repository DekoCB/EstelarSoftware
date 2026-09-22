<?php

namespace App\Http\Requests\Eventos;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventAttendeesImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('eventos.crear');
    }

    public function rules(): array
    {
        return [
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'archivo' => 'archivo',
        ];
    }
}
