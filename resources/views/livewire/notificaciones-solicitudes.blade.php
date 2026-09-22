<div wire:poll.20s x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">

    @if($total > 0)
    {{-- Campana con badge rojo --}}
    <button @click="open = !open"
            class="relative flex items-center justify-center w-9 h-9 rounded-xl
                   bg-red-500/10 border border-red-500/30 text-red-400
                   hover:bg-red-500/20 transition-all duration-200"
            aria-label="Alertas de solicitudes">

        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
        </svg>

        {{-- Badge contador --}}
        <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center
                     min-w-[18px] h-[18px] px-1 rounded-full
                     bg-red-500 text-white text-[10px] font-bold leading-none">
            {{ $total > 9 ? '9+' : $total }}
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute right-0 top-11 z-50 w-80
                bg-slate-900 border border-red-500/20 rounded-2xl shadow-xl shadow-black/40
                overflow-hidden"
         x-cloak>

        {{-- Header del dropdown --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <p class="text-xs font-semibold text-white">Solicitudes pendientes</p>
            </div>
            <span class="text-[10px] font-mono text-red-400 bg-red-500/10 px-2 py-0.5 rounded-lg">
                &lt; S/ 500
            </span>
        </div>

        {{-- Lista de solicitudes --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-slate-800/60">
            @foreach($solicitudes as $sol)
            <a href="{{ route('solicitudes.show', $sol) }}"
               @click="open = false"
               class="flex items-start gap-3 px-4 py-3 hover:bg-slate-800/50 transition-colors group">

                {{-- Avatar inicial --}}
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20
                            flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-amber-400">
                        {{ strtoupper(substr($sol->solicitante->name ?? '?', 0, 1)) }}
                    </span>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-200 truncate group-hover:text-white transition-colors">
                        {{ $sol->titulo }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ $sol->solicitante->name ?? 'Usuario' }}
                        <span class="text-slate-700 mx-1">·</span>
                        {{ $sol->created_at->diffForHumans() }}
                    </p>
                </div>

                <div class="flex-shrink-0 text-right">
                    <p class="text-xs font-bold font-mono text-amber-400">
                        S/ {{ number_format($sol->monto, 2) }}
                    </p>
                    <p class="text-[10px] text-slate-600 mt-0.5 uppercase tracking-wide">dinero</p>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-slate-800 bg-slate-950/40">
            <a href="{{ route('solicitudes.index') }}"
               @click="open = false"
               class="text-xs text-sky-400 hover:text-sky-300 transition-colors font-medium">
                Ver todas las solicitudes →
            </a>
        </div>

    </div>
    @endif

</div>
