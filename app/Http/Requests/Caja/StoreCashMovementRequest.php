<?php

namespace App\Http\Requests\Caja;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashMovementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tipo'              => ['required', 'in:ingreso,egreso'],
            'concepto'          => ['required', 'string', 'max:255'],
            'descripcion'       => ['nullable', 'string', 'max:1000'],
            'monto'             => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'moneda'            => ['required', 'in:PEN,USD'],
            'metodo_pago'       => ['required', 'in:efectivo,transferencia,yape,plin,tarjeta,cheque,otro'],
            'referencia'        => ['nullable', 'string', 'max:100'],
            'fecha'             => ['required', 'date'],
            'categoria'         => ['required', 'string'],
            'invoice_id'        => ['nullable', 'exists:invoices,id'],
            'quote_id'          => ['nullable', 'exists:quotes,id'],
            'client_id'         => ['nullable', 'exists:clients,id'],
            'comprobante_path'  => ['nullable', 'string', 'max:500'],
            'notas'             => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo'        => 'tipo de movimiento',
            'concepto'    => 'concepto',
            'monto'       => 'monto',
            'fecha'       => 'fecha',
            'categoria'   => 'categoría',
            'metodo_pago' => 'método de pago',
        ];
    }
}
