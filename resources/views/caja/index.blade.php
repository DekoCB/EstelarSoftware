<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-600">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="font-semibold text-white">Caja</h1>
        </div>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Caja</h2>
            <p class="text-sm text-slate-500 mt-0.5">Ingresos y egresos de la empresa</p>
        </div>
        @can('caja.crear')
        <div class="flex gap-2">
            <a href="{{ route('caja.create', ['tipo' => 'ingreso']) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      bg-gradient-to-r from-emerald-500 to-teal-500 text-white
                      shadow-[0_0_18px_rgba(16,185,129,0.3)] hover:shadow-[0_0_28px_rgba(16,185,129,0.5)]
                      transition-all active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Ingreso
            </a>
            <a href="{{ route('caja.create', ['tipo' => 'egreso']) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      bg-gradient-to-r from-rose-500 to-red-500 text-white
                      shadow-[0_0_18px_rgba(244,63,94,0.3)] hover:shadow-[0_0_28px_rgba(244,63,94,0.5)]
                      transition-all active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                </svg>
                Egreso
            </a>
        </div>
        @endcan
    </div>

    @livewire('caja.caja-list')

</x-app-layout>
