<?php

namespace App\Http\Requests\Eventos;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('eventos.crear');
    }

    public function rules(): array
    {
        return [
            'nombre'          => ['required', 'string', 'max:200'],
            'descripcion'     => ['nullable', 'string', 'max:2000'],
            'imagen'          => ['nullable', 'image', 'max:4096'],
            'lugar'           => ['nullable', 'string', 'max:255'],
            'direccion'       => ['nullable', 'string', 'max:500'],
            'latitud'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'        => ['nullable', 'numeric', 'between:-180,180'],
            'fecha_inicio'    => ['required', 'date'],
            'fecha_fin'       => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'hora_inicio'     => ['nullable', 'date_format:H:i'],
            'estado'          => ['required', Rule::in(Event::ESTADOS)],
            'responsable_id'  => ['nullable', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'         => 'nombre del evento',
            'fecha_inicio'   => 'fecha de inicio',
            'fecha_fin'      => 'fecha de fin',
            'responsable_id' => 'responsable',
        ];
    }
}
