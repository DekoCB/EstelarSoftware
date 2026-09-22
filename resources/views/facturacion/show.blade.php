<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('facturacion.index') }}" class="text-slate-600 hover:text-slate-400">Facturación</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold font-mono">{{ $factura->numero_completo ?: $factura->serie.'-????' }}</span>
        </div>
    </x-slot>

    {{-- ── Aviso eliminado ────────────────────────────────────────────── --}}
    @if($factura->trashed())
    <div class="bg-red-500/10 border border-red-500/30 rounded-2xl px-5 py-3 mb-4 flex items-center gap-3">
        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
        </svg>
        <p class="text-sm text-red-400">Este comprobante fue eliminado. Solo está disponible para consulta y descarga.</p>
    </div>
    @endif

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 mb-1 flex-wrap">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md
                        {{ $factura->esFactura() ? 'bg-sky-500/10 text-sky-400' : 'bg-violet-500/10 text-violet-400' }}">
                        {{ $factura->tipoLabel() }}
                    </span>
                    <h1 class="text-xl font-bold font-mono text-white">
                        {{ $factura->numero_completo ?: ($factura->serie . '-' . str_pad($factura->correlativo ?: '????', 8, '0', STR_PAD_LEFT)) }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $factura->estadoBadgeClass() }}">
                        {{ $factura->estadoLabel() }}
                    </span>
                </div>
                <p class="text-slate-300 font-semibold">{{ $factura->client->razon_social }}</p>
                <p class="text-xs text-slate-600 font-mono mt-0.5">{{ $factura->client->tipo_documento }}: {{ $factura->client->numero_documento }}</p>
                <div class="flex items-center gap-4 mt-2 text-xs text-slate-600 font-mono flex-wrap">
                    <span>Emisión: {{ $factura->fecha_emision->format('d/m/Y') }}</span>
                    @if($factura->emitido_at)
                    <span class="text-emerald-400">Enviado: {{ $factura->emitido_at->format('d/m/Y H:i') }}</span>
                    @endif
                    @if($factura->quote)
                    <span>Cot: <a href="{{ route('cotizaciones.show', $factura->quote) }}" class="text-sky-400 hover:underline">{{ $factura->quote->numero }}</a></span>
                    @endif
                </div>
                @if($factura->sunat_mensaje)
                @php
                    $msgColor  = $factura->estado_sunat === 'aceptado' ? 'text-emerald-400' : 'text-red-400';
                    $msgCorto  = Str::limit($factura->sunat_mensaje, 120);
                    $esTrunco  = strlen($factura->sunat_mensaje) > 120;
                @endphp
                <div x-data="{ expandido: false }" class="mt-2">
                    <p class="text-xs {{ $msgColor }} font-mono leading-relaxed">
                        <span x-show="!expandido">{{ $msgCorto }}</span>
                        <span x-show="expandido" x-cloak>{{ $factura->sunat_mensaje }}</span>
                        @if($esTrunco)
                        <button @click="expandido = !expandido"
                                class="ml-1 underline opacity-60 hover:opacity-100 transition-opacity text-[10px]">
                            <span x-show="!expandido">ver más</span>
                            <span x-show="expandido" x-cloak>ver menos</span>
                        </button>
                        @endif
                    </p>
                </div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                @if(!$factura->trashed())
                @can('facturacion.emitir')
                @if($factura->puedeEmitirse())
                <form method="POST" action="{{ route('facturacion.enviar', $factura) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                                   bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                                   hover:from-sky-400 hover:to-cyan-400 transition-all active:scale-[0.98]
                                   shadow-[0_0_16px_rgba(14,165,233,0.35)]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                        </svg>
                        Enviar a SUNAT
                    </button>
                </form>
                @endif
                @endcan

                @if($factura->sunat_doc_id && $factura->emitido_at)
                @can('facturacion.ver')
                <a href="{{ route('facturacion.pdf', $factura) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-sky-500/30 hover:text-sky-400 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('facturacion.xml', $factura) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-emerald-500/30 hover:text-emerald-400 transition-all">
                    XML
                </a>
                @if($factura->estado_sunat === 'aceptado')
                <a href="{{ route('facturacion.cdr', $factura) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          bg-slate-800 text-slate-300 border border-slate-700/60
                          hover:border-violet-500/30 hover:text-violet-400 transition-all">
                    CDR
                </a>
                @endif
                @endcan
                @elseif($factura->sunat_doc_id && !$factura->emitido_at)
                @can('facturacion.ver')
                <span class="text-xs text-slate-600 italic px-2 py-2">Envía a SUNAT para descargar PDF/XML</span>
                @endcan
                @endif

                @can('facturacion.anular')
                @if($factura->puedeBorrarse())
                <form method="POST" action="{{ route('facturacion.destroy', $factura) }}"
                      x-data @submit.prevent="if(confirm('¿Eliminar {{ $factura->numero_completo ?? $factura->serie }}?\n\nSolo se elimina este comprobante.')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-medium text-red-400
                                   border border-red-500/20 hover:bg-red-500/10 transition-colors">
                        Eliminar
                    </button>
                </form>
                @endif
                @endcan
                @endif {{-- !trashed --}}
            </div>
        </div>
    </div>

    {{-- ── Tabla de ítems ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800/60">
                    <h3 class="text-sm font-semibold text-white">Detalle</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800/40">
                            <th class="text-left px-6 py-3 text-xs font-medium text-slate-600">Descripción</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-600 hidden sm:table-cell">Cant.</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-600 hidden sm:table-cell">P. Unit.</th>
                            <th class="text-center px-3 py-3 text-xs font-medium text-slate-600 hidden sm:table-cell">IGV</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-slate-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @foreach($factura->items as $item)
                        <tr>
                            <td class="px-6 py-3.5">
                                <p class="text-xs font-medium text-white">{{ $item->descripcion }}</p>
                                <p class="text-[10px] text-slate-600 mt-0.5 font-mono">{{ $item->unidad_sunat }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs font-mono text-slate-400 hidden sm:table-cell">
                                {{ number_format($item->cantidad, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs font-mono text-slate-400 hidden sm:table-cell">
                                {{ $factura->monedaSimbolo() }} {{ number_format($item->precio_unitario, 2) }}
                            </td>
                            <td class="px-3 py-3.5 text-center hidden sm:table-cell">
                                @if($item->tipo_afectacion === '10')
                                <span class="text-[10px] text-sky-400 bg-sky-500/10 px-1.5 py-0.5 rounded-md">18%</span>
                                @elseif($item->tipo_afectacion === '20')
                                <span class="text-[10px] text-slate-500 bg-slate-700/40 px-1.5 py-0.5 rounded-md">Exo</span>
                                @else
                                <span class="text-[10px] text-slate-500 bg-slate-700/40 px-1.5 py-0.5 rounded-md">Ina</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right text-xs font-semibold font-mono text-white">
                                {{ $factura->monedaSimbolo() }} {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-700/60 bg-slate-800/30">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-xs text-slate-500 text-right hidden sm:table-cell">Subtotal (sin IGV)</td>
                            <td class="px-6 py-3 text-right text-xs font-mono font-semibold text-white">
                                {{ $factura->monedaSimbolo() }} {{ number_format($factura->subtotal, 2) }}
                            </td>
                        </tr>
                        @if($factura->igv > 0)
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-xs text-slate-500 text-right hidden sm:table-cell">IGV (18%)</td>
                            <td class="px-6 py-2 text-right text-xs font-mono text-slate-400">
                                {{ $factura->monedaSimbolo() }} {{ number_format($factura->igv, 2) }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-sm font-bold text-white text-right hidden sm:table-cell">TOTAL</td>
                            <td class="px-6 py-3 text-right text-base font-bold font-mono text-sky-400">
                                {{ $factura->monedaSimbolo() }} {{ number_format($factura->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div class="space-y-4">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Información</h3>
                <div>
                    <p class="text-[10px] text-slate-600">Total</p>
                    <p class="text-xl font-bold font-mono text-sky-400">
                        {{ $factura->monedaSimbolo() }} {{ number_format($factura->total, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-600">Moneda</p>
                    <p class="text-sm text-white mt-0.5">{{ $factura->moneda }}</p>
                </div>
                @if($factura->sunat_doc_id)
                <div>
                    <p class="text-[10px] text-slate-600">ID en API SUNAT</p>
                    <p class="text-sm font-mono text-slate-400 mt-0.5">#{{ $factura->sunat_doc_id }}</p>
                </div>
                @endif
                <div>
                    <p class="text-[10px] text-slate-600">Creado por</p>
                    <p class="text-sm text-white mt-0.5">{{ $factura->createdBy->name }}</p>
                    <p class="text-[10px] text-slate-600 font-mono">{{ $factura->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if(!$apiOk && !$factura->sunat_doc_id)
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4">
                <p class="text-xs font-semibold text-amber-400 mb-1">API SUNAT no conectada</p>
                <p class="text-[10px] text-amber-300/70">Configura la API para poder emitir este comprobante.</p>
            </div>
            @endif
        </div>
    </div>

</x-app-layout>
