<?php

namespace App\Http\Requests\Ventas;

use App\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCotizacionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('cotizaciones.crear'); }

    public function rules(): array
    {
        return [
            'client_id'          => ['required', 'exists:clients,id'],
            'project_id'         => ['nullable', 'exists:projects,id'],
            'fecha_emision'      => ['required', 'date'],
            'fecha_vencimiento'  => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'moneda'             => ['required', Rule::in(['PEN', 'USD'])],
            'tipo_cambio'        => ['nullable', 'numeric', 'min:0'],
            'incluye_igv'        => ['boolean'],
            'notas'              => ['nullable', 'string'],
            'terminos'           => ['nullable', 'string'],

            'items'                        => ['required', 'array', 'min:1'],
            'items.*.descripcion'          => ['required', 'string', 'max:300'],
            'items.*.cantidad'             => ['required', 'numeric', 'min:0.01'],
            'items.*.unidad'               => ['required', 'string', 'max:30'],
            'items.*.precio_unitario'      => ['required', 'numeric', 'min:0'],
            'items.*.descuento'            => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
