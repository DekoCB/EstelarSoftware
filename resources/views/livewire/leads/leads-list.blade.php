<div x-data="{ colapsados: {} }">
    {{-- Búsqueda ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Buscar por nombre, empresa, teléfono, email..."
                class="w-full bg-slate-800/80 border border-slate-700 text-white placeholder-slate-500
                       rounded-xl pl-9 pr-4 py-2.5 text-sm
                       focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50
                       transition-all duration-200"
            >
        </div>
        <div class="sm:w-56">
            <select
                wire:model.live="respuesta"
                class="w-full bg-slate-800/80 border border-slate-700 text-white
                       rounded-xl px-4 py-2.5 text-sm
                       focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50
                       transition-all duration-200"
            >
                <option value="">Todas las respuestas</option>
                <option value="sin_respuesta">— Sin respuesta —</option>
                @foreach($opcionesRespuesta as $clave => $op)
                    <option value="{{ $clave }}">{{ $op['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($cotizacion)
    <div class="flex items-center gap-2 mb-4">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold
                     bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">
            💰 Mostrando solo: quieren cotización
            <button type="button" wire:click="quitarFiltroCotizacion" class="hover:text-emerald-200 transition-colors" title="Quitar filtro">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </span>
    </div>
    @endif

    {{-- Tabla ───────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">

        <div wire:loading.delay class="relative">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm rounded-2xl z-10
                        flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <svg class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Buscando...
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">#</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Lugar</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Teléfono</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Respuesta</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Seguimiento</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @php $fechaAnterior = null; @endphp
                @forelse($leads as $lead)
                    @php $fechaLead = $lead->created_at->toDateString(); @endphp
                    @if($fechaLead !== $fechaAnterior)
                        @php $fechaAnterior = $fechaLead; @endphp
                        <tr wire:key="fecha-{{ $fechaLead }}"
                            class="bg-sky-500/5 hover:bg-sky-500/10 cursor-pointer select-none transition-colors"
                            @click="colapsados['{{ $fechaLead }}'] = !colapsados['{{ $fechaLead }}']">
                            <td colspan="8" class="px-5 py-2 text-[11px] font-bold text-sky-400 uppercase tracking-wider">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-3 h-3 transition-transform duration-150 flex-shrink-0"
                                         :class="colapsados['{{ $fechaLead }}'] ? '-rotate-90' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                    {{ $lead->created_at->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                                </span>
                            </td>
                        </tr>
                    @endif
                <tr wire:key="lead-{{ $lead->id }}" x-show="!colapsados['{{ $fechaLead }}']" class="hover:bg-slate-800/30 transition-colors duration-150 group">
                    <td class="px-5 py-4 text-slate-500 font-mono">
                        {{ $loop->iteration + ($leads->currentPage() - 1) * $leads->perPage() }}
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-medium text-white">{{ $lead->nombre }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @if($lead->origen === \App\Models\Lead::ORIGEN_WEB)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/15 text-emerald-400">
                                Demo Gratuita
                            </span>
                        @else
                            <span class="text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell text-slate-400">
                        {{ $lead->lugar ?? '—' }}
                    </td>
                    <td class="px-5 py-4 text-slate-400 whitespace-nowrap">
                        {{ $lead->telefono ?? '—' }}
                    </td>
                    <td class="px-5 py-4">
                        @if($lead->respuesta_rapida)
                            @php
                                $c = $lead->respuestaColor();
                                $esReunionVencida = false;
                                if (in_array($lead->respuesta_rapida, ['volver_a_llamar', 'reunion_pendiente']) && $lead->respuesta_fecha && $lead->respuesta_hora && !$lead->reunion_realizada) {
                                    $fechaHoraReunion = \Carbon\Carbon::parse($lead->respuesta_fecha->format('Y-m-d') . ' ' . $lead->respuesta_hora);
                                    $esReunionVencida = $fechaHoraReunion->isPast();
                                }
                            @endphp
                            <div class="flex flex-col items-start gap-1 {{ $esReunionVencida ? 'opacity-50' : '' }}">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-{{ $c }}-500/15 text-{{ $c }}-400"
                                      title="{{ $lead->respuesta_comentario }}">
                                    {{ $lead->respuestaLabel() }}
                                </span>
                                @if(in_array($lead->respuesta_rapida, ['volver_a_llamar', 'reunion_pendiente']) && $lead->respuesta_fecha)
                                    <p class="text-sm text-slate-400 mt-1 font-mono font-semibold">
                                        {{ $lead->respuesta_fecha->format('d/m') }} {{ $lead->respuesta_hora ? substr($lead->respuesta_hora, 0, 5) : '' }}
                                    </p>
                                @endif
                                @if($lead->quiere_cotizacion && $lead->ruc_dni)
                                    <p class="text-xs text-emerald-400/80 mt-1 font-mono">RUC/DNI: {{ $lead->ruc_dni }}</p>
                                @endif
                            </div>
                        @else
                        <span class="text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($lead->reunion_realizada || $lead->quiere_cotizacion)
                            <div class="flex flex-col items-start gap-1">
                                @if($lead->reunion_realizada)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-500/15 text-sky-400">
                                        ✅ Reunión realizada
                                    </span>
                                @endif
                                @if($lead->quiere_cotizacion)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/15 text-emerald-400">
                                        💰 Quiere cotización
                                    </span>
                                @endif
                                @if($lead->quiere_cotizacion && $lead->cotizacion_enviada)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-500/15 text-violet-400">
                                        📨 Cotización enviada
                                    </span>
                                @endif
                            </div>
                        @else
                        <span class="text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('leads.show', $lead) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-sky-400 hover:bg-sky-500/10
                                      transition-colors duration-150"
                               title="Ver detalle">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </a>
                            @can('leads.editar')
                            <a href="{{ route('leads.edit', $lead) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10
                                      transition-colors duration-150"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </a>
                            @if(in_array($lead->respuesta_rapida, \App\Models\Lead::RESPUESTAS_CON_COTIZACION))
                            <div x-data="{
                                    open: false,
                                    quiere: {{ $lead->quiere_cotizacion ? 'true' : 'false' }},
                                    rucDni: {{ Illuminate\Support\Js::from($lead->ruc_dni ?? '') }},
                                    reunion: {{ $lead->reunion_realizada ? 'true' : 'false' }},
                                    enviada: {{ $lead->cotizacion_enviada ? 'true' : 'false' }},
                                }">
                                <button type="button" @click="open = true"
                                        class="p-1.5 rounded-lg transition-colors duration-150"
                                        :class="quiere ? 'text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20' : 'text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/10'"
                                        title="Seguimiento / cotización">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m3-8.25c0-1.243-1.343-2.25-3-2.25s-3 1.007-3 2.25 1.343 2.25 3 2.25 3 1.007 3 2.25-1.343 2.25-3 2.25-3-1.007-3-2.25"/>
                                    </svg>
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
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display:none"
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
                                                class="w-full max-w-sm bg-slate-900 border border-slate-700/60 rounded-2xl
                                                       shadow-[0_0_80px_rgba(0,0,0,0.6)] p-6 text-left normal-case font-normal"
                                                @click.stop
                                            >
                                                <form method="POST" action="{{ route('leads.cotizacion.update', $lead) }}">
                                                    @csrf @method('PATCH')
                                                    <p class="text-sm font-semibold text-white mb-4">Seguimiento de {{ $lead->nombre }}</p>

                                                    <label class="flex items-center gap-2.5 cursor-pointer mb-4 pb-4 border-b border-slate-800/80">
                                                        <button type="button" role="switch" :aria-checked="reunion.toString()" @click="reunion = !reunion"
                                                                class="relative w-9 h-5 rounded-full transition-colors duration-200 flex-shrink-0"
                                                                :class="reunion ? 'bg-sky-500' : 'bg-slate-700'">
                                                            <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform duration-200"
                                                                  :class="reunion ? 'translate-x-4' : 'translate-x-0'"></span>
                                                        </button>
                                                        <span class="text-sm" :class="reunion ? 'text-sky-400 font-semibold' : 'text-slate-400'"
                                                              x-text="reunion ? 'Reunión realizada' : 'Reunión no realizada'"></span>
                                                    </label>

                                                    <label class="flex items-center gap-2.5 cursor-pointer mb-4">
                                                        <button type="button" role="switch" :aria-checked="quiere.toString()" @click="quiere = !quiere"
                                                                class="relative w-9 h-5 rounded-full transition-colors duration-200 flex-shrink-0"
                                                                :class="quiere ? 'bg-emerald-500' : 'bg-slate-700'">
                                                            <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform duration-200"
                                                                  :class="quiere ? 'translate-x-4' : 'translate-x-0'"></span>
                                                        </button>
                                                        <span class="text-sm" :class="quiere ? 'text-emerald-400 font-semibold' : 'text-slate-400'"
                                                              x-text="quiere ? 'Quiere cotización' : 'No quiere cotización'"></span>
                                                    </label>

                                                    <div x-show="quiere" class="space-y-4">
                                                        <div>
                                                            <label class="text-[11px] text-slate-500">RUC / DNI</label>
                                                            <input type="text" name="ruc_dni" x-model="rucDni" maxlength="11" inputmode="numeric"
                                                                   class="w-full bg-slate-800/80 border border-slate-700 text-white
                                                                          rounded-lg px-3 py-2 text-sm mt-1 font-mono
                                                                          focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50"
                                                                   placeholder="8 u 11 dígitos">
                                                            @error('ruc_dni')
                                                                <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>

                                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                                            <button type="button" role="switch" :aria-checked="enviada.toString()" @click="enviada = !enviada"
                                                                    class="relative w-9 h-5 rounded-full transition-colors duration-200 flex-shrink-0"
                                                                    :class="enviada ? 'bg-violet-500' : 'bg-slate-700'">
                                                                <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform duration-200"
                                                                      :class="enviada ? 'translate-x-4' : 'translate-x-0'"></span>
                                                            </button>
                                                            <span class="text-sm" :class="enviada ? 'text-violet-400 font-semibold' : 'text-slate-400'"
                                                                  x-text="enviada ? 'Cotización enviada' : 'Cotización no enviada'"></span>
                                                        </label>
                                                    </div>

                                                    <input type="hidden" name="quiere_cotizacion" :value="quiere ? 1 : 0">
                                                    <input type="hidden" name="reunion_realizada" :value="reunion ? 1 : 0">
                                                    <input type="hidden" name="cotizacion_enviada" :value="quiere && enviada ? 1 : 0">

                                                    <div class="flex items-center gap-3 mt-5">
                                                        <button type="button" @click="open = false"
                                                                class="flex-1 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white
                                                                       border border-slate-700/60 hover:bg-slate-800 transition-colors">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit"
                                                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white
                                                                       bg-emerald-500 hover:bg-emerald-400 transition-colors">
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            @endif
                            @endcan
                            @can('leads.eliminar')
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}"
                                  x-data
                                  @submit.prevent="if(confirm('¿Eliminar a {{ addslashes($lead->nombre) }}?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10
                                               transition-colors duration-150"
                                        title="Eliminar">
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
                    <td colspan="8" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-600">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                            </svg>
                            <p class="text-sm">No se encontraron leads</p>
                            @if($search || $respuesta !== '')
                            <button wire:click="$set('search',''); $set('respuesta','')"
                                    class="text-xs text-sky-400 hover:text-sky-300 transition-colors">
                                Limpiar filtros
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($leads->hasPages())
        <div class="px-5 py-4 border-t border-slate-800/60">
            {{ $leads->links() }}
        </div>
        @endif
    </div>

    <p class="text-xs text-slate-700 mt-3 font-mono">
        {{ $leads->total() }} lead(s) encontrado(s)
    </p>
</div>
