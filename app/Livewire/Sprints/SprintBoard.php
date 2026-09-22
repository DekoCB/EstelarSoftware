<?php

namespace App\Livewire\Sprints;

use App\Models\Requirement;
use App\Models\Sprint;
use App\Models\User;
use Livewire\Component;

class SprintBoard extends Component
{
    public int $sprintId;
    public int $projectId;

    public function mount(Sprint $sprint): void
    {
        $this->sprintId  = $sprint->id;
        $this->projectId = $sprint->project_id;
    }

    public function moverTarea(int $reqId, string $nuevoStatus): void
    {
        $req = Requirement::findOrFail($reqId);

        if ($req->sprint_id !== $this->sprintId) return;

        $user = auth()->user();

        // Practicante/desarrollador solo mueven sus tareas
        if (!$user->can('sprints.gestionar') && $req->assigned_to !== $user->id) return;

        $req->update(['status' => $nuevoStatus]);
    }

    public function asignarAlSprint(int $reqId): void
    {
        if (!auth()->user()->can('sprints.gestionar')) return;

        Requirement::where('id', $reqId)
            ->where('project_id', $this->projectId)
            ->whereNull('sprint_id')
            ->update(['sprint_id' => $this->sprintId]);
    }

    public function quitarDelSprint(int $reqId): void
    {
        if (!auth()->user()->can('sprints.gestionar')) return;

        Requirement::where('id', $reqId)
            ->where('sprint_id', $this->sprintId)
            ->update(['sprint_id' => null, 'status' => 'pendiente']);
    }

    public function render()
    {
        $user  = auth()->user();
        $soloMias = !$user->can('sprints.gestionar') && !$user->can('proyectos.ver');

        $tareas = Requirement::where('sprint_id', $this->sprintId)
            ->when($soloMias, fn($q) => $q->where('assigned_to', $user->id))
            ->with('assignedTo')
            ->orderByRaw("FIELD(priority,'critica','alta','media','baja')")
            ->get()
            ->groupBy('status');

        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('livewire.sprints.sprint-board', compact('tareas', 'usuarios'));
    }
}
