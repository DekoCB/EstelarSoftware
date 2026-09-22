<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\Quote;
use App\Models\QuotePayment;
use Illuminate\Support\Facades\DB;

class QuotePaymentController extends Controller
{
    // ── Generar plan por defecto (40/30/30) ──────────────────────────
    public function generarPlan(Quote $cotizacion)
    {
        $cotizacion->generarPlanDefault();
        return back()->with('success', 'Plan de cobros generado: 40% anticipo + 30% + 30%.');
    }

    // ── Crear cuota personalizada ────────────────────────────────────
    public function store(Quote $cotizacion)
    {
        $data = request()->validate([
            'nombre'            => ['required', 'string', 'max:150'],
            'porcentaje'        => ['required', 'numeric', 'min:0.01', 'max:100'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'notas'             => ['nullable', 'string'],
        ]);

        $data['monto'] = round((float) $cotizacion->total * $data['porcentaje'] / 100, 2);
        $data['orden'] = $cotizacion->payments()->max('orden') + 1;
        $data['estado'] = 'pendiente';

        $cotizacion->payments()->create($data);

        return back()->with('success', 'Cuota agregada.');
    }

    // ── Editar cuota ─────────────────────────────────────────────────
    public function update(Quote $cotizacion, QuotePayment $pago)
    {
        $data = request()->validate([
            'nombre'            => ['required', 'string', 'max:150'],
            'porcentaje'        => ['required', 'numeric', 'min:0.01', 'max:100'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'notas'             => ['nullable', 'string'],
        ]);

        $data['monto'] = round((float) $cotizacion->total * $data['porcentaje'] / 100, 2);
        $pago->update($data);

        return back()->with('success', 'Cuota actualizada.');
    }

    // ── Marcar como pagado ────────────────────────────────────────────
    public function marcarPagado(Quote $cotizacion, QuotePayment $pago)
    {
        $data = request()->validate([
            'metodo_pago' => ['nullable', 'string', 'max:80'],
            'fecha_pago'  => ['nullable', 'date'],
            'notas'       => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($cotizacion, $pago, $data) {
            $fechaPago = $data['fecha_pago'] ? $data['fecha_pago'] : now()->toDateString();

            // Crear movimiento de caja automáticamente
            $movimiento = CashMovement::create([
                'tipo'        => 'ingreso',
                'concepto'    => "Cobro {$pago->nombre} — {$cotizacion->numero}",
                'monto'       => $pago->monto,
                'moneda'      => $cotizacion->moneda ?? 'PEN',
                'metodo_pago' => $this->normalizarMetodo($data['metodo_pago'] ?? 'efectivo'),
                'fecha'       => $fechaPago,
                'categoria'   => 'cobro_cliente',
                'client_id'   => $cotizacion->client_id,
                'quote_id'    => $cotizacion->id,
                'user_id'     => auth()->id(),
                'notas'       => $data['notas'] ?? null,
            ]);

            $pago->update([
                'estado'           => 'pagada',
                'fecha_pago'       => $fechaPago,
                'metodo_pago'      => $data['metodo_pago'],
                'notas'            => $data['notas'] ?? $pago->notas,
                'cash_movement_id' => $movimiento->id,
            ]);

            // Si todas las cuotas están pagadas → cotización pagada
            if ($cotizacion->payments()->where('estado', '!=', 'pagada')->doesntExist()) {
                $cotizacion->update(['status' => 'facturado']);
            }
        });

        return back()->with('success', 'Cuota marcada como pagada y registrada en caja.');
    }

    // ── Desmarcar pago ────────────────────────────────────────────────
    public function desmarcarPagado(Quote $cotizacion, QuotePayment $pago)
    {
        DB::transaction(function () use ($pago) {
            // Eliminar el movimiento de caja vinculado
            if ($pago->cash_movement_id) {
                CashMovement::find($pago->cash_movement_id)?->delete();
            }

            $pago->update([
                'estado'           => 'pendiente',
                'fecha_pago'       => null,
                'cash_movement_id' => null,
            ]);
        });

        return back()->with('success', 'Pago revertido a pendiente y eliminado de caja.');
    }

    private function normalizarMetodo(?string $metodo): string
    {
        $mapa = [
            'yape' => 'yape', 'plin' => 'plin',
            'transferencia' => 'transferencia', 'transfer' => 'transferencia',
            'tarjeta' => 'tarjeta', 'efectivo' => 'efectivo',
            'cheque' => 'cheque',
        ];
        return $mapa[strtolower($metodo ?? '')] ?? 'efectivo';
    }

    // ── Eliminar cuota ────────────────────────────────────────────────
    public function destroy(Quote $cotizacion, QuotePayment $pago)
    {
        $pago->delete();
        return back()->with('success', 'Cuota eliminada.');
    }
}
