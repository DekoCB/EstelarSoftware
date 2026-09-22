<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('eventos.index') }}" class="text-slate-600 hover:text-slate-400 transition-colors font-mono">Eventos</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold truncate max-w-[240px]">{{ $evento->nombre }}</span>
        </div>
    </x-slot>

    @include('eventos._asistente-busqueda')

    <div x-data="{ modalLead: {{ $errors->any() ? 'true' : 'false' }}, modalAsistente: false, copiado: false }">

        {{-- ── Header card ─────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5">
            <div class="flex items-start justify-between gap-4 flex-wrap">

                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-white">{{ $evento->nombre }}</h1>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $evento->estadoBadgeClass() }}">
                                {{ $evento->estadoLabel() }}
                            </span>
                        </div>
                        @if($evento->descripcion)
                        <p class="text-sm text-slate-500 mt-0.5 max-w-xl">{{ $evento->descripcion }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap text-xs">
                            <p class="font-mono text-slate-500">
                                {{ $evento->fecha_inicio->format('d/m/Y') }}
                                @if($evento->fecha_fin && !$evento->fecha_fin->equalTo($evento->fecha_inicio))
                                    – {{ $evento->fecha_fin->format('d/m/Y') }}
                                @endif
                            </p>
                            @if($evento->lugar)
                            <span class="text-slate-700">·</span>
                            <p class="text-slate-500">{{ $evento->lugar }}</p>
                            @endif
                            @if($evento->responsable)
                            <span class="text-slate-700">·</span>
                            <p class="text-slate-500">{{ $evento->responsable->name }}</p>
                            @endif
                            @if($evento->tieneUbicacion())
                            <span class="text-slate-700">·</span>
                            <a href="https://www.openstreetmap.org/?mlat={{ $evento->latitud }}&mlon={{ $evento->longitud }}#map=17/{{ $evento->latitud }}/{{ $evento->longitud }}"
                               target="_blank" rel="noopener"
                               class="text-sky-400 hover:text-sky-300 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                </svg>
                                Ver ubicación
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    @can('eventos.editar')
                    <a href="{{ route('eventos.edit', $evento) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-300 border border-slate-700/60 hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                        </svg>
                        Editar
                    </a>
                    @endcan
                    @can('eventos.eliminar')
                    <form method="POST" action="{{ route('eventos.destroy', $evento) }}"
                          x-data
                          @submit.prevent="if(confirm('¿Eliminar el evento {{ addslashes($evento->nombre) }}? Se eliminarán también sus leads.')) $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-400 border border-slate-700/60 hover:border-red-500/30 hover:text-red-400 transition-all duration-200">
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

        {{-- ── KPIs ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Total leads</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $kpis['total_leads'] }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Nuevos</p>
                <p class="text-2xl font-bold text-sky-400 mt-1">{{ $kpis['nuevos'] }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Contactados</p>
                <p class="text-2xl font-bold text-amber-400 mt-1">{{ $kpis['contactados'] }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Convertidos</p>
                <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $kpis['convertidos'] }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Inscritos al evento</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $kpis['total_asistentes'] }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Asistieron</p>
                <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $kpis['asistieron'] }}</p>
            </div>
        </div>

        {{-- ── Asistentes del evento ───────────────────────────────── --}}
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <h2 class="text-sm font-semibold text-white">Asistentes e inscripciones</h2>
            <div class="flex items-center gap-2">
                <button
                    @click="navigator.clipboard.writeText('{{ route('eventos.inscripcion.create', $evento) }}'); copiado = true; setTimeout(() => copiado = false, 2000)"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-300 border border-slate-700/60 hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                    </svg>
                    <span x-text="copiado ? '¡Copiado!' : 'Copiar link de inscripción'"></span>
                </button>
                @can('eventos.checkin')
                <a href="{{ route('eventos.checkin', $evento) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-300 border border-slate-700/60 hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h4.5v4.5h-4.5v-4.5ZM3.75 15h4.5v4.5h-4.5V15ZM15 3.75h4.5v4.5H15v-4.5ZM15 15h1.5v1.5H15V15Zm3 0h1.5v1.5H18V15Zm-3 3h1.5v1.5H15V18Zm3 0h1.5v1.5H18V18Z"/>
                    </svg>
                    Check-in
                </a>
                @endcan
                <a href="{{ route('eventos.asistentes.exportar', $evento) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-300 border border-slate-700/60 hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 12m0 0 4.5-4.5M12 12V3"/>
                    </svg>
                    Exportar
                </a>
                @can('eventos.crear')
                <a href="{{ route('eventos.asistentes.importar', $evento) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-slate-800 text-slate-300 border border-slate-700/60 hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                    </svg>
                    Importar
                </a>
                @endcan
                @can('eventos.crear')
                <button
                    @click="modalAsistente = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                           shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                           transition-all duration-200 active:scale-[0.98]"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Agregar asistente
                </button>
                @endcan
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden mb-5">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800/80">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Asistente</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Contacto</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Código</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($evento->asistentes as $asistente)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-semibold text-white">{{ $asistente->nombres }}</p>
                            @if($asistente->empresa)
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $asistente->empresa }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <p class="text-xs text-slate-400">{{ $asistente->email ?: '—' }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $asistente->telefono ?: '' }}</p>
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            <p class="text-xs font-mono text-slate-500">{{ $asistente->codigo }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $asistente->estadoBadgeClass() }}">
                                {{ $asistente->estadoLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($asistente->estado !== 'cancelado')
                                <a href="{{ route('eventos.inscripcion.ticket', [$evento, $asistente]) }}" target="_blank" title="Ver ticket"
                                   class="p-1.5 rounded-lg text-sky-400 bg-sky-500/10 border border-sky-500/20
                                          shadow-[0_0_10px_rgba(14,165,233,0.25)]
                                          hover:bg-sky-500/20 hover:shadow-[0_0_16px_rgba(14,165,233,0.45)]
                                          transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/>
                                    </svg>
                                </a>
                                @endif
                                @can('eventos.eliminar')
                                @if($asistente->estado !== 'cancelado')
                                <form method="POST" action="{{ route('eventos.asistentes.destroy', [$evento, $asistente]) }}"
                                      x-data
                                      @submit.prevent="if(confirm('¿Cancelar el registro de {{ addslashes($asistente->nombres) }}?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Cancelar registro"
                                            class="p-1.5 rounded-lg text-red-400 bg-red-500/10 border border-red-500/20
                                                   shadow-[0_0_10px_rgba(239,68,68,0.25)]
                                                   hover:bg-red-500/20 hover:shadow-[0_0_16px_rgba(239,68,68,0.45)]
                                                   transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <p class="text-slate-600 text-sm">Aún no hay asistentes inscritos en este evento</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Leads del evento ─────────────────────────────────────── --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <h2 class="text-sm font-semibold text-white">Leads capturados</h2>
                @if($puedeVerTodos)
                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700/60">
                    Viendo los de todos los usuarios
                </span>
                @else
                <span class="text-[10px] px-2 py-0.5 rounded-md bg-sky-500/10 text-sky-400 border border-sky-500/20">
                    Viendo solo los tuyos
                </span>
                @endif
            </div>
            @can('eventos.crear')
            <button
                @click="modalLead = true"
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

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800/80">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Contacto</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Documento</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Contacto</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Ubicación</th>
                        @if($puedeVerTodos)
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Capturado por</th>
                        @endif
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 w-32"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($evento->leads as $lead)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-semibold text-white">{{ $lead->nombres }}</p>
                            @if($lead->empresa || $lead->rubro)
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                {{ $lead->empresa }}{{ $lead->empresa && $lead->rubro ? ' · ' : '' }}{{ $lead->rubro }}
                            </p>
                            @endif
                            @if($lead->client)
                            <a href="{{ route('clientes.show', $lead->client) }}" class="text-[10px] text-emerald-400 hover:text-emerald-300 mt-0.5 inline-block">
                                Ver cliente →
                            </a>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <p class="text-xs font-mono text-slate-400">
                                {{ $lead->numero_documento ? $lead->tipo_documento . ' ' . $lead->numero_documento : '—' }}
                            </p>
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            <p class="text-xs text-slate-400">{{ $lead->email ?: '—' }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $lead->telefono ?: '' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                            @if($lead->tieneUbicacion())
                            <a href="https://www.openstreetmap.org/?mlat={{ $lead->latitud }}&mlon={{ $lead->longitud }}#map=17/{{ $lead->latitud }}/{{ $lead->longitud }}"
                               target="_blank" rel="noopener"
                               class="text-sky-400 hover:text-sky-300 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                </svg>
                            </a>
                            @else
                            <span class="text-slate-700 text-xs">—</span>
                            @endif
                        </td>
                        @if($puedeVerTodos)
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <p class="text-xs text-slate-400">{{ $lead->createdBy->name ?? '—' }}</p>
                        </td>
                        @endif
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $lead->estadoBadgeClass() }}">
                                {{ $lead->estadoLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('eventos.editar')
                                @if(!$lead->convertido())
                                <form method="POST" action="{{ route('eventos.leads.convertir', [$evento, $lead]) }}"
                                      x-data
                                      @submit.prevent="if(confirm('¿Convertir a {{ addslashes($lead->nombres) }} en cliente?')) $el.submit()">
                                    @csrf
                                    <button type="submit" title="Convertir a cliente"
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-emerald-400 hover:bg-emerald-500/10 transition-colors flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('eventos.leads.edit', [$evento, $lead]) }}" title="Editar"
                                   class="p-1.5 rounded-lg text-slate-600 hover:text-sky-400 hover:bg-sky-500/10 transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('eventos.eliminar')
                                <form method="POST" action="{{ route('eventos.leads.destroy', [$evento, $lead]) }}"
                                      x-data
                                      @submit.prevent="if(confirm('¿Eliminar este lead?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Eliminar"
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-red-400 hover:bg-red-500/10 transition-colors flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $puedeVerTodos ? 7 : 6 }}" class="px-5 py-12 text-center">
                            <p class="text-slate-600 text-sm">
                                @if($puedeVerTodos)
                                    Aún no hay leads registrados en este evento
                                @else
                                    Aún no has registrado leads en este evento
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ══ MODAL NUEVO LEAD ═══════════════════════════════════════ --}}
        @can('eventos.crear')
        <template x-teleport="body">
            <div
                x-show="modalLead"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display:none"
            >
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="modalLead = false"></div>

                <div class="relative min-h-full flex items-start justify-center p-4 pt-12">
                    <div
                        x-show="modalLead"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="w-full max-w-3xl bg-slate-900 border border-slate-700/60
                               rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)] flex flex-col
                               max-h-[calc(100vh-6rem)]"
                        @click.stop
                    >
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80 flex-shrink-0">
                            <div>
                                <h2 class="text-base font-bold text-white">Nuevo lead</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Registra un contacto capturado en {{ $evento->nombre }}</p>
                            </div>
                            <button
                                @click="modalLead = false"
                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                       text-slate-500 hover:text-white hover:bg-slate-800 transition-colors duration-150"
                                aria-label="Cerrar"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="overflow-y-auto flex-1 px-6 py-5">
                            <form method="POST" action="{{ route('eventos.leads.store', $evento) }}" id="form-crear-lead">
                                @csrf
                                {{-- 'lead' => null es obligatorio: sin esto, heredaría por scope el último $lead del @forelse de arriba --}}
                                @include('eventos._lead_form', ['lead' => null])
                            </form>
                        </div>

                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-800/80 flex-shrink-0">
                            <button type="button" @click="modalLead = false"
                                    class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white
                                           border border-slate-700/60 hover:bg-slate-800 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" form="form-crear-lead"
                                    class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                                           bg-gradient-to-r from-sky-500 to-cyan-500
                                           hover:from-sky-400 hover:to-cyan-400
                                           shadow-[0_0_18px_rgba(14,165,233,0.35)]
                                           hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                                           transition-all duration-200 active:scale-[0.98]">
                                Guardar lead
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        @endcan

        {{-- ══ MODAL AGREGAR ASISTENTE ════════════════════════════════ --}}
        @can('eventos.crear')
        <template x-teleport="body">
            <div
                x-show="modalAsistente"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display:none"
            >
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="modalAsistente = false"></div>

                <div class="relative min-h-full flex items-start justify-center p-4 pt-12">
                    <div
                        x-show="modalAsistente"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="w-full max-w-lg bg-slate-900 border border-slate-700/60
                               rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)] flex flex-col
                               max-h-[calc(100vh-6rem)]"
                        @click.stop
                    >
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80 flex-shrink-0">
                            <div>
                                <h2 class="text-base font-bold text-white">Agregar asistente</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Registro manual — se genera su ticket con QR automáticamente</p>
                            </div>
                            <button
                                @click="modalAsistente = false"
                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                       text-slate-500 hover:text-white hover:bg-slate-800 transition-colors duration-150"
                                aria-label="Cerrar"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="overflow-y-auto flex-1 px-6 py-5">
                            <form method="POST" action="{{ route('eventos.asistentes.store', $evento) }}" id="form-crear-asistente" class="space-y-4"
                                  x-data="asistenteBusqueda('{{ route('api.consulta-documento') }}', 'DNI', '', '', '', '')">
                                @csrf
                                <div class="rounded-xl border border-sky-500/30 bg-sky-500/5 p-4">
                                    <div class="flex items-center gap-1.5 mb-3">
                                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                        </svg>
                                        <p class="text-xs font-semibold text-sky-400">Busca el documento y autocompleta los datos</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Tipo doc.</label>
                                            <select name="tipo_documento" x-model="tipoDocumento" @change="onTipoChange()"
                                                    class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                                @foreach(\App\Models\EventAttendee::TIPOS_DOCUMENTO as $tipo)
                                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                                N.º documento
                                                <span x-show="longitud" class="text-slate-600 font-normal" x-text="'(' + longitud + ' dígitos)'"></span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" name="numero_documento" x-model="numeroDocumento"
                                                       @input.debounce.600ms="onNumeroInput()" :maxlength="longitud ?? 20"
                                                       class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm font-mono pr-9 focus:border-sky-500 focus:ring-sky-500/30">
                                                <div class="absolute right-2.5 top-1/2 -translate-y-1/2">
                                                    <svg x-show="buscando" class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                    </svg>
                                                    <svg x-show="encontrado && !buscando" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p x-show="errorBusqueda && !buscando" x-text="errorBusqueda" class="text-amber-400 text-xs mt-2"></p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombres completos *</label>
                                    <input type="text" name="nombres" x-model="nombres" required
                                           class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Empresa</label>
                                    <input type="text" name="empresa" x-model="empresa"
                                           class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Dirección</label>
                                    <input type="text" name="direccion" x-model="direccion"
                                           placeholder="Se autocompleta con RUC, o escríbela manualmente"
                                           class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Correo</label>
                                        <input type="email" name="email"
                                               class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Celular / WhatsApp</label>
                                        <input type="text" name="telefono"
                                               class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-800/80 flex-shrink-0">
                            <button type="button" @click="modalAsistente = false"
                                    class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white
                                           border border-slate-700/60 hover:bg-slate-800 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" form="form-crear-asistente"
                                    class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                                           bg-gradient-to-r from-sky-500 to-cyan-500
                                           hover:from-sky-400 hover:to-cyan-400
                                           shadow-[0_0_18px_rgba(14,165,233,0.35)]
                                           hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                                           transition-all duration-200 active:scale-[0.98]">
                                Registrar asistente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        @endcan

    </div>

</x-app-layout>
