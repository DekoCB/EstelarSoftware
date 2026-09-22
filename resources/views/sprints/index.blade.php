<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('proyectos.index') }}" class="text-slate-600 hover:text-slate-400 font-mono">Proyectos</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="text-slate-600 hover:text-slate-400 truncate max-w-[150px]">{{ $proyecto->name }}</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Sprints</span>
        </div>
    </x-slot>

    <div x-data="{ modalSprint: false }">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Sprints</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $proyecto->name }}</p>
            </div>
            @can('sprints.gestionar')
            <button @click="modalSprint = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                           shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                           transition-all active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo sprint
            </button>
            @endcan
        </div>

        {{-- Lista de sprints --}}
        <div class="space-y-3">
            @forelse($proyecto->sprints->sortByDesc('created_at') as $sprint)
            @php $pct = $sprint->porcentajeCompletado(); @endphp
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 hover:border-slate-700/60 transition-colors">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-sm font-semibold text-white">{{ $sprint->name }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold {{ $sprint->statusBadgeClass() }}">
                                    {{ $sprint->statusLabel() }}
                                </span>
                            </div>
                            @if($sprint->goal)
                            <p class="text-xs text-slate-500 mt-1 max-w-lg">{{ $sprint->goal }}</p>
                            @endif
                            <div class="flex items-center gap-4 mt-2 text-xs text-slate-600 font-mono">
                                @if($sprint->start_date)
                                <span>{{ $sprint->start_date->format('d/m/Y') }} → {{ $sprint->end_date?->format('d/m/Y') ?? '?' }}</span>
                                @endif
                                <span>{{ $sprint->requirements->count() }} tareas</span>
                                <span class="text-sky-500">{{ $sprint->velocityCompletado() }}/{{ $sprint->velocityTotal() }} pts</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        {{-- Progreso circular --}}
                        <div class="text-center">
                            <div class="text-lg font-bold font-mono {{ $pct >= 100 ? 'text-emerald-400' : 'text-sky-400' }}">{{ $pct }}%</div>
                            <div class="text-[10px] text-slate-600">completado</div>
                        </div>
                        <a href="{{ route('sprints.show', [$proyecto, $sprint]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                                  bg-slate-800 text-slate-300 border border-slate-700/60
                                  hover:border-sky-500/30 hover:text-sky-400 transition-all">
                            Ver board
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Barra progreso --}}
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex-1 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700
                            {{ $pct >= 100 ? 'bg-emerald-400' : 'bg-sky-400' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-12 text-center">
                <svg class="w-10 h-10 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                </svg>
                <p class="text-slate-600 text-sm">Sin sprints creados</p>
                @can('sprints.gestionar')
                <p class="text-xs text-slate-700 mt-1">Crea el primer sprint para comenzar</p>
                @endcan
            </div>
            @endforelse
        </div>

        {{-- Modal nuevo sprint --}}
        @can('sprints.gestionar')
        <template x-teleport="body">
            <div x-show="modalSprint"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="modalSprint = false"></div>
                <div class="relative min-h-full flex items-center justify-center p-4">
                    <div class="w-full max-w-lg bg-slate-900 border border-slate-700/60 rounded-2xl shadow-2xl"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         @click.stop>
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80">
                            <h3 class="text-base font-bold text-white">Nuevo sprint</h3>
                            <button @click="modalSprint = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('sprints.store', $proyecto) }}" class="p-6 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombre del sprint <span class="text-red-400">*</span></label>
                                <input type="text" name="name" class="input-dark" placeholder="Ej. Sprint 1 — Autenticación" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Objetivo del sprint</label>
                                <textarea name="goal" rows="2" class="input-dark resize-none" placeholder="¿Qué queremos lograr en este sprint?"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Inicio</label>
                                    <input type="date" name="start_date" class="input-dark font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Fin</label>
                                    <input type="date" name="end_date" class="input-dark font-mono">
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="modalSprint = false" class="px-4 py-2.5 text-sm text-slate-400 hover:text-white border border-slate-700/60 rounded-xl hover:bg-slate-800 transition-colors">Cancelar</button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 transition-all active:scale-[0.98]">Crear sprint</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
        @endcan

    </div>
</x-app-layout>
