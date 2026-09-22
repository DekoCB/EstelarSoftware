<?php

namespace App\Http\Controllers\Eventos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreEventLeadRequest;
use App\Http\Requests\Eventos\UpdateEventLeadRequest;
use App\Models\Client;
use App\Models\Event;
use App\Models\EventLead;
use Illuminate\Support\Facades\DB;

class EventLeadController extends Controller
{
    public function store(StoreEventLeadRequest $request, Event $evento)
    {
        $evento->leads()->create($request->validated() + ['created_by' => auth()->id()]);

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Lead registrado correctamente.');
    }

    public function edit(Event $evento, EventLead $lead)
    {
        abort_unless($lead->event_id === $evento->id, 404);
        $this->autorizarPropietario($lead);

        return view('eventos.leads.edit', compact('evento', 'lead'));
    }

    public function update(UpdateEventLeadRequest $request, Event $evento, EventLead $lead)
    {
        abort_unless($lead->event_id === $evento->id, 404);
        $this->autorizarPropietario($lead);

        $lead->update($request->validated());

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Lead actualizado correctamente.');
    }

    public function destroy(Event $evento, EventLead $lead)
    {
        abort_unless($lead->event_id === $evento->id, 404);
        $this->autorizarPropietario($lead);

        $lead->delete();

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Lead eliminado correctamente.');
    }

    public function convertir(Event $evento, EventLead $lead)
    {
        abort_unless($lead->event_id === $evento->id, 404);
        $this->autorizarPropietario($lead);

        if ($lead->convertido()) {
            return back()->with('error', 'Este lead ya fue convertido a cliente.');
        }

        if (!$lead->numero_documento) {
            return back()->with('error', 'Para convertir el lead a cliente necesita un número de documento (DNI/RUC).');
        }

        DB::transaction(function () use ($lead) {
            $client = Client::where('numero_documento', $lead->numero_documento)->first();

            if (!$client) {
                $client = Client::create([
                    'tipo_documento'   => $lead->tipo_documento,
                    'numero_documento' => $lead->numero_documento,
                    'razon_social'     => $lead->empresa ?: $lead->nombres,
                    'nombre_comercial' => $lead->empresa ? $lead->nombres : null,
                    'email'            => $lead->email,
                    'telefono'         => $lead->telefono,
                    'direccion'        => $lead->direccion,
                    'estado'           => 'prospecto',
                    'created_by'       => auth()->id(),
                ]);
            }

            $lead->update([
                'client_id' => $client->id,
                'estado'    => 'convertido',
            ]);
        });

        return back()->with('success', 'Lead convertido a cliente correctamente.');
    }

    private function autorizarPropietario(EventLead $lead): void
    {
        $puedeVerTodos = auth()->user()->can('eventos.ver_todos');

        abort_if(!$puedeVerTodos && $lead->created_by !== auth()->id(), 403, 'No puedes gestionar leads registrados por otro usuario.');
    }
}
