<?php

namespace App\Http\Requests\Proyectos;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProyectoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('proyectos.crear'); }

    public function rules(): array
    {
        return [
            'client_id'           => ['required', 'exists:clients,id'],
            'name'                => ['required', 'string', 'max:200'],
            'description'         => ['nullable', 'string'],
            'status'              => ['required', Rule::in(Project::ESTADOS)],
            'start_date'          => ['nullable', 'date'],
            'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
            'responsible_user_id' => ['nullable', 'exists:users,id'],
            'checklist'           => ['nullable', 'array'],
            'checklist.*'         => ['string', 'max:150'],
            'notas_reunion'       => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'client_id'   => 'cliente',
            'name'        => 'nombre del proyecto',
            'status'      => 'estado',
            'start_date'  => 'fecha de inicio',
            'end_date'    => 'fecha de fin',
        ];
    }
}
