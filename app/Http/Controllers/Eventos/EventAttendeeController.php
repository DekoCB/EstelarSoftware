<?php

namespace App\Http\Controllers\Eventos;

use App\Exports\EventAttendeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreEventAttendeeRequest;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EventAttendeeController extends Controller
{
    public function create(Event $evento)
    {
        if ($evento->estado === 'cancelado') {
            abort(404);
        }

        return view('eventos.asistentes.inscripcion', compact('evento'));
    }

    public function store(StoreEventAttendeeRequest $request, Event $evento)
    {
        if ($evento->estado === 'cancelado') {
            abort(404);
        }

        $asistente = $this->registrar($evento, $request->validated(), auth()->id());

        return redirect()->route('eventos.inscripcion.ticket', [$evento, $asistente]);
    }

    public function ticket(Event $evento, EventAttendee $asistente)
    {
        abort_if($asistente->event_id !== $evento->id, 404);

        return view('eventos.asistentes.ticket', compact('evento', 'asistente'));
    }

    public function storeManual(StoreEventAttendeeRequest $request, Event $evento)
    {
        $this->registrar($evento, $request->validated(), auth()->id());

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Asistente registrado correctamente.');
    }

    public function exportar(Event $evento)
    {
        $nombreArchivo = 'asistentes_' . str($evento->nombre)->slug() . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new EventAttendeesExport($evento), $nombreArchivo);
    }

    public function destroy(Event $evento, EventAttendee $asistente)
    {
        abort_if($asistente->event_id !== $evento->id, 404);

        $asistente->update(['estado' => 'cancelado']);

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Registro cancelado.');
    }

    private function registrar(Event $evento, array $datos, ?int $creadoPor): EventAttendee
    {
        return DB::transaction(function () use ($evento, $datos, $creadoPor) {
            $evento = Event::lockForUpdate()->find($evento->id);

            $siguiente = $evento->asistentes()->count() + 1;

            return EventAttendee::create($datos + [
                'event_id'  => $evento->id,
                'codigo'    => sprintf('EV%d-%06d', $evento->id, $siguiente),
                'qr_token'  => (string) Str::uuid(),
                'estado'    => 'registrado',
                'created_by' => $creadoPor,
            ]);
        });
    }
}
