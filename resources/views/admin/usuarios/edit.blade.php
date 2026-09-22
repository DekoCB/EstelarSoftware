<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('usuarios.index') }}" class="text-slate-600 hover:text-slate-400">Usuarios</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">{{ $usuario->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Editar usuario</h2>
        </div>

        <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nombre completo <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('name') border-rose-500/60 @enderror">
                    @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Cargo</label>
                    <input type="text" name="cargo" value="{{ old('cargo', $usuario->cargo) }}"
                           placeholder="Ej: Desarrollador Backend"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Correo electrónico <span class="text-rose-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('email') border-rose-500/60 @enderror">
                    @error('email')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Rol <span class="text-rose-400">*</span></label>
                    <select name="rol"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}"
                                {{ old('rol', $usuario->roles->first()?->name) === $r->name ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Cambio de contraseña opcional --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4"
                 x-data="{ cambiarPass: false }">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-slate-400">Contraseña</p>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="cambiarPass"
                               class="w-3.5 h-3.5 rounded border-slate-600 bg-slate-800 text-sky-500 focus:ring-0 cursor-pointer">
                        <span class="text-xs text-slate-400">Cambiar contraseña</span>
                    </label>
                </div>

                <div x-show="cambiarPass" x-transition class="space-y-3">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1.5">Nueva contraseña</label>
                        <input type="password" name="password"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors
                                      @error('password') border-rose-500/60 @enderror">
                        @error('password')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1.5">Confirmar</label>
                        <input type="password" name="password_confirmation"
                               placeholder="Repite la contraseña"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>
                </div>

                <p x-show="!cambiarPass" class="text-[10px] text-slate-600">
                    Deja desmarcado si no quieres cambiar la contraseña actual.
                </p>
            </div>

            {{-- ── Permisos directos ─────────────────────────────────────────── --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5"
                 x-data="permisosUsuario()">

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold text-white">Permisos directos</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">
                            Azul = del rol · Verde = extra directo · Gris = del rol sin activar directo
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="seleccionarTodos()"
                                class="text-xs text-sky-400 hover:text-sky-300 transition-colors">Todo</button>
                        <span class="text-slate-700">·</span>
                        <button type="button" @click="limpiarDirectos()"
                                class="text-xs text-slate-500 hover:text-slate-400 transition-colors">Solo rol</button>
                    </div>
                </div>

                @foreach($grupos as $modulo => $permisosGrupo)
                @php $permisosValidos = array_filter($permisosGrupo, fn($p) => in_array($p, $existentes)) @endphp
                @if(count($permisosValidos) === 0) @continue @endif
                <div class="mb-3">
                    <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">{{ $modulo }}</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($permisosValidos as $permiso)
                        @php
                            $etiqueta = \App\Support\PermissionGroups::etiqueta($permiso);
                            $esDelRol = in_array($permiso, $permisosRol);
                            $esDirect = in_array($permiso, $permisosDirect);
                        @endphp
                        <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1.5 rounded-lg border transition-all"
                               :class="{
                                   'bg-sky-500/10 border-sky-500/20': rolBase['{{ $permiso }}'] && directos['{{ $permiso }}'],
                                   'bg-emerald-500/10 border-emerald-500/20': !rolBase['{{ $permiso }}'] && directos['{{ $permiso }}'],
                                   'bg-slate-700/20 border-slate-700/20': rolBase['{{ $permiso }}'] && !directos['{{ $permiso }}'],
                                   'border-transparent hover:border-slate-700/30 hover:bg-slate-800/30': !rolBase['{{ $permiso }}'] && !directos['{{ $permiso }}'],
                               }">
                            <input type="checkbox"
                                   name="permisos_directos[]"
                                   value="{{ $permiso }}"
                                   x-model="directos['{{ $permiso }}']"
                                   class="w-3.5 h-3.5 rounded border-slate-600 bg-slate-800 text-sky-500
                                          focus:ring-0 focus:ring-offset-0 cursor-pointer">
                            <span class="text-xs transition-colors"
                                  :class="{
                                      'text-sky-300':     rolBase['{{ $permiso }}'] && directos['{{ $permiso }}'],
                                      'text-emerald-300': !rolBase['{{ $permiso }}'] && directos['{{ $permiso }}'],
                                      'text-slate-500':   rolBase['{{ $permiso }}'] && !directos['{{ $permiso }}'],
                                      'text-slate-400':   !rolBase['{{ $permiso }}'] && !directos['{{ $permiso }}'],
                                  }">{{ $etiqueta }}</span>
                            @if($esDelRol)
                            <span class="text-[8px] text-slate-600 font-mono ml-0.5">rol</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                {{-- Desactivar --}}
                @if($usuario->id !== auth()->id())
                @can('usuarios.eliminar')
                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                      onsubmit="return confirm('¿Desactivar al usuario {{ $usuario->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-xs font-semibold text-rose-400
                                   bg-rose-500/10 hover:bg-rose-500/20 transition-colors">
                        {{ $usuario->activo ? 'Desactivar usuario' : 'Ya desactivado' }}
                    </button>
                </form>
                @endcan
                @else
                <div></div>
                @endif

                <div class="flex items-center gap-3">
                    <a href="{{ route('usuarios.index') }}"
                       class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                                   bg-gradient-to-r from-sky-500 to-cyan-500
                                   shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                                   transition-all active:scale-[0.98]">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    function permisosUsuario() {
        const permisosRol    = @json($permisosRol);
        const permisosDirect = @json($permisosDirect);
        const existentes     = @json($existentes);

        const directos = {};
        const rolBase  = {};
        existentes.forEach(p => {
            directos[p] = permisosDirect.includes(p);
            rolBase[p]  = permisosRol.includes(p);
        });

        return {
            directos,
            rolBase,
            seleccionarTodos() {
                existentes.forEach(p => { this.directos[p] = true; });
            },
            limpiarDirectos() {
                existentes.forEach(p => { this.directos[p] = false; });
            },
        };
    }
    </script>
</x-app-layout>
