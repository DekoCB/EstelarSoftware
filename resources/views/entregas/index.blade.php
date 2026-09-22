<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-600">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="font-semibold text-white">Entregas</h1>
        </div>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Entregas de proyectos</h2>
            <p class="text-sm text-slate-500 mt-0.5">Actas de entrega de proyectos a clientes</p>
        </div>
        @can('entregas.crear')
        <a href="{{ route('entregas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                  shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                  transition-all active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nueva acta
        </a>
        @endcan
    </div>

    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Título / Proyecto</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($entregas as $e)
                <tr class="hover:bg-slate-800/30 transition-colors group">
                    <td class="px-5 py-3.5">
                        <p class="text-xs font-semibold text-white">{{ $e->titulo }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $e->project->name ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-xs text-slate-300 truncate max-w-[180px]">{{ $e->client->razon_social }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs font-mono text-slate-400">{{ $e->fecha_entrega->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-[10px] px-2 py-0.5 rounded-md
                                     {{ $e->tipo === 'final' ? 'bg-sky-500/10 text-sky-400' : 'bg-slate-500/10 text-slate-400' }}">
                            {{ $e->tipoLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $e->estadoBadgeClass() }}">
                            {{ $e->estadoLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('entregas.show', $e) }}"
                           class="p-1.5 rounded-lg text-slate-600 hover:text-sky-400 hover:bg-sky-500/10
                                  transition-colors opacity-0 group-hover:opacity-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <p class="text-slate-600 text-sm">No hay actas de entrega registradas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entregas->hasPages())
    <div class="mt-4 px-2">
        {{ $entregas->links() }}
    </div>
    @endif

</x-app-layout>
