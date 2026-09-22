<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('proyectos.index') }}" class="text-slate-600 hover:text-slate-400 font-mono">Proyectos</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="text-slate-600 hover:text-slate-400 truncate max-w-[160px]">{{ $proyecto->name }}</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Editar</span>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Editar proyecto</h2>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-700/60 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Cancelar
            </a>
        </div>

        <form method="POST" action="{{ route('proyectos.update', $proyecto) }}">
            @csrf @method('PUT')
            @include('proyectos._form')
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('proyectos.show', $proyecto) }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white border border-slate-700/60 hover:bg-slate-800 transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 shadow-[0_0_18px_rgba(14,165,233,0.35)] transition-all active:scale-[0.98]">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
