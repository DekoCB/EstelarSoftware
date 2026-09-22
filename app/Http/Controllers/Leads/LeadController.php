<?php

namespace App\Http\Controllers\Leads;

use App\Exports\LeadsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Http\Requests\Leads\StorePublicLeadRequest;
use App\Http\Requests\Leads\UpdateLeadCotizacionRequest;
use App\Http\Requests\Leads\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\LeadMeeting;
use App\Support\LeadResponseOptions;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function index()
    {
        $totalLeads       = Lead::count();
        $leadsQuierenReunion = Lead::where('respuesta_rapida', 'reunion_pendiente')->count();

        return view('leads.index', [
            'totalLeads'          => $totalLeads,
            'totalCotizaciones'   => Lead::where('quiere_cotizacion', true)->count(),
            'reunionesHoy'        => LeadMeeting::hoy()->count(),
            'porcentajeReunion'   => $totalLeads > 0 ? round($leadsQuierenReunion / $totalLeads * 100) : 0,
        ]);
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(StoreLeadRequest $request)
    {
        Lead::create($this->conComentarioAuto($request->validated()) + ['created_by' => auth()->id()]);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead registrado correctamente.');
    }

    public function storePublico(StorePublicLeadRequest $request)
    {
        Lead::create($request->validated() + ['origen' => Lead::ORIGEN_WEB]);

        return response()->json(['success' => true]);
    }

    private function conComentarioAuto(array $data): array
    {
        $auto = LeadResponseOptions::comentarioAuto($data['respuesta_rapida'] ?? null);

        if ($auto && empty($data['respuesta_comentario'])) {
            $data['respuesta_comentario'] = $auto;
        }

        return $data;
    }

    public function show(Lead $lead)
    {
        $lead->load([
            'meetings' => fn ($q) => $q->orderByDesc('fecha_hora'),
        ]);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($this->conComentarioAuto($request->validated()));

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', 'Lead actualizado correctamente.');
    }

    public function actualizarCotizacion(UpdateLeadCotizacionRequest $request, Lead $lead)
    {
        $datos = $request->validated();

        $lead->update([
            'quiere_cotizacion'  => $datos['quiere_cotizacion'],
            'ruc_dni'            => $datos['quiere_cotizacion'] ? $datos['ruc_dni'] : null,
            'reunion_realizada'  => $datos['reunion_realizada'],
            'cotizacion_enviada' => $datos['quiere_cotizacion'] ? $datos['cotizacion_enviada'] : false,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Seguimiento actualizado correctamente.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead eliminado correctamente.');
    }

    public function calendario()
    {
        return view('leads.calendario');
    }

    public function exportar(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $nombreArchivo = 'leads_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new LeadsExport($desde, $hasta), $nombreArchivo);
    }

    public function verificarTelefono(Request $request)
    {
        $digitos = preg_replace('/\D/', '', (string) $request->query('telefono'));

        if (strlen($digitos) < 9) {
            return response()->json(['existe' => false, 'lead' => null]);
        }

        $lead = Lead::query()
            ->whereRaw("REPLACE(REPLACE(telefono, ' ', ''), '+', '') = ?", [$digitos])
            ->when($request->query('excluir'), fn ($q, $id) => $q->whereKeyNot($id))
            ->first(['id', 'nombre']);

        return response()->json([
            'existe' => (bool) $lead,
            'lead'   => $lead ? [
                'id'     => $lead->id,
                'nombre' => $lead->nombre,
                'url'    => route('leads.show', $lead->id),
            ] : null,
        ]);
    }

    public function eventos(Request $request)
    {
        $anio = (int) $request->input('anio', now()->year);
        $mes  = (int) $request->input('mes', now()->month);

        $inicio = now()->setDate($anio, $mes, 1)->startOfMonth();
        $fin    = $inicio->copy()->endOfMonth();

        $reuniones = LeadMeeting::with('lead:id,nombre,empresa,respuesta_rapida,sistema_interes')
            ->whereHas('lead')
            ->enRango($inicio, $fin)
            ->orderBy('fecha_hora')
            ->get();

        return response()->json(
            $reuniones->map(fn (LeadMeeting $r) => [
                'id'              => $r->id,
                'lead_id'         => $r->lead_id,
                'lead_nombre'     => $r->lead->nombre,
                'lead_empresa'    => $r->lead->empresa,
                'sistema_interes' => $r->lead->sistema_interes,
                'fecha'           => $r->fecha_hora->format('Y-m-d'),
                'hora'            => $r->fecha_hora->format('H:i'),
                'nota'            => $r->nota,
                'color'           => $r->lead->respuestaColor(),
                'label'           => $r->lead->respuestaLabel(),
            ])
        );
    }
}
