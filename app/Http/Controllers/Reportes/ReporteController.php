<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $years = range(now()->year, max(now()->year - 4, 2024));

        // ── FINANCIERO ────────────────────────────────────────────────────
        $flujoPorMes = collect(range(1, 12))->map(function ($m) use ($year) {
            $mes = Carbon::create($year, $m, 1);
            return [
                'mes'      => $mes->translatedFormat('M'),
                'ingresos' => (float) CashMovement::ingresos()->whereYear('fecha', $year)->whereMonth('fecha', $m)->sum('monto'),
                'egresos'  => (float) CashMovement::egresos()->whereYear('fecha', $year)->whereMonth('fecha', $m)->sum('monto'),
            ];
        });

        $totalIngresos  = (float) CashMovement::ingresos()->whereYear('fecha', $year)->sum('monto');
        $totalEgresos   = (float) CashMovement::egresos()->whereYear('fecha', $year)->sum('monto');
        $saldoAcumulado = (float) CashMovement::sum(DB::raw("CASE WHEN tipo='ingreso' THEN monto ELSE -monto END"));

        $porCategoria = CashMovement::whereYear('fecha', $year)
            ->select('categoria', 'tipo', DB::raw('SUM(monto) as total, COUNT(*) as cantidad'))
            ->groupBy('categoria', 'tipo')
            ->orderByDesc('total')
            ->get();

        // ── VENTAS ────────────────────────────────────────────────────────
        $cotizPorEstado = Quote::whereYear('fecha_emision', $year)
            ->select('status', DB::raw('COUNT(*) as cantidad, SUM(total) as monto'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $cotizPorMes = collect(range(1, 12))->map(fn ($m) => [
            'mes'      => Carbon::create($year, $m, 1)->translatedFormat('M'),
            'cantidad' => Quote::whereYear('fecha_emision', $year)->whereMonth('fecha_emision', $m)->count(),
            'monto'    => (float) Quote::whereYear('fecha_emision', $year)->whereMonth('fecha_emision', $m)->sum('total'),
        ]);

        $topClientes = Client::query()
            ->select('clients.*')
            ->selectSub(
                Quote::selectRaw('COALESCE(SUM(total), 0)')
                    ->whereColumn('quotes.client_id', 'clients.id')
                    ->whereYear('fecha_emision', $year),
                'total_cotizado'
            )
            ->selectSub(
                Quote::selectRaw('COUNT(*)')
                    ->whereColumn('quotes.client_id', 'clients.id')
                    ->whereYear('fecha_emision', $year),
                'total_cotizaciones'
            )
            ->whereHas('quotes', fn ($q) => $q->whereYear('fecha_emision', $year))
            ->orderByDesc('total_cotizado')
            ->limit(5)
            ->get();

        $totalCotiz      = Quote::whereYear('fecha_emision', $year)->count();
        $cotizAceptadas  = Quote::whereYear('fecha_emision', $year)->where('status', 'aceptado')->count();
        $totalCotizMonto = (float) Quote::whereYear('fecha_emision', $year)->sum('total');
        $tasaConversion  = $totalCotiz > 0 ? round(($cotizAceptadas / $totalCotiz) * 100, 1) : 0;

        // ── FACTURACIÓN ───────────────────────────────────────────────────
        $factPorMes = collect(range(1, 12))->map(fn ($m) => [
            'mes'      => Carbon::create($year, $m, 1)->translatedFormat('M'),
            'total'    => (float) Invoice::where('estado_sunat', 'aceptado')->whereYear('fecha_emision', $year)->whereMonth('fecha_emision', $m)->sum('total'),
            'igv'      => (float) Invoice::where('estado_sunat', 'aceptado')->whereYear('fecha_emision', $year)->whereMonth('fecha_emision', $m)->sum('igv'),
            'cantidad' => Invoice::where('estado_sunat', 'aceptado')->whereYear('fecha_emision', $year)->whereMonth('fecha_emision', $m)->count(),
        ]);

        $factPorTipo = Invoice::whereYear('fecha_emision', $year)
            ->where('estado_sunat', 'aceptado')
            ->select('tipo_comprobante', DB::raw('COUNT(*) as cantidad, SUM(total) as total'))
            ->groupBy('tipo_comprobante')
            ->get();

        $factPorEstadoSunat = Invoice::whereYear('fecha_emision', $year)
            ->select('estado_sunat', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('estado_sunat')
            ->pluck('cantidad', 'estado_sunat');

        $totalFacturado = (float) Invoice::where('estado_sunat', 'aceptado')->whereYear('fecha_emision', $year)->sum('total');
        $totalIgv       = (float) Invoice::where('estado_sunat', 'aceptado')->whereYear('fecha_emision', $year)->sum('igv');
        $totalDocs      = Invoice::where('estado_sunat', 'aceptado')->whereYear('fecha_emision', $year)->count();

        // ── PROYECTOS ─────────────────────────────────────────────────────
        $proyPorEstado = Project::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $reqPorEstado = Requirement::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $proyectosActivos = Project::with('client')
            ->whereIn('status', ['planificado', 'en_curso', 'pausado', 'en_revision'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $totalProyectos    = Project::count();
        $proyCompletados   = Project::where('status', 'entregado')->count();
        $proyEnCurso       = Project::where('status', 'en_curso')->count();
        $avancePromedio    = round((float) Project::whereIn('status', ['planificado', 'en_curso', 'pausado', 'en_revision'])->avg('progress') ?? 0, 1);

        return view('reportes.index', compact(
            'year', 'years',
            // Financiero
            'flujoPorMes', 'totalIngresos', 'totalEgresos', 'saldoAcumulado', 'porCategoria',
            // Ventas
            'cotizPorEstado', 'cotizPorMes', 'topClientes', 'totalCotiz', 'cotizAceptadas', 'totalCotizMonto', 'tasaConversion',
            // Facturación
            'factPorMes', 'factPorTipo', 'factPorEstadoSunat', 'totalFacturado', 'totalIgv', 'totalDocs',
            // Proyectos
            'proyPorEstado', 'reqPorEstado', 'proyectosActivos', 'totalProyectos', 'proyCompletados', 'proyEnCurso', 'avancePromedio',
        ));
    }
}
