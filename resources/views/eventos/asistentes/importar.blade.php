<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('eventos.index') }}" class="text-slate-600 hover:text-slate-400 transition-colors font-mono">Eventos</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('eventos.show', $evento) }}" class="text-slate-600 hover:text-slate-400 transition-colors truncate max-w-[200px]">{{ $evento->nombre }}</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Importar asistentes</span>
        </div>
    </x-slot>

    <div class="max-w-2xl">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Importar asistentes desde Excel</h2>
                <p class="text-sm text-slate-500 mt-0.5">Sube un archivo con varios asistentes a la vez</p>
            </div>
            <a href="{{ route('eventos.show', $evento) }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-slate-400
                      hover:text-white hover:bg-slate-800 transition-colors border border-slate-700/60">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
        </div>

        {{-- Paso 1: plantilla --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 mb-6">
            <h3 class="text-sm font-semibold text-white mb-2 flex items-center gap-2">
                <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">1</span>
                Descarga la plantilla
            </h3>
            <p class="text-sm text-slate-500 mb-4">
                Usa este formato exacto (columnas: nombres, empresa, tipo documento, numero documento, direccion, email, celular). Solo "nombres" es obligatorio.
            </p>
            <a href="{{ route('eventos.asistentes.plantilla', $evento) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white
                      transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 12m0 0 4.5-4.5M12 12V3"/>
                </svg>
                Descargar plantilla (.xlsx)
            </a>
        </div>

        {{-- Paso 2: subir --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">2</span>
                Sube el archivo lleno
            </h3>

            <form method="POST" action="{{ route('eventos.asistentes.importar.store', $evento) }}" enctype="multipart/form-data"
                  x-data="{ nombreArchivo: 'Ningún archivo seleccionado' }">
                @csrf

                <div class="flex items-center gap-3 flex-wrap">
                    <label for="archivo-input"
                           class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                  bg-sky-500/10 text-sky-400 border border-sky-500/30
                                  hover:bg-sky-500/20 transition-colors duration-150 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 12m0 0 4.5-4.5M12 12V3"/>
                        </svg>
                        Seleccionar archivo
                    </label>
                    <span class="text-sm text-slate-500 truncate" x-text="nombreArchivo"></span>
                </div>
                <input id="archivo-input" type="file" name="archivo" accept=".xlsx,.xls,.csv" class="sr-only"
                       @change="nombreArchivo = $event.target.files[0]?.name || 'Ningún archivo seleccionado'">
                @error('archivo')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end mt-5">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                                   bg-gradient-to-r from-sky-500 to-cyan-500
                                   hover:from-sky-400 hover:to-cyan-400
                                   shadow-[0_0_18px_rgba(14,165,233,0.35)]
                                   hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                                   transition-all duration-200 active:scale-[0.98]">
                        Importar
                    </button>
                </div>
            </form>
        </div>

        {{-- Resultado: errores fila por fila --}}
        @if(session('import_failures'))
        <div class="mt-6 bg-amber-500/5 border border-amber-500/20 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-amber-400 mb-4">Filas con errores</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-amber-500/20">
                            <th class="text-left px-3 py-2 text-xs font-semibold text-slate-500 uppercase">Fila</th>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-slate-500 uppercase">Campo</th>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-slate-500 uppercase">Error</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach(session('import_failures') as $failure)
                        <tr>
                            <td class="px-3 py-2 font-mono text-slate-300">{{ $failure->row() }}</td>
                            <td class="px-3 py-2 text-slate-400">{{ $failure->attribute() }}</td>
                            <td class="px-3 py-2 text-amber-400">{{ implode(', ', $failure->errors()) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

</x-app-layout>
