<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Http\Requests\Caja\StoreCashMovementRequest;
use App\Http\Requests\Caja\UpdateCashMovementRequest;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        return view('caja.index');
    }

    public function create(Request $request)
    {
        $tipo     = $request->get('tipo', 'ingreso');
        $clientes = Client::orderBy('razon_social')->get(['id', 'razon_social', 'nombre_comercial']);
        $facturas = Invoice::where('estado_sunat', 'aceptado')
            ->with('client')
            ->orderByDesc('fecha_emision')
            ->get(['id', 'serie', 'correlativo', 'tipo_comprobante', 'total', 'client_id']);
        $cotizaciones = Quote::whereIn('status', ['enviado', 'aceptado'])
            ->with('client')
            ->orderByDesc('created_at')
            ->get(['id', 'numero', 'total', 'client_id']);

        return view('caja.create', compact('tipo', 'clientes', 'facturas', 'cotizaciones'));
    }

    public function store(StoreCashMovementRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $movimiento = CashMovement::create($data);

        return redirect()
            ->route('caja.show', $movimiento)
            ->with('success', 'Movimiento registrado correctamente.');
    }

    public function show(CashMovement $movimiento)
    {
        $movimiento->load(['user', 'client', 'invoice', 'quote']);
        return view('caja.show', compact('movimiento'));
    }

    public function edit(CashMovement $movimiento)
    {
        $clientes = Client::orderBy('razon_social')->get(['id', 'razon_social', 'nombre_comercial']);
        return view('caja.edit', compact('movimiento', 'clientes'));
    }

    public function update(UpdateCashMovementRequest $request, CashMovement $movimiento)
    {
        $movimiento->update($request->validated());

        return redirect()
            ->route('caja.show', $movimiento)
            ->with('success', 'Movimiento actualizado.');
    }

    public function destroy(CashMovement $movimiento)
    {
        $movimiento->delete();
        return redirect()->route('caja.index')->with('success', 'Movimiento eliminado.');
    }
}
