<?php

namespace App\Livewire\Proyectos;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProyectosList extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $status  = '';
    public string $cliente = '';

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingStatus(): void  { $this->resetPage(); }
    public function updatingCliente(): void { $this->resetPage(); }

    public function render()
    {
        $user = auth()->user();

        // Practicante: solo ve sus proyectos asignados
        $soloAsignados = !$user->can('proyectos.ver') && $user->can('proyectos.ver_asignados');

        $proyectos = Project::with(['client', 'responsible'])
            ->when($soloAsignados,    fn($q) => $q->asignadoA($user->id))
            ->when($this->search,     fn($q) => $q->search($this->search))
            ->when($this->status,     fn($q) => $q->where('status', $this->status))
            ->when($this->cliente,    fn($q) => $q->where('client_id', $this->cliente))
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('livewire.proyectos.proyectos-list', compact('proyectos'));
    }
}
