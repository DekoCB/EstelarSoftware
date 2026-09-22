<?php

namespace App\Http\Requests\Caja;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashMovementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'concepto'    => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'monto'       => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'moneda'      => ['required', 'in:PEN,USD'],
            'metodo_pago' => ['required', 'in:efectivo,transferencia,yape,plin,tarjeta,cheque,otro'],
            'referencia'  => ['nullable', 'string', 'max:100'],
            'fecha'       => ['required', 'date'],
            'categoria'   => ['required', 'string'],
            'client_id'   => ['nullable', 'exists:clients,id'],
            'notas'       => ['nullable', 'string', 'max:2000'],
        ];
    }
}
