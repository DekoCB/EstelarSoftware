<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-600">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="font-semibold text-white">Dashboard</h1>
        </div>
    </x-slot>

    {{-- Saludo ──────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">
                Buen día, {{ Str::words(auth()->user()->name, 1, '') }}
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
            </p>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-600">
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
            Sistema operativo
        </div>
    </div>

    {{-- ── KPI Cards ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-3 gap-4 mb-6">

        {{-- Ingresos del mes --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                    hover:border-emerald-500/20 transition-colors duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center
                            group-hover:bg-emerald-500/15 transition-colors">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                    </svg>
                </div>
                @if($kpis['ingresos_var'] !== null)
                <span class="text-xs font-mono px-2 py-0.5 rounded-lg
                             {{ $kpis['ingresos_var'] >= 0 ? 'text-emerald-400 bg-emerald-500/10' : 'text-rose-400 bg-rose-500/10' }}">
                    {{ $kpis['ingresos_var'] >= 0 ? '+' : '' }}{{ $kpis['ingresos_var'] }}%
                </span>
                @endif
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['ingresos_mes'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-1">Ingresos {{ now()->translatedFormat('M') }}</p>
        </div>

        {{-- Egresos del mes --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                    hover:border-rose-500/20 transition-colors duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center
                            group-hover:bg-rose-500/15 transition-colors">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181"/>
                    </svg>
                </div>
                @if($kpis['egresos_var'] !== null)
                <span class="text-xs font-mono px-2 py-0.5 rounded-lg
                             {{ $kpis['egresos_var'] <= 0 ? 'text-emerald-400 bg-emerald-500/10' : 'text-rose-400 bg-rose-500/10' }}">
                    {{ $kpis['egresos_var'] >= 0 ? '+' : '' }}{{ $kpis['egresos_var'] }}%
                </span>
                @endif
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['egresos_mes'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-1">Egresos {{ now()->translatedFormat('M') }}</p>
        </div>

        {{-- Saldo de caja --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                    hover:border-sky-500/20 transition-colors duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 flex items-center justify-center
                            group-hover:bg-sky-500/15 transition-colors">
                    <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <span class="text-xs text-slate-500 bg-slate-800 px-2 py-0.5 rounded-lg font-mono">acumulado</span>
            </div>
            <p class="text-2xl font-bold font-mono {{ $kpis['saldo_total'] >= 0 ? 'text-white' : 'text-rose-400' }}">
                S/ {{ number_format($kpis['saldo_total'], 2) }}
            </p>
            <p class="text-xs text-slate-500 mt-1">Saldo total en caja</p>
        </div>

        {{-- Proyectos activos --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                    hover:border-violet-500/20 transition-colors duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center
                            group-hover:bg-violet-500/15 transition-colors">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>
                    </svg>
                </div>
                <span class="text-xs text-slate-500 bg-slate-800 px-2 py-0.5 rounded-lg font-mono">
                    {{ $kpis['proyectos_total'] }} total
                </span>
            </div>
            <p class="text-2xl font-bold text-white font-mono">{{ $kpis['proyectos_activos'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Proyectos activos</p>
        </div>

        {{-- Facturación del mes --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                    hover:border-amber-500/20 transition-colors duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center
                            group-hover:bg-amber-500/15 transition-colors">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z"/>
                    </svg>
                </div>
                <span class="text-xs font-mono px-2 py-0.5 rounded-lg text-sky-400 bg-sky-500/10">SUNAT</span>
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['facturado_mes'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $kpis['facturas_mes'] }} comprobante{{ $kpis['facturas_mes'] !== 1 ? 's' : '' }} emitido{{ $kpis['facturas_mes'] !== 1 ? 's' : '' }}</p>
        </div>

        {{-- Por cobrar (cuotas pendientes) --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                    hover:border-orange-500/20 transition-colors duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center
                            group-hover:bg-orange-500/15 transition-colors">
                    <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                @if($kpis['cuotas_pendientes'] > 0)
                <span class="text-xs font-mono px-2 py-0.5 rounded-lg text-orange-400 bg-orange-500/10">
                    {{ $kpis['cuotas_pendientes'] }} cuota{{ $kpis['cuotas_pendientes'] !== 1 ? 's' : '' }}
                </span>
                @endif
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['por_cobrar'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-1">Por cobrar · {{ $kpis['clientes_total'] }} clientes</p>
        </div>

    </div>

    {{-- ── Charts ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">

        {{-- Flujo de caja 6 meses (span 2) --}}
        <div class="xl:col-span-2 bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-white">Flujo de caja</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ingresos vs egresos — últimos 6 meses</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-slate-400">
                        <span class="w-3 h-0.5 rounded-full bg-emerald-400 inline-block"></span> Ingresos
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-400">
                        <span class="w-3 h-0.5 rounded-full bg-rose-400 inline-block"></span> Egresos
                    </span>
                </div>
            </div>
            <div id="chart-flujo" class="h-52"></div>
        </div>

        {{-- Estado proyectos (donut) --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-white">Estado de proyectos</h3>
                <p class="text-xs text-slate-500 mt-0.5">Distribución por estado</p>
            </div>
            @if($estadoProyectos->sum() > 0)
            <div id="chart-proyectos" class="h-52"></div>
            @else
            <div class="h-52 flex items-center justify-center">
                <p class="text-slate-600 text-sm">Sin proyectos registrados</p>
            </div>
            @endif
        </div>

    </div>

    {{-- ── Facturación mensual ─────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-white">Facturación SUNAT</h3>
                <p class="text-xs text-slate-500 mt-0.5">Total facturado mensual — últimos 6 meses</p>
            </div>
            @can('facturacion.ver')
            <a href="{{ route('facturacion.index') }}"
               class="text-xs text-sky-400 hover:text-sky-300 flex items-center gap-1">
                Ver facturas
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                </svg>
            </a>
            @endcan
        </div>
        <div id="chart-facturacion" class="h-48"></div>
    </div>

    {{-- ── Paneles de actividad ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">

        {{-- Proyectos en curso --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">Proyectos en curso</h3>
                @can('proyectos.ver')
                <a href="{{ route('proyectos.index') }}" class="text-xs text-sky-400 hover:text-sky-300 flex items-center gap-1">
                    Ver todos
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
                @endcan
            </div>
            @forelse($proyectosActivos as $p)
            <a href="{{ route('proyectos.show', $p) }}"
               class="block p-3 rounded-xl hover:bg-slate-800/50 transition-colors -mx-1 mb-1 group">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate group-hover:text-sky-300 transition-colors">
                            {{ $p->name }}
                        </p>
                        <p class="text-[10px] text-slate-500 truncate">{{ $p->client->razon_social ?? '—' }}</p>
                    </div>
                    <span class="text-[10px] font-mono text-sky-400 shrink-0">{{ $p->progress ?? 0 }}%</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-1">
                    <div class="h-1 rounded-full transition-all duration-500
                                {{ ($p->progress ?? 0) >= 80 ? 'bg-emerald-400' : (($p->progress ?? 0) >= 50 ? 'bg-sky-400' : 'bg-amber-400') }}"
                         style="width: {{ $p->progress ?? 0 }}%"></div>
                </div>
            </a>
            @empty
            <div class="py-8 text-center">
                <p class="text-slate-600 text-sm">No hay proyectos en curso</p>
            </div>
            @endforelse
        </div>

        {{-- Últimas facturas --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">Últimas facturas</h3>
                @can('facturacion.ver')
                <a href="{{ route('facturacion.index') }}" class="text-xs text-sky-400 hover:text-sky-300 flex items-center gap-1">
                    Ver todas
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
                @endcan
            </div>
            @forelse($ultimasFacturas as $f)
            <a href="{{ route('facturacion.show', $f) }}"
               class="flex items-center justify-between py-2.5 px-1 rounded-lg hover:bg-slate-800/50 transition-colors -mx-1 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-1.5 h-1.5 rounded-full shrink-0
                                {{ $f->estado_sunat === 'aceptado' ? 'bg-emerald-400' : ($f->estado_sunat === 'error' || $f->estado_sunat === 'rechazado' ? 'bg-rose-400' : 'bg-amber-400') }}">
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-mono font-semibold text-white group-hover:text-sky-300 transition-colors">
                            {{ $f->numero_completo ?: ($f->serie . '-????') }}
                        </p>
                        <p class="text-[10px] text-slate-500 truncate max-w-[150px]">{{ $f->client->razon_social ?? '—' }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    <p class="text-xs font-mono font-bold text-white">S/ {{ number_format($f->total, 2) }}</p>
                    <p class="text-[10px] text-slate-600">{{ $f->fecha_emision?->format('d/m/Y') }}</p>
                </div>
            </a>
            @empty
            <div class="py-8 text-center">
                <p class="text-slate-600 text-sm">No hay facturas recientes</p>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ── Fila inferior ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

        {{-- Movimientos recientes de caja --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">Últimos movimientos</h3>
                @can('caja.ver')
                <a href="{{ route('caja.index') }}" class="text-xs text-sky-400 hover:text-sky-300 flex items-center gap-1">
                    Ver caja
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
                @endcan
            </div>
            @forelse($movimientosRecientes as $m)
            <a href="{{ route('caja.show', $m) }}"
               class="flex items-center justify-between py-2.5 px-1 rounded-lg hover:bg-slate-800/50 transition-colors -mx-1 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0
                                {{ $m->tipo === 'ingreso' ? 'bg-emerald-500/15' : 'bg-rose-500/15' }}">
                        @if($m->tipo === 'ingreso')
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/>
                        </svg>
                        @else
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3"/>
                        </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate max-w-[160px] group-hover:text-sky-300 transition-colors">
                            {{ $m->concepto }}
                        </p>
                        <p class="text-[10px] text-slate-500">{{ $m->fecha->format('d/m/Y') }} · {{ $m->categoriaLabel() }}</p>
                    </div>
                </div>
                <p class="text-xs font-bold font-mono ml-3 shrink-0
                          {{ $m->tipo === 'ingreso' ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $m->tipo === 'ingreso' ? '+' : '-' }} S/ {{ $m->montoFormateado() }}
                </p>
            </a>
            @empty
            <div class="py-8 text-center">
                <p class="text-slate-600 text-sm">No hay movimientos registrados</p>
            </div>
            @endforelse
        </div>

        {{-- Próximos cobros --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-white">Próximos cobros</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Cuotas pendientes de cotizaciones</p>
                </div>
                @can('cotizaciones.ver')
                <a href="{{ route('cotizaciones.index') }}" class="text-xs text-sky-400 hover:text-sky-300 flex items-center gap-1">
                    Ver cotizaciones
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
                @endcan
            </div>
            @forelse($proximosCobros as $cuota)
            <a href="{{ route('cotizaciones.show', $cuota->quote) }}"
               class="flex items-center justify-between py-2.5 px-1 rounded-lg hover:bg-slate-800/50 transition-colors -mx-1 group">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0
                                    {{ $cuota->estaVencida() ? 'bg-rose-400' : 'bg-orange-400' }}"></div>
                        <p class="text-xs font-semibold text-white truncate max-w-[160px] group-hover:text-sky-300 transition-colors">
                            {{ $cuota->nombre }}
                        </p>
                    </div>
                    <p class="text-[10px] text-slate-500 pl-3.5 truncate max-w-[180px]">
                        {{ $cuota->quote->numero }} · {{ $cuota->quote->client->razon_social ?? '—' }}
                    </p>
                </div>
                <div class="text-right shrink-0 ml-3">
                    <p class="text-xs font-bold font-mono text-orange-400">S/ {{ number_format($cuota->monto, 2) }}</p>
                    @if($cuota->fecha_vencimiento)
                    <p class="text-[10px] font-mono {{ $cuota->estaVencida() ? 'text-rose-400' : 'text-slate-600' }}">
                        {{ $cuota->estaVencida() ? 'VENCIDA ' : '' }}{{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                    </p>
                    @else
                    <p class="text-[10px] text-slate-600">sin fecha</p>
                    @endif
                </div>
            </a>
            @empty
            <div class="py-8 text-center">
                <svg class="w-8 h-8 text-slate-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p class="text-slate-600 text-sm">Sin cobros pendientes</p>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ── ApexCharts ──────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
    <script>
    const dark = {
        background: 'transparent',
        foreColor:  '#94a3b8',
        grid:       '#1e293b',
        tooltip:    { background: '#0f172a', border: '#1e293b' },
    };

    // Datos del servidor
    const flujoCaja    = @json($flujoCaja);
    const facturacion6m = @json($facturacion6m);
    const estadoProyectos = @json($estadoProyectos);

    // ── Chart 1: Flujo de caja ─────────────────────────────────────────
    new ApexCharts(document.getElementById('chart-flujo'), {
        chart: {
            type: 'area', height: '100%',
            background: 'transparent',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 },
            sparkline: { enabled: false },
        },
        theme: { mode: 'dark' },
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.25,
                opacityTo: 0.01,
                stops: [0, 100],
            },
        },
        colors: ['#34d399', '#fb7185'],
        series: [
            { name: 'Ingresos', data: flujoCaja.map(m => m.ingresos) },
            { name: 'Egresos',  data: flujoCaja.map(m => m.egresos) },
        ],
        xaxis: {
            categories: flujoCaja.map(m => m.mes),
            labels: { style: { colors: '#475569', fontSize: '10px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: '#475569', fontSize: '10px' },
                formatter: v => 'S/ ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v.toFixed(0)),
            },
        },
        grid: {
            borderColor: '#1e293b',
            strokeDashArray: 4,
            padding: { top: 0, right: 0, bottom: 0, left: 0 },
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '11px' },
            y: { formatter: v => 'S/ ' + v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') },
        },
        legend: { show: false },
        dataLabels: { enabled: false },
    }).render();

    // ── Chart 2: Estado proyectos (donut) ─────────────────────────────
    @if($estadoProyectos->sum() > 0)
    const estadoLabels = {
        planificado: 'Planificado',
        en_curso:    'En curso',
        pausado:     'Pausado',
        en_revision: 'En revisión',
        entregado:   'Entregado',
        cancelado:   'Cancelado',
    };
    const estadoColors = {
        planificado: '#64748b',
        en_curso:    '#38bdf8',
        pausado:     '#fbbf24',
        en_revision: '#a78bfa',
        entregado:   '#34d399',
        cancelado:   '#f87171',
    };
    const estados   = Object.keys(estadoProyectos);
    const valores   = Object.values(estadoProyectos);
    const etiquetas = estados.map(k => estadoLabels[k] ?? k);
    const colores   = estados.map(k => estadoColors[k] ?? '#64748b');

    new ApexCharts(document.getElementById('chart-proyectos'), {
        chart: {
            type: 'donut', height: '100%',
            background: 'transparent',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 },
        },
        theme: { mode: 'dark' },
        series: valores,
        labels: etiquetas,
        colors: colores,
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#94a3b8',
                            fontSize: '11px',
                            formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                        },
                        value: { color: '#f1f5f9', fontSize: '20px', fontWeight: 700 },
                    },
                },
            },
        },
        stroke: { show: false },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            fontSize: '10px',
            labels: { colors: '#94a3b8' },
            markers: { size: 6, shape: 'circle' },
            itemMargin: { horizontal: 6, vertical: 2 },
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '11px' },
            y: { formatter: v => v + ' proyecto' + (v !== 1 ? 's' : '') },
        },
    }).render();
    @endif

    // ── Chart 3: Facturación mensual ───────────────────────────────────
    new ApexCharts(document.getElementById('chart-facturacion'), {
        chart: {
            type: 'bar', height: '100%',
            background: 'transparent',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 },
        },
        theme: { mode: 'dark' },
        series: [{ name: 'Facturado', data: facturacion6m.map(m => m.total) }],
        colors: ['#f59e0b'],
        xaxis: {
            categories: facturacion6m.map(m => m.mes),
            labels: { style: { colors: '#475569', fontSize: '10px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: '#475569', fontSize: '10px' },
                formatter: v => 'S/ ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v.toFixed(0)),
            },
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '50%',
            },
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                shadeIntensity: 0.3,
                gradientToColors: ['#d97706'],
                opacityFrom: 0.9,
                opacityTo: 0.6,
                stops: [0, 100],
            },
        },
        grid: {
            borderColor: '#1e293b',
            strokeDashArray: 4,
            padding: { top: 0, right: 0, bottom: 0, left: 0 },
        },
        dataLabels: { enabled: false },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '11px' },
            y: { formatter: v => 'S/ ' + v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') },
        },
        legend: { show: false },
    }).render();
    </script>
</x-app-layout>
