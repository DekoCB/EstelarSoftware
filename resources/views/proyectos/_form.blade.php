{{-- Partial compartido: create y edit --}}
<div class="space-y-6">

    {{-- Datos principales --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">1</span>
            Información del proyecto
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombre del proyecto <span class="text-red-400">*</span></label>
                <input type="text" name="name" class="input-dark @error('name') error @enderror"
                       placeholder="Ej. Sistema de ventas para Empresa XYZ"
                       value="{{ old('name', $proyecto->name ?? '') }}">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ── Combobox Cliente ──────────────────────────────────── --}}
            <div
                x-data="{
                    items:      {{ Js::from($clientes->map(fn($c) => ['id' => $c->id, 'label' => $c->razon_social . ($c->nombre_comercial ? ' — '.$c->nombre_comercial : ''), 'sub' => $c->numero_documento])) }},
                    search:     '',
                    selectedId: {{ old('client_id', isset($proyecto) ? $proyecto->client_id : 'null') }},
                    open:       false,
                    get filtered() {
                        const q = this.search.toLowerCase();
                        if (!q) return this.items.slice(0, 10);
                        return this.items.filter(i =>
                            i.label.toLowerCase().includes(q) || (i.sub && i.sub.includes(q))
                        ).slice(0, 10);
                    },
                    select(item) { this.selectedId = item.id; this.search = item.label; this.open = false; },
                    clear()      { this.selectedId = null; this.search = ''; this.$refs.input.focus(); },
                    init() {
                        if (this.selectedId) {
                            const found = this.items.find(i => i.id == this.selectedId);
                            if (found) this.search = found.label;
                        }
                    }
                }"
                class="relative"
            >
                <label class="block text-xs font-medium text-slate-400 mb-1.5">
                    Cliente <span class="text-red-400">*</span>
                </label>
                <input type="hidden" name="client_id" :value="selectedId">
                <div class="relative">
                    <input
                        x-ref="input"
                        type="text"
                        x-model="search"
                        @focus="open = true"
                        @input="open = true; selectedId = null"
                        @keydown.escape="open = false"
                        @click.outside="open = false"
                        placeholder="Escribe el nombre o RUC del cliente..."
                        autocomplete="off"
                        class="input-dark pr-8 @error('client_id') error @enderror"
                    >
                    <button x-show="selectedId" type="button" @click="clear()"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div x-show="open && filtered.length > 0"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute z-50 left-0 right-0 mt-1 bg-slate-800 border border-slate-700/60
                            rounded-xl overflow-hidden shadow-xl max-h-52 overflow-y-auto"
                     style="display:none">
                    <template x-for="item in filtered" :key="item.id">
                        <button type="button" @click="select(item)"
                                class="w-full text-left px-4 py-2.5 hover:bg-slate-700/60 transition-colors border-b border-slate-700/40 last:border-0">
                            <p class="text-sm text-white truncate" x-text="item.label"></p>
                            <p class="text-[10px] text-slate-500 font-mono mt-0.5" x-text="item.sub"></p>
                        </button>
                    </template>
                </div>
                @error('client_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ── Combobox Responsable ───────────────────────────────── --}}
            <div
                x-data="{
                    items:      {{ Js::from($usuarios->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'count' => $u->active_projects_count])) }},
                    search:     '',
                    selectedId: {{ old('responsible_user_id', isset($proyecto) ? ($proyecto->responsible_user_id ?? 'null') : 'null') }},
                    open:       false,
                    get filtered() {
                        const q = this.search.toLowerCase();
                        if (!q) return this.items.slice(0, 10);
                        return this.items.filter(i => i.name.toLowerCase().includes(q)).slice(0, 10);
                    },
                    select(item) { this.selectedId = item.id; this.search = item.name; this.open = false; },
                    clear()      { this.selectedId = null; this.search = ''; this.$refs.input.focus(); },
                    init() {
                        if (this.selectedId) {
                            const found = this.items.find(i => i.id == this.selectedId);
                            if (found) this.search = found.name;
                        }
                    },
                    loadColor(count) {
                        if (count === 0) return 'text-emerald-400 bg-emerald-500/10';
                        if (count <= 2)  return 'text-sky-400 bg-sky-500/10';
                        if (count <= 4)  return 'text-amber-400 bg-amber-500/10';
                        return 'text-red-400 bg-red-500/10';
                    }
                }"
                class="relative"
            >
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Responsable</label>
                <input type="hidden" name="responsible_user_id" :value="selectedId">
                <div class="relative">
                    <input
                        x-ref="input"
                        type="text"
                        x-model="search"
                        @focus="open = true"
                        @input="open = true; selectedId = null"
                        @keydown.escape="open = false"
                        @click.outside="open = false"
                        placeholder="Buscar por nombre..."
                        autocomplete="off"
                        class="input-dark pr-8"
                    >
                    <button x-show="selectedId" type="button" @click="clear()"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div x-show="open && filtered.length > 0"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute z-50 left-0 right-0 mt-1 bg-slate-800 border border-slate-700/60
                            rounded-xl overflow-hidden shadow-xl max-h-52 overflow-y-auto"
                     style="display:none">
                    <template x-for="item in filtered" :key="item.id">
                        <button type="button" @click="select(item)"
                                class="w-full text-left px-4 py-2.5 hover:bg-slate-700/60 transition-colors
                                       border-b border-slate-700/40 last:border-0 flex items-center justify-between gap-3">
                            <p class="text-sm text-white" x-text="item.name"></p>
                            <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded-lg flex-shrink-0"
                                  :class="loadColor(item.count)"
                                  x-text="item.count + ' activo' + (item.count !== 1 ? 's' : '')">
                            </span>
                        </button>
                    </template>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estado</label>
                <select name="status" class="input-dark">
                    @foreach(['planificado' => 'Planificado','en_curso' => 'En curso','pausado' => 'Pausado','en_revision' => 'En revisión','entregado' => 'Entregado','cancelado' => 'Cancelado'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $proyecto->status ?? 'planificado') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha de inicio</label>
                <input type="date" name="start_date" class="input-dark font-mono"
                       value="{{ old('start_date', isset($proyecto) ? $proyecto->start_date?->format('Y-m-d') : now()->format('Y-m-d')) }}">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha de entrega</label>
                <input type="date" name="end_date" class="input-dark font-mono"
                       value="{{ old('end_date', isset($proyecto) ? $proyecto->end_date?->format('Y-m-d') : '') }}">
                @error('end_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                          class="input-dark resize-none"
                          placeholder="Describe el alcance del proyecto...">{{ old('description', $proyecto->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── Checklist de entregables (solo en create) ─────────────── --}}
    @unless(isset($proyecto))
    <div
        x-data="{
            todos: true,
            toggleTodos() {
                this.todos = !this.todos;
                document.querySelectorAll('[data-checklist]').forEach(cb => cb.checked = this.todos);
            }
        }"
        class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6"
    >
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">2</span>
                Entregables del proyecto
            </h3>
            <button type="button" @click="toggleTodos()"
                    class="text-xs text-slate-500 hover:text-sky-400 transition-colors">
                <span x-text="todos ? 'Desmarcar todos' : 'Marcar todos'"></span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach(\App\Models\Project::PARTES_DEFAULT as $parte)
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer
                          bg-slate-800/40 border border-slate-700/40
                          hover:border-sky-500/30 hover:bg-slate-800/80 transition-all duration-150
                          has-[:checked]:border-sky-500/40 has-[:checked]:bg-sky-500/5">
                <input
                    type="checkbox"
                    name="checklist[]"
                    value="{{ $parte }}"
                    data-checklist
                    checked
                    class="w-4 h-4 rounded border-slate-600 bg-slate-700
                           text-sky-500 focus:ring-sky-500/30 focus:ring-offset-0 transition-colors"
                >
                <span class="text-xs text-slate-300 leading-tight">{{ $parte }}</span>
            </label>
            @endforeach
        </div>
        <p class="text-xs text-slate-600 mt-3">Desmarca los entregables que no apliquen para este proyecto.</p>
    </div>
    @endunless

    {{-- ── Notas de reunión ───────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">
                {{ isset($proyecto) ? '2' : '3' }}
            </span>
            Notas de reunión inicial
            <span class="text-xs font-normal text-slate-600">— opcional</span>
        </h3>
        <p class="text-xs text-slate-600 mb-3 ml-7">
            Escribe los requerimientos tal como los explicó el cliente. Sirven de referencia antes de formalizar el backlog.
        </p>
        <textarea
            name="notas_reunion"
            rows="5"
            class="input-dark font-mono resize-y text-xs leading-relaxed"
            placeholder="Ej: El cliente quiere un sistema que permita registrar ventas, gestionar inventario y generar reportes PDF. También mencionó que necesita acceso desde móvil y que el color corporativo es azul..."
        >{{ old('notas_reunion', $proyecto->notas_reunion ?? '') }}</textarea>
    </div>

</div>
