<?php

namespace App\Http\Requests\Proyectos;

use App\Models\Requirement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequerimientoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('requerimientos.crear'); }

    public function rules(): array
    {
        return [
            'project_id'  => ['required', 'exists:projects,id'],
            'phase_id'    => ['nullable', 'exists:project_phases,id'],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', Rule::in(Requirement::TYPES)],
            'priority'    => ['required', Rule::in(Requirement::PRIORITIES)],
            'status'      => ['required', Rule::in(Requirement::STATUSES)],
            'assigned_to'  => ['nullable', 'exists:users,id'],
            'story_points' => ['nullable', 'integer', 'in:1,2,3,5,8,13,21'],
        ];
    }
}
