<?php

namespace App\Http\Requests\Eventos;

use App\Models\EventTeam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Inscripcion publica de un equipo completo (1 a 4 integrantes) desde
 * el panel del evento en el landing. Llega como multipart (FormData)
 * por el logo opcional.
 */
class StoreEventTeamRequest extends FormRequest
{
    const ROLES = ['Capitán', 'Jugador', 'Suplente'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipo'                => [
                'required',
                'string',
                'max:150',
                Rule::unique('event_teams', 'nombre')
                    ->where('event_id', $this->route('evento')?->id),
            ],
            'logo'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'miembros'              => ['required', 'array', 'min:1', 'max:' . EventTeam::MAX_INTEGRANTES],
            'miembros.*.nombres'    => ['required', 'string', 'max:200'],
            'miembros.*.nickname'   => ['required', 'string', 'max:100'],
            'miembros.*.edad'       => ['required', 'integer', 'min:1', 'max:99'],
            'miembros.*.telefono'   => ['required', 'string', 'max:20'],
            'miembros.*.steam_id'   => ['required', 'string', 'max:50', 'distinct:ignore_case'],
            'miembros.*.rol'        => ['required', Rule::in(self::ROLES)],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $capitanes = collect($this->input('miembros', []))
                    ->where('rol', 'Capitán')
                    ->count();

                if ($capitanes > 1) {
                    $validator->errors()->add('miembros', 'Solo puede haber un capitán por equipo.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'equipo'              => 'nombre del equipo',
            'logo'                => 'logo del equipo',
            'miembros'            => 'integrantes',
            'miembros.*.nombres'  => 'nombre completo',
            'miembros.*.nickname' => 'nickname',
            'miembros.*.edad'     => 'edad',
            'miembros.*.telefono' => 'WhatsApp',
            'miembros.*.steam_id' => 'Steam ID',
            'miembros.*.rol'      => 'rol',
        ];
    }

    public function messages(): array
    {
        return [
            'equipo.unique'             => 'Ya hay un equipo inscrito con ese nombre.',
            'miembros.max'              => 'El equipo puede tener como máximo ' . EventTeam::MAX_INTEGRANTES . ' integrantes.',
            'miembros.*.steam_id.distinct' => 'Este Steam ID está repetido en el equipo.',
        ];
    }
}
