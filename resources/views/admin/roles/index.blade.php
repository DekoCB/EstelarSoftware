<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('usuarios.index') }}" class="text-slate-600 hover:text-slate-400">Admin</a>
            <span class="text-slate-700">/</span>
            <h1 class="font-semibold text-white">Roles y permisos</h1>
        </div>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Roles del sistema</h2>
            <p class="text-sm text-slate-500 mt-0.5">Define qué puede hacer cada rol en el ERP</p>
        </div>
        @can('usuarios.crear')
        <a href="{{ route('roles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-gradient-to-r from-violet-500 to-purple-500 text-white
                  shadow-[0_0_18px_rgba(139,92,246,0.35)] hover:shadow-[0_0_28px_rgba(139,92,246,0.55)]
                  transition-all active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo rol
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-400"
         x-data x-init="setTimeout(() => $el.remove(), 5000)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-sm text-rose-400">
        {{ session('error') }}</div>
    @endif

    <div class="space-y-3">
        @foreach($roles as $rol)
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 hover:border-slate-700/60 transition-colors">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-sm font-bold text-white">{{ $rol->name }}</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 font-mono">
                            {{ $rol->permissions->count() }} permisos
                        </span>
                        @if($rol->users && $rol->users->count() > 0)
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-sky-500/10 text-sky-400">
                            {{ $rol->users->count() }} usuario{{ $rol->users->count() !== 1 ? 's' : '' }}
                        </span>
                        @endif
                    </div>
                    {{-- Permisos agrupados por módulo --}}
                    @php
                        $grupos = \App\Support\PermissionGroups::grupos();
                        $rolPermisos = $rol->permissions->pluck('name');
                    @endphp
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($grupos as $modulo => $permisosGrupo)
                            @php $activos = collect($permisosGrupo)->filter(fn($p) => $rolPermisos->contains($p)) @endphp
                            @if($activos->isNotEmpty())
                            <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/60 border border-slate-700/30">
                                <span class="text-[9px] font-semibold text-slate-500 uppercase">{{ $modulo }}</span>
                                <span class="text-[9px] text-slate-400">·</span>
                                <span class="text-[9px] text-slate-300">
                                    {{ $activos->map(fn($p) => \App\Support\PermissionGroups::etiqueta($p))->join(', ') }}
                                </span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @can('usuarios.editar')
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('roles.edit', $rol) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400
                              bg-slate-800 hover:bg-slate-700 transition-colors">
                        Editar
                    </a>
                    @if($rol->users && $rol->users->count() === 0)
                    <form action="{{ route('roles.destroy', $rol) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar el rol {{ $rol->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-400
                                       bg-rose-500/10 hover:bg-rose-500/20 transition-colors">
                            Eliminar
                        </button>
                    </form>
                    @endif
                </div>
                @endcan
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>
