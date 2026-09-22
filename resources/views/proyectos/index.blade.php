<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 font-mono">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="text-sm font-semibold text-white">Proyectos</h1>
        </div>
    </x-slot>

    <div x-data="{ modalCrear: {{ $errors->any() ? 'true' : 'false' }} }">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Proyectos</h2>
                <p class="text-sm text-slate-500 mt-0.5">Ciclo de vida de los proyectos de software</p>
            </div>
            @can('proyectos.crear')
            <button
                @click="modalCrear = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                       bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                       shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                       transition-all duration-200 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo proyecto
            </button>
            @endcan
        </div>

        @livewire('proyectos.proyectos-list')

        {{-- ══ MODAL CREAR PROYECTO ══════════════════════════════════ --}}
        @can('proyectos.crear')
        <template x-teleport="body">
            <div
                x-show="modalCrear"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display:none"
            >
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="modalCrear = false"></div>

                <div class="relative min-h-full flex items-start justify-center p-4 pt-10">
                    <div
                        x-show="modalCrear"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="w-full max-w-3xl bg-slate-900 border border-slate-700/60
                               rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)] flex flex-col
                               max-h-[calc(100vh-5rem)]"
                        @click.stop
                    >
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80 flex-shrink-0">
                            <div>
                                <h2 class="text-base font-bold text-white">Nuevo proyecto</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Registra el proyecto y sus fases iniciales</p>
                            </div>
                            <button
                                @click="modalCrear = false"
                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                       text-slate-500 hover:text-white hover:bg-slate-800 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="overflow-y-auto flex-1 px-6 py-5">
                            <form method="POST" action="{{ route('proyectos.store') }}" id="form-crear-proyecto">
                                @csrf
                                @include('proyectos._form')
                            </form>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-800/80 flex-shrink-0">
                            <button
                                type="button"
                                @click="modalCrear = false"
                                class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white
                                       border border-slate-700/60 hover:bg-slate-800 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                form="form-crear-proyecto"
                                class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                                       bg-gradient-to-r from-sky-500 to-cyan-500
                                       hover:from-sky-400 hover:to-cyan-400
                                       shadow-[0_0_18px_rgba(14,165,233,0.35)]
                                       hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                                       transition-all duration-200 active:scale-[0.98]"
                            >
                                Crear proyecto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        @endcan

    </div>
</x-app-layout>
