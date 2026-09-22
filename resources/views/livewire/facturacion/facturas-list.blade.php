<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl px-4 py-3">
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <div class="relative w-full sm:flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por número o cliente..."
                       class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl pl-8 pr-3 py-2
                              text-xs text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
            </div>
            <select wire:model.live="tipo"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-32">
                <option value="">Tipo</option>
                <option value="01">Facturas</option>
                <option value="03">Boletas</option>
            </select>
            <select wire:model.live="estadoSunat"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-36">
                <option value="">Estado SUNAT</option>
                <option value="borrador">Borrador</option>
                <option value="pendiente">Pendiente</option>
                <option value="aceptado">Aceptado</option>
                <option value="rechazado">Rechazado</option>
                <option value="error">Error</option>
            </select>
            <label class="flex items-center gap-1.5 cursor-pointer shrink-0 select-none">
                <input type="checkbox" wire:model.live="conEliminados"
                       class="w-3.5 h-3.5 rounded border-slate-600 bg-slate-800 text-sky-500
                              focus:ring-0 focus:ring-offset-0 cursor-pointer">
                <span class="text-xs text-slate-500">Solo eliminados</span>
            </label>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Comprobante</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">SUNAT</th>
                    <th class="px-4 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($facturas as $f)
                <tr class="hover:bg-slate-800/30 transition-colors group {{ $f->trashed() ? 'opacity-50' : '' }}">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                {{ $f->esFactura() ? 'bg-sky-500/10 text-sky-400' : 'bg-violet-500/10 text-violet-400' }}">
                                {{ $f->tipoLabel() }}
                            </span>
                            <p class="text-xs font-bold font-mono {{ $f->trashed() ? 'line-through text-slate-500' : 'text-white' }}">
                                {{ $f->numero_completo ?: ($f->serie . '-????') }}
                            </p>
                            @if($f->trashed())
                            <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-red-500/10 text-red-400">Eliminado</span>
                            @endif
                        </div>
                        @if($f->quote)
                        <p class="text-[10px] text-slate-600 mt-0.5">Cot: {{ $f->quote->numero }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-xs text-white truncate max-w-[180px]">{{ $f->client->razon_social }}</p>
                        <p class="text-[10px] text-slate-600 font-mono">{{ $f->client->numero_documento }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs font-mono text-slate-400">{{ $f->fecha_emision->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <p class="text-xs font-bold font-mono text-white">
                            {{ $f->monedaSimbolo() }} {{ number_format($f->total, 2) }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $f->estadoBadgeClass() }}">
                            {{ $f->estadoLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('facturacion.show', $f) }}"
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
                        <p class="text-slate-600 text-sm">No hay comprobantes registrados</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($facturas->hasPages())
    <div class="px-2">{{ $facturas->links() }}</div>
    @endif
</div>
