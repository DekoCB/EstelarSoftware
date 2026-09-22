<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('planilla.index') }}" class="text-slate-600 hover:text-slate-400">Planilla</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">{{ $planilla->periodoFormateado() }} · {{ $planilla->user->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto space-y-5">

        @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-400"
             x-data x-init="setTimeout(() => $el.remove(), 4000)">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-sm text-rose-400">{{ session('error') }}</div>
        @endif

        {{-- Cabecera ────────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold
                                {{ $planilla->user->rolBadgeClass() }}">
                        {{ strtoupper(substr($planilla->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-base font-bold text-white">{{ $planilla->user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $planilla->user->cargo ?? $planilla->user->rolLabel() }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold font-mono text-white">
                        {{ $planilla->moneda === 'USD' ? '$' : 'S/' }} {{ number_format($planilla->monto, 2) }}
                    </p>
                    <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold
                                 {{ $planilla->estado === 'pagado' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">
                        {{ $planilla->estado === 'pagado' ? 'Pagado' : 'Pendiente' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Detalles ─────────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Detalles</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Concepto</dt>
                    <dd class="text-sm text-white">{{ $planilla->concepto }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Tipo</dt>
                    <dd>
                        <span class="text-xs px-2 py-0.5 rounded-md {{ $planilla->tipoBadgeClass() }}">
                            {{ $planilla->tipoLabel() }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Período</dt>
                    <dd class="text-sm font-mono text-white">{{ $planilla->periodoFormateado() }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Moneda</dt>
                    <dd class="text-sm font-mono text-white">{{ $planilla->moneda }}</dd>
                </div>
                @if($planilla->estado === 'pagado')
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Fecha de pago</dt>
                    <dd class="text-sm font-mono text-white">{{ $planilla->fecha_pago->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Método</dt>
                    <dd class="text-sm text-white">{{ $planilla->metodoPagoLabel() }}</dd>
                </div>
                @if($planilla->cashMovement)
                <div class="col-span-2">
                    <dt class="text-xs text-slate-500 mb-0.5">Movimiento en caja</dt>
                    <dd>
                        <a href="{{ route('caja.show', $planilla->cashMovement) }}"
                           class="text-xs text-sky-400 hover:underline font-mono">
                            Ver egreso en caja →
                        </a>
                    </dd>
                </div>
                @endif
                @endif
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Registrado por</dt>
                    <dd class="text-sm text-white">{{ $planilla->createdBy->name }}</dd>
                </div>
            </dl>
            @if($planilla->notas)
            <div class="mt-4 pt-4 border-t border-slate-800/60">
                <p class="text-xs text-slate-500 mb-1">Notas</p>
                <p class="text-sm text-slate-300">{{ $planilla->notas }}</p>
            </div>
            @endif
        </div>

        {{-- Acciones ─────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('planilla.index') }}"
               class="text-sm text-slate-500 hover:text-white transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
            <div class="flex gap-2">
                @if($planilla->estado === 'pagado')
                @can('planilla.pagar')
                <form action="{{ route('planilla.revertir', $planilla) }}" method="POST"
                      onsubmit="return confirm('¿Revertir este pago? Se eliminará el egreso en caja.')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-amber-400
                                   bg-amber-500/10 hover:bg-amber-500/20 transition-colors">
                        Revertir pago
                    </button>
                </form>
                @endcan
                @else
                @can('planilla.eliminar')
                <form action="{{ route('planilla.destroy', $planilla) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este registro?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-rose-400
                                   bg-rose-500/10 hover:bg-rose-500/20 transition-colors">
                        Eliminar
                    </button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
