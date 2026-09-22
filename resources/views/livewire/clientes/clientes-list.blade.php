<div>
    {{-- Filtros ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">

        {{-- Búsqueda --}}
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Buscar por razón social, RUC, email..."
                class="w-full bg-slate-800/80 border border-slate-700 text-white placeholder-slate-500
                       rounded-xl pl-9 pr-4 py-2.5 text-sm
                       focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50
                       transition-all duration-200"
            >
        </div>

        {{-- Filtro estado --}}
        <select
            wire:model.live="estado"
            class="bg-slate-800/80 border border-slate-700 text-slate-300 rounded-xl px-3 py-2.5 text-sm
                   focus:outline-none focus:border-sky-500 transition-colors"
        >
            <option value="">Todos los estados</option>
            <option value="prospecto">Prospecto</option>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="bloqueado">Bloqueado</option>
        </select>

        {{-- Filtro tipo documento --}}
        <select
            wire:model.live="tipoDocumento"
            class="bg-slate-800/80 border border-slate-700 text-slate-300 rounded-xl px-3 py-2.5 text-sm
                   focus:outline-none focus:border-sky-500 transition-colors"
        >
            <option value="">Todos los tipos</option>
            <option value="RUC">RUC</option>
            <option value="DNI">DNI</option>
            <option value="CE">C.E.</option>
            <option value="PASAPORTE">Pasaporte</option>
        </select>
    </div>

    {{-- Tabla ───────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">

        {{-- Loading overlay --}}
        <div wire:loading.delay class="relative">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm rounded-2xl z-10
                        flex items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <svg class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Buscando...
                </div>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Documento</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Razón social</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Estado</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($clientes as $cliente)
                <tr class="hover:bg-slate-800/30 transition-colors duration-150 group">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded
                                         bg-slate-800 text-slate-500 uppercase">
                                {{ $cliente->tipo_documento }}
                            </span>
                            <span class="font-mono text-slate-300 text-sm">{{ $cliente->numero_documento }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-medium text-white">{{ $cliente->razon_social }}</p>
                        @if($cliente->nombre_comercial)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $cliente->nombre_comercial }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell text-slate-400">
                        {{ $cliente->email ?? '—' }}
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold capitalize
                                     {{ $cliente->estadoBadgeClass() }}">
                            {{ $cliente->estado }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('clientes.show', $cliente) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-sky-400 hover:bg-sky-500/10
                                      transition-colors duration-150"
                               title="Ver detalle">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </a>
                            @can('clientes.editar')
                            <a href="{{ route('clientes.edit', $cliente) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10
                                      transition-colors duration-150"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </a>
                            @endcan
                            @can('clientes.eliminar')
                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                                  x-data
                                  @submit.prevent="if(confirm('¿Eliminar a {{ addslashes($cliente->razon_social) }}?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10
                                               transition-colors duration-150"
                                        title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-600">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            <p class="text-sm">No se encontraron clientes</p>
                            @if($search || $estado || $tipoDocumento)
                            <button wire:click="$set('search',''); $set('estado',''); $set('tipoDocumento','')"
                                    class="text-xs text-sky-400 hover:text-sky-300 transition-colors">
                                Limpiar filtros
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($clientes->hasPages())
        <div class="px-5 py-4 border-t border-slate-800/60">
            {{ $clientes->links() }}
        </div>
        @endif
    </div>

    <p class="text-xs text-slate-700 mt-3 font-mono">
        {{ $clientes->total() }} cliente(s) encontrado(s)
    </p>
</div>
