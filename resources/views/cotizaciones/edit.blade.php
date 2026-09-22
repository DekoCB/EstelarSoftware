<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('cotizaciones.index') }}" class="text-slate-600 hover:text-slate-400">Cotizaciones</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="text-slate-600 hover:text-slate-400 font-mono">{{ $cotizacion->numero }}</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Editar</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Editar {{ $cotizacion->numero }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $cotizacion->client->razon_social }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('cotizaciones.update', $cotizacion) }}">
            @csrf @method('PUT')
            @include('cotizaciones._form')
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('cotizaciones.show', $cotizacion) }}"
                   class="px-5 py-2.5 text-sm text-slate-400 hover:text-white border border-slate-700/60 rounded-xl hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400
                               transition-all active:scale-[0.98]">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
