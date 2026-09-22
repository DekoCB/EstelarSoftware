<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReporteDiarioRequest;
use App\Models\ReporteDiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReporteDiarioController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ReporteDiario::class);

        $esGestor = auth()->user()->hasPermissionTo('reportes_diarios.gestionar');

        $query = ReporteDiario::with('user')
            ->when(!$esGestor, fn($q) => $q->where('user_id', auth()->id()))
            ->when($request->area, fn($q, $v) => $q->where('area', $v))
            ->when($request->fecha_desde, fn($q, $v) => $q->whereDate('fecha', '>=', $v))
            ->when($request->fecha_hasta, fn($q, $v) => $q->whereDate('fecha', '<=', $v))
            ->when($request->user_id && $esGestor, fn($q, $v) => $q->where('user_id', $v))
            ->orderByDesc('fecha')
            ->orderByDesc('created_at');

        $reportes = $query->paginate(20)->withQueryString();

        $colaboradores = $esGestor
            ? \App\Models\User::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('reportes_diarios.index', compact('reportes', 'esGestor', 'colaboradores'));
    }

    public function create()
    {
        $this->authorize('create', ReporteDiario::class);

        return view('reportes_diarios.create');
    }

    public function store(StoreReporteDiarioRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('archivo_adjunto')) {
            $data['archivo_adjunto'] = $request->file('archivo_adjunto')
                ->store('reportes_diarios', 'private');
        }

        $data['user_id'] = auth()->id();

        // Filtrar filas vacías de impedimentos
        if (!empty($data['impedimentos'])) {
            $data['impedimentos'] = collect($data['impedimentos'])
                ->filter(fn($i) => !empty($i['descripcion']))
                ->values()
                ->all();
        }

        ReporteDiario::create($data);

        return redirect()->route('reportes_diarios.index')
            ->with('success', 'Reporte del día registrado correctamente.');
    }

    public function show(ReporteDiario $reportes_diario)
    {
        $this->authorize('view', $reportes_diario);

        $reportes_diario->load('user');

        return view('reportes_diarios.show', ['reporte' => $reportes_diario]);
    }

    public function descargarAdjunto(ReporteDiario $reportes_diario)
    {
        $this->authorize('view', $reportes_diario);

        if (! $reportes_diario->archivo_adjunto
            || ! Storage::disk('private')->exists($reportes_diario->archivo_adjunto)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('private')->download(
            $reportes_diario->archivo_adjunto,
            basename($reportes_diario->archivo_adjunto)
        );
    }

    public function destroy(ReporteDiario $reportes_diario)
    {
        $this->authorize('delete', $reportes_diario);

        if ($reportes_diario->archivo_adjunto) {
            Storage::disk('private')->delete($reportes_diario->archivo_adjunto);
        }

        $reportes_diario->delete();

        return redirect()->route('reportes_diarios.index')
            ->with('success', 'Reporte eliminado.');
    }
}
