<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tu entrada — {{ $evento->nombre }}</title>

    @php $config = \App\Models\EmpresaConfig::config(); @endphp
    @if($config->logo_sidebar)
        <link rel="icon" href="{{ $config->logoSidebarUrl() }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>@media print { body { background: white !important; } .no-print { display: none !important; } }</style>
</head>
<body class="font-sans antialiased bg-estelar-bg min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl">

        <div class="text-center mb-5 no-print">
            @if($asistente->team && !$asistente->team->pagado())
            {{-- Inscripcion por equipo: no esta concretada hasta confirmar el pago --}}
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                {{ $asistente->team->estadoPagoLabel() }} — la entrada se activa al confirmar el pago
            </div>
            @else
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
                Inscripción confirmada
            </div>
            @endif
        </div>

        {{-- ── Ticket ──────────────────────────────────────────────── --}}
        <div class="relative bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col md:flex-row shadow-[0_0_60px_rgba(0,0,0,0.4)]">

            {{-- Imagen vertical del evento --}}
            <div class="relative h-40 md:h-auto md:w-48 flex-shrink-0 overflow-hidden">
                @if($evento->imagenUrl())
                    <img src="{{ $evento->imagenUrl() }}" alt="" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-black/30"></div>
                @else
                    <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-900 to-sky-950 flex items-center justify-center">
                        @if($config->logoLoginUrl())
                            <img src="{{ $config->logoLoginUrl() }}" alt="Logo" class="h-12 w-auto object-contain opacity-70">
                        @endif
                    </div>
                @endif

                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-[9px] text-slate-300 uppercase tracking-widest mb-1">Organizado por</p>
                    @if($config->logoLoginUrl())
                        <img src="{{ $config->logoLoginUrl() }}" alt="Logo" class="h-6 w-auto object-contain">
                    @else
                        <span class="text-xs font-bold text-white">{{ $config->nombre_comercial ?: 'Estelar' }}</span>
                    @endif
                </div>
            </div>

            {{-- Divisor perforado --}}
            <div class="hidden md:block w-6 h-6 rounded-full bg-estelar-bg absolute left-48 -top-3 -translate-x-1/2 z-10"></div>
            <div class="hidden md:block w-6 h-6 rounded-full bg-estelar-bg absolute left-48 -bottom-3 -translate-x-1/2 z-10"></div>

            {{-- Talonario --}}
            <div class="flex-1 bg-white border-t md:border-t-0 md:border-l border-dashed border-slate-300 flex flex-col p-6 gap-4">

                <div>
                    <p class="text-[10px] font-semibold text-sky-600 uppercase tracking-widest">Entrada general</p>
                    <p class="text-lg font-bold text-slate-900 mt-0.5 leading-snug">{{ $evento->nombre }}</p>
                    @if($evento->descripcion)
                    <p class="text-xs text-slate-500 mt-1">{{ $evento->descripcion }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-3 text-xs">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Fecha</p>
                        <p class="text-slate-800 font-semibold font-mono">{{ $evento->fecha_inicio->format('d/m/Y') }}</p>
                    </div>
                    @if($evento->horaFormateada())
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Hora</p>
                        <p class="text-slate-800 font-semibold font-mono">{{ $evento->horaFormateada() }}</p>
                    </div>
                    @endif
                    @if($evento->lugar)
                    <div class="col-span-2">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Lugar</p>
                        <p class="text-slate-800 font-semibold">{{ $evento->lugar }}</p>
                    </div>
                    @endif
                    <div class="col-span-2">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Asistente</p>
                        <p class="text-slate-800 font-semibold">{{ $asistente->nombres }}</p>
                        @if($asistente->empresa)
                        <p class="text-slate-500">{{ $asistente->empresa }}</p>
                        @endif
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-200 pt-4 flex items-center gap-4">
                    <div class="bg-white p-1 flex-shrink-0">
                        {!! \App\Support\QrGenerator::svg($asistente->qr_token, 110) !!}
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">N.º de entrada</p>
                        <p class="text-sm text-slate-700 font-mono font-semibold tracking-wider">{{ $asistente->codigo }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row gap-3 no-print">
            @php
                $mensajeWhatsapp = "Aquí está mi entrada para *{$evento->nombre}*:\n" . url()->current();
            @endphp
            <a href="https://wa.me/51977765710?text={{ urlencode($mensajeWhatsapp) }}" target="_blank" rel="noopener"
               class="flex-1 py-3.5 sm:py-3 rounded-xl text-base sm:text-sm font-semibold text-white text-center
                      bg-emerald-600 hover:bg-emerald-500
                      shadow-[0_0_18px_rgba(16,185,129,0.35)] hover:shadow-[0_0_28px_rgba(16,185,129,0.55)]
                      transition-all duration-200 active:scale-[0.98] inline-flex items-center justify-center gap-2.5">
                <svg class="w-6 h-6 sm:w-4 sm:h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2Zm0 18.09h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.32c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.55-3.7 8.24-8.24 8.24Zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.04-.38-1.99-1.22-.73-.66-1.23-1.46-1.37-1.71-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.44.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.42h-.48c-.17 0-.44.06-.67.31-.23.25-.87.85-.87 2.08s.9 2.41 1.02 2.58c.12.17 1.77 2.7 4.29 3.78.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.16-.48-.28Z"/>
                </svg>
                Enviar por WhatsApp
            </a>
            <button onclick="window.print()"
                    class="flex-1 py-3.5 sm:py-3 rounded-xl text-base sm:text-sm font-semibold text-white
                           bg-gradient-to-r from-sky-500 to-cyan-500
                           shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                           transition-all duration-200 active:scale-[0.98]">
                Guardar / imprimir
            </button>
        </div>

        <p class="text-center text-slate-600 text-xs mt-5">
            Guarda este QR — te lo pedirán en el ingreso al evento.
        </p>
    </div>

</body>
</html>
