<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadsImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('leads.crear');
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
