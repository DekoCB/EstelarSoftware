{{--
    Variables esperadas:
    $grupos    → PermissionGroups::grupos()
    $existentes → Permission::pluck('name')->toArray()
    $activos   → array de permisos actualmente seleccionados
--}}
<div class="space-y-3" x-data="permisosManager()">

    {{-- Acciones globales --}}
    <div class="flex items-center justify-between mb-1">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Permisos</p>
        <div class="flex gap-2">
            <button type="button" @click="seleccionarTodos()"
                    class="text-xs text-sky-400 hover:text-sky-300 transition-colors">
                Seleccionar todo
            </button>
            <span class="text-slate-700">·</span>
            <button type="button" @click="deseleccionarTodos()"
                    class="text-xs text-slate-500 hover:text-slate-400 transition-colors">
                Limpiar
            </button>
        </div>
    </div>

    @foreach($grupos as $modulo => $permisosGrupo)
    @php $permisosValidos = array_filter($permisosGrupo, fn($p) => in_array($p, $existentes)) @endphp
    @if(count($permisosValidos) === 0) @continue @endif

    <div class="bg-slate-800/40 border border-slate-700/30 rounded-xl p-4">
        {{-- Cabecera del módulo con "seleccionar módulo" --}}
        <div class="flex items-center justify-between mb-3">
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox"
                       @change="toggleModulo('{{ $modulo }}', $event.target.checked)"
                       :checked="moduloCompleto('{{ $modulo }}')"
                       :indeterminate="moduloParcial('{{ $modulo }}')"
                       class="w-3.5 h-3.5 rounded border-slate-600 bg-slate-800 text-violet-500
                              focus:ring-0 focus:ring-offset-0 cursor-pointer">
                <span class="text-xs font-semibold text-slate-300 group-hover:text-white transition-colors">
                    {{ $modulo }}
                </span>
            </label>
            <span class="text-[10px] text-slate-600" x-text="contarModulo('{{ $modulo }}') + ' / {{ count($permisosValidos) }}'"></span>
        </div>
        {{-- Checkboxes de permisos --}}
        <div class="flex flex-wrap gap-2">
            @foreach($permisosValidos as $permiso)
            @php
                $accion   = last(explode('.', $permiso));
                $etiqueta = \App\Support\PermissionGroups::etiqueta($permiso);
                $checked  = in_array($permiso, $activos ?? []);
                $modKey   = "'{$modulo}'";
            @endphp
            <label class="flex items-center gap-1.5 cursor-pointer group px-2.5 py-1.5 rounded-lg
                          transition-colors hover:bg-slate-700/40"
                   :class="permisos['{{ $permiso }}'] ? 'bg-violet-500/10 border border-violet-500/20' : 'border border-transparent'">
                <input type="checkbox"
                       name="permisos[]"
                       value="{{ $permiso }}"
                       x-model="permisos['{{ $permiso }}']"
                       class="w-3.5 h-3.5 rounded border-slate-600 bg-slate-800 text-violet-500
                              focus:ring-0 focus:ring-offset-0 cursor-pointer">
                <span class="text-xs transition-colors"
                      :class="permisos['{{ $permiso }}'] ? 'text-violet-300' : 'text-slate-400 group-hover:text-slate-300'">
                    {{ $etiqueta }}
                </span>
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<script>
function permisosManager() {
    const grupos = @json($grupos);
    const existentes = @json($existentes);
    const activos = @json($activos ?? []);

    // Estado inicial
    const estado = {};
    existentes.forEach(p => { estado[p] = activos.includes(p); });

    return {
        permisos: estado,

        seleccionarTodos() {
            existentes.forEach(p => { this.permisos[p] = true; });
        },
        deseleccionarTodos() {
            existentes.forEach(p => { this.permisos[p] = false; });
        },
        toggleModulo(modulo, checked) {
            const lista = grupos[modulo] || [];
            lista.forEach(p => { if (existentes.includes(p)) this.permisos[p] = checked; });
        },
        moduloCompleto(modulo) {
            const lista = (grupos[modulo] || []).filter(p => existentes.includes(p));
            return lista.length > 0 && lista.every(p => this.permisos[p]);
        },
        moduloParcial(modulo) {
            const lista = (grupos[modulo] || []).filter(p => existentes.includes(p));
            const activos = lista.filter(p => this.permisos[p]);
            return activos.length > 0 && activos.length < lista.length;
        },
        contarModulo(modulo) {
            const lista = (grupos[modulo] || []).filter(p => existentes.includes(p));
            return lista.filter(p => this.permisos[p]).length;
        },
    };
}
</script>
