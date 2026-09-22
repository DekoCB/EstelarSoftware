<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 font-mono">Sistema</span>
            <span class="text-slate-700">/</span>
            <h1 class="text-sm font-semibold text-white">Configuración de empresa</h1>
        </div>
    </x-slot>

    <div
        x-data="{
            tab: '{{ $errors->hasAny(['logo_sidebar','logo_login','logo_documentos']) ? 'logos' : ($errors->hasAny(['igv_porcentaje','nubefact_token','sunat_modo']) ? 'sunat' : 'empresa') }}',

            ruc: '{{ old('ruc', $config->ruc ?? '') }}',
            razonSocial: '{{ old('razon_social', addslashes($config->razon_social ?? '')) }}',
            direccion: '{{ old('direccion', addslashes($config->direccion ?? '')) }}',
            buscando: false, encontrado: false, errorRuc: '',

            previews: { logo_sidebar: null, logo_login: null, logo_documentos: null },
            deletes:  { logo_sidebar: false, logo_login: false, logo_documentos: false },

            onRucInput() {
                this.encontrado = false; this.errorRuc = '';
                if (this.ruc.replace(/\D/g,'').length === 11) this.buscarRuc();
            },
            async buscarRuc() {
                this.buscando = true; this.errorRuc = ''; this.encontrado = false;
                try {
                    const r = await fetch(`{{ url('/api/consulta-documento') }}?tipo=RUC&numero=${this.ruc}`, {
                        headers: { 'Accept':'application/json',
                                   'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                    });
                    const d = await r.json();
                    if (!r.ok) { this.errorRuc = r.status===404 ? 'RUC no encontrado.' : 'Error al consultar.'; return; }
                    if (d.nombre) { this.razonSocial = d.nombre; this.encontrado = true; }
                    if (d.direccion && d.direccion !== '-') this.direccion = d.direccion;
                } catch { this.errorRuc = 'Sin conexión. Rellena manualmente.'; }
                finally  { this.buscando = false; }
            },
            onLogoChange(e, campo) {
                const f = e.target.files[0]; if (!f) return;
                this.deletes[campo] = false;
                const r = new FileReader(); r.onload = ev => this.previews[campo] = ev.target.result; r.readAsDataURL(f);
            },
            quitarLogo(campo) { this.deletes[campo] = true; this.previews[campo] = null; document.getElementById('input_'+campo).value = ''; }
        }"
        class="max-w-4xl"
    >

        {{-- ── Encabezado ──────────────────────────────────────────── --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Configuración de empresa</h2>
                <p class="text-sm text-slate-500 mt-0.5">Datos fiscales, logos e integración SUNAT</p>
            </div>
        </div>

        {{-- ── Tabs ────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-1 bg-slate-900/60 border border-slate-800/60 rounded-xl p-1 mb-6 overflow-x-auto">
            @foreach([
                ['empresa', 'Empresa',     'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
                ['logos',   'Logos',       'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
                ['sunat',   'SUNAT',       'M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
                ['series',  'Series',      'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z'],
                ['pfx',     'Certificado', 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'],
            ] as [$key, $label, $icon])
            <button
                type="button"
                @click="tab = '{{ $key }}'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap
                       transition-all duration-150 flex-1 justify-center"
                :class="tab === '{{ $key }}'
                    ? 'bg-sky-500/15 text-sky-400 ring-1 ring-sky-500/25'
                    : 'text-slate-500 hover:text-slate-300'"
            >
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ── Formulario (envuelve todos los tabs) ───────────────── --}}
        <form method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- ══ TAB 1: EMPRESA ══════════════════════════════════════ --}}
            <div x-show="tab === 'empresa'" class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">RUC</label>
                        <div class="relative">
                            <input type="text" name="ruc" x-model="ruc"
                                   @input.debounce.600ms="onRucInput()"
                                   maxlength="11"
                                   class="input-dark font-mono pr-10 @error('ruc') error @enderror"
                                   placeholder="20xxxxxxxxx">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg x-show="buscando" class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg x-show="encontrado && !buscando" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                            </div>
                        </div>
                        <p x-show="errorRuc" x-text="errorRuc" class="text-amber-400 text-xs mt-1"></p>
                        <p x-show="encontrado && !buscando" class="text-emerald-400 text-xs mt-1">✓ Completado desde SUNAT</p>
                        @error('ruc')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Email corporativo</label>
                        <input type="email" name="email" class="input-dark" placeholder="info@empresa.com"
                               value="{{ old('email', $config->email) }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Razón social</label>
                        <input type="text" name="razon_social" x-model="razonSocial"
                               class="input-dark @error('razon_social') error @enderror"
                               placeholder="Se autocompleta con el RUC">
                        @error('razon_social')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombre comercial</label>
                        <input type="text" name="nombre_comercial" class="input-dark"
                               placeholder="Nombre de fantasía"
                               value="{{ old('nombre_comercial', $config->nombre_comercial) }}">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Teléfono</label>
                        <input type="text" name="telefono" class="input-dark font-mono"
                               placeholder="+51 64 999 999"
                               value="{{ old('telefono', $config->telefono) }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Dirección fiscal</label>
                        <input type="text" name="direccion" x-model="direccion"
                               class="input-dark" placeholder="Se autocompleta con el RUC">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Sitio web</label>
                        <input type="url" name="web" class="input-dark"
                               placeholder="https://estelar.com.pe"
                               value="{{ old('web', $config->web) }}">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Ubigeo</label>
                        <input type="text" name="ubigeo" class="input-dark font-mono"
                               maxlength="6" placeholder="120101"
                               value="{{ old('ubigeo', $config->ubigeo) }}">
                    </div>

                    <div class="sm:col-span-2 pt-3 mt-1 border-t border-slate-800/60">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">
                            Avisos de reuniones
                        </label>
                        <p class="text-xs text-slate-600 mb-4">
                            La alarma suena hasta 3 veces por reunión, en 3 momentos distintos. Aplica a todas las reuniones del sistema, nuevas y ya agendadas.
                        </p>

                        <div class="space-y-4">

                            {{-- Aviso 1: antes --}}
                            @php
                                $reunionMinutos  = (string) old('recordatorio_reunion_minutos', $config->recordatorioReunionMinutos());
                                $reunionPresets  = ['5', '10', '15', '30', '60', '1440'];
                                $reunionCustom   = ! in_array($reunionMinutos, $reunionPresets, true);
                            @endphp
                            <div x-data="{ preset: '{{ $reunionCustom ? 'custom' : $reunionMinutos }}', custom: '{{ $reunionCustom ? $reunionMinutos : '' }}' }">
                                <p class="text-xs text-slate-300 mb-1.5">
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-sky-500/15 text-sky-400 text-[10px] font-bold mr-1">1</span>
                                    Antes de la hora
                                </p>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <select x-model="preset" class="input-dark max-w-xs @error('recordatorio_reunion_minutos') error @enderror">
                                        <option value="5">5 minutos antes</option>
                                        <option value="10">10 minutos antes</option>
                                        <option value="15">15 minutos antes</option>
                                        <option value="30">30 minutos antes</option>
                                        <option value="60">1 hora antes</option>
                                        <option value="1440">1 día antes</option>
                                        <option value="custom">Personalizado…</option>
                                    </select>

                                    <div x-show="preset === 'custom'" x-cloak class="flex items-center gap-2">
                                        <input type="number" x-model="custom" min="1" max="10080"
                                               class="input-dark w-24 font-mono" placeholder="Ej. 20">
                                        <span class="text-xs text-slate-500">minutos antes</span>
                                    </div>
                                </div>
                                <input type="hidden" name="recordatorio_reunion_minutos" :value="preset === 'custom' ? custom : preset">
                                @error('recordatorio_reunion_minutos')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Aviso 2: a la hora exacta (fijo) --}}
                            <div>
                                <p class="text-xs text-slate-300 mb-1.5">
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-sky-500/15 text-sky-400 text-[10px] font-bold mr-1">2</span>
                                    A la hora exacta
                                </p>
                                <p class="text-xs text-slate-600">Siempre activo, no requiere configurarse — suena justo cuando empieza la reunión.</p>
                            </div>

                            {{-- Aviso 3: después --}}
                            @php
                                $despuesMinutos = (string) old('alerta_despues_minutos', $config->alertaDespuesMinutos());
                                $despuesPresets = ['5', '10', '15', '30', '60'];
                                $despuesCustom  = ! in_array($despuesMinutos, $despuesPresets, true);
                            @endphp
                            <div x-data="{ preset: '{{ $despuesCustom ? 'custom' : $despuesMinutos }}', custom: '{{ $despuesCustom ? $despuesMinutos : '' }}' }">
                                <p class="text-xs text-slate-300 mb-1.5">
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-sky-500/15 text-sky-400 text-[10px] font-bold mr-1">3</span>
                                    Después de la hora
                                </p>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <select x-model="preset" class="input-dark max-w-xs @error('alerta_despues_minutos') error @enderror">
                                        <option value="5">5 minutos después</option>
                                        <option value="10">10 minutos después</option>
                                        <option value="15">15 minutos después</option>
                                        <option value="30">30 minutos después</option>
                                        <option value="60">1 hora después</option>
                                        <option value="custom">Personalizado…</option>
                                    </select>

                                    <div x-show="preset === 'custom'" x-cloak class="flex items-center gap-2">
                                        <input type="number" x-model="custom" min="1" max="10080"
                                               class="input-dark w-24 font-mono" placeholder="Ej. 20">
                                        <span class="text-xs text-slate-500">minutos después</span>
                                    </div>
                                </div>
                                <input type="hidden" name="alerta_despues_minutos" :value="preset === 'custom' ? custom : preset">
                                @error('alerta_despues_minutos')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ TAB 2: LOGOS ════════════════════════════════════════ --}}
            <div x-show="tab === 'logos'" class="space-y-4">

                {{-- Aviso de dimensiones --}}
                <div class="bg-sky-500/5 border border-sky-500/20 rounded-xl px-4 py-3 flex items-start gap-3">
                    <svg class="w-4 h-4 text-sky-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                    </svg>
                    <p class="text-xs text-sky-300/80 leading-relaxed">
                        Usa las dimensiones exactas para evitar distorsión. PNG con fondo transparente es el formato recomendado para sidebar y login. Los PDFs A4 usan el logo documentos en la cabecera (~55 × 20mm).
                    </p>
                </div>

                @php
                $logos = [
                    [
                        'campo'  => 'logo_sidebar',
                        'titulo' => 'Sidebar',
                        'dim'    => '200 × 200 px',
                        'ratio'  => '1 : 1',
                        'fmt'    => 'PNG · SVG · WebP',
                        'peso'   => '512 KB máx.',
                        'nota'   => 'Visible a 36×36px en el menú lateral. Fondo transparente.',
                        'url'    => $config->logoSidebarUrl(),
                    ],
                    [
                        'campo'  => 'logo_login',
                        'titulo' => 'Login',
                        'dim'    => '400 × 400 px',
                        'ratio'  => '1 : 1',
                        'fmt'    => 'PNG · SVG · WebP',
                        'peso'   => '1 MB máx.',
                        'nota'   => 'Visible a 56×56px en la pantalla de acceso. Fondo transparente.',
                        'url'    => $config->logoLoginUrl(),
                    ],
                    [
                        'campo'  => 'logo_documentos',
                        'titulo' => 'Documentos (A4)',
                        'dim'    => '400 × 150 px',
                        'ratio'  => '8 : 3',
                        'fmt'    => 'PNG · JPG · WebP',
                        'peso'   => '1 MB máx.',
                        'nota'   => 'Cabecera de boletas, facturas, cotizaciones y proformas. Blanco o transparente.',
                        'url'    => $config->logoDocumentosUrl(),
                    ],
                ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($logos as $logo)
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col gap-4">

                    <p class="text-sm font-semibold text-white">{{ $logo['titulo'] }}</p>

                    {{-- Preview --}}
                    <div class="bg-slate-800/40 border border-slate-700/30 rounded-xl flex items-center justify-center overflow-hidden" style="min-height:90px">
                        <img x-show="previews['{{ $logo['campo'] }}']"
                             :src="previews['{{ $logo['campo'] }}']"
                             class="max-h-20 max-w-full object-contain p-2">
                        @if($logo['url'])
                        <img x-show="!previews['{{ $logo['campo'] }}'] && !deletes['{{ $logo['campo'] }}']"
                             src="{{ $logo['url'] }}" class="max-h-20 max-w-full object-contain p-2">
                        @endif
                        <div x-show="!previews['{{ $logo['campo'] }}'] && (deletes['{{ $logo['campo'] }}'] || {{ $logo['url'] ? 'false' : 'true' }})"
                             class="flex flex-col items-center gap-1 py-4">
                            <svg class="w-7 h-7 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/>
                            </svg>
                            <p class="text-xs text-slate-600">Sin imagen</p>
                        </div>
                    </div>

                    {{-- Controles --}}
                    <input type="file" id="input_{{ $logo['campo'] }}" name="{{ $logo['campo'] }}"
                           accept="image/*" class="hidden"
                           @change="onLogoChange($event, '{{ $logo['campo'] }}')">
                    <input type="hidden" name="delete_{{ $logo['campo'] }}" :value="deletes['{{ $logo['campo'] }}'] ? '1' : '0'">

                    <div class="flex gap-2">
                        <label for="input_{{ $logo['campo'] }}"
                               class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                                      text-xs font-medium cursor-pointer text-slate-300
                                      bg-slate-800 border border-slate-700/60
                                      hover:border-sky-500/30 hover:text-sky-400 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                            Subir
                        </label>
                        @if($logo['url'])
                        <button type="button" x-show="!deletes['{{ $logo['campo'] }}']"
                                @click="quitarLogo('{{ $logo['campo'] }}')"
                                class="px-3 py-2 rounded-lg text-xs text-red-400 bg-slate-800
                                       border border-slate-700/60 hover:bg-red-500/10 transition-all">
                            Quitar
                        </button>
                        <button type="button" x-show="deletes['{{ $logo['campo'] }}']"
                                @click="deletes['{{ $logo['campo'] }}'] = false"
                                class="px-3 py-2 rounded-lg text-xs text-slate-400 bg-slate-800
                                       border border-slate-700/60 transition-all">
                            Cancelar
                        </button>
                        @endif
                    </div>

                    {{-- Especificaciones --}}
                    <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-[10px] pt-3 border-t border-slate-800/80">
                        <span class="text-slate-600 uppercase tracking-wide">Dimensiones</span>
                        <span class="font-mono font-semibold text-slate-400 text-right">{{ $logo['dim'] }}</span>
                        <span class="text-slate-600 uppercase tracking-wide">Ratio</span>
                        <span class="text-slate-500 text-right">{{ $logo['ratio'] }}</span>
                        <span class="text-slate-600 uppercase tracking-wide">Formatos</span>
                        <span class="text-slate-500 text-right">{{ $logo['fmt'] }}</span>
                        <span class="text-slate-600 uppercase tracking-wide">Peso</span>
                        <span class="text-slate-500 text-right">{{ $logo['peso'] }}</span>
                        <p class="col-span-2 text-slate-600 leading-relaxed pt-1 border-t border-slate-800/60 mt-1">{{ $logo['nota'] }}</p>
                    </div>

                    @error($logo['campo'])<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                </div>
                @endforeach
                </div>
            </div>

            {{-- ══ TAB 3: SUNAT ════════════════════════════════════════ --}}
            <div x-show="tab === 'sunat'" class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">IGV (%)</label>
                        <input type="number" name="igv_porcentaje" step="0.01" min="0" max="100"
                               class="input-dark font-mono"
                               value="{{ old('igv_porcentaje', $config->igv_porcentaje ?? '18.00') }}">
                        <p class="text-xs text-slate-600 mt-1">Actualmente 18% en Perú</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Moneda</label>
                        <select name="moneda" class="input-dark">
                            <option value="PEN" {{ ($config->moneda ?? 'PEN') === 'PEN' ? 'selected' : '' }}>PEN — Sol peruano</option>
                            <option value="USD" {{ ($config->moneda ?? 'PEN') === 'USD' ? 'selected' : '' }}>USD — Dólar americano</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Modo SUNAT</label>
                        <select name="sunat_modo" class="input-dark">
                            <option value="sandbox"    {{ ($config->sunat_modo ?? 'sandbox') === 'sandbox'    ? 'selected' : '' }}>🧪 Sandbox — pruebas</option>
                            <option value="produccion" {{ ($config->sunat_modo ?? 'sandbox') === 'produccion' ? 'selected' : '' }}>🟢 Producción</option>
                        </select>
                        <p class="text-xs text-amber-500/70 mt-1">No activar producción sin certificado PFX</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">URL Nubefact OSE</label>
                        <input type="url" name="nubefact_url" class="input-dark font-mono text-xs"
                               value="{{ old('nubefact_url', $config->nubefact_url ?? 'https://api.nubefact.com/api/v1') }}">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">
                            Token Nubefact
                            <span class="text-slate-600 font-normal ml-1">— obtenlo en tu panel Nubefact</span>
                        </label>
                        <input type="password" name="nubefact_token" class="input-dark font-mono"
                               placeholder="{{ $config->nubefact_token ? '••••••••••••••••' : 'Pega tu token aquí' }}"
                               autocomplete="off">
                        @if($config->nubefact_token)
                        <p class="text-emerald-400 text-xs mt-1">✓ Token guardado. Deja vacío para no modificarlo.</p>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ══ TAB 4: SERIES Y CORRELATIVOS ═══════════════════════ --}}
            <div x-show="tab === 'series'"
                 x-data="{
                     apiStatus: null,
                     checking: false,
                     async checkApi() {
                         this.checking = true; this.apiStatus = null;
                         try {
                             const r = await fetch('{{ url('/api/sunat-api-status') }}', {
                                 headers: { 'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                             });
                             this.apiStatus = await r.json();
                         } catch(e) {
                             this.apiStatus = { connected: false, error: 'Sin conexión con la API' };
                         }
                         this.checking = false;
                     }
                 }"
                 class="space-y-5">

                {{-- Series configuradas --}}
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                    <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                        </svg>
                        Series de comprobantes
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Serie boleta</label>
                            <input type="text" name="serie_boleta" class="input-dark font-mono uppercase"
                                   maxlength="4" placeholder="B001"
                                   value="{{ old('serie_boleta', $config->serie_boleta ?? 'B001') }}">
                            <p class="text-xs text-slate-600 mt-1">Formato: B + 3 dígitos (B001, B002…)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Serie factura</label>
                            <input type="text" name="serie_factura" class="input-dark font-mono uppercase"
                                   maxlength="4" placeholder="F001"
                                   value="{{ old('serie_factura', $config->serie_factura ?? 'F001') }}">
                            <p class="text-xs text-slate-600 mt-1">Formato: F + 3 dígitos (F001, F002…)</p>
                        </div>
                    </div>
                </div>

                {{-- Estado de la API SUNAT externa --}}
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                            </svg>
                            API de Facturación SUNAT
                        </h3>
                        <button type="button" @click="checkApi()"
                                :disabled="checking"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                       text-sky-400 bg-sky-500/10 border border-sky-500/20 hover:bg-sky-500/20 transition-colors
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" :class="checking ? 'animate-spin' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            </svg>
                            <span x-text="checking ? 'Verificando...' : 'Verificar conexión'"></span>
                        </button>
                    </div>

                    {{-- Datos de conexión actuales --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl px-4 py-3">
                            <p class="text-[10px] text-slate-600 mb-1 uppercase tracking-wider">URL de la API</p>
                            <p class="text-xs font-mono text-slate-300">{{ config('services.sunat_api.url', '—') }}</p>
                        </div>
                        <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl px-4 py-3">
                            <p class="text-[10px] text-slate-600 mb-1 uppercase tracking-wider">Company ID</p>
                            <p class="text-xs font-mono text-slate-300">{{ config('services.sunat_api.company_id', '—') }}</p>
                        </div>
                        <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl px-4 py-3">
                            <p class="text-[10px] text-slate-600 mb-1 uppercase tracking-wider">Branch ID</p>
                            <p class="text-xs font-mono text-slate-300">{{ config('services.sunat_api.branch_id', '—') }}</p>
                        </div>
                    </div>

                    {{-- Resultado del ping --}}
                    <div x-show="apiStatus !== null">
                        <div x-show="apiStatus?.connected"
                             class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-4 py-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse flex-shrink-0"></span>
                            <p class="text-xs text-emerald-300 font-medium">API conectada y respondiendo correctamente</p>
                        </div>
                        <div x-show="!apiStatus?.connected"
                             class="flex items-start gap-2 bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3">
                            <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0 mt-1"></span>
                            <div>
                                <p class="text-xs text-red-300 font-medium">API no accesible</p>
                                <p class="text-[10px] text-red-400/70 mt-0.5" x-text="apiStatus?.error ?? 'Verifica que la API esté corriendo'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Instrucciones setup --}}
                    <div class="mt-4 bg-slate-800/30 border border-slate-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-slate-400 mb-2">Setup inicial de la API</p>
                        <ol class="space-y-1.5 text-xs text-slate-500 list-decimal list-inside">
                            <li>Corre la API: <code class="font-mono bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded text-[10px]">php artisan serve --port=8001</code> (en la carpeta de la API)</li>
                            <li>Login: <code class="font-mono bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded text-[10px]">POST /api/auth/login</code> → guarda el token en <code class="font-mono bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded text-[10px]">.env SUNAT_API_TOKEN</code></li>
                            <li>Crea la empresa: <code class="font-mono bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded text-[10px]">POST /api/v1/companies/complete</code> con el RUC de la empresa</li>
                            <li>Actualiza <code class="font-mono bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded text-[10px]">SUNAT_API_COMPANY_ID</code> y <code class="font-mono bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded text-[10px]">SUNAT_API_BRANCH_ID</code> en el <code class="font-mono">.env</code></li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- ══ TAB 5: CERTIFICADO PFX ══════════════════════════════ --}}
            <div x-show="tab === 'pfx'" class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">

                <div class="bg-amber-500/5 border border-amber-500/20 rounded-xl px-4 py-3 flex items-start gap-3 mb-5">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                    <div class="text-xs text-amber-300/80 leading-relaxed">
                        El archivo <code class="font-mono bg-amber-500/10 px-1 rounded">.pfx</code> debe subirse manualmente al servidor en
                        <code class="font-mono bg-amber-500/10 px-1 rounded">storage/app/certs/</code> (directorio privado, nunca en <code class="font-mono bg-amber-500/10 px-1 rounded">public/</code>).
                        La clave se guarda encriptada en la base de datos.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Ruta del archivo PFX</label>
                        <input type="text" name="certificado_pfx_path" class="input-dark font-mono text-xs"
                               placeholder="storage/app/certs/certificado.pfx"
                               value="{{ old('certificado_pfx_path', $config->certificado_pfx_path) }}">
                        <p class="text-xs text-slate-600 mt-1">Ruta relativa desde la raíz del proyecto</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Clave del certificado PFX</label>
                        <input type="password" name="certificado_pfx_clave" class="input-dark font-mono"
                               placeholder="{{ $config->certificado_pfx_clave ? '••••••••' : 'Clave del certificado' }}"
                               autocomplete="off">
                        @if($config->certificado_pfx_clave)
                        <p class="text-emerald-400 text-xs mt-1">✓ Clave guardada. Deja vacío para no modificarla.</p>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── Botón guardar ───────────────────────────────────────── --}}
            @can('configuracion.editar')
            <div class="flex justify-end mt-5">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               hover:from-sky-400 hover:to-cyan-400
                               shadow-[0_0_18px_rgba(14,165,233,0.35)]
                               hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                               transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                    </svg>
                    Guardar configuración
                </button>
            </div>
            @endcan

        </form>
    </div>

</x-app-layout>
