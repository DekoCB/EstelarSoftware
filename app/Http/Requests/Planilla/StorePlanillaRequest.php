<?php

namespace App\Http\Requests\Planilla;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanillaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id'  => ['required', 'exists:users,id'],
            'periodo'  => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'tipo'     => ['required', 'in:sueldo,honorario,comision,bono,adelanto,otro'],
            'concepto' => ['required', 'string', 'max:255'],
            'monto'    => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'moneda'   => ['required', 'in:PEN,USD'],
            'notas'    => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'  => 'personal',
            'periodo'  => 'período',
            'tipo'     => 'tipo de pago',
            'concepto' => 'concepto',
            'monto'    => 'monto',
        ];
    }
}
