<?php

namespace App\Livewire\Leads;

use App\Models\Lead;
use App\Support\LeadResponseOptions;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LeadsList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $respuesta = '';

    #[Url]
    public bool $cotizacion = false;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRespuesta(): void { $this->resetPage(); }

    public function quitarFiltroCotizacion(): void
    {
        $this->cotizacion = false;
        $this->resetPage();
    }

    public function render()
    {
        $leads = Lead::query()
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->respuesta !== '', function ($q) {
                $this->respuesta === 'sin_respuesta'
                    ? $q->whereNull('respuesta_rapida')
                    : $q->where('respuesta_rapida', $this->respuesta);
            })
            ->when($this->cotizacion, fn ($q) => $q->where('quiere_cotizacion', true))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.leads.leads-list', [
            'leads'             => $leads,
            'opcionesRespuesta' => LeadResponseOptions::opciones(),
        ]);
    }
}
