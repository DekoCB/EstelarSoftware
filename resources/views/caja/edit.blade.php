<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('caja.index') }}" class="text-slate-600 hover:text-slate-400">Caja</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('caja.show', $movimiento) }}" class="text-slate-600 hover:text-slate-400 font-mono">
                {{ $movimiento->concepto }}
            </a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Editar</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Editar movimiento</h2>
        </div>

        <form action="{{ route('caja.update', $movimiento) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-5">

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold
                                 {{ $movimiento->tipo === 'ingreso' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $movimiento->tipo === 'ingreso' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        {{ ucfirst($movimiento->tipo) }}
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Concepto <span class="text-rose-400">*</span></label>
                    <input type="text" name="concepto" value="{{ old('concepto', $movimiento->concepto) }}"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('concepto') border-rose-500/60 @enderror">
                    @error('concepto')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Monto <span class="text-rose-400">*</span></label>
                        <input type="number" name="monto" value="{{ old('monto', $movimiento->monto) }}"
                               step="0.01" min="0.01"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white font-mono focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Moneda</label>
                        <select name="moneda"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            <option value="PEN" {{ old('moneda', $movimiento->moneda) === 'PEN' ? 'selected' : '' }}>PEN — Soles</option>
                            <option value="USD" {{ old('moneda', $movimiento->moneda) === 'USD' ? 'selected' : '' }}>USD — Dólares</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Fecha <span class="text-rose-400">*</span></label>
                        <input type="date" name="fecha" value="{{ old('fecha', $movimiento->fecha->toDateString()) }}"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Método de pago</label>
                        <select name="metodo_pago"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            @foreach(\App\Models\CashMovement::METODOS_PAGO as $val => $label)
                            <option value="{{ $val }}" {{ old('metodo_pago', $movimiento->metodo_pago) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Categoría <span class="text-rose-400">*</span></label>
                    <select name="categoria"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                        @if($movimiento->tipo === 'ingreso')
                            @foreach(\App\Models\CashMovement::CATEGORIAS_INGRESO as $val => $label)
                            <option value="{{ $val }}" {{ old('categoria', $movimiento->categoria) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        @else
                            @foreach(\App\Models\CashMovement::CATEGORIAS_EGRESO as $val => $label)
                            <option value="{{ $val }}" {{ old('categoria', $movimiento->categoria) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Cliente</label>
                    <select name="client_id"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                        <option value="">Sin cliente asociado</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('client_id', $movimiento->client_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->razon_social }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Referencia</label>
                    <input type="text" name="referencia" value="{{ old('referencia', $movimiento->referencia) }}"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Notas</label>
                    <textarea name="notas" rows="3"
                              class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                     text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors resize-none">{{ old('notas', $movimiento->notas) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('caja.show', $movimiento) }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                               transition-all active:scale-[0.98]">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
