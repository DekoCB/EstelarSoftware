<?php

namespace App\Http\Controllers\Proyectos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Proyectos\StoreProyectoRequest;
use App\Http\Requests\Proyectos\UpdateProyectoRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\User;

class ProyectoController extends Controller
{
    public function index()
    {
        [$clientes, $usuarios] = $this->formData();
        return view('proyectos.index', compact('clientes', 'usuarios'));
    }

    public function create()
    {
        [$clientes, $usuarios] = $this->formData();
        return view('proyectos.create', compact('clientes', 'usuarios'));
    }

    public function store(StoreProyectoRequest $request)
    {
        $data = $request->safe()->except('checklist');

        $data['checklist'] = collect($request->input('checklist', []))
            ->map(fn($nombre) => ['nombre' => $nombre, 'completado' => false])
            ->values()
            ->toArray();

        $proyecto = Project::create($data + ['created_by' => auth()->id()]);

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Project $proyecto)
    {
        $proyecto->load(['client', 'responsible', 'phases.requirements', 'requirements']);
        return view('proyectos.show', compact('proyecto'));
    }

    public function edit(Project $proyecto)
    {
        [$clientes, $usuarios] = $this->formData();
        $proyecto->load('phases');
        return view('proyectos.edit', compact('proyecto', 'clientes', 'usuarios'));
    }

    public function toggleChecklist(Project $proyecto, int $index)
    {
        $checklist = $proyecto->checklist ?? [];

        if (!isset($checklist[$index])) {
            return back();
        }

        $checklist[$index]['completado'] = !$checklist[$index]['completado'];
        $proyecto->update(['checklist' => $checklist]);

        return back()->with('success', 'Entregable actualizado.');
    }

    public function updateNotas(Project $proyecto)
    {
        request()->validate(['notas_reunion' => ['nullable', 'string']]);
        $proyecto->update(['notas_reunion' => request()->input('notas_reunion')]);
        return back()->with('success', 'Notas actualizadas.');
    }

    private function formData(): array
    {
        $clientes = Client::activos()
            ->orderBy('razon_social')
            ->get(['id', 'razon_social', 'nombre_comercial', 'numero_documento']);

        $activos = ['planificado', 'en_curso', 'pausado', 'en_revision'];
        $usuarios = User::withCount([
                'responsibleProjects as active_projects_count' => fn($q) => $q->whereIn('status', $activos),
            ])
            ->orderBy('name')
            ->get(['id', 'name']);

        return [$clientes, $usuarios];
    }

    public function update(UpdateProyectoRequest $request, Project $proyecto)
    {
        $proyecto->update($request->validated());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Project $proyecto)
    {
        $proyecto->delete();
        return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado.');
    }

    // ── Actualizar progreso de una fase ──────────────────────────────
    public function updatePhase(Project $proyecto, ProjectPhase $fase)
    {
        $fase->update([
            'progress' => request()->validate(['progress' => 'required|integer|min:0|max:100'])['progress'],
            'status'   => request()->input('status', $fase->status),
        ]);
        $proyecto->recalcularProgreso();

        return back()->with('success', 'Fase actualizada.');
    }
}
