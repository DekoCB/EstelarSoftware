<?php

namespace App\Http\Requests\Entregas;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntregaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'project_id'       => ['required', 'exists:projects,id'],
            'client_id'        => ['required', 'exists:clients,id'],
            'titulo'           => ['required', 'string', 'max:255'],
            'descripcion'      => ['nullable', 'string', 'max:2000'],
            'fecha_entrega'    => ['required', 'date'],
            'tipo'             => ['required', 'in:parcial,final'],
            'estado'           => ['required', 'in:borrador,firmado,observado'],
            'items_entregados' => ['nullable', 'array'],
            'items_entregados.*' => ['string', 'max:500'],
            'observaciones'    => ['nullable', 'string', 'max:2000'],
            'firma_cliente'    => ['nullable', 'string', 'max:255'],
            'dni_firmante'     => ['nullable', 'string', 'max:20'],
            'cargo_firmante'   => ['nullable', 'string', 'max:100'],
        ];
    }
}
