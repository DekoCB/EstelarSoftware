<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('leads.index') }}" class="text-slate-600 hover:text-slate-400 transition-colors font-mono">Leads</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">{{ $lead->nombre }}</span>
        </div>
    </x-slot>

    @php
        $proximas = $lead->meetings->filter(fn($m) => $m->fecha_hora->isFuture())->sortBy('fecha_hora');
        $pasadas  = $lead->meetings->filter(fn($m) => ! $m->fecha_hora->isFuture())->sortByDesc('fecha_hora');
    @endphp

    <div x-data="{ modalReunion: false }" class="max-w-4xl space-y-6">

        {{-- ── Header ─────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-white">{{ $lead->nombre }}</h2>
                    @if($lead->origen === \App\Models\Lead::ORIGEN_WEB)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-sky-500/15 text-sky-400">
                            Sitio web
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-0.5">{{ $lead->lugar ?? 'Sin lugar registrado' }}</p>
            </div>
            <div class="flex items-center gap-2">
                @can('leads.editar')
                <a href="{{ route('leads.edit', $lead) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                          text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white
                          transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                    </svg>
                    Editar
                </a>
                @endcan
                @can('leads.eliminar')
                <form method="POST" action="{{ route('leads.destroy', $lead) }}"
                      x-data
                      @submit.prevent="if(confirm('¿Eliminar a {{ addslashes($lead->nombre) }}?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   text-red-400 border border-red-500/30 hover:bg-red-500/10
                                   transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>

        {{-- ── Datos de contacto ─────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Datos de contacto</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Empresa</p>
                    <p class="text-slate-200">{{ $lead->empresa ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Email</p>
                    <p class="text-slate-200">{{ $lead->email ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Lugar</p>
                    <p class="text-slate-200">{{ $lead->lugar ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Teléfono</p>
                    <p class="text-slate-200 font-mono">{{ $lead->telefono ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Área de interés</p>
                    <p class="text-slate-200">{{ $lead->sistema_interes ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Respuesta</p>
                    @if($lead->respuesta_rapida)
                        @php $c = $lead->respuestaColor(); @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-{{ $c }}-500/15 text-{{ $c }}-400">
                            {{ $lead->respuestaLabel() }}
                        </span>
                    @else
                        <p class="text-slate-200">—</p>
                    @endif
                </div>
            </div>
            @if($lead->mensaje)
            <div class="mt-4 pt-4 border-t border-slate-800/60">
                <p class="text-xs text-slate-500 mb-1">Mensaje del formulario web</p>
                <p class="text-slate-300 text-sm leading-relaxed">{{ $lead->mensaje }}</p>
            </div>
            @endif
            @if($lead->respuesta_comentario)
            <div class="mt-4 pt-4 border-t border-slate-800/60">
                <p class="text-xs text-slate-500 mb-1">Comentario</p>
                <p class="text-slate-300 text-sm leading-relaxed">{{ $lead->respuesta_comentario }}</p>
            </div>
            @endif
        </div>

        {{-- ── Reuniones ──────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-white">Reuniones</h3>
                @can('leads.editar')
                <button @click="modalReunion = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                               bg-sky-500/10 border border-sky-500/30 text-sky-400 hover:bg-sky-500/20
                               transition-colors duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Agendar reunión
                </button>
                @endcan
            </div>

            {{-- Form inline para agendar (colapsado por defecto) --}}
            @can('leads.editar')
            <div x-show="modalReunion" x-cloak x-transition class="mb-5 p-4 bg-slate-950/50 border border-slate-800 rounded-xl">
                <form method="POST" action="{{ route('leads.reuniones.store', $lead) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    @csrf
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha y hora <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="fecha_hora" class="input-dark @error('fecha_hora') error @enderror" value="{{ old('fecha_hora') }}">
                        @error('fecha_hora')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Nota</label>
                        <input type="text" name="nota" class="input-dark" placeholder="Ej. Llamada de seguimiento" value="{{ old('nota') }}">
                    </div>
                    <div class="sm:col-span-4 flex items-center justify-end gap-2">
                        <button type="button" @click="modalReunion = false"
                                class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white
                                       border border-slate-700/60 hover:bg-slate-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-semibold text-white
                                       bg-gradient-to-r from-sky-500 to-cyan-500
                                       hover:from-sky-400 hover:to-cyan-400 transition-all duration-200">
                            Guardar reunión
                        </button>
                    </div>
                </form>
            </div>
            @endcan

            {{-- Próximas --}}
            <div class="mb-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Próximas reuniones</p>
                @forelse($proximas as $reunion)
                <div x-data="{ editando: false }" class="rounded-xl bg-sky-500/5 border border-sky-500/20 mb-2">
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="text-sm text-white font-medium">{{ $reunion->fecha_hora->format('d/m/Y H:i') }}</p>
                            @if($reunion->nota)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $reunion->nota }}</p>
                            @endif
                            @if($reunion->recordatorio_minutos)
                            @php
                                $labels = ['5' => '5 min antes', '10' => '10 min antes', '15' => '15 min antes', '30' => '30 min antes', '60' => '1 hora antes', '1440' => '1 día antes'];
                            @endphp
                            <p class="text-xs text-amber-400/80 mt-0.5">
                                🔔 Recordatorio {{ $labels[(string) $reunion->recordatorio_minutos] ?? $reunion->recordatorio_minutos . ' min antes' }}
                            </p>
                            @endif
                        </div>
                        @can('leads.editar')
                        <div class="flex items-center gap-1">
                            <button type="button" @click="editando = !editando" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10 transition-colors duration-150" title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('leads.reuniones.destroy', [$lead, $reunion]) }}"
                                  x-data
                                  @submit.prevent="if(confirm('¿Eliminar esta reunión?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors duration-150" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                    @can('leads.editar')
                    <div x-show="editando" x-cloak x-transition class="px-4 pb-4">
                        <form method="POST" action="{{ route('leads.reuniones.update', [$lead, $reunion]) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-sky-500/10">
                            @csrf @method('PUT')
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha y hora</label>
                                <input type="datetime-local" name="fecha_hora" class="input-dark" value="{{ $reunion->fecha_hora->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Nota</label>
                                <input type="text" name="nota" class="input-dark" value="{{ $reunion->nota }}">
                            </div>
                            <div class="sm:col-span-4 flex items-center justify-end gap-2">
                                <button type="button" @click="editando = false"
                                        class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white
                                               border border-slate-700/60 hover:bg-slate-800 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 rounded-xl text-xs font-semibold text-white
                                               bg-gradient-to-r from-sky-500 to-cyan-500
                                               hover:from-sky-400 hover:to-cyan-400 transition-all duration-200">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                    @endcan
                </div>
                @empty
                <p class="text-sm text-slate-600">No hay reuniones próximas.</p>
                @endforelse
            </div>

            {{-- Pasadas --}}
            @if($pasadas->isNotEmpty())
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Reuniones pasadas</p>
                @foreach($pasadas as $reunion)
                <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-slate-800/30 border border-slate-800 mb-2">
                    <div>
                        <p class="text-sm text-slate-300">{{ $reunion->fecha_hora->format('d/m/Y H:i') }}</p>
                        @if($reunion->nota)
                        <p class="text-xs text-slate-600 mt-0.5">{{ $reunion->nota }}</p>
                        @endif
                    </div>
                    @can('leads.editar')
                    <form method="POST" action="{{ route('leads.reuniones.destroy', [$lead, $reunion]) }}"
                          x-data
                          @submit.prevent="if(confirm('¿Eliminar esta reunión?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-slate-600 hover:text-red-400 hover:bg-red-500/10 transition-colors duration-150" title="Eliminar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                    </form>
                    @endcan
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

</x-app-layout>
