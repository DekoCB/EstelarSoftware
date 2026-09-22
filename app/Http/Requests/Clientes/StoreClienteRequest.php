<?php

namespace App\Http\Requests\Clientes;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('clientes.crear');
    }

    public function rules(): array
    {
        return [
            'tipo_documento'   => ['required', Rule::in(Client::TIPOS_DOCUMENTO)],
            'numero_documento' => ['required', 'string', 'max:15', 'unique:clients,numero_documento'],
            'razon_social'     => ['required', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'email'            => ['nullable', 'email', 'max:150'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'direccion'        => ['nullable', 'string', 'max:500'],
            'ubigeo'           => ['nullable', 'string', 'size:6'],
            'estado'           => ['required', Rule::in(Client::ESTADOS)],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_documento'   => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'razon_social'     => 'razón social',
            'nombre_comercial' => 'nombre comercial',
            'estado'           => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'numero_documento.unique' => 'Ya existe un cliente registrado con ese número de documento.',
        ];
    }
}
