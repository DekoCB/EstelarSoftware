<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('usuarios.index') }}" class="text-slate-600 hover:text-slate-400">Usuarios</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Nuevo usuario</span>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Nuevo usuario</h2>
            <p class="text-sm text-slate-500 mt-0.5">Crea un acceso al sistema para un miembro del equipo</p>
        </div>

        <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">

                {{-- Nombre --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Nombre completo <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Ej: Carlos Huamán"
                           autofocus
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('name') border-rose-500/60 @enderror">
                    @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Cargo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Cargo</label>
                    <input type="text" name="cargo" value="{{ old('cargo') }}"
                           placeholder="Ej: Desarrollador Backend, Analista de Ventas"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Correo electrónico <span class="text-rose-400">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="correo@empresa.com"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('email') border-rose-500/60 @enderror">
                    @error('email')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Rol --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Rol <span class="text-rose-400">*</span>
                    </label>
                    <select name="rol"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors
                                   @error('rol') border-rose-500/60 @enderror">
                        <option value="">-- Seleccionar rol --</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ old('rol') === $r->name ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('rol')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>

            {{-- Contraseña --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">
                <p class="text-xs font-semibold text-slate-400">Contraseña inicial</p>

                <div>
                    <label class="block text-xs text-slate-500 mb-1.5">
                        Contraseña <span class="text-rose-400">*</span>
                    </label>
                    <input type="password" name="password"
                           placeholder="Mínimo 8 caracteres"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('password') border-rose-500/60 @enderror">
                    @error('password')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs text-slate-500 mb-1.5">
                        Confirmar contraseña <span class="text-rose-400">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="Repite la contraseña"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                </div>

                <p class="text-[10px] text-slate-600">
                    El usuario podrá cambiar su contraseña desde su perfil después de iniciar sesión.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('usuarios.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                               transition-all active:scale-[0.98]">
                    Crear usuario
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
