<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('solicitudes.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white">{{ $solicitude->titulo }}</h1>
                <p class="text-sm text-slate-400 mt-0.5">Solicitud #{{ $solicitude->id }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-4">

        {{-- Tarjeta principal --}}
        <div class="card p-6 space-y-4">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500
                                flex items-center justify-center text-sm font-bold text-white uppercase flex-shrink-0">
                        {{ substr($solicitude->solicitante->name, 0, 2) }}
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ $solicitude->solicitante->name }}</p>
                        <p class="text-xs text-slate-400">{{ $solicitude->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @php $color = $solicitude->estadoColor(); @endphp
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg
                             bg-{{ $color }}-500/15 text-{{ $color }}-400 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-400"></span>
                    {{ $solicitude->estadoLabel() }}
                </span>
            </div>

            <div class="border-t border-slate-800/60 pt-4 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Tipo</p>
                    @if($solicitude->tipo === 'dinero')
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Dinero
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-purple-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                            Objeto
                        </span>
                    @endif
                </div>
                @if($solicitude->monto)
                <div>
                    <p class="text-xs text-slate-500 mb-1">Monto</p>
                    <p class="text-lg font-bold text-white font-mono">S/ {{ number_format($solicitude->monto, 2) }}</p>
                </div>
                @endif
            </div>

            <div class="border-t border-slate-800/60 pt-4">
                <p class="text-xs text-slate-500 mb-1.5">Descripción</p>
                <p class="text-slate-300 leading-relaxed whitespace-pre-line">{{ $solicitude->descripcion }}</p>
            </div>

            {{-- Respuesta admin --}}
            @if($solicitude->respuesta_admin)
            <div class="border-t border-slate-800/60 pt-4">
                <p class="text-xs text-slate-500 mb-1.5">Respuesta del administrador</p>
                <div class="bg-slate-800/50 rounded-xl p-3 text-slate-300 text-sm leading-relaxed">
                    {{ $solicitude->respuesta_admin }}
                </div>
                @if($solicitude->atendioPor)
                <p class="text-xs text-slate-500 mt-2">
                    Por {{ $solicitude->atendioPor->name }} · {{ $solicitude->atendido_at->format('d/m/Y H:i') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- Acciones del gestor --}}
        @can('solicitudes.gestionar')
            @if($solicitude->estado === 'pendiente')
            <div class="card p-5" x-data="{ accion: null }">
                <p class="text-sm font-semibold text-slate-300 mb-4">Gestionar solicitud</p>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button @click="accion = 'aprobar'"
                            :class="accion === 'aprobar' ? 'bg-sky-500/20 border-sky-500 text-sky-400' : 'border-slate-700 text-slate-400 hover:border-slate-600'"
                            class="flex items-center justify-center gap-2 p-3 rounded-xl border text-sm font-semibold transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Aprobar
                    </button>
                    <button @click="accion = 'rechazar'"
                            :class="accion === 'rechazar' ? 'bg-red-500/20 border-red-500 text-red-400' : 'border-slate-700 text-slate-400 hover:border-slate-600'"
                            class="flex items-center justify-center gap-2 p-3 rounded-xl border text-sm font-semibold transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        Rechazar
                    </button>
                </div>

                {{-- Form aprobar --}}
                <form x-show="accion === 'aprobar'" x-transition
                      method="POST" action="{{ route('solicitudes.aprobar', $solicitude) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="respuesta_admin" rows="2" placeholder="Nota opcional para el solicitante..."
                              class="input-dark w-full resize-none text-sm"></textarea>
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white transition-colors">
                        Confirmar aprobación
                    </button>
                </form>

                {{-- Form rechazar --}}
                <form x-show="accion === 'rechazar'" x-transition
                      method="POST" action="{{ route('solicitudes.rechazar', $solicitude) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="respuesta_admin" rows="2" placeholder="Explica el motivo del rechazo (requerido)..."
                              class="input-dark w-full resize-none text-sm" required></textarea>
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl text-sm font-semibold bg-red-500 hover:bg-red-400 text-white transition-colors">
                        Confirmar rechazo
                    </button>
                </form>
            </div>
            @endif

            @if($solicitude->estado === 'aprobado')
            <form method="POST" action="{{ route('solicitudes.entregar', $solicitude) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-3 rounded-xl text-sm font-semibold
                               bg-emerald-500/20 border border-emerald-500/40 text-emerald-400
                               hover:bg-emerald-500/30 transition-colors duration-150">
                    Marcar como entregado
                </button>
            </form>
            @endif
        @endcan

        {{-- Cancelar (propio + pendiente) --}}
        @if($solicitude->user_id === auth()->id() && $solicitude->estado === 'pendiente')
        <form method="POST" action="{{ route('solicitudes.destroy', $solicitude) }}"
              onsubmit="return confirm('¿Cancelar esta solicitud?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="text-sm text-slate-500 hover:text-red-400 transition-colors">
                Cancelar solicitud
            </button>
        </form>
        @endif

    </div>
</x-app-layout>
