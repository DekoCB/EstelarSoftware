<div x-data="{
    edit: null,
    abrirEditar(data) { this.edit = { ...data }; },
    cerrarEditar()    { this.edit = null; },
    get editUrl() {
        return this.edit
            ? '/proyectos/{{ $projectId }}/requerimientos/' + this.edit.id
            : '#';
    }
}">

    @php
    $columnas = [
        'pendiente'   => ['label' => 'Por hacer',   'color' => 'slate'],
        'en_progreso' => ['label' => 'En progreso', 'color' => 'sky'],
        'en_revision' => ['label' => 'En revisión', 'color' => 'violet'],
        'completado'  => ['label' => 'Hecho ✓',     'color' => 'emerald'],
    ];
    $colorMap = [
        'slate'   => ['header' => 'bg-slate-800 text-slate-400',           'border' => 'border-slate-700/40'],
        'sky'     => ['header' => 'bg-sky-500/10 text-sky-400',            'border' => 'border-sky-500/20'],
        'violet'  => ['header' => 'bg-violet-500/10 text-violet-400',      'border' => 'border-violet-500/20'],
        'emerald' => ['header' => 'bg-emerald-500/10 text-emerald-400',    'border' => 'border-emerald-500/20'],
    ];
    $prioColor = [
        'critica' => 'border-l-red-500',
        'alta'    => 'border-l-amber-400',
        'media'   => 'border-l-sky-500',
        'baja'    => 'border-l-slate-600',
    ];
    @endphp

    {{-- ── Modal editar requerimiento ───────────────────────────────── --}}
    <template x-teleport="body">
        <div x-show="edit !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="cerrarEditar()"></div>
            <div class="relative min-h-full flex items-start justify-center p-4 pt-10">
                <div class="w-full max-w-lg bg-slate-900 border border-slate-700/60 rounded-2xl shadow-2xl"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                        <h3 class="text-base font-bold text-white">Editar tarea</h3>
                        <button @click="cerrarEditar()"
                                class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" :action="editUrl" class="p-6 space-y-4">
                        @csrf @method('PATCH')

                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Título</label>
                            <input type="text" name="title" :value="edit?.title" class="input-dark" required>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Descripción</label>
                            <textarea name="description" rows="3" class="input-dark resize-none"
                                      x-init="$watch('edit', v => { if(v) $el.value = v.description || '' })"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Prioridad</label>
                                <select name="priority" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.priority })">
                                    <option value="critica">🔴 Crítica</option>
                                    <option value="alta">🟡 Alta</option>
                                    <option value="media">🔵 Media</option>
                                    <option value="baja">⚪ Baja</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Story points</label>
                                <select name="story_points" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.story_points ?? '' })">
                                    <option value="">— sin estimar</option>
                                    @foreach([1,2,3,5,8,13,21] as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estado</label>
                                <select name="status" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.status })">
                                    <option value="pendiente">Por hacer</option>
                                    <option value="en_progreso">En progreso</option>
                                    <option value="en_revision">En revisión</option>
                                    <option value="completado">Hecho</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Asignado a</label>
                                <select name="assigned_to" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.assigned_to ?? '' })">
                                    <option value="">Sin asignar</option>
                                    @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="cerrarEditar()"
                                    class="px-4 py-2.5 text-sm text-slate-400 hover:text-white border border-slate-700/60 rounded-xl hover:bg-slate-800 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                                           bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400
                                           transition-all active:scale-[0.98]">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Kanban ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($columnas as $estado => $col)
        @php
            $items  = $tareas[$estado] ?? collect();
            $c      = $colorMap[$col['color']];
            $colPts = $items->sum('story_points');
        @endphp

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl flex flex-col">

            <div class="flex items-center justify-between px-4 py-3 {{ $c['header'] }} border-b {{ $c['border'] }} rounded-t-2xl flex-shrink-0">
                <span class="text-xs font-semibold">{{ $col['label'] }}</span>
                <div class="flex items-center gap-2">
                    @if($colPts > 0)
                    <span class="text-[10px] font-mono text-slate-600">{{ $colPts }}pt</span>
                    @endif
                    <span class="text-xs font-mono bg-slate-900/40 px-1.5 py-0.5 rounded-md">{{ $items->count() }}</span>
                </div>
            </div>

            <div class="p-2 space-y-2 flex-1 min-h-[120px]">
                @forelse($items as $req)
                @php $pc = $prioColor[$req->priority] ?? 'border-l-slate-600'; @endphp

                <div class="bg-slate-800/70 border border-slate-700/50 border-l-2 {{ $pc }}
                            rounded-xl hover:border-slate-600/70 transition-colors group">
                    <div class="p-3">

                        {{-- Fila superior: tipo + pts + editar --}}
                        <div class="flex items-start justify-between gap-1.5 mb-2">
                            <span class="text-[10px] text-slate-500 bg-slate-700/60 px-1.5 py-0.5 rounded-md">
                                {{ $req->typeLabel() }}
                            </span>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                @if($req->story_points)
                                <span class="text-[11px] font-bold font-mono text-sky-400 bg-sky-500/10 border border-sky-500/20 px-1.5 py-0.5 rounded-md leading-none">
                                    {{ $req->story_points }}
                                </span>
                                @endif
                                <button type="button"
                                        @click.stop="abrirEditar({{ Js::from([
                                            'id'           => $req->id,
                                            'title'        => $req->title,
                                            'description'  => $req->description ?? '',
                                            'priority'     => $req->priority,
                                            'status'       => $req->status,
                                            'story_points' => $req->story_points,
                                            'assigned_to'  => $req->assigned_to,
                                        ]) }})"
                                        class="w-6 h-6 flex items-center justify-center rounded-md
                                               text-slate-700 hover:text-sky-400 hover:bg-sky-500/10
                                               opacity-0 group-hover:opacity-100 transition-all"
                                        title="Editar tarea">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Título --}}
                        <p class="text-xs font-semibold text-white leading-snug mb-2">{{ $req->title }}</p>

                        {{-- Descripción --}}
                        @if($req->description)
                        <p class="text-[10px] text-slate-500 leading-relaxed mb-2 line-clamp-2">{{ $req->description }}</p>
                        @endif

                        {{-- Asignado --}}
                        @if($req->assignedTo)
                        <div class="flex items-center gap-1.5 mb-2">
                            <div class="w-5 h-5 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30
                                        border border-sky-500/30 flex items-center justify-center
                                        text-[9px] font-bold text-sky-300 uppercase flex-shrink-0">
                                {{ substr($req->assignedTo->name, 0, 1) }}
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium truncate">{{ $req->assignedTo->name }}</p>
                        </div>
                        @endif

                        {{-- Mover (form POST — sin wire:click) --}}
                        <div class="relative pt-2 border-t border-slate-700/40" x-data="{ open: false }">
                            <button type="button" @click.stop="open = !open"
                                    class="flex items-center gap-0.5 text-[10px] text-slate-600
                                           hover:text-sky-400 transition-colors px-1.5 py-1 rounded-lg hover:bg-sky-500/5">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                </svg>
                                Mover a
                            </button>

                            <div x-show="open" @click.outside="open = false"
                                 class="absolute left-0 bottom-8 z-30 bg-slate-800 border border-slate-700/60
                                        rounded-xl shadow-2xl w-36 overflow-hidden"
                                 style="display:none">
                                @foreach($columnas as $s => $cl)
                                @if($s !== $estado)
                                <form method="POST"
                                      action="{{ route('requerimientos.update', ['proyecto' => $projectId, 'requerimiento' => $req->id]) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $s }}">
                                    <button type="submit"
                                            class="w-full text-left px-3 py-2 text-xs text-slate-300 hover:bg-slate-700/60 transition-colors">
                                        {{ $cl['label'] }}
                                    </button>
                                </form>
                                @endif
                                @endforeach

                                @can('sprints.gestionar')
                                <div class="border-t border-slate-700/40">
                                    <form method="POST"
                                          action="{{ route('requerimientos.asignar-sprint', ['proyecto' => $projectId, 'requerimiento' => $req->id]) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="sprint_id" value="">
                                        <button type="submit"
                                                class="w-full text-left px-3 py-2 text-xs text-amber-400 hover:bg-amber-500/10 transition-colors">
                                            → Backlog
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </div>
                        </div>

                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 opacity-40">
                    <svg class="w-5 h-5 text-slate-700 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"/>
                    </svg>
                    <p class="text-[11px] text-slate-700">Vacío</p>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

</div>
