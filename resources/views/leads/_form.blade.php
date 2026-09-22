{{--
    Partial compartido por create.blade.php, edit.blade.php y el modal de index.blade.php
    Variables esperadas: $lead (opcional, para edición)
--}}
@php
    $opcionesRespuesta = \App\Support\LeadResponseOptions::opciones();
@endphp
<div
    class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6"
    x-data="{
        respuesta: '{{ old('respuesta_rapida', $lead->respuesta_rapida ?? '') }}',
        comentario: {{ Illuminate\Support\Js::from(old('respuesta_comentario', $lead->respuesta_comentario ?? '')) }},
        fecha: {{ Illuminate\Support\Js::from(old('respuesta_fecha', isset($lead) && $lead->respuesta_fecha ? $lead->respuesta_fecha->format('Y-m-d') : '')) }},
        hora: {{ Illuminate\Support\Js::from(old('respuesta_hora', isset($lead) && $lead->respuesta_hora ? substr($lead->respuesta_hora, 0, 5) : '')) }},
        opciones: {{ Illuminate\Support\Js::from($opcionesRespuesta) }},
        mostrarOpciones: false,
        get necesitaFechaHora() { return (this.opciones[this.respuesta]?.requiere || []).some(r => r === 'fecha' || r === 'hora'); },
        get necesitaSistema() { return (this.opciones[this.respuesta]?.requiere || []).includes('sistema'); },
        get comentarioAuto() { return this.opciones[this.respuesta]?.comentario_auto || null; },
    }"
    x-init="$watch('respuesta', value => { if (comentarioAuto && !comentario) comentario = comentarioAuto; })"
    x-on:opciones-actualizadas.window="opciones = $event.detail.opciones"
    x-on:agendar-hora.window="respuesta = 'reunion_pendiente'; fecha = $event.detail.fecha; hora = $event.detail.hora"
