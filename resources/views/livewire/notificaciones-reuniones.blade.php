<div wire:poll.20s x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">

    @if($total > 0)
    {{-- Campana con badge --}}
    <button @click="open = !open"
            class="relative flex items-center justify-center w-9 h-9 rounded-xl
                   bg-sky-500/10 border border-sky-500/30 text-sky-400
                   hover:bg-sky-500/20 transition-all duration-200"
            aria-label="Reuniones de hoy">

        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
        </svg>

        {{-- Badge contador --}}
        <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center
                     min-w-[18px] h-[18px] px-1 rounded-full
                     bg-sky-500 text-white text-[10px] font-bold leading-none">
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
                bg-slate-900 border border-sky-500/20 rounded-2xl shadow-xl shadow-black/40
                overflow-hidden"
         x-cloak>

        {{-- Header del dropdown --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                <p class="text-xs font-semibold text-white">Reuniones de hoy</p>
            </div>
            <span class="text-[10px] font-mono text-sky-400 bg-sky-500/10 px-2 py-0.5 rounded-lg">
                {{ now()->locale('es')->isoFormat('D MMM') }}
            </span>
        </div>

        {{-- Lista de reuniones --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-slate-800/60">
            @foreach($reuniones as $r)
            @php
                $esLlamada = $r->lead->respuesta_rapida === 'volver_a_llamar';
                $tipoLabel = $esLlamada ? 'llamada' : 'reunión';
            @endphp
            <a href="{{ route('leads.show', $r->lead) }}"
               @click="open = false"
               class="flex items-start gap-3 px-4 py-3 hover:bg-slate-800/50 transition-colors group">

                {{-- Avatar inicial --}}
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 border
                            {{ $esLlamada ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-sky-500/10 border-sky-500/20' }}">
                    <span class="text-xs font-bold {{ $esLlamada ? 'text-emerald-400' : 'text-sky-400' }}">
                        {{ strtoupper(substr($r->lead->nombre ?? '?', 0, 1)) }}
                    </span>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-200 truncate group-hover:text-white transition-colors">
                        {{ $r->lead->nombre }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ $r->lead->empresa ?? 'Sin empresa' }}
                        <span class="text-slate-700 mx-1">·</span>
                        {{ $r->fecha_hora->locale('es')->diffForHumans() }}
                    </p>
                </div>

                <div class="flex-shrink-0 text-right">
                    <p class="text-xs font-bold font-mono {{ $esLlamada ? 'text-emerald-400' : 'text-sky-400' }}">
                        {{ $r->fecha_hora->format('H:i') }}
                    </p>
                    <p class="text-[10px] text-slate-600 mt-0.5 uppercase tracking-wide">{{ $tipoLabel }}</p>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-slate-800 bg-slate-950/40">
            <a href="{{ route('leads.calendario') }}"
               @click="open = false"
               class="text-xs text-sky-400 hover:text-sky-300 transition-colors font-medium">
                Ver calendario de reuniones →
            </a>
        </div>

    </div>
    @endif

</div>
