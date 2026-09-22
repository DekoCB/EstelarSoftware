<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuotePayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Proyectos que NO están finalizados (todo lo que sigue activo)
    private const ESTADOS_ACTIVOS = ['planificado', 'en_curso', 'pausado', 'en_revision'];

    // Cotizaciones que representan oportunidades vigentes
    private const COTIZ_ACTIVAS = ['enviado', 'aceptado'];

    public function __invoke()
    {
        $now        = Carbon::now();
        $mesActual  = $now->month;
        $anioActual = $now->year;
        $mesAnterior = $now->copy()->subMonth();

        // ── KPIs (caché 5 min) ─────────────────────────────────────────
        $kpis = Cache::remember("dashboard_kpis_{$anioActual}_{$mesActual}", 300, function () use ($mesActual, $anioActual, $mesAnterior) {
            $ingMes = CashMovement::ingresos()->whereYear('fecha', $anioActual)->whereMonth('fecha', $mesActual)->sum('monto');
            $egrMes = CashMovement::egresos()->whereYear('fecha', $anioActual)->whereMonth('fecha', $mesActual)->sum('monto');
            $ingAnt = CashMovement::ingresos()->whereYear('fecha', $mesAnterior->year)->whereMonth('fecha', $mesAnterior->month)->sum('monto');
            $egrAnt = CashMovement::egresos()->whereYear('fecha', $mesAnterior->year)->whereMonth('fecha', $mesAnterior->month)->sum('monto');

            $saldoTotal = CashMovement::sum(DB::raw("CASE WHEN tipo='ingreso' THEN monto ELSE -monto END"));

            return [
                'ingresos_mes'      => (float) $ingMes,
                'egresos_mes'       => (float) $egrMes,
                'saldo_total'       => (float) $saldoTotal,
                'ingresos_var'      => $ingAnt > 0 ? round((($ingMes - $ingAnt) / $ingAnt) * 100, 1) : null,
                'egresos_var'       => $egrAnt > 0 ? round((($egrMes - $egrAnt) / $egrAnt) * 100, 1) : null,
                // Proyectos: todos los que no están entregados/cancelados
                'proyectos_activos' => Project::whereIn('status', self::ESTADOS_ACTIVOS)->count(),
                'proyectos_total'   => Project::count(),
                // Facturación
                'facturas_mes'      => Invoice::where('estado_sunat', 'aceptado')
                                              ->whereYear('fecha_emision', $anioActual)
                                              ->whereMonth('fecha_emision', $mesActual)
                                              ->count(),
                'facturado_mes'     => (float) Invoice::where('estado_sunat', 'aceptado')
                                              ->whereYear('fecha_emision', $anioActual)
                                              ->whereMonth('fecha_emision', $mesActual)
                                              ->sum('total'),
                'clientes_total'    => Client::count(),
                // Cotizaciones: enviadas + aceptadas (oportunidades abiertas)
                'cotiz_pendientes'  => Quote::whereIn('status', self::COTIZ_ACTIVAS)->count(),
                // Por cobrar: cuotas pendientes de cotizaciones
                'por_cobrar'        => (float) QuotePayment::where('estado', 'pendiente')->sum('monto'),
                'cuotas_pendientes' => QuotePayment::where('estado', 'pendiente')->count(),
            ];
        });

        // ── Chart: Flujo de caja 6 meses ──────────────────────────────
        $flujoCaja = Cache::remember("dashboard_flujo_{$anioActual}_{$mesActual}", 300, function () use ($now) {
            return collect(range(5, 0))
                ->map(fn($i) => $now->copy()->subMonths($i))
                ->map(function (Carbon $m) {
                    return [
                        'mes'      => $m->translatedFormat('M Y'),
                        'ingresos' => (float) CashMovement::ingresos()->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->sum('monto'),
                        'egresos'  => (float) CashMovement::egresos()->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->sum('monto'),
                    ];
                })->values();
        });

        // ── Chart: Facturación mensual 6 meses ────────────────────────
        $facturacion6m = Cache::remember("dashboard_fact_{$anioActual}_{$mesActual}", 300, function () use ($now) {
            return collect(range(5, 0))
                ->map(fn($i) => $now->copy()->subMonths($i))
                ->map(function (Carbon $m) {
                    return [
                        'mes'   => $m->translatedFormat('M Y'),
                        'total' => (float) Invoice::where('estado_sunat', 'aceptado')
                                                  ->whereYear('fecha_emision', $m->year)
                                                  ->whereMonth('fecha_emision', $m->month)
                                                  ->sum('total'),
                    ];
                })->values();
        });

        // ── Chart: Estado de proyectos (donut) ────────────────────────
        $estadoProyectos = Cache::remember("dashboard_proyectos_{$anioActual}_{$mesActual}", 300, function () {
            return Project::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        });

        // ── Paneles (sin caché — datos recientes) ─────────────────────
        $proyectosActivos = Project::with(['client', 'phases'])
            ->whereIn('status', self::ESTADOS_ACTIVOS)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $ultimasFacturas = Invoice::with('client')
            ->whereNotIn('estado_sunat', ['borrador'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $movimientosRecientes = CashMovement::with('client')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $cotizacionesActivas = Quote::with('client')
            ->whereIn('status', self::COTIZ_ACTIVAS)
            ->orderByDesc('fecha_emision')
            ->limit(5)
            ->get();

        // Próximos cobros pendientes (cuotas con fecha o sin fecha, ordenadas)
        $proximosCobros = QuotePayment::with(['quote.client'])
            ->where('estado', 'pendiente')
            ->orderByRaw('fecha_vencimiento IS NULL ASC')
            ->orderBy('fecha_vencimiento')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'kpis',
            'flujoCaja',
            'facturacion6m',
            'estadoProyectos',
            'proyectosActivos',
            'ultimasFacturas',
            'movimientosRecientes',
            'cotizacionesActivas',
            'proximosCobros',
        ));
    }
}
