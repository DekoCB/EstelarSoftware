<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('facturacion.index') }}" class="text-slate-600 hover:text-slate-400">Facturación</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Nuevo comprobante</span>
        </div>
    </x-slot>

    @php $igvPct = $config?->igv_porcentaje ?? 18; @endphp

    @if(!$apiOk)
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl px-5 py-4 mb-5 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-amber-400">API SUNAT no configurada — modo borrador</p>
            <p class="text-xs text-amber-300/70 mt-1">El comprobante se guardará en estado borrador. Configura la API para poder emitirlo a SUNAT.</p>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-500/10 border border-red-500/30 rounded-2xl px-5 py-4 mb-4">
        <p class="text-sm font-semibold text-red-400 mb-2">Corrige los siguientes errores:</p>
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
            <li class="text-xs text-red-300">• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($preQuote)
    <div class="bg-sky-500/10 border border-sky-500/30 rounded-2xl px-5 py-3 mb-5 flex items-center gap-3">
        <svg class="w-4 h-4 text-sky-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
        </svg>
        <p class="text-xs text-sky-300">
            Pre-llenado desde cotización <span class="font-bold font-mono">{{ $preQuote->numero }}</span>
            @if($prePago) — cuota <span class="font-bold">{{ $prePago->nombre }}</span> ({{ $prePago->porcentaje }}%) @endif
        </p>
    </div>
    @endif

    <div class="max-w-4xl mx-auto"
         x-data="{
            tipo: '{{ old('tipo_comprobante', $preQuote?->client?->tipo_documento === 'RUC' ? '01' : '03') ?? '01' }}',
            igvPct: {{ $igvPct }},
            incluyeIgv: true,
            items: {{ Js::from(count($preItems) ? $preItems : [['descripcion' => '', 'unidad_sunat' => 'ZZ', 'cantidad' => 1, 'precio_unitario' => 0, 'tipo_afectacion' => '10']]) }},
            addItem() { this.items.push({ descripcion: '', unidad_sunat: 'ZZ', cantidad: 1, precio_unitario: 0, tipo_afectacion: '10' }); },
            removeItem(i) { if(this.items.length > 1) this.items.splice(i, 1); },
            subtotalItem(item) {
                return parseFloat(item.cantidad||0) * parseFloat(item.precio_unitario||0);
            },
            igvItem(item) {
                return item.tipo_afectacion === '10' ? this.subtotalItem(item) * this.igvPct / 100 : 0;
            },
            get subtotal() { return this.items.reduce((s,i) => s + this.subtotalItem(i), 0); },
            get igv()      { return this.items.reduce((s,i) => s + this.igvItem(i), 0); },
            get total()    { return this.subtotal + this.igv; },
            fmt(n) { return new Intl.NumberFormat('es-PE',{minimumFractionDigits:2}).format(n||0); }
         }">

        <h2 class="text-xl font-bold text-white mb-6">Nuevo comprobante electrónico</h2>

        <form method="POST" action="{{ route('facturacion.store') }}" class="space-y-6">
            @csrf

            {{-- ── 1: Tipo + datos ──────────────────────────────────── --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">1</span>
                    Tipo de comprobante
                </h3>

                {{-- Toggle Factura / Boleta --}}
                <div class="flex gap-3 mb-5">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="tipo_comprobante" value="01" x-model="tipo" class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 transition-all
                                    peer-checked:border-sky-500 peer-checked:bg-sky-500/10
                                    border-slate-700/60 hover:border-slate-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" :class="tipo==='01' ? 'text-sky-400' : 'text-slate-500'">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                            <span class="text-sm font-semibold" :class="tipo==='01' ? 'text-sky-400' : 'text-slate-400'">Factura</span>
                            <span class="text-[10px]" :class="tipo==='01' ? 'text-sky-500/70' : 'text-slate-600'">F001 · Empresas (RUC)</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="tipo_comprobante" value="03" x-model="tipo" class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 transition-all
                                    peer-checked:border-violet-500 peer-checked:bg-violet-500/10
                                    border-slate-700/60 hover:border-slate-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" :class="tipo==='03' ? 'text-violet-400' : 'text-slate-500'">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                            <span class="text-sm font-semibold" :class="tipo==='03' ? 'text-violet-400' : 'text-slate-400'">Boleta</span>
                            <span class="text-[10px]" :class="tipo==='03' ? 'text-violet-500/70' : 'text-slate-600'">B001 · Personas (DNI)</span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Serie (auto según tipo, desde configuración) --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Serie</label>
                        <input type="text" name="serie"
                               :value="tipo === '01' ? '{{ $serieFactura }}' : '{{ $serieBoleta }}'"
                               class="input-dark font-mono"
                               readonly>
                        <p class="text-[10px] text-slate-600 mt-1">
                            Factura: <span class="font-mono">{{ $serieFactura }}</span> ·
                            Boleta: <span class="font-mono">{{ $serieBoleta }}</span>
                            <a href="{{ route('configuracion.index') }}?tab=series" class="text-sky-500 hover:underline ml-1">cambiar</a>
                        </p>
                    </div>

                    {{-- Cotización de referencia --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">
                            Cotización de referencia <span class="text-slate-600 font-normal">— opcional</span>
                        </label>
                        <select name="quote_id" class="input-dark">
                            <option value="">Sin cotización</option>
                            @foreach($cotizaciones as $cot)
                            <option value="{{ $cot->id }}" {{ (old('quote_id') ?? $preQuote?->id) == $cot->id ? 'selected' : '' }}>
                                {{ $cot->numero }} — {{ $cot->client->razon_social }} ({{ $cot->monedaSimbolo() }} {{ number_format($cot->total, 2) }})
                            </option>
                            @endforeach
                        </select>
                        @if($prePago)
                        <input type="hidden" name="payment_id" value="{{ $prePago->id }}">
                        @endif
                    </div>

                    {{-- Cliente --}}
                    <div class="sm:col-span-2 relative"
                         x-data="{
                            items:      {{ Js::from($clientes->map(fn($c) => ['id' => $c->id, 'label' => $c->razon_social, 'sub' => $c->tipo_documento.' · '.$c->numero_documento, 'tipo' => $c->tipo_documento])) }},
                            search: '', selectedId: {{ old('client_id', $preQuote?->client_id ?? 'null') }}, open: false,
                            get filtered() {
                                const q = this.search.toLowerCase();
                                const items = this.$parent.tipo === '01'
                                    ? this.items.filter(i => i.tipo === 'RUC')
                                    : this.items;
                                if (!q) return items.slice(0,10);
                                return items.filter(i => i.label.toLowerCase().includes(q)||i.sub.includes(q)).slice(0,10);
                            },
                            select(item){ this.selectedId=item.id; this.search=item.label; this.open=false; },
                            clear(){ this.selectedId=null; this.search=''; this.$refs.input.focus(); },
                            init(){ if(this.selectedId){ const f=this.items.find(i=>i.id==this.selectedId); if(f) this.search=f.label; } }
                         }">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">
                            Cliente <span class="text-red-400">*</span>
                            <span class="text-slate-600 font-normal ml-1" x-text="$parent.tipo==='01' ? '— solo clientes con RUC' : ''"></span>
                        </label>
                        <input type="hidden" name="client_id" :value="selectedId">
                        <div class="relative">
                            <input x-ref="input" type="text" x-model="search"
                                   @focus="open=true" @input="open=true; selectedId=null"
                                   @keydown.escape="open=false" @click.outside="open=false"
                                   placeholder="Buscar cliente..." autocomplete="off"
                                   class="input-dark pr-8 @error('client_id') error @enderror">
                            <button x-show="selectedId" type="button" @click="clear()"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="open && filtered.length > 0"
                             class="absolute z-[9999] left-0 right-0 mt-1 bg-slate-800 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl max-h-52 overflow-y-auto"
                             style="display:none">
                            <template x-for="item in filtered" :key="item.id">
                                <button type="button" @click="select(item)"
                                        class="w-full text-left px-4 py-2.5 hover:bg-slate-700/60 transition-colors border-b border-slate-700/40 last:border-0">
                                    <p class="text-sm text-white truncate" x-text="item.label"></p>
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5" x-text="item.sub"></p>
                                </button>
                            </template>
                        </div>
                        @error('client_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha de emisión</label>
                        <input type="date" name="fecha_emision" class="input-dark font-mono"
                               value="{{ old('fecha_emision', now()->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Moneda</label>
                        <select name="moneda" class="input-dark">
                            <option value="PEN" {{ old('moneda') !== 'USD' ? 'selected' : '' }}>S/ Soles (PEN)</option>
                            <option value="USD" {{ old('moneda') === 'USD' ? 'selected' : '' }}>$ Dólares (USD)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── 2: Ítems ──────────────────────────────────────────── --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">2</span>
                        Detalle de servicios
                    </h3>
                    <button type="button" @click="addItem()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   text-sky-400 bg-sky-500/10 border border-sky-500/20 hover:bg-sky-500/20 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar ítem
                    </button>
                </div>

                <div class="hidden sm:grid grid-cols-12 gap-2 mb-2 px-1">
                    <p class="col-span-5 text-[10px] font-semibold text-slate-600 uppercase tracking-wider">Descripción</p>
                    <p class="col-span-2 text-[10px] font-semibold text-slate-600 uppercase tracking-wider text-center">Cantidad</p>
                    <p class="col-span-2 text-[10px] font-semibold text-slate-600 uppercase tracking-wider text-right">Precio unit.</p>
                    <p class="col-span-1 text-[10px] font-semibold text-slate-600 uppercase tracking-wider text-center">IGV</p>
                    <p class="col-span-2 text-[10px] font-semibold text-slate-600 uppercase tracking-wider text-right">Total</p>
                </div>

                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center bg-slate-800/40 border border-slate-700/40 rounded-xl p-3">

                            {{-- Descripción + unidad --}}
                            <div class="col-span-12 sm:col-span-5 space-y-1.5">
                                <input type="text"
                                       x-model="item.descripcion"
                                       :name="`items[${index}][descripcion]`"
                                       placeholder="Descripción del servicio..."
                                       class="w-full bg-slate-800 border border-slate-700/40 rounded-lg px-3 py-2 text-xs text-white
                                              placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                                <select x-model="item.unidad_sunat"
                                        :name="`items[${index}][unidad_sunat]`"
                                        class="w-full bg-slate-800 border border-slate-700/40 rounded-lg px-3 py-1.5 text-xs text-slate-400
                                               focus:outline-none focus:border-sky-500/60 transition-colors">
                                    <option value="ZZ">ZZ — Servicio</option>
                                    <option value="NIU">NIU — Unidad</option>
                                    <option value="HUR">HUR — Hora</option>
                                    <option value="DIA">DIA — Día</option>
                                    <option value="MON">MON — Mes</option>
                                </select>
                            </div>

                            {{-- Cantidad --}}
                            <div class="col-span-3 sm:col-span-2">
                                <input type="number"
                                       x-model="item.cantidad"
                                       :name="`items[${index}][cantidad]`"
                                       min="0.01" step="0.01" placeholder="1"
                                       class="w-full bg-slate-800 border border-slate-700/40 rounded-lg px-3 py-2 text-xs text-white text-center
                                              focus:outline-none focus:border-sky-500/60 transition-colors">
                            </div>

                            {{-- Precio --}}
                            <div class="col-span-4 sm:col-span-2">
                                <input type="number"
                                       x-model="item.precio_unitario"
                                       :name="`items[${index}][precio_unitario]`"
                                       min="0" step="0.01" placeholder="0.00"
                                       class="w-full bg-slate-800 border border-slate-700/40 rounded-lg px-3 py-2 text-xs text-white text-right
                                              focus:outline-none focus:border-sky-500/60 transition-colors">
                            </div>

                            {{-- Afectación IGV --}}
                            <div class="col-span-3 sm:col-span-1">
                                <select x-model="item.tipo_afectacion"
                                        :name="`items[${index}][tipo_afectacion]`"
                                        class="w-full bg-slate-800 border border-slate-700/40 rounded-lg px-1 py-2 text-[10px] text-slate-400
                                               focus:outline-none focus:border-sky-500/60 transition-colors text-center">
                                    <option value="10" title="Gravado">18%</option>
                                    <option value="20" title="Exonerado">Exo</option>
                                    <option value="30" title="Inafecto">Ina</option>
                                </select>
                            </div>

                            {{-- Total + eliminar --}}
                            <div class="col-span-2 sm:col-span-2 flex items-center justify-end gap-2">
                                <p class="text-xs font-semibold font-mono text-white"
                                   x-text="fmt(subtotalItem(item) + igvItem(item))"></p>
                                <button type="button" @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="w-5 h-5 flex-shrink-0 flex items-center justify-center rounded-md
                                               text-slate-600 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Totales --}}
                <div class="mt-5 pt-4 border-t border-slate-800/60 space-y-2 max-w-xs ml-auto">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Subtotal (sin IGV)</span>
                        <span class="font-mono font-semibold text-white" x-text="fmt(subtotal)"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>IGV (<span x-text="igvPct"></span>%)</span>
                        <span class="font-mono text-slate-300" x-text="fmt(igv)"></span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-700/40">
                        <span class="text-sm font-semibold text-white">Total</span>
                        <span class="text-base font-bold font-mono text-sky-400" x-text="fmt(total)"></span>
                    </div>
                </div>
            </div>

            {{-- ── 3: Notas ─────────────────────────────────────────── --}}
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">3</span>
                    Notas <span class="text-xs font-normal text-slate-600">— opcional</span>
                </h3>
                <textarea name="notas" rows="3" class="input-dark resize-none text-xs"
                          placeholder="Observaciones para el comprobante...">{{ old('notas') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('facturacion.index') }}"
                   class="px-5 py-2.5 text-sm text-slate-400 hover:text-white border border-slate-700/60 rounded-xl hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400
                               transition-all active:scale-[0.98] shadow-[0_0_18px_rgba(14,165,233,0.35)]">
                    {{ $apiOk ? 'Crear y registrar en SUNAT API' : 'Guardar borrador' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
