<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('asistencias.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white">Enviar Justificación</h1>
                <p class="text-sm text-slate-400 mt-0.5">Justifica una ausencia o registro faltante</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-lg">

        @if($yaExiste)
        <div class="card p-6 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
            </svg>
            <p class="text-white font-semibold">Ya tienes {{ $tipo }} registrada para esta fecha</p>
            <p class="text-slate-400 text-sm mt-1">No es necesario enviar una justificación.</p>
            <a href="{{ route('asistencias.index') }}" class="inline-block mt-4 text-sm text-sky-400 hover:underline">Volver al reporte</a>
        </div>

        @elseif($yaEnviada)
        <div class="card p-6 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <p class="text-white font-semibold">Ya enviaste una justificación para esta fecha</p>
            <p class="text-slate-400 text-sm mt-1">Espera a que el administrador la revise.</p>
            <a href="{{ route('asistencias.index') }}" class="inline-block mt-4 text-sm text-sky-400 hover:underline">Volver al reporte</a>
        </div>

        @else
        <form method="POST" action="{{ route('asistencias.justificar.store') }}">
            @csrf
            <div class="card p-6 space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    {{-- Fecha --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Fecha</label>
                        <input type="date" name="fecha"
                               value="{{ old('fecha', $fecha) }}"
                               max="{{ today()->format('Y-m-d') }}"
                               class="input-field w-full" required>
                        @error('fecha')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Tipo</label>
                        <select name="tipo" class="input-field w-full" required>
                            <option value="entrada" @selected(old('tipo',$tipo)==='entrada')>Entrada</option>
                            <option value="salida"  @selected(old('tipo',$tipo)==='salida')>Salida</option>
                        </select>
                        @error('tipo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Hora justificada --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">
                        Hora a registrar
                        <span class="text-slate-500 font-normal text-xs ml-1">(la hora real en que llegaste/saliste)</span>
                    </label>
                    <input type="time" name="hora_justificada"
                           value="{{ old('hora_justificada') }}"
                           class="input-field w-full font-mono" required>
                    @error('hora_justificada')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Motivo --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Motivo / Explicación</label>
                    <textarea name="motivo" rows="4"
                              placeholder="Explica por qué no pudiste registrar tu asistencia en ese momento..."
                              class="w-full resize-none rounded-xl border border-slate-700 bg-slate-800 text-slate-100 text-sm px-3 py-2.5 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                              required>{{ old('motivo') }}</textarea>
                    <p class="text-xs text-slate-600 mt-1">Sé específico: el administrador tomará una decisión en base a tu justificación.</p>
                    @error('motivo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="{{ route('asistencias.index') }}"
                   class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-white transition-colors duration-150">
                    Enviar justificación
                </button>
            </div>
        </form>
        @endif
    </div>
</x-app-layout>
