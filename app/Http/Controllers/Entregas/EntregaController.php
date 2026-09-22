<?php

namespace App\Http\Controllers\Entregas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entregas\StoreEntregaRequest;
use App\Http\Requests\Entregas\UpdateEntregaRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectDelivery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class EntregaController extends Controller
{
    public function index()
    {
        $entregas = ProjectDelivery::with(['project', 'client', 'createdBy'])
            ->orderByDesc('fecha_entrega')
            ->paginate(20);

        return view('entregas.index', compact('entregas'));
    }

    public function create()
    {
        $proyectos = Project::with('client')
            ->whereIn('status', ['en_progreso', 'completado'])
            ->orderBy('name')
            ->get();
        $clientes = Client::orderBy('razon_social')->get(['id', 'razon_social']);

        return view('entregas.create', compact('proyectos', 'clientes'));
    }

    public function store(StoreEntregaRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $entrega = ProjectDelivery::create($data);

        return redirect()
            ->route('entregas.show', $entrega)
            ->with('success', 'Acta de entrega registrada.');
    }

    public function show(ProjectDelivery $entrega)
    {
        $entrega->load(['project', 'client', 'createdBy']);
        return view('entregas.show', compact('entrega'));
    }

    public function edit(ProjectDelivery $entrega)
    {
        $proyectos = Project::with('client')
            ->whereIn('status', ['en_progreso', 'completado'])
            ->orderBy('name')
            ->get();
        $clientes = Client::orderBy('razon_social')->get(['id', 'razon_social']);

        return view('entregas.edit', compact('entrega', 'proyectos', 'clientes'));
    }

    public function update(UpdateEntregaRequest $request, ProjectDelivery $entrega)
    {
        $data = $request->validated();

        if (isset($data['estado']) && $data['estado'] === 'firmado' && $entrega->estado !== 'firmado') {
            $data['firmado_at'] = now();
        }

        $entrega->update($data);

        return redirect()
            ->route('entregas.show', $entrega)
            ->with('success', 'Acta actualizada.');
    }

    public function destroy(ProjectDelivery $entrega)
    {
        $entrega->delete();
        return redirect()->route('entregas.index')->with('success', 'Acta eliminada.');
    }

    public function acta(ProjectDelivery $entrega)
    {
        $entrega->load(['project', 'client', 'createdBy']);
        $config = \App\Models\EmpresaConfig::first();

        $pdf = Pdf::loadView('entregas.pdf-acta', compact('entrega', 'config'))
            ->setPaper('a4', 'portrait');

        $filename = 'ACTA-ENTREGA-' . str_pad($entrega->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }
}
