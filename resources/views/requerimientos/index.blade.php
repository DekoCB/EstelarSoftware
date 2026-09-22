<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('proyectos.index') }}" class="text-slate-600 hover:text-slate-400 font-mono">Proyectos</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="text-slate-600 hover:text-slate-400 truncate max-w-[160px]">{{ $proyecto->name }}</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Requerimientos</span>
        </div>
    </x-slot>

    @php
        $columnas = [
            'pendiente'   => ['label' => 'Pendiente',   'color' => 'slate'],
            'en_progreso' => ['label' => 'En progreso', 'color' => 'sky'],
            'en_revision' => ['label' => 'En revisión', 'color' => 'violet'],
            'completado'  => ['label' => 'Completado',  'color' => 'emerald'],
            'rechazado'   => ['label' => 'Rechazado',   'color' => 'red'],
        ];
        $requerimientos  = $proyecto->requirements->load('assignedTo', 'phase', 'sprint');
        $totalReqs       = $requerimientos->count();
        $completados     = $requerimientos->where('status', 'completado')->count();
        $totalPts        = $requerimientos->sum('story_points');
        $estimados       = $requerimientos->whereNotNull('story_points')->count();

        // borde izquierdo por prioridad
        $prioColor = [
            'critica' => ['border' => 'border-l-red-500',   'dot' => 'bg-red-500'],
            'alta'    => ['border' => 'border-l-amber-400', 'dot' => 'bg-amber-400'],
            'media'   => ['border' => 'border-l-sky-500',   'dot' => 'bg-sky-500'],
            'baja'    => ['border' => 'border-l-slate-600', 'dot' => 'bg-slate-600'],
        ];

        $colorMap = [
            'slate'   => ['header' => 'bg-slate-800/80 text-slate-400',           'border' => 'border-slate-700/50',   'glow' => 'border-sky-500/40'],
            'sky'     => ['header' => 'bg-sky-500/10 text-sky-400',               'border' => 'border-sky-500/20',     'glow' => 'border-sky-500/50'],
            'violet'  => ['header' => 'bg-violet-500/10 text-violet-400',         'border' => 'border-violet-500/20',  'glow' => 'border-violet-500/50'],
            'emerald' => ['header' => 'bg-emerald-500/10 text-emerald-400',       'border' => 'border-emerald-500/20', 'glow' => 'border-emerald-500/50'],
            'red'     => ['header' => 'bg-red-500/10 text-red-400',               'border' => 'border-red-500/20',     'glow' => 'border-red-500/50'],
        ];
    @endphp

    {{-- ══ Estado Alpine: filtros + edición + drag & drop ══════════════ --}}
    <div x-data="{
        modalAgregar: {{ $errors->any() ? 'true' : 'false' }},

        filtro: { q: '', prioridad: '', tipo: '', asignado: '' },
        visible(priority, type, assignedId, title) {
            const f = this.filtro;
            if (f.prioridad && f.prioridad !== priority) return false;
            if (f.tipo     && f.tipo     !== type)     return false;
            if (f.asignado && String(assignedId) !== f.asignado) return false;
            if (f.q && !title.toLowerCase().includes(f.q.toLowerCase())) return false;
            return true;
        },
        get hayFiltros() { const f=this.filtro; return !!(f.q||f.prioridad||f.tipo||f.asignado); },
        limpiar() { this.filtro = { q:'', prioridad:'', tipo:'', asignado:'' }; },

        edit: null,
        abrirEditar(data) { this.edit = { ...data }; },
        cerrarEditar()    { this.edit = null; },
        get editUrl() {
            return this.edit ? '/proyectos/{{ $proyecto->id }}/requerimientos/'+this.edit.id : '#';
        },

        over: null,
        drag(e, id, from) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('id', String(id));
            e.dataTransfer.setData('from', from);
            e.currentTarget.style.opacity = '0.35';
        },
        endDrag(e) { e.currentTarget.style.opacity = ''; this.over = null; },
        enter(s)   { this.over = s; },
        leave(e)   { if (!e.currentTarget.contains(e.relatedTarget)) this.over = null; },
        drop(e, status) {
            this.over = null;
            const id = e.dataTransfer.getData('id'), from = e.dataTransfer.getData('from');
            if (!id || from === status) return;
            const form = document.getElementById('dd-form');
            form.action = '/proyectos/{{ $proyecto->id }}/requerimientos/' + id;
            document.getElementById('dd-status').value = status;
            form.submit();
        }
    }">

    {{-- ── Encabezado ─────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-white">Requerimientos</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ $proyecto->name }}</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- KPIs rápidos --}}
            <div class="flex items-center gap-4 bg-slate-900 border border-slate-800/60 rounded-xl px-4 py-2">
                <div class="text-center">
                    <p class="text-[10px] text-slate-600 uppercase tracking-wide">Completados</p>
                    <p class="text-sm font-bold font-mono {{ $completados === $totalReqs && $totalReqs > 0 ? 'text-emerald-400' : 'text-white' }}">
                        {{ $completados }}<span class="text-slate-600">/{{ $totalReqs }}</span>
                    </p>
                </div>
                @if($totalPts)
                <div class="w-px h-7 bg-slate-800"></div>
                <div class="text-center">
                    <p class="text-[10px] text-slate-600 uppercase tracking-wide">Story pts</p>
                    <p class="text-sm font-bold font-mono text-sky-400">{{ $totalPts }}</p>
                </div>
                @endif
                @if($totalReqs > 0 && $estimados < $totalReqs)
                <div class="w-px h-7 bg-slate-800"></div>
                <div class="text-center">
                    <p class="text-[10px] text-slate-600 uppercase tracking-wide">Sin estimar</p>
                    <p class="text-sm font-bold font-mono text-amber-400">{{ $totalReqs - $estimados }}</p>
                </div>
                @endif
            </div>

            @can('requerimientos.crear')
            <button @click="modalAgregar = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                           shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                           transition-all active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo requerimiento
            </button>
            @endcan
        </div>
    </div>

    {{-- ── Filtros ──────────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl px-4 py-3 mb-5">
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">

            {{-- Búsqueda --}}
            <div class="relative w-full sm:flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" x-model="filtro.q" placeholder="Buscar por título..."
                       class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl pl-8 pr-3 py-2
                              text-xs text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
            </div>

            {{-- Prioridad --}}
            <select x-model="filtro.prioridad"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors cursor-pointer shrink-0 w-full sm:w-36">
                <option value="">Prioridad</option>
                <option value="critica">🔴 Crítica</option>
                <option value="alta">🟡 Alta</option>
                <option value="media">🔵 Media</option>
                <option value="baja">⚪ Baja</option>
            </select>

            {{-- Tipo --}}
            <select x-model="filtro.tipo"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors cursor-pointer shrink-0 w-full sm:w-32">
                <option value="">Tipo</option>
                <option value="funcional">Funcional</option>
                <option value="tecnico">Técnico</option>
                <option value="negocio">Negocio</option>
                <option value="ux_ui">UX / UI</option>
            </select>

            {{-- Asignado --}}
            <select x-model="filtro.asignado"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors cursor-pointer shrink-0 w-full sm:w-36">
                <option value="">Asignado a</option>
                @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>

            {{-- Limpiar --}}
            <button x-show="hayFiltros" @click="limpiar()" style="display:none"
                    class="shrink-0 flex items-center gap-1.5 text-xs text-slate-500 hover:text-red-400
                           px-3 py-2 rounded-xl border border-slate-700/40 hover:border-red-500/30 hover:bg-red-500/5 transition-all">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </button>
        </div>
    </div>

    {{-- ── Modal crear ─────────────────────────────────────────────────── --}}
    @can('requerimientos.crear')
    <template x-teleport="body">
        <div x-show="modalAgregar"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="modalAgregar = false"></div>
            <div class="relative min-h-full flex items-start justify-center p-4 pt-10">
                <div class="w-full max-w-lg bg-slate-900 border border-slate-700/60 rounded-2xl shadow-2xl"
                     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                        <h3 class="text-base font-bold text-white">Nuevo requerimiento</h3>
                        <button @click="modalAgregar = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('requerimientos.store', $proyecto) }}" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $proyecto->id }}">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Título <span class="text-red-400">*</span></label>
                            <input type="text" name="title" class="input-dark" placeholder="¿Qué debe hacer el sistema?" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Descripción / Criterios de aceptación</label>
                            <textarea name="description" rows="3" class="input-dark resize-none"
                                      placeholder="Como usuario quiero... para poder...&#10;- Criterio 1&#10;- Criterio 2"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Tipo</label>
                                <select name="type" class="input-dark">
                                    <option value="funcional">Funcional</option>
                                    <option value="tecnico">Técnico</option>
                                    <option value="negocio">Negocio</option>
                                    <option value="ux_ui">UX / UI</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Prioridad</label>
                                <select name="priority" class="input-dark">
                                    <option value="media">🔵 Media</option>
                                    <option value="alta">🟡 Alta</option>
                                    <option value="critica">🔴 Crítica</option>
                                    <option value="baja">⚪ Baja</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estado inicial</label>
                                <select name="status" class="input-dark">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_progreso">En progreso</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Story points</label>
                                <select name="story_points" class="input-dark">
                                    <option value="">— sin estimar</option>
                                    @foreach([1,2,3,5,8,13,21] as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                Asignar a
                                <span class="text-slate-600 font-normal ml-1">— quién lo trabaja</span>
                            </label>
                            <select name="assigned_to" class="input-dark">
                                <option value="">Sin asignar</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($proyecto->phases->isNotEmpty())
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Fase</label>
                            <select name="phase_id" class="input-dark">
                                <option value="">Sin fase</option>
                                @foreach($proyecto->phases as $fase)
                                <option value="{{ $fase->id }}">{{ $fase->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="modalAgregar = false"
                                    class="px-4 py-2.5 text-sm text-slate-400 hover:text-white border border-slate-700/60 rounded-xl hover:bg-slate-800 transition-colors">Cancelar</button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 transition-all active:scale-[0.98]">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    @endcan

    {{-- ── Modal editar ─────────────────────────────────────────────────── --}}
    @can('requerimientos.editar')
    <template x-teleport="body">
        <div x-show="edit !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="cerrarEditar()"></div>
            <div class="relative min-h-full flex items-start justify-center p-4 pt-10">
                <div class="w-full max-w-lg bg-slate-900 border border-slate-700/60 rounded-2xl shadow-2xl"
                     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                        <h3 class="text-base font-bold text-white">Editar requerimiento</h3>
                        <button @click="cerrarEditar()" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" :action="editUrl" class="p-6 space-y-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Título <span class="text-red-400">*</span></label>
                            <input type="text" name="title" :value="edit?.title" class="input-dark" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Descripción / Criterios de aceptación</label>
                            <textarea name="description" rows="3" class="input-dark resize-none"
                                      x-init="$watch('edit', v => { if(v) $el.value = v.description||'' })"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Tipo</label>
                                <select name="type" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.type })">
                                    <option value="funcional">Funcional</option>
                                    <option value="tecnico">Técnico</option>
                                    <option value="negocio">Negocio</option>
                                    <option value="ux_ui">UX / UI</option>
                                </select>
                            </div>
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
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estado</label>
                                <select name="status" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.status })">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_progreso">En progreso</option>
                                    <option value="en_revision">En revisión</option>
                                    <option value="completado">Completado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1.5">Story points</label>
                                <select name="story_points" class="input-dark"
                                        x-init="$watch('edit', v => { if(v) $el.value = v.story_points??'' })">
                                    <option value="">— sin estimar</option>
                                    @foreach([1,2,3,5,8,13,21] as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Asignar a</label>
                            <select name="assigned_to" class="input-dark"
                                    x-init="$watch('edit', v => { if(v) $el.value = v.assigned_to??'' })">
                                <option value="">Sin asignar</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($proyecto->phases->isNotEmpty())
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Fase</label>
                            <select name="phase_id" class="input-dark"
                                    x-init="$watch('edit', v => { if(v) $el.value = v.phase_id??'' })">
                                <option value="">Sin fase</option>
                                @foreach($proyecto->phases as $fase)
                                <option value="{{ $fase->id }}">{{ $fase->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="cerrarEditar()"
                                    class="px-4 py-2.5 text-sm text-slate-400 hover:text-white border border-slate-700/60 rounded-xl hover:bg-slate-800 transition-colors">Cancelar</button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 transition-all active:scale-[0.98]">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    @endcan

    {{-- Form oculto drag & drop --}}
    <form id="dd-form" method="POST" style="display:none">
        @csrf @method('PATCH')
        <input type="hidden" name="status" id="dd-status">
    </form>

    {{-- ── Kanban ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 items-start">
        @foreach($columnas as $estado => $col)
        @php
            $items   = $requerimientos->where('status', $estado);
            $c       = $colorMap[$col['color']];
            $colPts  = $items->sum('story_points');
            $colDone = $estado === 'completado' ? $items->count() : $items->where('status','completado')->count();
        @endphp

        <div class="bg-slate-900/80 border rounded-2xl transition-all duration-150"
             :class="over === '{{ $estado }}' ? 'border-sky-500/50 shadow-[0_0_20px_rgba(14,165,233,0.08)]' : '{{ $c['border'] }}'">

            {{-- Cabecera --}}
            <div class="flex items-center justify-between px-4 py-3 {{ $c['header'] }} border-b {{ $c['border'] }} rounded-t-2xl">
                <span class="text-xs font-semibold tracking-wide">{{ $col['label'] }}</span>
                <div class="flex items-center gap-2">
                    @if($colPts > 0)
                    <span class="text-[10px] font-mono text-slate-500 bg-slate-900/60 px-1.5 py-0.5 rounded-md">
                        {{ $colPts }}pt
                    </span>
                    @endif
                    <span class="text-[11px] font-semibold font-mono bg-slate-900/60 px-1.5 py-0.5 rounded-md text-slate-400">
                        {{ $items->count() }}
                    </span>
                </div>
            </div>

            {{-- Drop zone --}}
            <div class="p-2 space-y-2 min-h-[100px] rounded-b-2xl transition-colors duration-150"
                 :class="over === '{{ $estado }}' ? 'bg-sky-500/[0.04]' : ''"
                 @dragover.prevent
                 @dragenter="enter('{{ $estado }}')"
                 @dragleave="leave($event)"
                 @drop.prevent="drop($event, '{{ $estado }}')">

                @forelse($items as $req)
                @php $pc = $prioColor[$req->priority] ?? $prioColor['baja']; @endphp

                <div x-show="visible(
                        '{{ $req->priority }}',
                        '{{ $req->type }}',
                        {{ $req->assigned_to ?? 'null' }},
                        {{ Js::from($req->title) }}
                     )"
                     draggable="true"
                     @dragstart="drag($event, {{ $req->id }}, '{{ $req->status }}')"
                     @dragend="endDrag($event)"
                     class="relative bg-slate-800/70 border border-slate-700/50 border-l-2 {{ $pc['border'] }}
                            rounded-xl overflow-hidden hover:border-slate-600/70 hover:bg-slate-800/90
                            transition-all duration-150 cursor-grab active:cursor-grabbing select-none group
                            shadow-sm hover:shadow-md">

                    <div class="p-3">
                        {{-- Fila 1: tipo + pts + editar --}}
                        <div class="flex items-start justify-between gap-2 mb-2.5">
                            <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                                <span class="text-[10px] font-medium text-slate-500 bg-slate-700/60 px-1.5 py-0.5 rounded-md">
                                    {{ $req->typeLabel() }}
                                </span>
                                @if($req->sprint)
                                <span class="text-[10px] font-medium text-violet-400 bg-violet-500/10 px-1.5 py-0.5 rounded-md truncate max-w-[80px]"
                                      title="Sprint: {{ $req->sprint->name }}">
                                    ⚡ {{ $req->sprint->name }}
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                @if($req->story_points)
                                <span class="text-[11px] font-bold font-mono text-sky-400 bg-sky-500/10
                                             border border-sky-500/20 px-1.5 py-0.5 rounded-md leading-none">
                                    {{ $req->story_points }}
                                </span>
                                @endif
                                @can('requerimientos.editar')
                                <button type="button"
                                        @click.stop="abrirEditar({{ Js::from([
                                            'id'           => $req->id,
                                            'title'        => $req->title,
                                            'description'  => $req->description ?? '',
                                            'type'         => $req->type,
                                            'priority'     => $req->priority,
                                            'status'       => $req->status,
                                            'assigned_to'  => $req->assigned_to,
                                            'story_points' => $req->story_points,
                                            'phase_id'     => $req->phase_id,
                                        ]) }})"
                                        class="w-6 h-6 flex items-center justify-center rounded-md
                                               text-slate-700 hover:text-sky-400 hover:bg-sky-500/10
                                               opacity-0 group-hover:opacity-100 transition-all"
                                        title="Editar">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </div>

                        {{-- Indicador de prioridad + título --}}
                        <div class="flex items-start gap-2 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $pc['dot'] }} flex-shrink-0 mt-1.5"></span>
                            <p class="text-xs font-semibold text-white leading-snug">{{ $req->title }}</p>
                        </div>

                        {{-- Descripción snippet --}}
                        @if($req->description)
                        <p class="text-[10px] text-slate-500 leading-relaxed mb-2.5 line-clamp-2 ml-3.5">
                            {{ $req->description }}
                        </p>
                        @endif

                        {{-- Footer: asignado + mover --}}
                        <div class="flex items-center justify-between pt-2 border-t border-slate-700/40 mt-0.5">

                            {{-- Asignado --}}
                            @if($req->assignedTo)
                            <div class="flex items-center gap-1.5 min-w-0">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30
                                            border border-sky-500/30 flex items-center justify-center
                                            text-[9px] font-bold text-sky-300 uppercase flex-shrink-0">
                                    {{ substr($req->assignedTo->name, 0, 1) }}
                                </div>
                                <p class="text-[10px] text-slate-400 truncate font-medium">{{ $req->assignedTo->name }}</p>
                            </div>
                            @else
                            <p class="text-[10px] text-slate-700 italic">Sin asignar</p>
                            @endif

                            {{-- Menú mover --}}
                            @can('requerimientos.editar')
                            <div class="relative flex-shrink-0" x-data="{ open: false }">
                                <button type="button" @click.stop="open = !open"
                                        class="flex items-center gap-0.5 text-[10px] text-slate-600
                                               hover:text-sky-400 transition-colors px-1.5 py-1 rounded-lg
                                               hover:bg-sky-500/5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                    </svg>
                                    Mover
                                </button>
                                <div x-show="open" @click.outside="open = false"
                                     class="absolute right-0 bottom-8 z-30 bg-slate-800 border border-slate-700/60
                                            rounded-xl shadow-2xl w-36 overflow-hidden"
                                     style="display:none">
                                    @foreach($columnas as $s => $cl)
                                    @if($s !== $estado)
                                    <form method="POST" action="{{ route('requerimientos.update', [$proyecto, $req]) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $s }}">
                                        <button type="submit"
                                                class="w-full text-left px-3 py-2 text-xs text-slate-300
                                                       hover:bg-slate-700/60 transition-colors">
                                            {{ $cl['label'] }}
                                        </button>
                                    </form>
                                    @endif
                                    @endforeach
                                    <div class="border-t border-slate-700/40">
                                        <form method="POST" action="{{ route('requerimientos.destroy', [$proyecto, $req]) }}"
                                              x-data @submit.prevent="if(confirm('¿Eliminar este requerimiento?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-full text-left px-3 py-2 text-xs text-red-400
                                                           hover:bg-red-500/10 transition-colors">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 opacity-40">
                    <svg class="w-6 h-6 text-slate-700 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"/>
                    </svg>
                    <p class="text-[11px] text-slate-700">Vacío</p>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    </div>{{-- fin wrapper x-data --}}
</x-app-layout>
