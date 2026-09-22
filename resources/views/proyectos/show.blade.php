<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('proyectos.index') }}" class="text-slate-600 hover:text-slate-400 font-mono">Proyectos</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold truncate max-w-[240px]">{{ $proyecto->name }}</span>
        </div>
    </x-slot>

    {{-- Header del proyecto --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-white">{{ $proyecto->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $proyecto->statusBadgeClass() }}">
                            {{ $proyecto->statusLabel() }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $proyecto->client->razon_social }}</p>
                    @if($proyecto->description)
                    <p class="text-sm text-slate-400 mt-2 max-w-2xl leading-relaxed">{{ $proyecto->description }}</p>
                    @endif
                </div>
            </div>

            <div class="flex gap-2 flex-shrink-0">
                @can('sprints.ver')
                <a href="{{ route('sprints.index', $proyecto) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-sky-500/30 hover:text-sky-400 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                    Sprints
                </a>
                @endcan
                @can('requerimientos.ver')
                <a href="{{ route('requerimientos.index', $proyecto) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-violet-500/30 hover:text-violet-400 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                    </svg>
                    Requerimientos
                </a>
                @endcan
                @can('proyectos.editar')
                <a href="{{ route('proyectos.edit', $proyecto) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-sky-500/30 hover:text-sky-400 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                    </svg>
                    Editar
                </a>
                @endcan
            </div>
        </div>

        {{-- Barra de progreso general --}}
        <div class="mt-5 pt-5 border-t border-slate-800/60">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Progreso general</span>
                <span class="text-sm font-bold font-mono {{ $proyecto->progress >= 100 ? 'text-emerald-400' : 'text-sky-400' }}">
                    {{ $proyecto->progress }}%
                </span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700
                    {{ $proyecto->progress >= 100 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : 'bg-gradient-to-r from-sky-500 to-cyan-400' }}"
                     style="width: {{ $proyecto->progress }}%">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Fases --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Fases del proyecto</h3>
                @can('proyectos.editar')
                <button
                    x-data
                    @click="$dispatch('open-modal-fase')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                           text-sky-400 bg-sky-500/10 border border-sky-500/20 hover:bg-sky-500/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Nueva fase
                </button>
                @endcan
            </div>

            @forelse($proyecto->phases as $fase)
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5"
                 x-data="{ editando: false, progreso: {{ $fase->progress }}, status: '{{ $fase->status }}' }">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-md bg-slate-800 flex items-center justify-center text-xs font-mono text-slate-500 flex-shrink-0">
                            {{ $loop->iteration }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-white">{{ $fase->name }}</p>
                            @if($fase->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $fase->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-lg {{ $fase->statusBadgeClass() }} capitalize">
                            {{ str_replace('_',' ',$fase->status) }}
                        </span>
                        @can('proyectos.editar')
                        <button @click="editando = !editando"
                                class="p-1.5 rounded-lg text-slate-600 hover:text-sky-400 hover:bg-sky-500/10 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/>
                            </svg>
                        </button>
                        @endcan
                    </div>
                </div>

                {{-- Barra de progreso de la fase --}}
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                            {{ $fase->progress >= 100 ? 'bg-emerald-400' : 'bg-sky-400' }}"
                             :style="`width: ${progreso}%`">
                        </div>
                    </div>
                    <span class="text-xs font-mono text-slate-400 w-8 text-right" x-text="`${progreso}%`"></span>
                </div>

                {{-- Formulario inline para editar progreso --}}
                @can('proyectos.editar')
                <form x-show="editando"
                      method="POST"
                      action="{{ route('proyectos.fases.update', [$proyecto, $fase]) }}"
                      class="border-t border-slate-800/60 pt-3 mt-3 space-y-3">
                    @csrf @method('PATCH')
                    <div class="flex items-center gap-3">
                        <label class="text-xs text-slate-500 w-20 flex-shrink-0">Progreso</label>
                        <input type="range" name="progress" min="0" max="100" step="5"
                               x-model="progreso"
                               class="flex-1 accent-sky-400">
                        <span class="text-xs font-mono text-sky-400 w-8 text-right" x-text="`${progreso}%`"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-xs text-slate-500 w-20 flex-shrink-0">Estado</label>
                        <select name="status" x-model="status" class="flex-1 bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-sky-500">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_curso">En curso</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="editando = false" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-medium bg-sky-500 text-white hover:bg-sky-400 transition-colors">Guardar</button>
                    </div>
                </form>
                @endcan
            </div>
            @empty
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-8 text-center">
                <p class="text-slate-600 text-sm">Sin fases definidas</p>
                @can('proyectos.editar')
                <p class="text-xs text-slate-700 mt-1">Usa el botón "Nueva fase" para agregar</p>
                @endcan
            </div>
            @endforelse
        </div>

        {{-- Panel lateral --}}
        <div class="space-y-4">

            {{-- Info general --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Información</h3>
                <div>
                    <p class="text-xs text-slate-600">Responsable</p>
                    <p class="text-sm text-white mt-0.5">{{ $proyecto->responsible->name ?? 'Sin asignar' }}</p>
                </div>
                @if($proyecto->start_date)
                <div>
                    <p class="text-xs text-slate-600">Inicio</p>
                    <p class="text-sm text-white font-mono mt-0.5">{{ $proyecto->start_date->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($proyecto->end_date)
                <div>
                    <p class="text-xs text-slate-600">Entrega</p>
                    <p class="text-sm font-mono mt-0.5
                        {{ $proyecto->end_date->isPast() && $proyecto->status !== 'entregado' ? 'text-red-400' : 'text-white' }}">
                        {{ $proyecto->end_date->format('d/m/Y') }}
                        @if($proyecto->end_date->isPast() && $proyecto->status !== 'entregado')
                        <span class="text-xs text-red-400 ml-1">vencido</span>
                        @endif
                    </p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-slate-600">Requerimientos</p>
                    <p class="text-sm text-white mt-0.5">
                        {{ $proyecto->requirements->where('status','completado')->count() }}
                        / {{ $proyecto->requirements->count() }} completados
                    </p>
                </div>
            </div>

            {{-- Acciones rápidas --}}
            @can('proyectos.eliminar')
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Zona peligrosa</h3>
                <form method="POST" action="{{ route('proyectos.destroy', $proyecto) }}"
                      x-data @submit.prevent="if(confirm('¿Eliminar {{ addslashes($proyecto->name) }}?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full py-2 rounded-xl text-xs font-medium text-red-400
                                   border border-red-500/20 hover:bg-red-500/10 transition-colors">
                        Eliminar proyecto
                    </button>
                </form>
            </div>
            @endcan

        </div>
    </div>

    {{-- ── Checklist de entregables ────────────────────────────────── --}}
    @if(!empty($proyecto->checklist))
    @php
        $completados = $proyecto->checklistCompletados();
        $total       = $proyecto->checklistTotal();
        $pctCheck    = $total > 0 ? round(($completados / $total) * 100) : 0;
    @endphp
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white">Entregables del proyecto</h3>
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono text-slate-500">{{ $completados }}/{{ $total }}</span>
                <div class="w-24 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500
                        {{ $pctCheck >= 100 ? 'bg-emerald-400' : 'bg-sky-400' }}"
                         style="width: {{ $pctCheck }}%"></div>
                </div>
                <span class="text-xs font-mono {{ $pctCheck >= 100 ? 'text-emerald-400' : 'text-sky-400' }}">
                    {{ $pctCheck }}%
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($proyecto->checklist as $index => $item)
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl border transition-all duration-150
                        {{ $item['completado']
                            ? 'bg-emerald-500/5 border-emerald-500/20'
                            : 'bg-slate-800/40 border-slate-700/40' }}">
                @can('proyectos.editar')
                <form method="POST" action="{{ route('proyectos.checklist.toggle', [$proyecto, $index]) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 transition-all duration-150
                                   {{ $item['completado']
                                       ? 'bg-emerald-500 border-emerald-500 text-white'
                                       : 'border-slate-600 hover:border-sky-500 bg-transparent' }}">
                        @if($item['completado'])
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        @endif
                    </button>
                </form>
                @else
                <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0
                            {{ $item['completado'] ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-600' }}">
                    @if($item['completado'])
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    @endif
                </div>
                @endcan

                <span class="text-xs {{ $item['completado'] ? 'text-slate-500 line-through' : 'text-slate-300' }} leading-tight">
                    {{ $item['nombre'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Notas de reunión ────────────────────────────────────────── --}}
    <div
        x-data="{ editando: false }"
        class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5"
    >
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                Notas de reunión
            </h3>
            @can('proyectos.editar')
            <button @click="editando = !editando"
                    class="text-xs text-slate-500 hover:text-sky-400 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/>
                </svg>
                <span x-text="editando ? 'Cancelar' : 'Editar'"></span>
            </button>
            @endcan
        </div>

        {{-- Modo lectura --}}
        <div x-show="!editando">
            @if($proyecto->notas_reunion)
            <pre class="font-mono text-xs text-slate-300 whitespace-pre-wrap leading-relaxed bg-slate-800/40 rounded-xl p-4 border border-slate-700/40">{{ $proyecto->notas_reunion }}</pre>
            @else
            <p class="text-xs text-slate-600 italic py-3 text-center">
                Sin notas registradas — haz click en "Editar" para agregar
            </p>
            @endif
        </div>

        {{-- Modo edición --}}
        @can('proyectos.editar')
        <div x-show="editando" style="display:none">
            <form method="POST" action="{{ route('proyectos.notas.update', $proyecto) }}" class="space-y-3">
                @csrf @method('PATCH')
                <textarea
                    name="notas_reunion"
                    rows="7"
                    class="input-dark font-mono text-xs resize-y leading-relaxed"
                    placeholder="Escribe los requerimientos y notas de la reunión con el cliente..."
                >{{ $proyecto->notas_reunion }}</textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="editando = false"
                            class="px-4 py-2 text-xs text-slate-400 hover:text-white transition-colors border border-slate-700/60 rounded-lg hover:bg-slate-800">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-xs font-semibold text-white bg-sky-500 hover:bg-sky-400 transition-colors">
                        Guardar notas
                    </button>
                </div>
            </form>
        </div>
        @endcan
    </div>

    {{-- Modal nueva fase --}}
    @can('proyectos.editar')
    <div x-data="{ open: false }" @open-modal-fase.window="open = true">
        <template x-teleport="body">
            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 style="display:none">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="open = false"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-slate-700/60 rounded-2xl shadow-2xl p-6"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     @click.stop>
                    <h3 class="text-base font-bold text-white mb-4">Nueva fase</h3>
                    <form method="POST" action="{{ route('proyectos.fases.update', [$proyecto, 0]) }}">
                        @csrf @method('PATCH')
                        {{-- Usamos un endpoint temporal; en producción harías un route POST para crear fases --}}
                        <input type="text" name="name" class="input-dark mb-4" placeholder="Nombre de la fase" required>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="open = false" class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">Cancelar</button>
                            <button type="submit" class="px-5 py-2 rounded-xl text-sm font-medium bg-sky-500 text-white hover:bg-sky-400 transition-colors">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
    @endcan

</x-app-layout>
