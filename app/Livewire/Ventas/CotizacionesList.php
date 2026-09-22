<?php

namespace App\Livewire\Ventas;

use App\Models\Quote;
use Livewire\Component;
use Livewire\WithPagination;

class CotizacionesList extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $status  = '';
    public string $moneda  = '';
    public string $orderBy = 'created_at';
    public string $orderDir = 'desc';

    protected $queryString = [
        'search'  => ['except' => ''],
        'status'  => ['except' => ''],
    ];

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingStatus(): void  { $this->resetPage(); }

    public function render()
    {
        $quotes = Quote::with(['client', 'createdBy'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->moneda, fn($q) => $q->where('moneda', $this->moneda))
            ->orderBy($this->orderBy, $this->orderDir)
            ->paginate(15);

        return view('livewire.ventas.cotizaciones-list', compact('quotes'));
    }
}
