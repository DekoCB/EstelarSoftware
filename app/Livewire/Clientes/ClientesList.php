<?php

namespace App\Livewire\Clientes;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientesList extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $estado         = '';
    public string $tipoDocumento  = '';

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingEstado(): void        { $this->resetPage(); }
    public function updatingTipoDocumento(): void { $this->resetPage(); }

    public function render()
    {
        $clientes = Client::query()
            ->when($this->search,        fn($q) => $q->search($this->search))
            ->when($this->estado,        fn($q) => $q->where('estado', $this->estado))
            ->when($this->tipoDocumento, fn($q) => $q->where('tipo_documento', $this->tipoDocumento))
            ->orderBy('razon_social')
            ->paginate(15);

        return view('livewire.clientes.clientes-list', compact('clientes'));
    }
}
