<x-app-layout>
<x-slot name="header">
    <div class="flex items-center gap-2 text-sm">
        <span class="text-slate-600">Inicio</span>
        <span class="text-slate-700">/</span>
        <h1 class="font-semibold text-white">Reportes</h1>
    </div>
</x-slot>

<div x-data="reportes()" x-init="init()">

    {{-- ── Encabezado + filtro año ──────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Reportes y estadísticas</h2>
            <p class="text-sm text-slate-500 mt-0.5">Análisis financiero, ventas, facturación y proyectos</p>
        </div>
        <form method="GET" action="{{ route('reportes.index') }}" class="flex items-center gap-2">
            <label class="text-xs text-slate-500 font-mono">AÑO</label>
            <select name="year" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700/60 text-white text-sm rounded-xl px-3 py-2
                       focus:outline-none focus:border-sky-500/50">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────────────── --}}
    <div class="flex gap-1 mb-6 bg-slate-900/60 border border-slate-800/60 rounded-2xl p-1 w-fit">
        @foreach([
            ['financiero', 'Financiero',   'M12 3v17.25m0-17.25c-1.19 0-2.355.008-3.516.023...M12 3c1.19 0 2.355.008 3.516.023'],
            ['ventas',     'Ventas',        'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z'],
            ['facturacion','Facturación',   'M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
            ['proyectos',  'Proyectos',     'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6'],
        ] as [$tab, $label, $icon])
        <button @click="activeTab = '{{ $tab }}'"
            :class="activeTab === '{{ $tab }}'
                ? 'bg-slate-800 text-white shadow-sm'
                : 'text-slate-500 hover:text-slate-300'"
            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
            </svg>
            <span class="hidden sm:inline">{{ $label }}</span>
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: FINANCIERO                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'financiero'" x-transition>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Ingresos {{ $year }}</p>
                <p class="text-2xl font-bold text-emerald-400 font-mono">S/ {{ number_format($totalIngresos, 2) }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Egresos {{ $year }}</p>
                <p class="text-2xl font-bold text-rose-400 font-mono">S/ {{ number_format($totalEgresos, 2) }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Balance {{ $year }}</p>
                @php $balance = $totalIngresos - $totalEgresos; @endphp
                <p class="text-2xl font-bold font-mono {{ $balance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    S/ {{ number_format($balance, 2) }}
                </p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Saldo acumulado</p>
                <p class="text-2xl font-bold font-mono {{ $saldoAcumulado >= 0 ? 'text-sky-400' : 'text-rose-400' }}">
                    S/ {{ number_format($saldoAcumulado, 2) }}
                </p>
            </div>
        </div>

        {{-- Gráfico flujo mensual --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-6">
            <h3 class="text-sm font-semibold text-white mb-4">Flujo de caja mensual — {{ $year }}</h3>
            <div id="chart-flujo" class="h-64"></div>
        </div>

        {{-- Tabla por categoría --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/60">
                <h3 class="text-sm font-semibold text-white">Movimientos por categoría</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800/60">
                            <th class="text-left text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Categoría</th>
                            <th class="text-left text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Tipo</th>
                            <th class="text-right text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Cantidad</th>
                            <th class="text-right text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @forelse($porCategoria as $cat)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-3 text-slate-200 font-mono text-xs">{{ $cat->categoria ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-medium
                                    {{ $cat->tipo === 'ingreso' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ ucfirst($cat->tipo) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right text-slate-400 font-mono">{{ $cat->cantidad }}</td>
                            <td class="px-6 py-3 text-right font-mono font-semibold
                                {{ $cat->tipo === 'ingreso' ? 'text-emerald-400' : 'text-rose-400' }}">
                                S/ {{ number_format($cat->total, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-600 text-sm">Sin movimientos en {{ $year }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: VENTAS                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'ventas'" x-transition style="display:none">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Cotizaciones {{ $year }}</p>
                <p class="text-2xl font-bold text-white font-mono">{{ $totalCotiz }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Monto cotizado</p>
                <p class="text-2xl font-bold text-sky-400 font-mono">S/ {{ number_format($totalCotizMonto, 2) }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Aceptadas</p>
                <p class="text-2xl font-bold text-emerald-400 font-mono">{{ $cotizAceptadas }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Tasa de conversión</p>
                <p class="text-2xl font-bold font-mono {{ $tasaConversion >= 50 ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ $tasaConversion }}%
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Gráfico cotizaciones por mes --}}
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Cotizaciones por mes — {{ $year }}</h3>
                <div id="chart-cotiz-mes" class="h-56"></div>
            </div>
            {{-- Donut por estado --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Por estado</h3>
                <div id="chart-cotiz-estado" class="h-56"></div>
            </div>
        </div>

        {{-- Top clientes --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/60">
                <h3 class="text-sm font-semibold text-white">Top clientes por monto cotizado — {{ $year }}</h3>
            </div>
            <div class="divide-y divide-slate-800/40">
                @forelse($topClientes as $i => $cliente)
                <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-800/30 transition-colors">
                    <span class="w-7 h-7 rounded-lg bg-slate-800 border border-slate-700/60 flex items-center justify-center
                                 text-xs font-bold font-mono text-slate-400">#{{ $i+1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $cliente->razon_social }}</p>
                        <p class="text-xs text-slate-500">{{ $cliente->total_cotizaciones }} cotización(es)</p>
                    </div>
                    <span class="text-sm font-mono font-semibold text-sky-400">
                        S/ {{ number_format($cliente->total_cotizado, 2) }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-slate-600 text-sm">Sin cotizaciones en {{ $year }}</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: FACTURACIÓN                                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'facturacion'" x-transition style="display:none">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Total facturado {{ $year }}</p>
                <p class="text-2xl font-bold text-emerald-400 font-mono">S/ {{ number_format($totalFacturado, 2) }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">IGV recaudado</p>
                <p class="text-2xl font-bold text-amber-400 font-mono">S/ {{ number_format($totalIgv, 2) }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Documentos emitidos</p>
                <p class="text-2xl font-bold text-sky-400 font-mono">{{ $totalDocs }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Ticket promedio</p>
                <p class="text-2xl font-bold text-violet-400 font-mono">
                    S/ {{ $totalDocs > 0 ? number_format($totalFacturado / $totalDocs, 2) : '0.00' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Gráfico facturación mensual --}}
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Facturación mensual — {{ $year }}</h3>
                <div id="chart-fact-mes" class="h-56"></div>
            </div>
            {{-- Donut por tipo --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Por tipo de comprobante</h3>
                <div id="chart-fact-tipo" class="h-56"></div>
            </div>
        </div>

        {{-- Estado SUNAT --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/60">
                <h3 class="text-sm font-semibold text-white">Estado SUNAT — {{ $year }}</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-x divide-slate-800/60">
                @php
                    $estadoColors = [
                        'aceptado'  => 'text-emerald-400',
                        'pendiente' => 'text-amber-400',
                        'enviando'  => 'text-sky-400',
                        'rechazado' => 'text-rose-400',
                        'anulado'   => 'text-slate-500',
                        'borrador'  => 'text-slate-600',
                        'error'     => 'text-rose-600',
                    ];
                @endphp
                @foreach($factPorEstadoSunat as $estado => $cantidad)
                <div class="px-4 py-5 text-center">
                    <p class="text-xl font-bold font-mono {{ $estadoColors[$estado] ?? 'text-slate-400' }}">{{ $cantidad }}</p>
                    <p class="text-xs text-slate-600 mt-1 capitalize">{{ $estado }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: PROYECTOS                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'proyectos'" x-transition style="display:none">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Total proyectos</p>
                <p class="text-2xl font-bold text-white font-mono">{{ $totalProyectos }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">En curso</p>
                <p class="text-2xl font-bold text-sky-400 font-mono">{{ $proyEnCurso }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Completados</p>
                <p class="text-2xl font-bold text-emerald-400 font-mono">{{ $proyCompletados }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Avance promedio</p>
                <p class="text-2xl font-bold text-violet-400 font-mono">{{ $avancePromedio }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Donut proyectos por estado --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Proyectos por estado</h3>
                <div id="chart-proy-estado" class="h-56"></div>
            </div>
            {{-- Requerimientos por estado --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Requerimientos por estado</h3>
                <div id="chart-req-estado" class="h-56"></div>
            </div>
        </div>

        {{-- Tabla proyectos activos --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/60">
                <h3 class="text-sm font-semibold text-white">Proyectos activos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800/60">
                            <th class="text-left text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Proyecto</th>
                            <th class="text-left text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Cliente</th>
                            <th class="text-left text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Estado</th>
                            <th class="text-right text-xs text-slate-500 uppercase tracking-wider px-6 py-3">Avance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @forelse($proyectosActivos as $proyecto)
                        @php
                            $statusColor = match($proyecto->status) {
                                'en_curso'    => 'bg-sky-500/10 text-sky-400',
                                'planificado' => 'bg-slate-700/60 text-slate-400',
                                'pausado'     => 'bg-amber-500/10 text-amber-400',
                                'en_revision' => 'bg-violet-500/10 text-violet-400',
                                default       => 'bg-slate-700/60 text-slate-400',
                            };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-3">
                                <a href="{{ route('proyectos.show', $proyecto) }}" class="text-white hover:text-sky-400 transition-colors font-medium">
                                    {{ Str::limit($proyecto->name, 40) }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-slate-400 text-xs">{{ $proyecto->client?->razon_social ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $proyecto->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-20 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-sky-500 rounded-full" style="width: {{ $proyecto->progress ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-mono text-slate-400 w-8 text-right">{{ $proyecto->progress ?? 0 }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-600 text-sm">No hay proyectos activos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>{{-- /x-data --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function reportes() {
    return {
        activeTab: 'financiero',
        chartsInit: { financiero: false, ventas: false, facturacion: false, proyectos: false },

        init() {
            this.$watch('activeTab', tab => {
                if (!this.chartsInit[tab]) {
                    this.chartsInit[tab] = true;
                    this.$nextTick(() => this['init_' + tab]());
                }
            });
            // Iniciar primer tab
            this.$nextTick(() => { this.chartsInit.financiero = true; this.init_financiero(); });
        },

        // ── FINANCIERO ──────────────────────────────────────────────────
        init_financiero() {
            const meses   = @json($flujoPorMes->pluck('mes'));
            const ingresos = @json($flujoPorMes->pluck('ingresos'));
            const egresos  = @json($flujoPorMes->pluck('egresos'));
            new ApexCharts(document.querySelector('#chart-flujo'), {
                chart: { type: 'bar', height: 256, background: 'transparent', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [
                    { name: 'Ingresos', data: ingresos },
                    { name: 'Egresos',  data: egresos  },
                ],
                colors: ['#34d399', '#f87171'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                xaxis: { categories: meses, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' }, formatter: v => 'S/ ' + v.toLocaleString() } },
                legend: { labels: { colors: '#94a3b8' } },
                grid: { borderColor: '#1e293b', strokeDashArray: 4 },
                tooltip: { theme: 'dark' },
            }).render();
        },

        // ── VENTAS ──────────────────────────────────────────────────────
        init_ventas() {
            const meses    = @json($cotizPorMes->pluck('mes'));
            const montos   = @json($cotizPorMes->pluck('monto'));
            new ApexCharts(document.querySelector('#chart-cotiz-mes'), {
                chart: { type: 'area', height: 224, background: 'transparent', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Monto', data: montos }],
                colors: ['#38bdf8'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: { categories: meses, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' }, formatter: v => 'S/ ' + v.toLocaleString() } },
                grid: { borderColor: '#1e293b', strokeDashArray: 4 },
                tooltip: { theme: 'dark' },
            }).render();

            const estadoData = @json($cotizPorEstado->values());
            const estadoLabels = estadoData.map(e => e.status.charAt(0).toUpperCase() + e.status.slice(1));
            const estadoValues = estadoData.map(e => Number(e.cantidad));
            new ApexCharts(document.querySelector('#chart-cotiz-estado'), {
                chart: { type: 'donut', height: 224, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: estadoValues,
                labels: estadoLabels,
                colors: ['#38bdf8', '#34d399', '#f87171', '#a78bfa', '#fb923c'],
                legend: { position: 'bottom', labels: { colors: '#94a3b8' } },
                plotOptions: { pie: { donut: { size: '65%' } } },
                tooltip: { theme: 'dark' },
            }).render();
        },

        // ── FACTURACIÓN ─────────────────────────────────────────────────
        init_facturacion() {
            const meses  = @json($factPorMes->pluck('mes'));
            const totales = @json($factPorMes->pluck('total'));
            const igvs    = @json($factPorMes->pluck('igv'));
            new ApexCharts(document.querySelector('#chart-fact-mes'), {
                chart: { type: 'bar', height: 224, background: 'transparent', toolbar: { show: false }, stacked: true, fontFamily: 'Inter, sans-serif' },
                series: [
                    { name: 'Subtotal', data: totales.map((t, i) => Math.max(0, t - igvs[i])) },
                    { name: 'IGV',      data: igvs },
                ],
                colors: ['#34d399', '#fbbf24'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                xaxis: { categories: meses, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' }, formatter: v => 'S/ ' + v.toLocaleString() } },
                legend: { labels: { colors: '#94a3b8' } },
                grid: { borderColor: '#1e293b', strokeDashArray: 4 },
                tooltip: { theme: 'dark' },
            }).render();

            const tipoData = @json($factPorTipo);
            new ApexCharts(document.querySelector('#chart-fact-tipo'), {
                chart: { type: 'donut', height: 224, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: tipoData.map(t => Number(t.cantidad)),
                labels: tipoData.map(t => t.tipo_comprobante === 'factura' ? 'Factura' : 'Boleta'),
                colors: ['#38bdf8', '#a78bfa'],
                legend: { position: 'bottom', labels: { colors: '#94a3b8' } },
                plotOptions: { pie: { donut: { size: '65%' } } },
                tooltip: { theme: 'dark' },
            }).render();
        },

        // ── PROYECTOS ───────────────────────────────────────────────────
        init_proyectos() {
            const proyEstados = @json($proyPorEstado);
            const proyLabels  = Object.keys(proyEstados).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
            const proyValues  = Object.values(proyEstados).map(Number);
            new ApexCharts(document.querySelector('#chart-proy-estado'), {
                chart: { type: 'donut', height: 224, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: proyValues,
                labels: proyLabels,
                colors: ['#38bdf8', '#34d399', '#fbbf24', '#a78bfa', '#f87171', '#94a3b8'],
                legend: { position: 'bottom', labels: { colors: '#94a3b8' } },
                plotOptions: { pie: { donut: { size: '65%' } } },
                tooltip: { theme: 'dark' },
            }).render();

            const reqEstados = @json($reqPorEstado);
            const reqLabels  = Object.keys(reqEstados).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
            const reqValues  = Object.values(reqEstados).map(Number);
            new ApexCharts(document.querySelector('#chart-req-estado'), {
                chart: { type: 'bar', height: 224, background: 'transparent', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Requerimientos', data: reqValues }],
                colors: ['#38bdf8'],
                plotOptions: { bar: { borderRadius: 6, horizontal: true } },
                xaxis: { categories: reqLabels, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                grid: { borderColor: '#1e293b', strokeDashArray: 4 },
                tooltip: { theme: 'dark' },
            }).render();
        },
    };
}
</script>
@endpush

</x-app-layout>
