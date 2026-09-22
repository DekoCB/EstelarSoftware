<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl px-4 py-3">
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por número o cliente..."
                       class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl pl-8 pr-3 py-2
                              text-xs text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
            </div>
            <select wire:model.live="status"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-36">
                <option value="">Estado</option>
                <option value="borrador">Borrador</option>
                <option value="enviado">Enviado</option>
                <option value="aceptado">Aceptado</option>
                <option value="rechazado">Rechazado</option>
                <option value="facturado">Facturado</option>
            </select>
            <select wire:model.live="moneda"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-24">
                <option value="">Moneda</option>
                <option value="PEN">PEN</option>
                <option value="USD">USD</option>
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Número</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($quotes as $q)
                <tr class="hover:bg-slate-800/30 transition-colors group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <p class="text-xs font-bold font-mono text-white">{{ $q->numero }}</p>
                            @if($q->estaVencida())
                            <span class="text-[9px] font-semibold text-red-400 bg-red-500/10 px-1.5 py-0.5 rounded-md">VENCIDA</span>
                            @endif
                        </div>
                        @if($q->project)
                        <p class="text-[10px] text-slate-600 mt-0.5 truncate max-w-[160px]">{{ $q->project->name }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-xs text-white truncate max-w-[180px]">{{ $q->client->razon_social }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs font-mono text-slate-400">{{ $q->fecha_emision->format('d/m/Y') }}</p>
                        @if($q->fecha_vencimiento)
                        <p class="text-[10px] text-slate-600 font-mono">vence {{ $q->fecha_vencimiento->format('d/m/Y') }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <p class="text-xs font-bold font-mono text-white">
                            {{ $q->monedaSimbolo() }} {{ number_format($q->total, 2) }}
                        </p>
                        @if($q->incluye_igv)
                        <p class="text-[10px] text-slate-600">inc. IGV</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $q->statusBadgeClass() }}">
                            {{ $q->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('cotizaciones.show', $q) }}"
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
                        <p class="text-slate-600 text-sm">No hay cotizaciones{{ $search ? ' que coincidan con la búsqueda' : '' }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($quotes->hasPages())
    <div class="px-2">
        {{ $quotes->links() }}
    </div>
    @endif

</div>
