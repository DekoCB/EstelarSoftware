<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('roles.index') }}" class="text-slate-600 hover:text-slate-400">Roles</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Nuevo rol</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Nuevo rol</h2>
            <p class="text-sm text-slate-500 mt-0.5">Define un nombre y selecciona los permisos</p>
        </div>

        <form action="{{ route('roles.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nombre del rol --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                    Nombre del rol <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Ej: supervisor, contador, gerente"
                       autofocus
                       class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                              text-sm text-white placeholder-slate-600 focus:outline-none focus:border-violet-500/60 transition-colors
                              @error('name') border-rose-500/60 @enderror">
                @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-[10px] text-slate-600 mt-1.5">Usa minúsculas sin espacios, ej: <span class="font-mono">soporte-tecnico</span></p>
            </div>

            {{-- Permisos --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                @include('admin.roles._permisos', ['activos' => old('permisos', [])])
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-violet-500 to-purple-500
                               shadow-[0_0_18px_rgba(139,92,246,0.35)] hover:shadow-[0_0_28px_rgba(139,92,246,0.55)]
                               transition-all active:scale-[0.98]">
                    Crear rol
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
