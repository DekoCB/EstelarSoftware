<?php

namespace App\Http\Controllers\Eventos;

use App\Exports\EventAttendeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreEventAttendeeRequest;
use App\Http\Requests\Eventos\StoreEventTeamRequest;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventTeam;
use App\Support\QrGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Pagina propia del evento (banner a pantalla completa + registro
     * guiado + ticket flotante) — distinta del formulario generico de
     * arriba, pensada para un evento puntual con su propia identidad
     * visual (hoy: "Go Left!!").
     */
    public function showcase(Event $evento)
    {
        if ($evento->estado === 'cancelado') {
            abort(404);
        }

        return view('eventos.asistentes.showcase', compact('evento'));
    }

    public function store(StoreEventAttendeeRequest $request, Event $evento)
    {
        if ($evento->estado === 'cancelado') {
            abort(404);
        }

        $asistente = $this->registrar($evento, $request->validated(), auth()->id());

        // La pagina "showcase" pide JSON (fetch con Accept: application/
        // json) para armar el ticket sin recargar; el formulario generico
        // de eventos.asistentes.inscripcion sigue mandando un POST normal
        // y recibe el redirect de siempre -mismo endpoint, dos clientes-.
        if ($request->wantsJson()) {
            $mensajeWhatsapp = "Aquí está mi entrada para *{$evento->nombre}*:\n"
                . route('eventos.inscripcion.ticket', [$evento, $asistente]);

            return response()->json([
                'codigo'           => $asistente->codigo,
                'qr_svg'           => QrGenerator::svg($asistente->qr_token, 130),
                'nombre_asistente' => $asistente->nombres,
                'ticket_url'       => route('eventos.inscripcion.ticket', [$evento, $asistente]),
                'whatsapp_url'     => 'https://wa.me/51977765710?text=' . urlencode($mensajeWhatsapp),
            ], 201);
        }

        return redirect()->route('eventos.inscripcion.ticket', [$evento, $asistente]);
    }

    /**
     * Inscripcion de un equipo completo (1 a 4 integrantes) desde el
     * panel del evento. Cada integrante queda como un EventAttendee
     * normal -con su propio codigo/QR para el check-in- ligado al
     * EventTeam, que guarda el nombre y el logo opcional.
     */
    public function storeEquipo(StoreEventTeamRequest $request, Event $evento)
    {
        if ($evento->estado === 'cancelado') {
            abort(404);
        }

        $datos = $request->validated();
        $logo = $request->hasFile('logo')
            ? $request->file('logo')->store('eventos/equipos', 'public')
            : null;

        try {
            [$equipo, $integrantes] = DB::transaction(function () use ($evento, $datos, $logo) {
                $equipo = EventTeam::create([
                    'event_id' => $evento->id,
                    'nombre'   => $datos['equipo'],
                    'logo'     => $logo,
                ]);

                $integrantes = collect($datos['miembros'])->map(fn (array $miembro) => $this->registrar(
                    $evento,
                    $miembro + ['equipo' => $equipo->nombre, 'event_team_id' => $equipo->id],
                    auth()->id(),
                ));

                return [$equipo, $integrantes];
            });
        } catch (\Throwable $e) {
            if ($logo) {
                Storage::disk('public')->delete($logo);
            }
            throw $e;
        }

        $principal = $integrantes->first();

        $mensajeWhatsapp = "Inscribí al equipo *{$equipo->nombre}* en *{$evento->nombre}*. "
            . "Te envío mi comprobante de pago para confirmar la inscripción.\n\nEntradas:\n"
            . $integrantes->map(fn (EventAttendee $a) => "• {$a->nickname}: " . route('eventos.inscripcion.ticket', [$evento, $a]))
                ->implode("\n");

        return response()->json([
            'codigo'           => $principal->codigo,
            'qr_svg'           => QrGenerator::svg($principal->qr_token, 130),
            'nombre_asistente' => $principal->nombres,
            'ticket_url'       => route('eventos.inscripcion.ticket', [$evento, $principal]),
            'whatsapp_url'     => 'https://wa.me/51977765710?text=' . urlencode($mensajeWhatsapp),
            'equipo'           => [
                'nombre'   => $equipo->nombre,
                'logo_url'          => $equipo->logoUrl(),
                'estado_pago'       => $equipo->estado_pago,
                'estado_pago_label' => $equipo->estadoPagoLabel(),
                'comprobante_url'   => route('eventos.inscripcion.equipo.comprobante', [$evento, $equipo]),
            ],
            'integrantes'      => $integrantes->map(fn (EventAttendee $a) => [
                'nombres'    => $a->nombres,
                'nickname'   => $a->nickname,
                'rol'        => $a->rol,
                'codigo'     => $a->codigo,
                'ticket_url' => route('eventos.inscripcion.ticket', [$evento, $a]),
            ])->values(),
        ], 201);
    }

    public function ticket(Event $evento, EventAttendee $asistente)
    {
        abort_if($asistente->event_id !== $evento->id, 404);

        $asistente->load('team');

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
