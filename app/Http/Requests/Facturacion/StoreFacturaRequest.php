<?php

namespace App\Http\Requests\Facturacion;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacturaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('facturacion.emitir'); }

    public function rules(): array
    {
        return [
            'tipo_comprobante'  => ['required', Rule::in([Invoice::TIPO_FACTURA, Invoice::TIPO_BOLETA])],
            'serie'             => ['required', 'string', 'max:4'],
            'client_id'         => ['required', 'exists:clients,id'],
            'quote_id'          => ['nullable', 'exists:quotes,id'],
            'fecha_emision'     => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'moneda'            => ['required', Rule::in(['PEN', 'USD'])],
            'notas'             => ['nullable', 'string'],

            'items'                          => ['required', 'array', 'min:1'],
            'items.*.descripcion'            => ['required', 'string', 'max:300'],
            'items.*.unidad_sunat'           => ['required', 'string', 'max:3'],
            'items.*.cantidad'               => ['required', 'numeric', 'min:0.01'],
            'items.*.precio_unitario'        => ['required', 'numeric', 'min:0'],
            'items.*.tipo_afectacion'        => ['required', Rule::in(['10', '20', '30'])],
        ];
    }
}
