<div>
    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="search"
                   placeholder="Buscar proyecto o cliente..."
                   class="w-full input-dark pl-9">
        </div>
        <select wire:model.live="status" class="bg-slate-800/80 border border-slate-700 text-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-sky-500 transition-colors">
            <option value="">Todos los estados</option>
            <option value="planificado">Planificado</option>
            <option value="en_curso">En curso</option>
            <option value="pausado">Pausado</option>
            <option value="en_revision">En revisión</option>
            <option value="entregado">Entregado</option>
            <option value="cancelado">Cancelado</option>
        </select>
    </div>

    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <div wire:loading.delay class="relative">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <svg class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Cargando...
                </div>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyecto</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Responsable</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Progreso</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Estado</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($proyectos as $proyecto)
                <tr class="hover:bg-slate-800/30 transition-colors duration-150">
                    <td class="px-5 py-4">
                        <p class="font-medium text-white">{{ $proyecto->name }}</p>
                        @if($proyecto->end_date)
                        <p class="text-xs text-slate-600 mt-0.5 font-mono">
                            Entrega: {{ $proyecto->end_date->format('d/m/Y') }}
                        </p>
                        @endif
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell text-slate-400 text-sm">
                        {{ $proyecto->client->razon_social }}
                    </td>
                    <td class="px-5 py-4 hidden lg:table-cell text-slate-400 text-sm">
                        {{ $proyecto->responsible->name ?? '—' }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 min-w-[100px]">
                            <div class="flex-1 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500
                                    {{ $proyecto->progress >= 100 ? 'bg-emerald-400' : ($proyecto->progress >= 50 ? 'bg-sky-400' : 'bg-sky-500/60') }}"
                                     style="width: {{ $proyecto->progress }}%">
                                </div>
                            </div>
                            <span class="text-xs font-mono text-slate-400 w-8 text-right">{{ $proyecto->progress }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                     {{ $proyecto->statusBadgeClass() }}">
                            {{ $proyecto->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('proyectos.show', $proyecto) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-sky-400 hover:bg-sky-500/10 transition-colors" title="Ver">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </a>
                            @can('proyectos.editar')
                            <a href="{{ route('proyectos.edit', $proyecto) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10 transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                </svg>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-600">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>
                            </svg>
                            <p class="text-sm">No hay proyectos</p>
                            @if($search || $status)
                            <button wire:click="$set('search',''); $set('status','')" class="text-xs text-sky-400 hover:text-sky-300">Limpiar filtros</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($proyectos->hasPages())
        <div class="px-5 py-4 border-t border-slate-800/60">{{ $proyectos->links() }}</div>
        @endif
    </div>
    <p class="text-xs text-slate-700 mt-3 font-mono">{{ $proyectos->total() }} proyecto(s)</p>
</div>
