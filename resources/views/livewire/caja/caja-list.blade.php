<div class="space-y-4">

    {{-- KPIs resumen ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-slate-900 border border-emerald-500/20 rounded-2xl px-5 py-4">
            <p class="text-xs text-slate-500 mb-1">Ingresos</p>
            <p class="text-xl font-bold font-mono text-emerald-400">S/ {{ number_format($totalIngresos, 2) }}</p>
        </div>
        <div class="bg-slate-900 border border-rose-500/20 rounded-2xl px-5 py-4">
            <p class="text-xs text-slate-500 mb-1">Egresos</p>
            <p class="text-xl font-bold font-mono text-rose-400">S/ {{ number_format($totalEgresos, 2) }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-700/40 rounded-2xl px-5 py-4
                    {{ $saldo >= 0 ? 'border-sky-500/20' : 'border-amber-500/20' }}">
            <p class="text-xs text-slate-500 mb-1">Saldo</p>
            <p class="text-xl font-bold font-mono {{ $saldo >= 0 ? 'text-sky-400' : 'text-amber-400' }}">
                S/ {{ number_format($saldo, 2) }}
            </p>
        </div>
    </div>

    {{-- Filtros ────────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl px-4 py-3">
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <div class="relative flex-1 min-w-[180px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Buscar concepto, referencia..."
                       class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl pl-8 pr-3 py-2
                              text-xs text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
            </div>
            <select wire:model.live="tipo"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-28">
                <option value="">Tipo</option>
                <option value="ingreso">Ingreso</option>
                <option value="egreso">Egreso</option>
            </select>
            <select wire:model.live="categoria"
                    class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                           focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-40">
                <option value="">Categoría</option>
                <optgroup label="Ingresos">
                    <option value="cobro_cliente">Cobro a cliente</option>
                    <option value="anticipo_cliente">Anticipo de cliente</option>
                    <option value="otro_ingreso">Otro ingreso</option>
                </optgroup>
                <optgroup label="Egresos">
                    <option value="pago_proveedor">Pago a proveedor</option>
                    <option value="planilla">Planilla</option>
                    <option value="servicios">Servicios</option>
                    <option value="equipos">Equipos</option>
                    <option value="impuestos">Impuestos</option>
                    <option value="otro_egreso">Otro egreso</option>
                </optgroup>
            </select>
            <input type="month" wire:model.live="mes"
                   class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-xs text-slate-300
                          focus:outline-none focus:border-sky-500/60 transition-colors shrink-0 w-full sm:w-36">
        </div>
    </div>

    {{-- Tabla ──────────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Concepto</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Categoría</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Monto</th>
                    <th class="px-4 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($movimientos as $m)
                <tr class="hover:bg-slate-800/30 transition-colors group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $m->tipo === 'ingreso' ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>
                            <div>
                                <p class="text-xs font-semibold text-white">{{ $m->concepto }}</p>
                                @if($m->client)
                                <p class="text-[10px] text-slate-500 truncate max-w-[160px]">{{ $m->client->razon_social }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="text-[10px] px-2 py-0.5 rounded-md
                                     {{ $m->tipo === 'ingreso' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                            {{ $m->categoriaLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs font-mono text-slate-400">{{ $m->fecha->format('d/m/Y') }}</p>
                        <p class="text-[10px] text-slate-600">{{ $m->metodoPagoLabel() }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <p class="text-sm font-bold font-mono {{ $m->tipo === 'ingreso' ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $m->tipo === 'ingreso' ? '+' : '-' }} {{ $m->moneda === 'USD' ? '$' : 'S/' }} {{ $m->montoFormateado() }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('caja.show', $m) }}"
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
                    <td colspan="5" class="px-5 py-12 text-center">
                        <p class="text-slate-600 text-sm">No hay movimientos{{ $search ? ' que coincidan' : '' }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movimientos->hasPages())
    <div class="px-2">
        {{ $movimientos->links() }}
    </div>
    @endif

</div>
