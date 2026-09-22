<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 font-mono">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="text-sm font-semibold text-white">Leads</h1>
        </div>
    </x-slot>

    {{-- Alpine scope: modal + reabre si hay errores de validación --}}
    <div
        x-data="{ modalCrear: {{ $errors->any() ? 'true' : 'false' }} }"
        x-on:agendar-hora.window="modalCrear = true"
        @if(request()->filled('agendar_fecha'))
        x-init="$nextTick(() => $dispatch('agendar-hora', {
            fecha: {{ Illuminate\Support\Js::from(request('agendar_fecha')) }},
            hora: {{ Illuminate\Support\Js::from(request('agendar_hora')) }},
        }))"
        @endif
    >

        {{-- ── Header ─────────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Leads</h2>
                <p class="text-sm text-slate-500 mt-0.5">Contactos comerciales antes de convertirse en clientes</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @can('leads.ver')
                <a href="{{ route('leads.calendario') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                          text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white
                          transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                    </svg>
                    Calendario
                </a>
                @endcan
                @can('leads.ver')
                <a href="{{ route('leads.index', ['cotizacion' => 1]) }}"
                   class="relative inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                          text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white
                          transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m3-8.25c0-1.243-1.343-2.25-3-2.25s-3 1.007-3 2.25 1.343 2.25 3 2.25 3 1.007 3 2.25-1.343 2.25-3 2.25-3-1.007-3-2.25"/>
                    </svg>
                    Cotizaciones
                    @if($totalCotizaciones > 0)
                    <span class="absolute -top-2 -right-2 flex items-center justify-center
                                 min-w-[18px] h-[18px] px-1 rounded-full
                                 bg-emerald-500 text-white text-[10px] font-bold leading-none">
                        {{ $totalCotizaciones > 9 ? '9+' : $totalCotizaciones }}
                    </span>
                    @endif
                </a>
                @endcan
                @can('leads.ver')
                <div x-data="{ open: false, desde: '', hasta: '' }">
                    <button
                        @click="open = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                               text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white
                               transition-all duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 7.5 12 3m0 0 4.5 4.5M12 3v13.5"/>
                        </svg>
                        Exportar Excel
                    </button>

                    <template x-teleport="body">
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 overflow-y-auto"
                            style="display:none; z-index:60;"
                        >
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="open = false"></div>

                            <div class="relative min-h-full flex items-center justify-center p-4">
                                <div
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-250"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                    class="w-full max-w-xs bg-slate-900 border border-slate-700/60
                                           rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)] p-5"
                                    @click.stop
                                >
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-xs font-semibold text-slate-400">Filtrar por fecha de registro</p>
                                        <button @click="open = false" type="button" class="text-slate-500 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="space-y-2.5">
                                        <div>
                                            <label class="text-[11px] text-slate-500">Desde</label>
                                            <input type="date" x-model="desde"
                                                   class="w-full bg-slate-800/80 border border-slate-700 text-white
                                                          rounded-lg px-3 py-2 text-sm mt-1
                                                          focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-slate-500">Hasta</label>
                                            <input type="date" x-model="hasta"
                                                   class="w-full bg-slate-800/80 border border-slate-700 text-white
                                                          rounded-lg px-3 py-2 text-sm mt-1
                                                          focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50">
                                        </div>
                                    </div>
                                    <a :href="'{{ route('leads.exportar') }}?desde=' + desde + '&hasta=' + hasta"
                                       class="mt-4 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                              text-white bg-gradient-to-r from-sky-500 to-cyan-500
                                              hover:from-sky-400 hover:to-cyan-400 transition-all duration-200">
                                        Descargar
                                    </a>
                                    <p class="text-[11px] text-slate-600 mt-2">Deja los campos vacíos para exportar todos los leads.</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                @endcan
                @can('leads.crear')
                <a href="{{ route('leads.importar') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                          text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white
                          transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Importar Excel
                </a>
                <button
                    @click="modalCrear = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                           shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                           transition-all duration-200 active:scale-[0.98]"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Nuevo lead
                </button>
                @endcan
            </div>
        </div>

        {{-- ── Tarjetas de resumen ────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

            {{-- Card: total de leads registrados --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                        hover:border-sky-500/20 transition-colors duration-300 group flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-sky-500/10 flex items-center justify-center
                            group-hover:bg-sky-500/15 transition-colors flex-shrink-0">
                    <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white font-mono">{{ $totalLeads }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">Lead{{ $totalLeads !== 1 ? 's' : '' }} registrado{{ $totalLeads !== 1 ? 's' : '' }} en total</p>
                </div>
            </div>

            {{-- Card: reuniones/llamadas agendadas para hoy --}}
            @can('leads.ver')
            <a href="{{ route('leads.calendario') }}"
               class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                      hover:border-amber-500/20 transition-colors duration-300 group flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center
                            group-hover:bg-amber-500/15 transition-colors flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white font-mono">{{ $reunionesHoy }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">Reunión{{ $reunionesHoy !== 1 ? 'es' : '' }} agendada{{ $reunionesHoy !== 1 ? 's' : '' }} para hoy</p>
                </div>
            </a>
            @endcan

            {{-- Card: % de leads que quieren una reunión --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5
                        hover:border-yellow-500/20 transition-colors duration-300 group flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-yellow-500/10 flex items-center justify-center
                            group-hover:bg-yellow-500/15 transition-colors flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white font-mono">{{ $porcentajeReunion }}%</p>
                    <p class="text-xs text-slate-500 mt-0.5">Del total quiere una reunión</p>
                </div>
            </div>

        </div>

        {{-- ── Lista Livewire ──────────────────────────────────────── --}}
        @livewire('leads.leads-list')

        {{-- ══ MODAL CREAR LEAD ════════════════════════════════════ --}}
        @can('leads.crear')
        <template x-teleport="body">
            <div
                x-show="modalCrear"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display:none"
            >
                {{-- Overlay --}}
                <div
                    class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                    @click="modalCrear = false"
                ></div>

                {{-- Contenedor centrado --}}
                <div class="relative min-h-full flex items-start justify-center p-4 pt-12">
                    <div
                        x-show="modalCrear"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="w-full max-w-xl bg-slate-900 border border-slate-700/60
                               rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)] flex flex-col
                               max-h-[calc(100vh-6rem)]"
                        @click.stop
                    >
                        {{-- Header del modal --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80 flex-shrink-0">
                            <div>
                                <h2 class="text-base font-bold text-white">Nuevo lead</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Alta rápida manual</p>
                            </div>
                            <button
                                @click="modalCrear = false"
                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                       text-slate-500 hover:text-white hover:bg-slate-800
                                       transition-colors duration-150"
                                aria-label="Cerrar"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Body scrollable --}}
                        <div class="overflow-y-auto flex-1 px-6 py-5">
                            <form
                                method="POST"
                                action="{{ route('leads.store') }}"
                                id="form-crear-lead"
                            >
                                @csrf
                                @include('leads._form')
                            </form>
                        </div>

                        {{-- Footer del modal --}}
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-800/80 flex-shrink-0">
                            <button
                                type="button"
                                @click="modalCrear = false"
                                class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white
                                       border border-slate-700/60 hover:bg-slate-800 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                form="form-crear-lead"
                                class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                                       bg-gradient-to-r from-sky-500 to-cyan-500
                                       hover:from-sky-400 hover:to-cyan-400
                                       shadow-[0_0_18px_rgba(14,165,233,0.35)]
                                       hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                                       transition-all duration-200 active:scale-[0.98]"
                            >
                                Guardar lead
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </template>
        @endcan

    </div>

</x-app-layout>
