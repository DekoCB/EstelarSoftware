<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 font-mono">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="text-sm font-semibold text-white">Facturación SUNAT</h1>
        </div>
    </x-slot>

    {{-- Alerta API no configurada --}}
    @if(!$apiOk)
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl px-5 py-4 mb-5 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-amber-400">API SUNAT no configurada</p>
            <p class="text-xs text-amber-300/70 mt-1">
                Para emitir comprobantes debes configurar <code class="font-mono bg-amber-500/10 px-1 rounded">SUNAT_API_URL</code> y
                <code class="font-mono bg-amber-500/10 px-1 rounded">SUNAT_API_TOKEN</code> en el <code class="font-mono">.env</code>.
                La API de facturación debe estar corriendo en el puerto configurado.
            </p>
            <p class="text-xs text-amber-300/50 mt-1.5">
                Corre la API: <code class="font-mono bg-slate-800 px-1.5 py-0.5 rounded">php artisan serve --port=8001</code>
                (desde la carpeta API-GO-Facturacion-Electronica-sunat-peru-main)
            </p>
        </div>
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Comprobantes</h2>
            <p class="text-sm text-slate-500 mt-0.5">Facturas y boletas electrónicas SUNAT</p>
        </div>
        @can('facturacion.emitir')
        <a href="{{ route('facturacion.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-gradient-to-r from-sky-500 to-cyan-500 text-white
                  shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                  transition-all active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo comprobante
        </a>
        @endcan
    </div>

    @livewire('facturacion.facturas-list')

</x-app-layout>
