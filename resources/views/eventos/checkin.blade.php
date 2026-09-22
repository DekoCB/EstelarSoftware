<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('eventos.index') }}" class="text-slate-600 hover:text-slate-400 transition-colors font-mono">Eventos</a>
            <span class="text-slate-700">/</span>
            <a href="{{ route('eventos.show', $evento) }}" class="text-slate-600 hover:text-slate-400 transition-colors truncate max-w-[200px]">{{ $evento->nombre }}</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Check-in</span>
        </div>
    </x-slot>

    @vite(['resources/js/checkin.js'])

    <div class="max-w-md mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-white">Control de ingreso</h1>
            <p class="text-sm text-slate-500 mt-1">Escanea el QR del ticket de cada asistente</p>
        </div>

        <div id="checkin-app"
             data-scan-url="{{ route('eventos.checkin.scan', $evento) }}"
             data-csrf="{{ csrf_token() }}"
             class="space-y-4">

            <div id="qr-reader" class="rounded-2xl overflow-hidden border border-slate-800/60 bg-slate-900"></div>

            <div id="qr-result" class="rounded-2xl border border-slate-800/60 bg-slate-900 p-5 text-center">
                <p class="text-sm text-slate-500">Apunta la cámara al código QR del ticket</p>
            </div>
        </div>
    </div>

</x-app-layout>
