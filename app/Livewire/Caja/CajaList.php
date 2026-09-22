<?php

namespace App\Livewire\Caja;

use App\Models\CashMovement;
use Livewire\Component;
use Livewire\WithPagination;

class CajaList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $tipo      = '';
    public string $categoria = '';
    public string $mes       = '';

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingTipo(): void     { $this->resetPage(); }
    public function updatingCategoria(): void { $this->resetPage(); }
    public function updatingMes(): void      { $this->resetPage(); }

    public function render()
    {
        $movimientos = CashMovement::with(['client', 'user'])
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('concepto', 'like', "%{$this->search}%")
                   ->orWhere('referencia', 'like', "%{$this->search}%")
                   ->orWhereHas('client', fn($c) => $c->where('razon_social', 'like', "%{$this->search}%"))
            ))
            ->when($this->tipo, fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->categoria, fn($q) => $q->where('categoria', $this->categoria))
            ->when($this->mes, function ($q) {
                [$year, $month] = explode('-', $this->mes);
                return $q->delMes($year, $month);
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(20);

        // totales del filtro actual (sin paginar)
        $base = CashMovement::query()
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('concepto', 'like', "%{$this->search}%")
                   ->orWhere('referencia', 'like', "%{$this->search}%")
            ))
            ->when($this->tipo, fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->categoria, fn($q) => $q->where('categoria', $this->categoria))
            ->when($this->mes, function ($q) {
                [$year, $month] = explode('-', $this->mes);
                return $q->delMes($year, $month);
            });

        $totalIngresos = (clone $base)->ingresos()->sum('monto');
        $totalEgresos  = (clone $base)->egresos()->sum('monto');
        $saldo         = $totalIngresos - $totalEgresos;

        return view('livewire.caja.caja-list', compact(
            'movimientos', 'totalIngresos', 'totalEgresos', 'saldo'
        ));
    }
}
