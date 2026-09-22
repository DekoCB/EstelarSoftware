<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 font-mono">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="text-sm font-semibold text-white">{{ $modulo }}</h1>
        </div>
    </x-slot>

    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-md">

            {{-- Ícono animado --}}
            <div class="relative inline-flex items-center justify-center mb-8">
                <div class="w-24 h-24 rounded-3xl bg-slate-800/80 border border-slate-700/60
                            flex items-center justify-center">
                    <svg class="w-10 h-10 text-slate-600" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877
                                 M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766m-3.704 3.796
                                 L6.747 9.767a2.25 2.25 0 0 1 0-3.184l3.62-3.619a2.25 2.25 0 0 1
                                 3.184 0l1.437 1.437m-3.704 3.796 3.704-3.796"/>
                    </svg>
                </div>

                {{-- Badge de sprint pulsando --}}
                <div class="absolute -top-2 -right-2 flex items-center gap-1
                            bg-slate-900 border border-sky-500/30 rounded-full
                            px-2.5 py-1 shadow-[0_0_12px_rgba(14,165,233,0.2)]">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                    <span class="text-[10px] font-mono font-semibold text-sky-400">
                        Sprint {{ $sprint }}
                    </span>
                </div>
            </div>

            {{-- Texto --}}
            <h2 class="text-2xl font-bold text-white mb-2">En construcción</h2>
            <p class="text-slate-400 text-sm mb-2">
                El módulo de <span class="text-white font-medium">{{ $modulo }}</span>
                está siendo desarrollado.
            </p>
            <p class="text-slate-600 text-xs mb-8 font-mono">
                Disponible en el sprint {{ $sprint }}
                @if($sprint <= 2) — próximamente @elseif($sprint <= 4) — en desarrollo @else — planificado @endif
            </p>

            {{-- Barra de progreso visual por sprint --}}
            <div class="bg-slate-800/60 border border-slate-700/40 rounded-2xl p-5 mb-8 text-left">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-3 font-semibold">
                    Progreso del proyecto
                </p>
                <div class="space-y-2">
                    @foreach([
                        [1, 'Base · Auth · Clientes', true],
                        [2, 'Proyectos · Requerimientos', false],
                        [3, 'Cotizaciones · Ventas', false],
                        [4, 'Facturación SUNAT', false],
                        [5, 'Caja · Entregas', false],
                        [6, 'Dashboard · Reportes', false],
                    ] as [$s, $desc, $done])
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-bold font-mono
                            {{ $done
                                ? 'bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/30'
                                : ($s == $sprint
                                    ? 'bg-sky-500/20 text-sky-400 ring-1 ring-sky-500/30'
                                    : 'bg-slate-800 text-slate-600') }}">
                            {{ $s }}
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span class="text-xs {{ $done ? 'text-slate-300' : ($s == $sprint ? 'text-slate-300' : 'text-slate-600') }}">
                                {{ $desc }}
                            </span>
                            @if($done)
                            <span class="text-[10px] text-emerald-400 font-mono">✓ listo</span>
                            @elseif($s == $sprint)
                            <span class="text-[10px] text-sky-400 font-mono animate-pulse">en curso</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium
                      bg-slate-800 text-slate-300 border border-slate-700/60
                      hover:border-sky-500/30 hover:text-sky-400 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver al dashboard
            </a>

        </div>
    </div>

</x-app-layout>
