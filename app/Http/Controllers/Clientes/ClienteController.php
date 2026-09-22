<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clientes\StoreClienteRequest;
use App\Http\Requests\Clientes\UpdateClienteRequest;
use App\Models\Client;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.index');
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        Client::create($request->validated() + ['created_by' => auth()->id()]);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Client $cliente)
    {
        $cliente->load([
            'projects.phases',
            'projects.responsible',
            'quotes.payments',
            'quotes.invoices',
            'invoices',
            'cashMovements',
            'deliveries',
        ]);

        // Todas las cuotas de todas las cotizaciones del cliente
        $todasLasCuotas = $cliente->quotes->flatMap(fn($q) => $q->payments)->sortBy('orden');

        $kpis = [
            'total_cotizado'    => $cliente->totalCotizado(),
            'total_facturado'   => $cliente->totalFacturado(),
            'total_cobrado'     => $cliente->totalCobrado(),
            'saldo_pendiente'   => $cliente->saldoPendiente(),
            'proyectos_activos' => $cliente->projects->whereIn('status', ['planificado', 'en_curso'])->count(),
            'cuotas_vencidas'   => $todasLasCuotas->where('estado', 'vencida')->count(),
        ];

        return view('clientes.show', compact('cliente', 'kpis', 'todasLasCuotas'));
    }

    public function edit(Client $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Client $cliente)
    {
        $cliente->update($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $cliente)
    {
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
