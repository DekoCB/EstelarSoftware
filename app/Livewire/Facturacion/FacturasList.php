<?php

namespace App\Livewire\Facturacion;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class FacturasList extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $tipo          = '';
    public string $estadoSunat   = '';
    public string $moneda        = '';
    public bool   $conEliminados = false;

    protected $queryString = [
        'search'        => ['except' => ''],
        'tipo'          => ['except' => ''],
        'estadoSunat'   => ['except' => ''],
        'conEliminados' => ['except' => false],
    ];

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingTipo(): void          { $this->resetPage(); }
    public function updatingEstadoSunat(): void   { $this->resetPage(); }
    public function updatingConEliminados(): void { $this->resetPage(); }

    public function render()
    {
        $facturas = Invoice::with(['client'])
            ->when($this->conEliminados, fn($q) => $q->onlyTrashed())
            ->when($this->search,        fn($q) => $q->search($this->search))
            ->when($this->tipo,          fn($q) => $q->where('tipo_comprobante', $this->tipo))
            ->when($this->estadoSunat,   fn($q) => $q->where('estado_sunat', $this->estadoSunat))
            ->when($this->moneda,        fn($q) => $q->where('moneda', $this->moneda))
            ->orderByDesc('fecha_emision')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.facturacion.facturas-list', compact('facturas'));
    }
}
