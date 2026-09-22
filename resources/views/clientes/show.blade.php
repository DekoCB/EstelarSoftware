<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('clientes.index') }}" class="text-slate-600 hover:text-slate-400 transition-colors font-mono">Clientes</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold truncate max-w-[240px]">{{ $cliente->razon_social }}</span>
        </div>
    </x-slot>

    {{-- ── Header card ─────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-white">{{ $cliente->razon_social }}</h1>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold capitalize {{ $cliente->estadoBadgeClass() }}">
                            {{ $cliente->estado }}
                        </span>
                    </div>
                    @if($cliente->nombre_comercial)
                    <p class="text-sm text-slate-500 mt-0.5">{{ $cliente->nombre_comercial }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1 flex-wrap">
                        <p class="text-xs font-mono text-slate-600">{{ $cliente->tipo_documento }} · {{ $cliente->numero_documento }}</p>
                        @if($cliente->email)
                        <span class="text-slate-700">·</span>
                        <p class="text-xs text-slate-500">{{ $cliente->email }}</p>
                        @endif
                        @if($cliente->telefono)
                        <span class="text-slate-700">·</span>
                        <p class="text-xs font-mono text-slate-500">{{ $cliente->telefono }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                @can('clientes.editar')
                <a href="{{ route('clientes.edit', $cliente) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-300 border border-slate-700/60 hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                    </svg>
                    Editar
                </a>
                @endcan
                @can('clientes.eliminar')
                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                      x-data
                      @submit.prevent="if(confirm('¿Eliminar a {{ addslashes($cliente->razon_social) }}?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-red-400 border border-slate-700/60 hover:border-red-500/30 hover:bg-red-500/10 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

        {{-- Total cotizado --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Cotizado</p>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['total_cotizado'], 2) }}</p>
            <p class="text-xs text-slate-600 mt-1">{{ $cliente->quotes->count() }} cotizacion(es)</p>
        </div>

        {{-- Total facturado --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Facturado</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['total_facturado'], 2) }}</p>
            <p class="text-xs text-slate-600 mt-1">{{ $cliente->invoices->count() }} comprobante(s)</p>
        </div>

        {{-- Total cobrado --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Cobrado</p>
                <div class="w-8 h-8 rounded-lg bg-teal-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white font-mono">S/ {{ number_format($kpis['total_cobrado'], 2) }}</p>
            <p class="text-xs text-slate-600 mt-1">en {{ $cliente->cashMovements->where('tipo','ingreso')->count() }} pago(s)</p>
        </div>

        {{-- Saldo pendiente --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 {{ $kpis['saldo_pendiente'] > 0 ? 'border-orange-500/30' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Saldo</p>
                <div class="w-8 h-8 rounded-lg {{ $kpis['saldo_pendiente'] > 0 ? 'bg-orange-500/10' : 'bg-slate-800' }} flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $kpis['saldo_pendiente'] > 0 ? 'text-orange-400' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold font-mono {{ $kpis['saldo_pendiente'] > 0 ? 'text-orange-400' : 'text-slate-500' }}">
                S/ {{ number_format($kpis['saldo_pendiente'], 2) }}
            </p>
            @if($kpis['cuotas_vencidas'] > 0)
            <p class="text-xs text-red-400 mt-1 font-medium">{{ $kpis['cuotas_vencidas'] }} cuota(s) vencida(s)</p>
            @else
            <p class="text-xs text-slate-600 mt-1">pendiente de cobro</p>
            @endif
        </div>
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────────── --}}
    <div x-data="{ tab: 'resumen' }">

        {{-- Nav de tabs --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-1 mb-4 flex flex-wrap gap-1">
            @php
            $tabs = [
                'resumen'      => 'Resumen',
                'proyectos'    => 'Proyectos (' . $cliente->projects->count() . ')',
                'cotizaciones' => 'Cotizaciones (' . $cliente->quotes->count() . ')',
                'cuotas'       => 'Cuotas (' . $todasLasCuotas->count() . ')',
                'facturacion'  => 'Facturación (' . $cliente->invoices->count() . ')',
                'entregas'     => 'Entregas (' . $cliente->deliveries->count() . ')',
                'movimientos'  => 'Movimientos (' . $cliente->cashMovements->count() . ')',
            ];
            @endphp
            @foreach($tabs as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}'
                        ? 'bg-sky-500/10 text-sky-400 border-sky-500/20'
                        : 'text-slate-500 hover:text-slate-300 border-transparent'"
                    class="flex-1 min-w-fit px-4 py-2 rounded-xl text-sm font-medium border transition-all duration-150 whitespace-nowrap">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ── Tab: Resumen ────────────────────────────────────────── --}}
        <div x-show="tab === 'resumen'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Info de contacto --}}
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Datos de contacto</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-600">Email</p>
                            <p class="text-sm text-white">{{ $cliente->email ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-600">Teléfono</p>
                            <p class="text-sm text-white font-mono">{{ $cliente->telefono ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-600">Dirección</p>
                            <p class="text-sm text-white">{{ $cliente->direccion ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-800">
                        <p class="text-xs text-slate-600">Registrado por <span class="text-slate-400">{{ $cliente->createdBy->name ?? 'Sistema' }}</span> el <span class="font-mono text-slate-400">{{ $cliente->created_at->format('d/m/Y') }}</span></p>
                    </div>
                </div>

                {{-- Estado general del portafolio --}}
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Estado del portafolio</h3>

                    @if($kpis['cuotas_vencidas'] > 0)
                    <div class="flex items-start gap-3 p-3 bg-red-500/10 border border-red-500/20 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                        <p class="text-xs text-red-300">{{ $kpis['cuotas_vencidas'] }} cuota(s) vencida(s) sin pagar. Revisar el tab Cuotas.</p>
                    </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-slate-800/60">
                            <span class="text-xs text-slate-500">Proyectos activos</span>
                            <span class="text-sm font-semibold text-white">{{ $kpis['proyectos_activos'] }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-800/60">
                            <span class="text-xs text-slate-500">Cotizaciones aceptadas</span>
                            <span class="text-sm font-semibold text-white">{{ $cliente->quotes->whereIn('status', ['aceptado','facturado'])->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-800/60">
                            <span class="text-xs text-slate-500">Facturas aceptadas SUNAT</span>
                            <span class="text-sm font-semibold text-white">{{ $cliente->invoices->where('estado_sunat','aceptado')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-800/60">
                            <span class="text-xs text-slate-500">Cuotas pagadas</span>
                            <span class="text-sm font-semibold text-emerald-400">{{ $todasLasCuotas->where('estado','pagada')->count() }} / {{ $todasLasCuotas->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-slate-500">Entregas firmadas</span>
                            <span class="text-sm font-semibold text-white">{{ $cliente->deliveries->where('estado','firmado')->count() }}</span>
                        </div>
                    </div>

                    @if($kpis['total_facturado'] > 0)
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-500">Cobrado vs Facturado</span>
                            <span class="text-xs font-mono text-slate-400">{{ number_format(min(100, ($kpis['total_cobrado'] / max(1,$kpis['total_facturado'])) * 100), 0) }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-2">
                            <div class="bg-teal-500 h-2 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, ($kpis['total_cobrado'] / max(1,$kpis['total_facturado'])) * 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Tab: Proyectos ──────────────────────────────────────── --}}
        <div x-show="tab === 'proyectos'" x-cloak>
            @if($cliente->projects->isEmpty())
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-10 text-center">
                <p class="text-slate-600 text-sm">Este cliente no tiene proyectos registrados.</p>
                @can('proyectos.crear')
                <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl text-sm font-medium bg-sky-500/10 text-sky-400 border border-sky-500/20 hover:bg-sky-500/20 transition-colors">
                    Crear proyecto
                </a>
                @endcan
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($cliente->projects->sortByDesc('created_at') as $proyecto)
                @php
                $statusClasses = match($proyecto->status) {
                    'en_curso'    => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
                    'planificado' => 'bg-slate-700 text-slate-300',
                    'pausado'     => 'bg-yellow-500/10 text-yellow-400 ring-1 ring-yellow-500/20',
                    'en_revision' => 'bg-purple-500/10 text-purple-400 ring-1 ring-purple-500/20',
                    'entregado'   => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                    'cancelado'   => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
                    default       => 'bg-slate-700 text-slate-400',
                };
                @endphp
                <a href="{{ route('proyectos.show', $proyecto) }}"
                   class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 hover:border-sky-500/30 transition-colors group block">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="text-sm font-semibold text-white group-hover:text-sky-400 transition-colors truncate pr-2">{{ $proyecto->name }}</h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium flex-shrink-0 capitalize {{ $statusClasses }}">
                            {{ str_replace('_', ' ', $proyecto->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-600">Progreso</span>
                            <span class="text-xs font-mono text-slate-400">{{ $proyecto->progress }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-1.5">
                            <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ $proyecto->progress }}%"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-slate-600">
                        <span>{{ $proyecto->phases->count() }} fases</span>
                        @if($proyecto->responsible)
                        <span>· {{ $proyecto->responsible->name }}</span>
                        @endif
                        @if($proyecto->end_date)
                        <span class="ml-auto font-mono">{{ \Carbon\Carbon::parse($proyecto->end_date)->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Tab: Cotizaciones ───────────────────────────────────── --}}
        <div x-show="tab === 'cotizaciones'" x-cloak>
            @if($cliente->quotes->isEmpty())
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-10 text-center">
                <p class="text-slate-600 text-sm">No hay cotizaciones para este cliente.</p>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Número</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cuotas</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($cliente->quotes->sortByDesc('fecha_emision') as $cot)
                        @php
                        $cotStatusClass = match($cot->status) {
                            'aceptado'  => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                            'facturado' => 'bg-teal-500/10 text-teal-400 ring-1 ring-teal-500/20',
                            'enviado'   => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
                            'rechazado' => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
                            default     => 'bg-slate-700 text-slate-400',
                        };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-slate-300 text-xs">{{ $cot->numero }}</td>
                            <td class="px-5 py-3.5 text-slate-400 text-xs font-mono">{{ \Carbon\Carbon::parse($cot->fecha_emision)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-white font-semibold">S/ {{ number_format($cot->total, 2) }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium capitalize {{ $cotStatusClass }}">{{ $cot->status }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-xs text-slate-500">
                                {{ $cot->payments->where('estado','pagada')->count() }}/{{ $cot->payments->count() }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('cotizaciones.show', $cot) }}"
                                       class="text-xs text-slate-500 hover:text-sky-400 transition-colors">Ver</a>
                                    @can('cotizaciones.pdf')
                                    <a href="{{ route('cotizaciones.pdf', $cot) }}" target="_blank"
                                       class="text-xs text-slate-500 hover:text-emerald-400 transition-colors">PDF</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Tab: Cuotas ─────────────────────────────────────────── --}}
        <div x-show="tab === 'cuotas'" x-cloak>
            @if($todasLasCuotas->isEmpty())
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-10 text-center">
                <p class="text-slate-600 text-sm">No hay plan de cobros configurado para este cliente.</p>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cuota</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cotización</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Monto</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Vencimiento</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Método</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($todasLasCuotas->sortBy([['quote_id','asc'],['orden','asc']]) as $cuota)
                        @php
                        $cuotaClass = match($cuota->estado) {
                            'pagada'   => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                            'vencida'  => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
                            default    => 'bg-slate-700 text-slate-400',
                        };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors {{ $cuota->estado === 'vencida' ? 'bg-red-500/5' : '' }}">
                            <td class="px-5 py-3.5 text-slate-300 text-xs">{{ $cuota->nombre }}</td>
                            <td class="px-5 py-3.5 text-xs font-mono text-slate-500">{{ $cuota->quote->numero ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-white font-semibold text-xs">S/ {{ number_format($cuota->monto, 2) }}</td>
                            <td class="px-5 py-3.5 text-center text-xs font-mono text-slate-500">
                                {{ $cuota->fecha_vencimiento ? \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium capitalize {{ $cuotaClass }}">
                                    {{ $cuota->estado }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 capitalize">{{ $cuota->metodo_pago ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-slate-700">
                        <tr>
                            <td colspan="2" class="px-5 py-3 text-xs text-slate-500">
                                {{ $todasLasCuotas->where('estado','pagada')->count() }} pagadas · {{ $todasLasCuotas->where('estado','pendiente')->count() }} pendientes · {{ $todasLasCuotas->where('estado','vencida')->count() }} vencidas
                            </td>
                            <td class="px-5 py-3 text-right font-mono text-sm font-bold text-white">
                                S/ {{ number_format($todasLasCuotas->sum('monto'), 2) }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Tab: Facturación ─────────────────────────────────────── --}}
        <div x-show="tab === 'facturacion'" x-cloak>
            @if($cliente->invoices->isEmpty())
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-10 text-center">
                <p class="text-slate-600 text-sm">No hay comprobantes emitidos para este cliente.</p>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Número</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">SUNAT</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($cliente->invoices->sortByDesc('fecha_emision') as $inv)
                        @php
                        $invClass = match($inv->estado_sunat) {
                            'aceptado'  => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                            'rechazado' => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
                            'anulado'   => 'bg-slate-700 text-slate-500 line-through',
                            'enviando'  => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
                            'error'     => 'bg-orange-500/10 text-orange-400 ring-1 ring-orange-500/20',
                            default     => 'bg-slate-700 text-slate-400',
                        };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-slate-300 text-xs">{{ $inv->numero_completo ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-400">{{ $inv->esFactura() ? 'Factura' : 'Boleta' }}</td>
                            <td class="px-5 py-3.5 text-xs font-mono text-slate-500">{{ \Carbon\Carbon::parse($inv->fecha_emision)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-white font-semibold text-xs">S/ {{ number_format($inv->total, 2) }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium capitalize {{ $invClass }}">{{ $inv->estado_sunat }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('facturacion.show', $inv) }}"
                                   class="text-xs text-slate-500 hover:text-sky-400 transition-colors">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Tab: Entregas ───────────────────────────────────────── --}}
        <div x-show="tab === 'entregas'" x-cloak>
            @if($cliente->deliveries->isEmpty())
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-10 text-center">
                <p class="text-slate-600 text-sm">No hay actas de entrega para este cliente.</p>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Título</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Firmante</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($cliente->deliveries->sortByDesc('fecha_entrega') as $entrega)
                        @php
                        $entClass = match($entrega->estado) {
                            'firmado'    => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                            'observado'  => 'bg-yellow-500/10 text-yellow-400 ring-1 ring-yellow-500/20',
                            default      => 'bg-slate-700 text-slate-400',
                        };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 text-slate-300 text-xs">{{ $entrega->titulo }}</td>
                            <td class="px-5 py-3.5 text-center text-xs text-slate-400 capitalize">{{ $entrega->tipo }}</td>
                            <td class="px-5 py-3.5 text-center text-xs font-mono text-slate-500">{{ \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium capitalize {{ $entClass }}">{{ $entrega->estado }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ $entrega->firma_cliente ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('entregas.show', $entrega) }}"
                                   class="text-xs text-slate-500 hover:text-sky-400 transition-colors">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Tab: Movimientos ─────────────────────────────────────── --}}
        <div x-show="tab === 'movimientos'" x-cloak>
            @if($cliente->cashMovements->isEmpty())
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-10 text-center">
                <p class="text-slate-600 text-sm">No hay movimientos de caja vinculados a este cliente.</p>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Concepto</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Monto</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Método</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($cliente->cashMovements->sortByDesc('fecha') as $mov)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 text-xs font-mono text-slate-500">{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-300">{{ $mov->concepto }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium capitalize
                                    {{ $mov->tipo === 'ingreso' ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20' : 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20' }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-semibold text-xs {{ $mov->tipo === 'ingreso' ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $mov->tipo === 'ingreso' ? '+' : '-' }} S/ {{ number_format($mov->monto, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 capitalize">{{ $mov->metodo_pago }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-slate-700">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-xs text-slate-500">
                                Total ingresos del cliente
                            </td>
                            <td class="px-5 py-3 text-right font-mono text-sm font-bold text-emerald-400">
                                S/ {{ number_format($cliente->cashMovements->where('tipo','ingreso')->sum('monto'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>

    </div>{{-- /x-data tabs --}}

</x-app-layout>
