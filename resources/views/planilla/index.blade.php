<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-600">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="font-semibold text-white">Planilla</h1>
        </div>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Planilla de personal</h2>
            <p class="text-sm text-slate-500 mt-0.5">Pagos al equipo por servicios prestados</p>
        </div>
        @can('planilla.crear')
        <a href="{{ route('planilla.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                  shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                  transition-all active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Registrar pago
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-400"
         x-data x-init="setTimeout(() => $el.remove(), 5000)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-sm text-rose-400">{{ session('error') }}</div>
    @endif

    {{-- Filtros + resumen ────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('planilla.index') }}" class="mb-5">
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-4 flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-[10px] text-slate-500 mb-1 uppercase tracking-wider">Período</label>
                <input type="month" name="periodo" value="{{ $periodo }}"
                       class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-sm text-white
                              focus:outline-none focus:border-sky-500/60 transition-colors">
            </div>
            <div>
                <label class="block text-[10px] text-slate-500 mb-1 uppercase tracking-wider">Personal</label>
                <select name="user_id"
                        class="bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2 text-sm text-slate-300
                               focus:outline-none focus:border-sky-500/60 transition-colors min-w-[160px]">
                    <option value="">Todos</option>
                    @foreach($personal as $p)
                    <option value="{{ $p->id }}" {{ $userId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-sky-500/20 hover:bg-sky-500/30
                           border border-sky-500/30 transition-colors">
                Filtrar
            </button>

            {{-- KPIs del período --}}
            <div class="ml-auto flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Total período</p>
                    <p class="text-sm font-bold font-mono text-white">S/ {{ number_format($totalPeriodo, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-emerald-500 uppercase tracking-wider">Pagado</p>
                    <p class="text-sm font-bold font-mono text-emerald-400">S/ {{ number_format($pagado, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-amber-500 uppercase tracking-wider">Pendiente</p>
                    <p class="text-sm font-bold font-mono text-amber-400">S/ {{ number_format($pendiente, 2) }}</p>
                </div>
            </div>
        </div>
    </form>

    {{-- Tabla ────────────────────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/80">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Personal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Concepto</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Período</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Monto</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($pagos as $pago)
                <tr class="hover:bg-slate-800/30 transition-colors group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold shrink-0
                                        {{ $pago->user->rolBadgeClass() }}">
                                {{ strtoupper(substr($pago->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">{{ $pago->user->name }}</p>
                                <p class="text-[10px] text-slate-500">{{ $pago->user->cargo ?? $pago->tipoLabel() }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-xs text-slate-300 truncate max-w-[200px]">{{ $pago->concepto }}</p>
                        <span class="inline-flex items-center mt-0.5 px-1.5 py-0.5 rounded text-[9px] font-semibold {{ $pago->tipoBadgeClass() }}">
                            {{ $pago->tipoLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs font-mono text-slate-400">{{ $pago->periodoFormateado() }}</p>
                        @if($pago->fecha_pago)
                        <p class="text-[10px] text-slate-600">Pagado {{ $pago->fecha_pago->format('d/m/Y') }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <p class="text-sm font-bold font-mono text-white">
                            {{ $pago->moneda === 'USD' ? '$' : 'S/' }} {{ number_format($pago->monto, 2) }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($pago->estado === 'pagado')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold
                                     bg-emerald-500/15 text-emerald-400">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            Pagado
                        </span>
                        @else
                        @can('planilla.pagar')
                        <button type="button"
                                @click="$dispatch('open-pagar', { id: {{ $pago->id }}, nombre: '{{ $pago->user->name }}', monto: 'S/ {{ number_format($pago->monto, 2) }}' })"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold
                                       bg-amber-500/15 text-amber-400 hover:bg-amber-500/25 transition-colors cursor-pointer">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Pendiente · Pagar
                        </button>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-amber-500/10 text-amber-400">Pendiente</span>
                        @endcan
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('planilla.show', $pago) }}"
                           class="p-1.5 rounded-lg text-slate-600 hover:text-sky-400 hover:bg-sky-500/10
                                  transition-colors opacity-0 group-hover:opacity-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-14 text-center">
                        <svg class="w-10 h-10 text-slate-800 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                        </svg>
                        <p class="text-slate-600 text-sm">No hay pagos registrados para este período</p>
                        @can('planilla.crear')
                        <a href="{{ route('planilla.create') }}" class="mt-3 inline-flex text-xs text-sky-400 hover:text-sky-300">
                            Registrar primer pago →
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal pagar ───────────────────────────────────────────────────── --}}
    @can('planilla.pagar')
    <div x-data="{ open: false, pagoId: null, nombre: '', monto: '' }"
         @open-pagar.window="open = true; pagoId = $event.detail.id; nombre = $event.detail.nombre; monto = $event.detail.monto">

        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
             style="display:none" @click.self="open = false">
            <div class="bg-slate-900 border border-slate-700/60 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-sm font-bold text-white mb-1">Procesar pago</h3>
                <p class="text-xs text-slate-400 mb-4">
                    <span x-text="nombre"></span> · <span class="font-mono text-emerald-400" x-text="monto"></span>
                </p>

                <form :action="`/planilla/${pagoId}/pagar`" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs text-slate-400 mb-1.5">Método de pago <span class="text-rose-400">*</span></label>
                        <select name="metodo_pago" required
                                class="w-full bg-slate-800 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia bancaria</option>
                            <option value="yape">Yape</option>
                            <option value="plin">Plin</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="cheque">Cheque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1.5">Fecha de pago <span class="text-rose-400">*</span></label>
                        <input type="date" name="fecha_pago" value="{{ now()->toDateString() }}" required
                               class="w-full bg-slate-800 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1.5">Notas</label>
                        <input type="text" name="notas" placeholder="Nro. operación, referencia..."
                               class="w-full bg-slate-800 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="open = false"
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-400
                                       hover:bg-slate-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white
                                       bg-gradient-to-r from-emerald-500 to-teal-500
                                       shadow-[0_0_14px_rgba(16,185,129,0.3)] hover:shadow-[0_0_20px_rgba(16,185,129,0.5)]
                                       transition-all active:scale-[0.98]">
                            Confirmar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

</x-app-layout>
