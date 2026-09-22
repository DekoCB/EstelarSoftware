<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('planilla.index') }}" class="text-slate-600 hover:text-slate-400">Planilla</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Registrar pago</span>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Registrar pago de planilla</h2>
            <p class="text-sm text-slate-500 mt-0.5">El pago queda pendiente hasta que se confirme el desembolso</p>
        </div>

        <form action="{{ route('planilla.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-4">

                {{-- Personal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Personal <span class="text-rose-400">*</span>
                    </label>
                    <select name="user_id" required
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors
                                   @error('user_id') border-rose-500/60 @enderror">
                        <option value="">-- Seleccionar persona --</option>
                        @foreach($personal as $p)
                        <option value="{{ $p->id }}" {{ old('user_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}{{ $p->cargo ? ' — ' . $p->cargo : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Período --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                            Período <span class="text-rose-400">*</span>
                        </label>
                        <input type="month" name="periodo" value="{{ old('periodo', $periodo) }}"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors
                                      @error('periodo') border-rose-500/60 @enderror">
                        @error('periodo')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Tipo</label>
                        <select name="tipo"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            @foreach(\App\Models\PayrollPayment::TIPOS as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo', 'honorario') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Concepto --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Concepto <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="concepto" value="{{ old('concepto') }}"
                           placeholder="Ej: Honorarios desarrollo backend mayo 2026"
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
                               step="0.01" min="1" placeholder="0.00"
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

                {{-- Notas --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Notas</label>
                    <textarea name="notas" rows="2" placeholder="Observaciones opcionales..."
                              class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                     text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60
                                     transition-colors resize-none">{{ old('notas') }}</textarea>
                </div>

            </div>

            <div class="bg-slate-900/50 border border-slate-800/40 rounded-xl px-4 py-3">
                <p class="text-xs text-slate-500">
                    El pago se guarda como <span class="text-amber-400 font-semibold">Pendiente</span>.
                    Desde el listado puedes confirmarlo con el método y fecha de desembolso,
                    lo que lo registrará automáticamente en <span class="text-sky-400">Caja</span> como egreso.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('planilla.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                               transition-all active:scale-[0.98]">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
