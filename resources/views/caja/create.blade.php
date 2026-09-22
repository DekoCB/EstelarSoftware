<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('caja.index') }}" class="text-slate-600 hover:text-slate-400">Caja</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Nuevo {{ ucfirst($tipo) }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Registrar {{ ucfirst($tipo) }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">Nuevo movimiento de caja</p>
        </div>

        <form action="{{ route('caja.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">

            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-5">

                {{-- Tipo badge --}}
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold
                                 {{ $tipo === 'ingreso' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $tipo === 'ingreso' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        {{ ucfirst($tipo) }}
                    </span>
                    <span class="text-[10px] text-slate-600">
                        ¿Es un {{ $tipo === 'ingreso' ? 'egreso' : 'ingreso' }}?
                        <a href="{{ route('caja.create', ['tipo' => $tipo === 'ingreso' ? 'egreso' : 'ingreso']) }}"
                           class="text-sky-500 hover:underline">Cambiar</a>
                    </span>
                </div>

                {{-- Concepto --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Concepto <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="concepto" value="{{ old('concepto') }}"
                           placeholder="Ej: Pago cuota 1 - Proyecto Web Molinera SAC"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('concepto') border-rose-500/60 @enderror">
                    @error('concepto')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Monto + Moneda --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                            Monto <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" name="monto" value="{{ old('monto') }}"
                               step="0.01" min="0.01" placeholder="0.00"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white font-mono placeholder-slate-600
                                      focus:outline-none focus:border-sky-500/60 transition-colors
                                      @error('monto') border-rose-500/60 @enderror">
                        @error('monto')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Moneda</label>
                        <select name="moneda"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            <option value="PEN" {{ old('moneda', 'PEN') === 'PEN' ? 'selected' : '' }}>PEN — Soles</option>
                            <option value="USD" {{ old('moneda') === 'USD' ? 'selected' : '' }}>USD — Dólares</option>
                        </select>
                    </div>
                </div>

                {{-- Fecha + Método pago --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                            Fecha <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors
                                      @error('fecha') border-rose-500/60 @enderror">
                        @error('fecha')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Método de pago</label>
                        <select name="metodo_pago"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            @foreach(\App\Models\CashMovement::METODOS_PAGO as $val => $label)
                            <option value="{{ $val }}" {{ old('metodo_pago', 'efectivo') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Categoría --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Categoría <span class="text-rose-400">*</span>
                    </label>
                    <select name="categoria"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors
                                   @error('categoria') border-rose-500/60 @enderror">
                        <option value="">-- Seleccionar --</option>
                        @if($tipo === 'ingreso')
                            @foreach(\App\Models\CashMovement::CATEGORIAS_INGRESO as $val => $label)
                            <option value="{{ $val }}" {{ old('categoria') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        @else
                            @foreach(\App\Models\CashMovement::CATEGORIAS_EGRESO as $val => $label)
                            <option value="{{ $val }}" {{ old('categoria') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('categoria')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Cliente (opcional) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Cliente <span class="text-slate-600">(opcional)</span></label>
                    <select name="client_id"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                        <option value="">Sin cliente asociado</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->razon_social }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Referencia --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Número de operación / referencia <span class="text-slate-600">(opcional)</span>
                    </label>
                    <input type="text" name="referencia" value="{{ old('referencia') }}"
                           placeholder="Ej: OP-2026001, comprobante #123"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                </div>

                {{-- Notas --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Notas</label>
                    <textarea name="notas" rows="3" placeholder="Notas adicionales..."
                              class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                     text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60
                                     transition-colors resize-none">{{ old('notas') }}</textarea>
                </div>

            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('caja.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400
                          hover:text-white hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               {{ $tipo === 'ingreso'
                                    ? 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-[0_0_18px_rgba(16,185,129,0.3)] hover:shadow-[0_0_28px_rgba(16,185,129,0.5)]'
                                    : 'bg-gradient-to-r from-rose-500 to-red-500 shadow-[0_0_18px_rgba(244,63,94,0.3)] hover:shadow-[0_0_28px_rgba(244,63,94,0.5)]' }}
                               transition-all active:scale-[0.98]">
                    Guardar {{ ucfirst($tipo) }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
