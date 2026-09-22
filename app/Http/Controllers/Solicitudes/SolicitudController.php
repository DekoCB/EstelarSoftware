<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolicitudRequest;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $esGestor = $user->hasPermissionTo('solicitudes.gestionar');

        $query = Solicitud::with(['solicitante', 'atendioPor'])
            ->when(!$esGestor, fn($q) => $q->where('user_id', $user->id))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->tipo, fn($q, $t) => $q->where('tipo', $t))
            ->latest();

        $solicitudes = $query->paginate(15)->withQueryString();

        $pendientes = Solicitud::when(!$esGestor, fn($q) => $q->where('user_id', $user->id))
            ->where('estado', 'pendiente')->count();

        return view('solicitudes.index', compact('solicitudes', 'esGestor', 'pendientes'));
    }

    public function create()
    {
        return view('solicitudes.create');
    }

    public function store(SolicitudRequest $request)
    {
        $data = $request->validated();
        if ($data['tipo'] === 'objeto') {
            $data['monto'] = null;
        }

        Solicitud::create(array_merge($data, ['user_id' => auth()->id()]));

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud enviada correctamente. El administrador la revisará pronto.');
    }

    public function show(Solicitud $solicitude)
    {
        $this->authorize('view', $solicitude);
        $solicitude->load(['solicitante', 'atendioPor']);
        return view('solicitudes.show', compact('solicitude'));
    }

    public function aprobar(Request $request, Solicitud $solicitude)
    {
        $this->authorize('gestionar', $solicitude);

        $request->validate([
            'respuesta_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $solicitude->update([
            'estado'          => 'aprobado',
            'respuesta_admin' => $request->respuesta_admin,
            'atendido_por'    => auth()->id(),
            'atendido_at'     => now(),
        ]);

        return back()->with('success', 'Solicitud aprobada.');
    }

    public function rechazar(Request $request, Solicitud $solicitude)
    {
        $this->authorize('gestionar', $solicitude);

        $request->validate([
            'respuesta_admin' => ['required', 'string', 'max:500'],
        ]);

        $solicitude->update([
            'estado'          => 'rechazado',
            'respuesta_admin' => $request->respuesta_admin,
            'atendido_por'    => auth()->id(),
            'atendido_at'     => now(),
        ]);

        return back()->with('success', 'Solicitud rechazada.');
    }

    public function entregar(Solicitud $solicitude)
    {
        $this->authorize('gestionar', $solicitude);

        $solicitude->update([
            'estado'       => 'entregado',
            'atendido_por' => auth()->id(),
            'atendido_at'  => now(),
        ]);

        return back()->with('success', 'Solicitud marcada como entregada.');
    }

    public function destroy(Solicitud $solicitude)
    {
        $this->authorize('view', $solicitude);

        abort_if(
            $solicitude->user_id !== auth()->id() || $solicitude->estado !== 'pendiente',
            403,
            'Solo puedes cancelar tus propias solicitudes pendientes.'
        );

        $solicitude->delete();

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud cancelada.');
    }
}