>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Nombre --}}
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombre</label>
            <input
                type="text"
                name="nombre"
                class="input-dark @error('nombre') error @enderror"
                placeholder="Nombre completo del contacto (si se deja vacío, se guarda como '-')"
                value="{{ old('nombre', $lead->nombre ?? '') }}"
                style="text-transform:uppercase"
                x-on:input="let s = $el.selectionStart, e = $el.selectionEnd; $el.value = $el.value.toUpperCase(); $el.setSelectionRange(s, e)"
            >
            @error('nombre')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Lugar --}}
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Lugar</label>
            <input
                type="text"
                name="lugar"
                class="input-dark"
                placeholder="Ej: Huancayo"
                value="{{ old('lugar', $lead->lugar ?? '') }}"
            >
        </div>

        {{-- Teléfono --}}
        <div
            x-data="{
                duplicado: null,
                verificando: false,
                timer: null,
                digitosLocales(v) {
                    let s = (v || '').trim();
                    if (s.startsWith('+51')) {
                        s = s.slice(3);
                    } else if (s.startsWith('51') && s.replace(/\D/g, '').length > 9) {
                        s = s.slice(2);
                    }
                    return s.replace(/\D/g, '').slice(0, 9);
                },
                formatear(v) {
                    const d = this.digitosLocales(v);
                    if (d.length === 0) return '';
                    let out = '+51 ' + d.slice(0, 3);
                    if (d.length > 3) out += ' ' + d.slice(3, 6);
                    if (d.length > 6) out += ' ' + d.slice(6, 9);
                    return out;
                },
                onInput(e) {
                    e.target.value = this.formatear(e.target.value);
                    this.duplicado = null;
                    clearTimeout(this.timer);
                    if (this.digitosLocales(e.target.value).length === 9) {
                        this.timer = setTimeout(() => this.verificar(e.target.value), 400);
                    }
                },
                async verificar(valor) {
                    this.verificando = true;
                    try {
                        const url = new URL('{{ route('leads.verificar-telefono') }}', window.location.origin);
                        url.searchParams.set('telefono', valor);
                        @if(isset($lead))
                        url.searchParams.set('excluir', '{{ $lead->id }}');
                        @endif
                        const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const data = await r.json();
                        this.duplicado = data.existe ? data.lead : null;
                    } catch (e) {
                        this.duplicado = null;
                    } finally {
                        this.verificando = false;
                    }
                },
            }"
            x-init="$nextTick(() => { const el = $el.querySelector('input'); el.value = formatear(el.value); })"
        >
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Teléfono</label>
            <input
                type="text"
                name="telefono"
                class="input-dark font-mono @error('telefono') error @enderror"
                placeholder="+51 999 999 999"
                value="{{ old('telefono', $lead->telefono ?? '') }}"
                @input="onInput"
                inputmode="numeric"
                maxlength="15"
            >
            @error('telefono')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <template x-if="verificando">
                <p class="text-slate-500 text-xs mt-1">Verificando...</p>
            </template>
            <template x-if="duplicado">
                <div class="mt-1">
                    <p class="text-amber-400 text-xs leading-snug">
                        ⚠️ Este número ya está registrado <span x-text="'— ' + duplicado.nombre"></span>
                    </p>
                    <a :href="duplicado.url"
                       class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-md text-[11px] font-semibold
                              text-amber-400 bg-amber-500/10 border border-amber-500/30
                              hover:bg-amber-500/20 transition-colors duration-150">
                        Ir al lead →
                    </a>
                </div>
            </template>
        </div>

        {{-- Respuesta rápida --}}
        <div class="sm:col-span-2">
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-medium text-slate-400">Respuesta rápida</label>
                @can('leads.editar')
                <button type="button" @click="mostrarOpciones = true"
                        class="text-[11px] text-sky-500 hover:text-sky-400 transition-colors">Editar opciones →</button>
                @endcan
            </div>
            <select name="respuesta_rapida" x-model="respuesta" class="input-dark @error('respuesta_rapida') error @enderror">
                <option value="">— Sin respuesta —</option>
                <template x-for="(op, clave) in opciones" :key="clave">
                    <option :value="clave" x-text="op.label"></option>
                </template>
            </select>
            @error('respuesta_rapida')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Modal: editar opciones de respuesta rápida --}}
        @can('leads.editar')
        <template x-teleport="body">
            <div
                x-show="mostrarOpciones"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 overflow-y-auto"
                style="display:none; z-index:60;"
            >
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="mostrarOpciones = false"></div>

                <div class="relative min-h-full flex items-start justify-center p-4 pt-16">
                    <div
                        x-show="mostrarOpciones"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="w-full max-w-lg bg-slate-900 border border-slate-700/60 rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)]"
                        @click.stop
                    >
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80">
                            <div>
                                <h2 class="text-base font-bold text-white">Opciones de respuesta rápida</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Edita el texto y color, o agrega opciones nuevas</p>
                            </div>
                            <button @click="mostrarOpciones = false" type="button"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-white hover:bg-slate-800 transition-colors duration-150">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-5">
                            @livewire('leads.lead-response-options-manager')
                        </div>
                    </div>
                </div>
            </div>
        </template>
        @endcan

        {{-- Fecha / Hora (volver a llamar, reunión pendiente) --}}
        <template x-if="necesitaFechaHora">
            <div class="sm:col-span-2 grid grid-cols-2 gap-4 bg-slate-800/40 border border-slate-700/40 rounded-xl p-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha</label>
                    <input type="date" name="respuesta_fecha" x-model="fecha" class="input-dark @error('respuesta_fecha') error @enderror">
                    @error('respuesta_fecha')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Hora</label>
                    <input type="time" name="respuesta_hora" x-model="hora" class="input-dark @error('respuesta_hora') error @enderror">
                    @error('respuesta_hora')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <template x-if="necesitaSistema">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Sistema que quiere</label>
                        <input type="text" name="sistema_interes" class="input-dark @error('sistema_interes') error @enderror"
                               placeholder="Ej: Sistema de ventas, ERP contable..."
                               value="{{ old('sistema_interes', $lead->sistema_interes ?? '') }}">
                        @error('sistema_interes')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </template>
            </div>
        </template>

        {{-- Comentario --}}
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                Comentario
                <span x-show="comentarioAuto" class="text-emerald-400 normal-case font-normal">(autocompletado, puedes editarlo)</span>
            </label>
            <textarea name="respuesta_comentario" x-model="comentario" rows="3"
                      class="input-dark @error('respuesta_comentario') error @enderror"
                      placeholder="Notas adicionales sobre la llamada..."></textarea>
            @error('respuesta_comentario')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>
