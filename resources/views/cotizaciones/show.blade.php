<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('cotizaciones.index') }}" class="text-slate-600 hover:text-slate-400">Cotizaciones</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold font-mono">{{ $cotizacion->numero }}</span>
        </div>
    </x-slot>

    @php
        $siguientes = collect(\App\Models\Quote::ESTADOS)
            ->reject(fn($s) => $s === $cotizacion->status)
            ->values();
    @endphp

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 mb-1 flex-wrap">
                    <h1 class="text-xl font-bold font-mono text-white">{{ $cotizacion->numero }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $cotizacion->statusBadgeClass() }}">
                        {{ $cotizacion->statusLabel() }}
                    </span>
                    @if($cotizacion->estaVencida())
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-500/10 text-red-400 ring-1 ring-red-500/20">
                        Vencida
                    </span>
                    @endif
                </div>
                <p class="text-slate-400 font-semibold">{{ $cotizacion->client->razon_social }}</p>
                @if($cotizacion->project)
                <p class="text-xs text-slate-600 mt-0.5">Proyecto: {{ $cotizacion->project->name }}</p>
                @endif
                <div class="flex items-center gap-4 mt-2 text-xs text-slate-600 font-mono flex-wrap">
                    <span>Emitida: {{ $cotizacion->fecha_emision->format('d/m/Y') }}</span>
                    @if($cotizacion->fecha_vencimiento)
                    <span class="{{ $cotizacion->estaVencida() ? 'text-red-400' : '' }}">
                        Vence: {{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}
                    </span>
                    @endif
                    @if($cotizacion->sent_at)
                    <span class="text-sky-400">Enviada: {{ $cotizacion->sent_at->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                @can('cotizaciones.pdf')
                <a href="{{ route('cotizaciones.pdf', $cotizacion) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-sky-500/30 hover:text-sky-400 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                    Ver PDF
                </a>
                @endcan

                @can('cotizaciones.editar')
                @if(in_array($cotizacion->status, ['borrador', 'enviado']))
                <a href="{{ route('cotizaciones.edit', $cotizacion) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-sky-500/30 hover:text-sky-400 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/>
                    </svg>
                    Editar
                </a>
                @endif
                @endcan

                @can('cotizaciones.aprobar')
                <div x-data="{ menuEstado: false }" class="relative">
                    <button @click="menuEstado = !menuEstado"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                                   bg-sky-500 text-white hover:bg-sky-400 transition-all">
                        Cambiar estado
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>
                    <div x-show="menuEstado" @click.outside="menuEstado = false"
                         class="absolute right-0 top-11 z-20 bg-slate-800 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl w-40"
                         style="display:none">
                        @foreach(\App\Models\Quote::ESTADO_LABELS as $s => $label)
                        @if($s !== $cotizacion->status)
                        <form method="POST" action="{{ route('cotizaciones.estado', $cotizacion) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $s }}">
                            <button type="submit" class="w-full text-left px-3 py-2.5 text-xs text-slate-300 hover:bg-slate-700/60 transition-colors">
                                {{ $label }}
                            </button>
                        </form>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Tabla de ítems ───────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800/60">
                    <h3 class="text-sm font-semibold text-white">Ítems</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800/40">
                            <th class="text-left px-6 py-3 text-xs font-medium text-slate-600">Descripción</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-600">Cant.</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-600 hidden sm:table-cell">P. Unit.</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-600 hidden sm:table-cell">Dto %</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-slate-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @foreach($cotizacion->items as $item)
                        <tr>
                            <td class="px-6 py-3.5">
                                <p class="text-xs font-medium text-white">{{ $item->descripcion }}</p>
                                <p class="text-[10px] text-slate-600 mt-0.5">{{ $item->unidad }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs font-mono text-slate-400">{{ number_format($item->cantidad, 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-xs font-mono text-slate-400 hidden sm:table-cell">{{ $cotizacion->monedaSimbolo() }} {{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-xs font-mono text-slate-600 hidden sm:table-cell">{{ $item->descuento > 0 ? $item->descuento.'%' : '—' }}</td>
                            <td class="px-6 py-3.5 text-right text-xs font-semibold font-mono text-white">{{ $cotizacion->monedaSimbolo() }} {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-700/60 bg-slate-800/30">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-xs text-slate-500 text-right hidden sm:table-cell">Subtotal</td>
                            <td class="px-6 py-3 text-right text-xs font-mono font-semibold text-white">{{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->subtotal, 2) }}</td>
                        </tr>
                        @if($cotizacion->igv > 0)
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-xs text-slate-500 text-right hidden sm:table-cell">IGV ({{ $config?->igv_porcentaje ?? 18 }}%)</td>
                            <td class="px-6 py-2 text-right text-xs font-mono text-slate-400">{{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->igv, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-sm font-bold text-white text-right hidden sm:table-cell">TOTAL</td>
                            <td class="px-6 py-3 text-right text-base font-bold font-mono text-sky-400">
                                {{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Notas --}}
            @if($cotizacion->notas || $cotizacion->terminos)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($cotizacion->notas)
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Notas</h4>
                    <p class="text-xs text-slate-400 leading-relaxed whitespace-pre-wrap">{{ $cotizacion->notas }}</p>
                </div>
                @endif
                @if($cotizacion->terminos)
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Términos</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-mono whitespace-pre-wrap">{{ $cotizacion->terminos }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- ── Panel lateral ────────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Resumen --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Resumen</h3>
                <div>
                    <p class="text-[10px] text-slate-600">Moneda</p>
                    <p class="text-sm font-semibold text-white mt-0.5">{{ $cotizacion->moneda }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-600">Total</p>
                    <p class="text-xl font-bold font-mono text-sky-400 mt-0.5">
                        {{ $cotizacion->monedaSimbolo() }} {{ number_format($cotizacion->total, 2) }}
                    </p>
                    @if($cotizacion->incluye_igv)
                    <p class="text-[10px] text-slate-600 mt-0.5">Incluye IGV</p>
                    @else
                    <p class="text-[10px] text-slate-600 mt-0.5">Sin IGV</p>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] text-slate-600">Creada por</p>
                    <p class="text-sm text-white mt-0.5">{{ $cotizacion->createdBy->name }}</p>
                    <p class="text-[10px] text-slate-600 font-mono">{{ $cotizacion->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- Zona peligrosa --}}
            @can('cotizaciones.eliminar')
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Zona peligrosa</h3>
                <form method="POST" action="{{ route('cotizaciones.destroy', $cotizacion) }}"
                      x-data @submit.prevent="if(confirm('¿Eliminar la cotización {{ $cotizacion->numero }}?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full py-2 rounded-xl text-xs font-medium text-red-400
                                   border border-red-500/20 hover:bg-red-500/10 transition-colors">
                        Eliminar cotización
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>

    {{-- ══ Plan de cobros ════════════════════════════════════════════════ --}}
    @php
        $pagos        = $cotizacion->payments;
        $cobrado      = $cotizacion->montoCobrado();
        $pendiente    = $cotizacion->montoPendiente();
        $pctCobrado   = $cotizacion->porcentajeCobrado();
        $sim          = $cotizacion->monedaSimbolo();
    @endphp

    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mt-5">

        {{-- Cabecera --}}
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <div>
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                    </svg>
                    Plan de cobros
                </h3>
                @if($pagos->isNotEmpty())
                <p class="text-xs text-slate-500 mt-0.5 ml-6">
                    Cobrado: <span class="text-emerald-400 font-semibold font-mono">{{ $sim }} {{ number_format($cobrado, 2) }}</span>
                    de <span class="text-white font-semibold font-mono">{{ $sim }} {{ number_format($cotizacion->total, 2) }}</span>
                </p>
                @endif
            </div>
            @can('cotizaciones.editar')
            <div class="flex items-center gap-2">
                @if($pagos->isEmpty())
                <form method="POST" action="{{ route('cotizaciones.pagos.plan', $cotizacion) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   text-sky-400 bg-sky-500/10 border border-sky-500/20 hover:bg-sky-500/20 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Generar plan (40% + 30% + 30%)
                    </button>
                </form>
                @endif
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   text-slate-400 bg-slate-800/60 border border-slate-700/40 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar cuota
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute right-0 top-9 z-30 bg-slate-800 border border-slate-700/60 rounded-2xl shadow-2xl p-4 w-72"
                         style="display:none">
                        <p class="text-xs font-semibold text-white mb-3">Nueva cuota</p>
                        <form method="POST" action="{{ route('cotizaciones.pagos.store', $cotizacion) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Nombre</label>
                                <input type="text" name="nombre" placeholder="Anticipo, 2da cuota..." class="input-dark text-xs py-1.5" required>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">% del total ({{ $sim }} {{ number_format($cotizacion->total, 2) }})</label>
                                <input type="number" name="porcentaje" min="0.01" max="100" step="0.01"
                                       placeholder="40" class="input-dark text-xs py-1.5 font-mono" required>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Fecha vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="input-dark text-xs py-1.5 font-mono">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="px-3 py-1.5 text-xs text-slate-500 hover:text-white transition-colors">Cancelar</button>
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-sky-500 text-white hover:bg-sky-400 transition-colors">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        {{-- Barra de progreso --}}
        @if($pagos->isNotEmpty())
        <div class="mb-5">
            <div class="flex items-center justify-between mb-1.5 text-[10px] text-slate-600 font-mono">
                <span>{{ $pctCobrado }}% cobrado</span>
                <span>Pendiente: {{ $sim }} {{ number_format($pendiente, 2) }}</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700
                    {{ $pctCobrado >= 100 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : 'bg-gradient-to-r from-sky-500 to-cyan-400' }}"
                     style="width: {{ $pctCobrado }}%"></div>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($pagos as $pago)
            <div class="bg-slate-800/50 border {{ $pago->estado === 'pagada' ? 'border-emerald-500/20' : ($pago->estaVencida() ? 'border-red-500/20' : 'border-slate-700/40') }} rounded-2xl p-4"
                 x-data="{ modalPagar: false }">

                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-white">{{ $pago->nombre }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $pago->estadoBadgeClass() }}">
                                {{ $pago->estadoLabel() }}
                            </span>
                            @if($pago->invoice)
                            <a href="{{ route('facturacion.show', $pago->invoice) }}"
                               class="text-[10px] text-violet-400 hover:underline font-mono">
                                {{ $pago->invoice->numero_completo ?? 'Ver comprobante' }}
                            </a>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-1.5 flex-wrap text-xs text-slate-500 font-mono">
                            <span class="text-white font-bold">{{ $sim }} {{ number_format($pago->monto, 2) }}</span>
                            <span class="text-slate-600">{{ $pago->porcentaje }}%</span>
                            @if($pago->fecha_vencimiento)
                            <span class="{{ $pago->estaVencida() ? 'text-red-400' : '' }}">Vence: {{ $pago->fecha_vencimiento->format('d/m/Y') }}</span>
                            @endif
                            @if($pago->fecha_pago)
                            <span class="text-emerald-400">Pagado: {{ $pago->fecha_pago->format('d/m/Y') }}</span>
                            @endif
                            @if($pago->metodo_pago)
                            <span>{{ $pago->metodo_pago }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        @can('facturacion.emitir')
                        @if(!$pago->invoice_id)
                        <a href="{{ route('facturacion.create') }}?quote_id={{ $cotizacion->id }}&payment_id={{ $pago->id }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                  text-sky-400 bg-sky-500/10 border border-sky-500/20 hover:bg-sky-500/20 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                            Emitir
                        </a>
                        @endif
                        @endcan

                        @can('cotizaciones.editar')
                        @if($pago->estado !== 'pagada')
                        <button @click="modalPagar = !modalPagar"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                       text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            Cobrado
                        </button>
                        @else
                        <form method="POST" action="{{ route('cotizaciones.pagos.revertir', [$cotizacion, $pago]) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-[10px] text-slate-600 hover:text-amber-400 px-2 py-1.5 rounded-lg transition-colors">Revertir</button>
                        </form>
                        @endif
                        @if($pago->estado !== 'pagada')
                        <form method="POST" action="{{ route('cotizaciones.pagos.destroy', [$cotizacion, $pago]) }}"
                              x-data @submit.prevent="if(confirm('¿Eliminar esta cuota?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-700 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </div>
                </div>

                {{-- Formulario cobro inline --}}
                <div x-show="modalPagar" class="mt-3 pt-3 border-t border-slate-700/40" style="display:none">
                    <p class="text-xs font-semibold text-white mb-3">Registrar cobro — {{ $sim }} {{ number_format($pago->monto, 2) }}</p>
                    <form method="POST" action="{{ route('cotizaciones.pagos.pagar', [$cotizacion, $pago]) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Fecha de cobro</label>
                            <input type="date" name="fecha_pago" value="{{ now()->format('Y-m-d') }}" class="input-dark text-xs py-1.5 font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Método</label>
                            <select name="metodo_pago" class="input-dark text-xs py-1.5">
                                <option value="">— sin especificar</option>
                                <option value="Transferencia bancaria">Transferencia bancaria</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">N° operación</label>
                            <input type="text" name="notas" placeholder="Referencia, código..." class="input-dark text-xs py-1.5">
                        </div>
                        <div class="sm:col-span-3 flex justify-end gap-2">
                            <button type="button" @click="modalPagar = false" class="px-3 py-1.5 text-xs text-slate-500 hover:text-white transition-colors">Cancelar</button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-emerald-500 text-white hover:bg-emerald-400 transition-colors">
                                Confirmar cobro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="text-center py-10 text-slate-600">
            <svg class="w-8 h-8 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
            </svg>
            <p class="text-sm">Sin plan de cobros</p>
            <p class="text-xs mt-1 text-slate-700">Genera el plan estándar (40% + 30% + 30%) o agrega cuotas personalizadas</p>
        </div>
        @endif
    </div>

</x-app-layout>
