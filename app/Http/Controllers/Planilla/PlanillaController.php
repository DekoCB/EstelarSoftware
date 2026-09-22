<?php

namespace App\Http\Controllers\Planilla;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planilla\StorePlanillaRequest;
use App\Models\CashMovement;
use App\Models\PayrollPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanillaController extends Controller
{
    public function index(Request $request)
    {
        $periodoActual = now()->format('Y-m');
        $periodo = $request->get('periodo', $periodoActual);
        $userId  = $request->get('user_id');

        $pagos = PayrollPayment::with(['user', 'createdBy'])
            ->when($periodo, fn($q) => $q->where('periodo', $periodo))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderBy('estado')
            ->orderByDesc('created_at')
            ->get();

        // Resumen del periodo
        $totalPeriodo   = $pagos->sum('monto');
        $pagado         = $pagos->where('estado', 'pagado')->sum('monto');
        $pendiente      = $pagos->where('estado', 'pendiente')->sum('monto');

        // Personal con pagos en el sistema (para el filtro)
        $personal = User::whereHas('payrollPayments')->orderBy('name')->get(['id', 'name', 'cargo']);

        return view('planilla.index', compact(
            'pagos', 'periodo', 'periodoActual', 'userId',
            'totalPeriodo', 'pagado', 'pendiente', 'personal'
        ));
    }

    public function create()
    {
        $personal = User::where('activo', true)->orderBy('name')->get(['id', 'name', 'cargo']);
        $periodo  = now()->format('Y-m');
        return view('planilla.create', compact('personal', 'periodo'));
    }

    public function store(StorePlanillaRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $pago = PayrollPayment::create($data);

        return redirect()
            ->route('planilla.show', $pago)
            ->with('success', 'Pago de planilla registrado.');
    }

    public function show(PayrollPayment $planilla)
    {
        $planilla->load(['user', 'createdBy', 'cashMovement']);
        return view('planilla.show', compact('planilla'));
    }

    public function pagar(Request $request, PayrollPayment $planilla)
    {
        if ($planilla->estado === 'pagado') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        $data = $request->validate([
            'metodo_pago' => ['required', 'in:efectivo,transferencia,yape,plin,tarjeta,cheque,otro'],
            'fecha_pago'  => ['required', 'date'],
            'notas'       => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($planilla, $data) {
            $movimiento = CashMovement::create([
                'tipo'        => 'egreso',
                'concepto'    => "{$planilla->tipoLabel()} {$planilla->periodoFormateado()} — {$planilla->user->name}",
                'monto'       => $planilla->monto,
                'moneda'      => $planilla->moneda,
                'metodo_pago' => $data['metodo_pago'],
                'fecha'       => $data['fecha_pago'],
                'categoria'   => 'planilla',
                'user_id'     => auth()->id(),
                'notas'       => $data['notas'] ?? null,
            ]);

            $planilla->update([
                'estado'           => 'pagado',
                'metodo_pago'      => $data['metodo_pago'],
                'fecha_pago'       => $data['fecha_pago'],
                'notas'            => $data['notas'] ?? $planilla->notas,
                'cash_movement_id' => $movimiento->id,
            ]);
        });

        return back()->with('success', 'Pago procesado y registrado en caja.');
    }

    public function revertir(PayrollPayment $planilla)
    {
        if ($planilla->estado !== 'pagado') {
            return back()->with('error', 'Este pago no está pagado.');
        }

        DB::transaction(function () use ($planilla) {
            if ($planilla->cash_movement_id) {
                CashMovement::find($planilla->cash_movement_id)?->delete();
            }
            $planilla->update([
                'estado'           => 'pendiente',
                'fecha_pago'       => null,
                'metodo_pago'      => null,
                'cash_movement_id' => null,
            ]);
        });

        return back()->with('success', 'Pago revertido a pendiente.');
    }

    public function destroy(PayrollPayment $planilla)
    {
        if ($planilla->estado === 'pagado') {
            return back()->with('error', 'No puedes eliminar un pago ya procesado. Reviértelo primero.');
        }
        $planilla->delete();
        return redirect()->route('planilla.index')->with('success', 'Registro eliminado.');
    }
}
