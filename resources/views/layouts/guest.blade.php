<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — Acceso</title>

    @php $faviconConfig = \App\Models\EmpresaConfig::config(); @endphp
    @if($faviconConfig->logo_sidebar)
        <link rel="icon" href="{{ $faviconConfig->logoSidebarUrl() }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-estelar-bg min-h-screen">

<div class="min-h-screen flex">

    {{-- ── Panel izquierdo: branding ────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-[55%] relative overflow-hidden flex-col items-center justify-center p-14">

        {{-- Fondo animado --}}
        <div class="absolute inset-0 tech-grid"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        {{-- Contenido branding --}}
        <div class="relative z-10 w-full max-w-lg">

            {{-- Logotipo --}}
            @php $config = \App\Models\EmpresaConfig::config(); $logoUrl = $config->logoLoginUrl(); @endphp
            <div class="flex items-center gap-4 mb-12">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-14 w-auto object-contain flex-shrink-0">
                @else
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-400 to-cyan-500
                            flex items-center justify-center shadow-[0_0_35px_rgba(14,165,233,0.5)] flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375
                                 m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375
                                 m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375
                                 m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight leading-tight">
                        {{ $config->nombre_comercial ?: 'Estelar' }} <span class="text-sky-400">ERP</span>
                    </h1>
                    <p class="text-slate-500 text-sm font-light">Software Empresarial</p>
                </div>
            </div>

            {{-- Tagline --}}
            <h2 class="text-4xl font-bold text-white leading-snug mb-4">
                Gestión empresarial<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-cyan-400">
                    inteligente y conectada
                </span>
            </h2>
            <p class="text-slate-400 text-base font-light mb-12 leading-relaxed">
                Control total de tu empresa: clientes, proyectos, facturación
                electrónica SUNAT, caja y más — desde un solo lugar.
            </p>

            {{-- Stats cards --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/40
                            rounded-2xl p-5 hover:border-sky-500/30 transition-colors duration-300">
                    <div class="text-2xl font-bold text-sky-400 font-mono mb-1">9</div>
                    <div class="text-xs text-slate-500 uppercase tracking-widest">Módulos</div>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/40
                            rounded-2xl p-5 hover:border-sky-500/30 transition-colors duration-300">
                    <div class="text-lg font-bold text-cyan-400 font-mono mb-1">SUNAT</div>
                    <div class="text-xs text-slate-500 uppercase tracking-widest">Facturación</div>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/40
                            rounded-2xl p-5 hover:border-sky-500/30 transition-colors duration-300">
                    <div class="text-2xl font-bold text-sky-400 font-mono mb-1">4</div>
                    <div class="text-xs text-slate-500 uppercase tracking-widest">Roles</div>
                </div>
            </div>

            {{-- Footer branding --}}
            <p class="text-slate-700 text-xs mt-12 font-mono">
                © {{ date('Y') }} Estelar Software Empresarial — El Tambo, Huancayo, Perú
            </p>
        </div>
    </div>

    {{-- ── Panel derecho: formulario ─────────────────────────────── --}}
    <div class="w-full lg:w-[45%] flex items-center justify-center p-6 lg:p-12
                relative border-l border-slate-800/60">

        {{-- Subtle glow behind the form --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                    w-80 h-80 bg-sky-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-sm relative z-10">
            {{ $slot }}
        </div>
    </div>

</div>

</body>
</html>
