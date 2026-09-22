<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscripción — {{ $evento->nombre }}</title>

    @php $config = \App\Models\EmpresaConfig::config(); @endphp
    @if($config->logo_sidebar)
        <link rel="icon" href="{{ $config->logoSidebarUrl() }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('eventos._asistente-busqueda')
</head>
<body class="font-sans antialiased bg-estelar-bg min-h-screen flex items-center justify-center p-4 py-10">

    <div class="w-full max-w-md">

        {{-- ── Banner del evento ──────────────────────────────────────── --}}
        <div class="relative rounded-2xl overflow-hidden mb-5 border border-slate-800/60 shadow-[0_0_50px_rgba(0,0,0,0.35)]
                    {{ $evento->imagenUrl() ? '' : 'bg-gradient-to-br from-slate-900 via-slate-900 to-sky-950' }}"
             @if($evento->imagenUrl()) style="background-image:linear-gradient(to top, rgba(10,15,30,.95), rgba(10,15,30,.25)), url('{{ $evento->imagenUrl() }}'); background-size:cover; background-position:center;" @endif
        >
            <div class="relative px-6 pt-8 pb-6 {{ $evento->imagenUrl() ? 'min-h-[220px] flex flex-col justify-end' : '' }}">
                @if($config->logoLoginUrl())
                    <img src="{{ $config->logoLoginUrl() }}" alt="Logo" class="h-10 w-auto object-contain mb-4">
                @endif
                <h1 class="text-2xl font-bold text-white leading-tight">{{ $evento->nombre }}</h1>
                <div class="flex items-center gap-3 flex-wrap text-xs text-slate-300 mt-2">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        {{ $evento->fecha_inicio->format('d/m/Y') }}{{ $evento->horaFormateada() ? ' · ' . $evento->horaFormateada() : '' }}
                    </span>
                    @if($evento->lugar)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        {{ $evento->lugar }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-white mb-4">Regístrate para asistir</h2>

            @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-xs text-red-400">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('eventos.inscripcion.store', $evento) }}" class="space-y-5"
                  x-data="asistenteBusqueda(
                      '{{ route('api.consulta-documento-publico') }}',
                      '{{ old('tipo_documento', 'DNI') }}',
                      '{{ old('numero_documento') }}',
                      '{{ addslashes(old('nombres', '')) }}',
                      '{{ addslashes(old('empresa', '')) }}',
                      '{{ addslashes(old('direccion', '')) }}'
                  )">
                @csrf

                <div class="rounded-xl border border-sky-500/30 bg-sky-500/5 p-4">
                    <div class="flex items-center gap-1.5 mb-3">
                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                        <p class="text-xs font-semibold text-sky-400">Busca tu documento y autocompleta tus datos</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Tipo doc.</label>
                            <select name="tipo_documento" x-model="tipoDocumento" @change="onTipoChange()"
                                    class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                                @foreach(\App\Models\EventAttendee::TIPOS_DOCUMENTO as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                N.º documento
                                <span x-show="longitud" class="text-slate-600 font-normal" x-text="'(' + longitud + ' dígitos)'"></span>
                            </label>
                            <div class="relative">
                                <input type="text" name="numero_documento" x-model="numeroDocumento"
                                       @input.debounce.600ms="onNumeroInput()" :maxlength="longitud ?? 20"
                                       class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm font-mono pr-9 focus:border-sky-500 focus:ring-sky-500/30">
                                <div class="absolute right-2.5 top-1/2 -translate-y-1/2">
                                    <svg x-show="buscando" class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    <svg x-show="encontrado && !buscando" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p x-show="errorBusqueda && !buscando" x-text="errorBusqueda" class="text-amber-400 text-xs mt-2"></p>
                </div>

                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2.5">Tus datos</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombres completos *</label>
                            <input type="text" name="nombres" x-model="nombres" required
                                   class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Empresa</label>
                            <input type="text" name="empresa" x-model="empresa"
                                   class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Dirección</label>
                            <input type="text" name="direccion" x-model="direccion"
                                   placeholder="Se autocompleta con RUC, o escríbela manualmente"
                                   class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2.5">Contacto</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Correo</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-400 mb-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2Zm0 18.09h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.32c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.55-3.7 8.24-8.24 8.24Zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.04-.38-1.99-1.22-.73-.66-1.23-1.46-1.37-1.71-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.44.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.42h-.48c-.17 0-.44.06-.67.31-.23.25-.87.85-.87 2.08s.9 2.41 1.02 2.58c.12.17 1.77 2.7 4.29 3.78.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.16-.48-.28Z"/>
                                </svg>
                                Celular / WhatsApp
                            </label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                   placeholder="Ej. 987654321"
                                   class="w-full rounded-xl bg-slate-800 border-slate-700 text-white text-sm focus:border-sky-500 focus:ring-sky-500/30">
                            <p class="text-slate-600 text-[11px] mt-1">Para poder enviarte tu entrada por WhatsApp</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                               transition-all duration-200 active:scale-[0.98]">
                    Generar mi entrada
                </button>
            </form>
        </div>

        <p class="text-center text-slate-700 text-xs mt-6 font-mono">
            © {{ date('Y') }} {{ $config->nombre_comercial ?: 'Estelar' }}
        </p>
    </div>

    @stack('scripts')
</body>
</html>
