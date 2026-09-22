<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('roles.index') }}" class="text-slate-600 hover:text-slate-400">Roles</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">{{ $rol->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Editar rol: <span class="text-violet-400">{{ $rol->name }}</span></h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ $rol->users->count() }} usuario{{ $rol->users->count() !== 1 ? 's' : '' }} con este rol
                </p>
            </div>
        </div>

        <form action="{{ route('roles.update', $rol) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nombre --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                    Nombre del rol <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $rol->name) }}"
                       class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                              text-sm text-white focus:outline-none focus:border-violet-500/60 transition-colors
                              @error('name') border-rose-500/60 @enderror">
                @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Permisos --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                @include('admin.roles._permisos', ['activos' => old('permisos', $activos)])
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
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
