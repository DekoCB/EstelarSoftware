<?php

namespace App\Http\Controllers\Eventos;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventCheckinController extends Controller
{
    public function show(Event $evento)
    {
        return view('eventos.checkin', compact('evento'));
    }

    public function scan(Event $evento, Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $asistente = $evento->asistentes()->where('qr_token', $request->input('qr_token'))->first();

        if (!$asistente) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Este QR no corresponde a ningún registro de este evento.',
            ], 404);
        }

        if ($asistente->estado === 'cancelado') {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Este registro fue cancelado.',
                'asistente' => $asistente->only(['nombres', 'empresa', 'codigo']),
            ], 409);
        }

        // Inscripcion por equipo: no esta concretada hasta confirmar el pago.
        if ($asistente->team && !$asistente->team->pagado()) {
            return response()->json([
                'ok'        => false,
                'mensaje'   => "El equipo {$asistente->team->nombre} tiene el pago sin confirmar ({$asistente->team->estadoPagoLabel()}).",
                'asistente' => $asistente->only(['nombres', 'empresa', 'codigo']),
            ], 409);
        }

        if ($asistente->estado === 'asistio') {
            return response()->json([
                'ok'         => false,
                'ya_asistio' => true,
                'mensaje'    => 'Este asistente ya había registrado su ingreso.',
                'asistente'  => $asistente->only(['nombres', 'empresa', 'codigo']),
                'checked_in_at' => optional($asistente->checked_in_at)->format('d/m/Y H:i'),
            ], 200);
        }

        $asistente->update([
            'estado'        => 'asistio',
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
        ]);

        return response()->json([
            'ok'        => true,
            'mensaje'   => 'Ingreso registrado correctamente.',
            'asistente' => $asistente->only(['nombres', 'empresa', 'codigo']),
        ]);
    }
}
