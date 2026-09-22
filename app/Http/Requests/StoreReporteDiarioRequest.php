<?php

namespace App\Http\Requests;

use App\Models\ReporteDiario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReporteDiarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ReporteDiario::class);
    }

    public function rules(): array
    {
        return [
            'area'                 => ['required', Rule::in(array_keys(ReporteDiario::AREAS))],
            'fecha'                => ['required', 'date'],
            'proyectos_asignados'  => ['nullable', 'string', 'max:255'],
            'sprint_iteracion'     => ['nullable', 'string', 'max:255'],
            'modulo_componente'    => ['nullable', 'string', 'max:255'],
            'horas_trabajadas'     => ['required', 'numeric', 'min:0.5', 'max:24'],

            'tareas'               => ['required', 'array', 'min:1'],
            'tareas.*.descripcion' => ['required', 'string', 'max:500'],
            'tareas.*.tipo'        => ['required', Rule::in(ReporteDiario::TIPOS_TAREA)],
            'tareas.*.estado'      => ['required', Rule::in(ReporteDiario::ESTADOS_TAREA)],
            'tareas.*.tiempo_horas'=> ['required', 'numeric', 'min:0.1', 'max:24'],

            'logros_destacados'    => ['nullable', 'string', 'max:2000'],

            'impedimentos'              => ['nullable', 'array'],
            'impedimentos.*.descripcion'=> ['required_with:impedimentos', 'string', 'max:500'],
            'impedimentos.*.impacto'    => ['required_with:impedimentos', Rule::in(ReporteDiario::IMPACTOS)],
            'impedimentos.*.requiere_apoyo' => ['required_with:impedimentos', 'boolean'],

            'plan_siguiente_dia'   => ['nullable', 'string', 'max:2000'],
            'archivo_adjunto'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'tareas.required'              => 'Debes registrar al menos una tarea.',
            'tareas.min'                   => 'Debes registrar al menos una tarea.',
            'tareas.*.descripcion.required'=> 'La descripción de cada tarea es obligatoria.',
            'tareas.*.tipo.required'       => 'El tipo de cada tarea es obligatorio.',
            'tareas.*.estado.required'     => 'El estado de cada tarea es obligatorio.',
            'tareas.*.tiempo_horas.required'=> 'El tiempo de cada tarea es obligatorio.',
            'archivo_adjunto.max'          => 'El archivo no puede superar los 5 MB.',
        ];
    }
}
