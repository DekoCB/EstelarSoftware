<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('caja.index') }}" class="text-slate-600 hover:text-slate-400">Caja</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold truncate max-w-[240px]">{{ $movimiento->concepto }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Encabezado ─────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold
                                     {{ $movimiento->tipo === 'ingreso' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $movimiento->tipo === 'ingreso' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                            {{ ucfirst($movimiento->tipo) }}
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-md
                                     {{ $movimiento->tipo === 'ingreso' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                            {{ $movimiento->categoriaLabel() }}
                        </span>
                    </div>
                    <h1 class="text-xl font-bold text-white">{{ $movimiento->concepto }}</h1>
                    @if($movimiento->descripcion)
                    <p class="text-sm text-slate-500 mt-1">{{ $movimiento->descripcion }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <p class="text-2xl font-bold font-mono {{ $movimiento->tipo === 'ingreso' ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $movimiento->tipo === 'ingreso' ? '+' : '-' }}
                        {{ $movimiento->moneda === 'USD' ? '$' : 'S/' }} {{ $movimiento->montoFormateado() }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $movimiento->moneda }}</p>
                </div>
            </div>
        </div>

        {{-- Detalles ────────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Detalles</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Fecha</dt>
                    <dd class="text-sm font-mono text-white">{{ $movimiento->fecha->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Método de pago</dt>
                    <dd class="text-sm text-white">{{ $movimiento->metodoPagoLabel() }}</dd>
                </div>
                @if($movimiento->referencia)
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Referencia / N° operación</dt>
                    <dd class="text-sm font-mono text-white">{{ $movimiento->referencia }}</dd>
                </div>
                @endif
                @if($movimiento->client)
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Cliente</dt>
                    <dd>
                        <a href="{{ route('clientes.show', $movimiento->client) }}"
                           class="text-sm text-sky-400 hover:underline">{{ $movimiento->client->razon_social }}</a>
                    </dd>
                </div>
                @endif
                @if($movimiento->invoice)
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Comprobante</dt>
                    <dd>
                        <a href="{{ route('facturacion.show', $movimiento->invoice) }}"
                           class="text-sm font-mono text-sky-400 hover:underline">{{ $movimiento->invoice->numero_completo }}</a>
                    </dd>
                </div>
                @endif
                @if($movimiento->quote)
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Cotización</dt>
                    <dd>
                        <a href="{{ route('cotizaciones.show', $movimiento->quote) }}"
                           class="text-sm font-mono text-sky-400 hover:underline">{{ $movimiento->quote->numero }}</a>
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Registrado por</dt>
                    <dd class="text-sm text-white">{{ $movimiento->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Fecha de registro</dt>
                    <dd class="text-sm font-mono text-slate-400">{{ $movimiento->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
            @if($movimiento->notas)
            <div class="mt-4 pt-4 border-t border-slate-800/60">
                <p class="text-xs text-slate-500 mb-1">Notas</p>
                <p class="text-sm text-slate-300">{{ $movimiento->notas }}</p>
            </div>
            @endif
        </div>

        {{-- Acciones ────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('caja.index') }}"
               class="text-sm text-slate-500 hover:text-white transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver a Caja
            </a>
            <div class="flex gap-2">
                @can('caja.editar')
                <a href="{{ route('caja.edit', $movimiento) }}"
                   class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300
                          bg-slate-800 hover:bg-slate-700 transition-colors">
                    Editar
                </a>
                @endcan
                @can('caja.eliminar')
                <form action="{{ route('caja.destroy', $movimiento) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este movimiento?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-rose-400
                                   bg-rose-500/10 hover:bg-rose-500/20 transition-colors">
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>

    </div>
</x-app-layout>
