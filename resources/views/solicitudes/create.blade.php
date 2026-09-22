<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-white">Nueva Solicitud</h1>
            <p class="text-sm text-slate-400 mt-0.5">Pide dinero para gastos o un objeto que necesites</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('solicitudes.store') }}" x-data="{ tipo: '{{ old('tipo', 'dinero') }}' }">
            @csrf

            <div class="card p-6 space-y-5">

                {{-- Tipo --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Tipo de solicitud</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="dinero" x-model="tipo" class="sr-only">
                            <div :class="tipo === 'dinero' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-700 text-slate-400 hover:border-slate-600'"
                                 class="flex items-center gap-3 p-4 rounded-xl border transition-all duration-150">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-sm">Dinero</p>
                                    <p class="text-xs opacity-70">Pasaje, viáticos, compras</p>
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="objeto" x-model="tipo" class="sr-only">
                            <div :class="tipo === 'objeto' ? 'border-purple-500 bg-purple-500/10 text-purple-400' : 'border-slate-700 text-slate-400 hover:border-slate-600'"
                                 class="flex items-center gap-3 p-4 rounded-xl border transition-all duration-150">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-sm">Objeto</p>
                                    <p class="text-xs opacity-70">Material, equipo, herramienta</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('tipo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Título --}}
                <div>
                    <label for="titulo" class="block text-sm font-medium text-slate-300 mb-1.5">Título</label>
                    <input id="titulo" name="titulo" type="text"
                           value="{{ old('titulo') }}"
                           placeholder="Ej: Pasaje para reunión en cliente X"
                           class="input-field w-full"
                           required>
                    @error('titulo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Monto (solo dinero) --}}
                <div x-show="tipo === 'dinero'" x-transition>
                    <label for="monto" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Monto solicitado <span class="text-slate-500">(S/)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-mono">S/</span>
                        <input id="monto" name="monto" type="number" step="0.01" min="0.01"
                               value="{{ old('monto') }}"
                               placeholder="0.00"
                               class="input-field w-full pl-9">
                    </div>
                    @error('monto')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Descripción --}}
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Descripción / Motivo
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                              placeholder="Explica para qué necesitas esto, cuándo lo usarás, etc."
                              class="input-dark w-full resize-none"
                              required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="{{ route('solicitudes.index') }}"
                   class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white transition-colors duration-150">
                    Enviar solicitud
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
