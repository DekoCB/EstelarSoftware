<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-600">Admin</span>
            <span class="text-slate-700">/</span>
            <h1 class="font-semibold text-white">Usuarios</h1>
        </div>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Usuarios del sistema</h2>
            <p class="text-sm text-slate-500 mt-0.5">Gestiona accesos y roles del equipo</p>
        </div>
        @can('usuarios.crear')
        <a href="{{ route('usuarios.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                  shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                  transition-all active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo usuario
        </a>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-400"
         x-data x-init="setTimeout(() => $el.remove(), 5000)">
        {!! session('success') !!}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-sm text-rose-400">
        {{ session('error') }}
    </div>
    @endif

    {{-- Tabla --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuario</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Correo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Cargo</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rol</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @foreach($usuarios as $u)
                <tr class="{{ !$u->activo ? 'opacity-50' : '' }} hover:bg-slate-800/30 transition-colors group">
                    {{-- Nombre + avatar inicial --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-bold
                                        {{ $u->rolBadgeClass() }}">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white flex items-center gap-1.5">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                    <span class="text-[9px] text-sky-400 bg-sky-500/10 px-1.5 py-0.5 rounded">tú</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-xs text-slate-400 font-mono">{{ $u->email }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs text-slate-500">{{ $u->cargo ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $u->rolBadgeClass() }}">
                            {{ $u->rolLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @can('usuarios.editar')
                        <form action="{{ route('usuarios.toggle', $u) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold transition-colors
                                           {{ $u->activo
                                               ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20'
                                               : 'bg-slate-700 text-slate-500 hover:bg-slate-600' }}"
                                    {{ $u->id === auth()->id() ? 'disabled title=No puedes desactivarte' : '' }}>
                                <span class="w-1.5 h-1.5 rounded-full {{ $u->activo ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                {{ $u->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                        @else
                        <span class="text-[10px] px-2.5 py-1 rounded-lg
                                     {{ $u->activo ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-500 bg-slate-700' }}">
                            {{ $u->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        @endcan
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @can('usuarios.editar')
                            <a href="{{ route('usuarios.edit', $u) }}"
                               class="p-1.5 rounded-lg text-slate-600 hover:text-sky-400 hover:bg-sky-500/10 transition-colors"
                               title="Editar">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                </svg>
                            </a>
                            @endcan
                            @can('usuarios.editar')
                            <form action="{{ route('usuarios.reset-password', $u) }}" method="POST"
                                  onsubmit="return confirm('¿Resetear la contraseña de {{ $u->name }}?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-amber-400 hover:bg-amber-500/10 transition-colors"
                                        title="Resetear contraseña">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Leyenda de roles --}}
    <div class="mt-5 bg-slate-900 border border-slate-800/60 rounded-2xl p-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Roles del sistema</p>
        <div class="flex flex-wrap gap-2">
            @foreach([
                ['super-admin',    'bg-violet-500/15 text-violet-400', 'Acceso total al sistema'],
                ['administrativo', 'bg-sky-500/15 text-sky-400',       'Acceso operativo completo'],
                ['ventas',         'bg-emerald-500/15 text-emerald-400','Clientes y cotizaciones'],
                ['desarrollador',  'bg-amber-500/15 text-amber-400',   'Proyectos asignados'],
                ['practicante',    'bg-slate-500/15 text-slate-400',   'Vista limitada'],
            ] as [$rol, $badge, $desc])
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800/40 border border-slate-800">
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $badge }}">{{ $rol }}</span>
                <span class="text-[10px] text-slate-500">{{ $desc }}</span>
            </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
