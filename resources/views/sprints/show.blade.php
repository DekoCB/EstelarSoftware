<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('proyectos.index') }}" class="text-slate-600 hover:text-slate-400 font-mono">Proyectos</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="text-slate-600 hover:text-slate-400 truncate max-w-[120px]">{{ $proyecto->name }}</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('sprints.index', $proyecto) }}" class="text-slate-600 hover:text-slate-400">Sprints</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold truncate max-w-[120px]">{{ $sprint->name }}</span>
        </div>
    </x-slot>

    {{-- Header del sprint --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                    <h1 class="text-lg font-bold text-white">{{ $sprint->name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $sprint->statusBadgeClass() }}">
                        {{ $sprint->statusLabel() }}
                    </span>
                </div>
                @if($sprint->goal)
                <p class="text-sm text-slate-400 mb-2">{{ $sprint->goal }}</p>
                @endif
                <div class="flex items-center gap-4 text-xs text-slate-600 font-mono flex-wrap">
                    @if($sprint->start_date)
                    <span>{{ $sprint->start_date->format('d/m/Y') }} — {{ $sprint->end_date?->format('d/m/Y') ?? 'sin fecha' }}</span>
                    @endif
                    <span class="text-sky-400">{{ $sprint->velocityCompletado() }} / {{ $sprint->velocityTotal() }} pts completados</span>
                    <span>{{ $sprint->porcentajeCompletado() }}% hecho</span>
                </div>
            </div>

            @can('sprints.gestionar')
            <div x-data="{ menuStatus: false }" class="relative flex-shrink-0">
                <button @click="menuStatus = !menuStatus"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                               bg-slate-800 text-slate-300 border border-slate-700/60
                               hover:border-sky-500/30 hover:text-sky-400 transition-all">
                    Cambiar estado
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div x-show="menuStatus" @click.outside="menuStatus = false"
                     class="absolute right-0 top-11 z-20 bg-slate-800 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl w-40"
                     style="display:none">
                    @foreach(['planificacion' => 'Planificación', 'activo' => 'Activo', 'completado' => 'Completado', 'cancelado' => 'Cancelado'] as $s => $label)
                    @if($s !== $sprint->status)
                    <form method="POST" action="{{ route('sprints.update', [$proyecto, $sprint]) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $s }}">
                        <button type="submit" class="w-full text-left px-3 py-2.5 text-xs text-slate-300 hover:bg-slate-700/60 transition-colors">
                            {{ $label }}
                        </button>
                    </form>
                    @endif
                    @endforeach
                </div>
            </div>
            @endcan
        </div>

        {{-- Barra velocidad --}}
        @if($sprint->velocityTotal() > 0)
        <div class="mt-4 pt-4 border-t border-slate-800/60">
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-slate-800 rounded-full h-2 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-emerald-400 transition-all duration-700"
                         style="width: {{ $sprint->porcentajeCompletado() }}%"></div>
                </div>
                <span class="text-xs font-mono text-slate-500">
                    {{ $sprint->velocityCompletado() }}/{{ $sprint->velocityTotal() }} pts
                </span>
            </div>
        </div>
        @endif
    </div>

    {{-- Layout: board + panel derecho --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Sprint Board (Livewire) --}}
        <div class="xl:col-span-2">
            <h3 class="text-sm font-semibold text-white mb-3">Board del sprint</h3>
            @livewire('sprints.sprint-board', ['sprint' => $sprint])
        </div>

        {{-- Panel derecho: Backlog + Daily --}}
        <div x-data="{ panelTab: 'backlog' }" class="space-y-4">

            {{-- Tabs del panel --}}
            <div class="flex gap-1 bg-slate-900/60 border border-slate-800/60 rounded-xl p-1">
                <button @click="panelTab = 'backlog'"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition-all"
                        :class="panelTab === 'backlog' ? 'bg-sky-500/15 text-sky-400' : 'text-slate-500 hover:text-slate-300'">
                    Backlog ({{ $backlog->count() }})
                </button>
                <button @click="panelTab = 'daily'"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition-all"
                        :class="panelTab === 'daily' ? 'bg-sky-500/15 text-sky-400' : 'text-slate-500 hover:text-slate-300'">
                    Daily
                </button>
            </div>

            {{-- Tab: Backlog sin asignar --}}
            <div x-show="panelTab === 'backlog'" class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800/60">
                    <p class="text-xs font-semibold text-slate-400">Tareas sin sprint asignado</p>
                </div>
                <div class="p-2 space-y-1.5 max-h-[420px] overflow-y-auto">
                    @forelse($backlog as $req)
                    <div class="flex items-center justify-between gap-2 bg-slate-800/40 border border-slate-700/30 rounded-xl px-3 py-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-white truncate">{{ $req->title }}</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="text-[10px] px-1.5 py-0.5 rounded-md {{ $req->priorityBadge() }} capitalize">{{ $req->priority }}</span>
                                @if($req->story_points)
                                <span class="text-[10px] font-mono text-sky-400">{{ $req->story_points }}pt</span>
                                @endif
                            </div>
                        </div>
                        @can('sprints.gestionar')
                        <form method="POST" action="{{ route('requerimientos.asignar-sprint', [$proyecto, $req]) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="sprint_id" value="{{ $sprint->id }}">
                            <button type="submit"
                                    class="p-1.5 rounded-lg text-slate-600 hover:text-sky-400 hover:bg-sky-500/10 transition-colors flex-shrink-0"
                                    title="Agregar a este sprint">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                    @empty
                    <p class="text-xs text-slate-700 text-center py-6">Todo el backlog está en sprints</p>
                    @endforelse
                </div>
            </div>

            {{-- Tab: Daily Standups --}}
            <div x-show="panelTab === 'daily'" class="space-y-3">

                {{-- Formulario daily del usuario actual --}}
                @can('sprints.daily')
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-4">
                    <p class="text-xs font-semibold text-white mb-3">
                        Mi standup — {{ today()->format('d/m/Y') }}
                        @if($dailyHoy)
                        <span class="text-emerald-400 ml-2">✓ registrado</span>
                        @endif
                    </p>
                    <form method="POST" action="{{ route('sprints.daily.store', [$proyecto, $sprint]) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1 uppercase tracking-wide">¿Qué hice ayer?</label>
                            <textarea name="yesterday" rows="2" required
                                      class="input-dark text-xs resize-none"
                                      placeholder="Tareas completadas ayer...">{{ $dailyHoy?->yesterday }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1 uppercase tracking-wide">¿Qué haré hoy?</label>
                            <textarea name="today" rows="2" required
                                      class="input-dark text-xs resize-none"
                                      placeholder="Plan para hoy...">{{ $dailyHoy?->today }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1 uppercase tracking-wide">¿Bloqueos?</label>
                            <textarea name="blockers" rows="1"
                                      class="input-dark text-xs resize-none"
                                      placeholder="Ninguno / describe el bloqueo...">{{ $dailyHoy?->blockers }}</textarea>
                        </div>
                        <button type="submit"
                                class="w-full py-2 rounded-xl text-xs font-semibold text-white
                                       bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400
                                       transition-all active:scale-[0.98]">
                            {{ $dailyHoy ? 'Actualizar standup' : 'Registrar standup' }}
                        </button>
                    </form>
                </div>
                @endcan

                {{-- Dailies del equipo (solo admin/gestionar) --}}
                @can('sprints.gestionar')
                @php
                    $reportesHoy = $sprint->dailyReports()
                        ->whereDate('date', today())
                        ->with('user')
                        ->get();
                @endphp
                @if($reportesHoy->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-800/60">
                        <p class="text-xs font-semibold text-slate-400">Standups de hoy — equipo</p>
                    </div>
                    <div class="divide-y divide-slate-800/60">
                        @foreach($reportesHoy as $rep)
                        <div class="px-4 py-3">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-[8px] font-bold text-sky-400 uppercase">
                                    {{ substr($rep->user->name, 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-white">{{ $rep->user->name }}</span>
                            </div>
                            <div class="space-y-1.5 text-xs">
                                <div>
                                    <span class="text-slate-600">Ayer: </span>
                                    <span class="text-slate-400">{{ $rep->yesterday }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-600">Hoy: </span>
                                    <span class="text-slate-400">{{ $rep->today }}</span>
                                </div>
                                @if($rep->blockers)
                                <div class="flex items-start gap-1">
                                    <span class="text-amber-500 flex-shrink-0">⚠</span>
                                    <span class="text-amber-400/80">{{ $rep->blockers }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endcan

            </div>
        </div>
    </div>

</x-app-layout>
