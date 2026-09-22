<?php

namespace App\Http\Requests\Eventos;

use App\Models\EventAttendee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventAttendeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres'           => ['required', 'string', 'max:200'],
            'empresa'           => ['nullable', 'string', 'max:200'],
            'tipo_documento'    => ['nullable', Rule::in(EventAttendee::TIPOS_DOCUMENTO)],
            'numero_documento'  => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('event_attendees', 'numero_documento')
                    ->where('event_id', $this->route('evento')?->id),
            ],
            'direccion'         => ['nullable', 'string', 'max:500'],
            'email'             => ['nullable', 'email', 'max:150'],
            'telefono'          => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombres'          => 'nombres',
            'numero_documento' => 'número de documento',
        ];
    }
}
