<?php

namespace App\Http\Controllers\Eventos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreComprobantePagoRequest;
use App\Http\Requests\Eventos\UpdatePagoEquipoRequest;
use App\Models\Event;
use App\Models\EventTeam;
use Illuminate\Support\Facades\Storage;

/**
 * Pago de la inscripcion por equipo: el equipo sube su comprobante
 * (publico, por token) y el staff lo revisa desde el detalle del evento.
 */
class EventTeamPagoController extends Controller
{
    public function storeComprobante(StoreComprobantePagoRequest $request, Event $evento, EventTeam $equipo)
    {
        abort_unless($equipo->event_id === $evento->id, 404);

        if (!$equipo->puedeSubirComprobante()) {
            return response()->json([
                'message' => 'El pago de este equipo ya fue confirmado.',
            ], 409);
        }

        $anterior = $equipo->comprobante_pago;
        $ruta = $request->file('comprobante')->store("eventos/comprobantes/{$evento->id}", 'local');

        $equipo->update([
            'comprobante_pago'      => $ruta,
            'comprobante_subido_at' => now(),
            'estado_pago'           => 'en_revision',
            'pago_revisado_at'      => null,
            'pago_revisado_by'      => null,
        ]);

        if ($anterior && $anterior !== $ruta) {
            Storage::disk('local')->delete($anterior);
        }

        return response()->json([
            'estado_pago'       => $equipo->estado_pago,
            'estado_pago_label' => $equipo->estadoPagoLabel(),
        ]);
    }

    public function verComprobante(Event $evento, EventTeam $equipo)
    {
        abort_unless($equipo->event_id === $evento->id, 404);
        abort_unless($equipo->comprobante_pago && Storage::disk('local')->exists($equipo->comprobante_pago), 404);

        return Storage::disk('local')->response($equipo->comprobante_pago);
    }

    public function update(UpdatePagoEquipoRequest $request, Event $evento, EventTeam $equipo)
    {
        abort_unless($equipo->event_id === $evento->id, 404);

        $equipo->update([
            'estado_pago'      => $request->validated('estado_pago'),
            'pago_revisado_at' => now(),
            'pago_revisado_by' => auth()->id(),
        ]);

        return back()->with('success', $equipo->pagado()
            ? "Pago del equipo {$equipo->nombre} confirmado."
            : "Pago del equipo {$equipo->nombre} rechazado.");
    }
}
