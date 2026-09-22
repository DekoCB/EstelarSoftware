{{--
    Bloque de mapa reutilizable. Debe usarse DENTRO de un elemento con
    x-data="ubicacionPicker(latInicial, lngInicial)" (ver _leaflet-assets.blade.php).
    Variables esperadas: $mapId (string único por instancia), $incluirPrecision (bool, opcional)
--}}
<div>
    <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-medium text-slate-400">Ubicación precisa</label>
        <button type="button"
                @click="obtenerGps()"
                :disabled="buscandoGps"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-400
                       hover:text-sky-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <svg x-show="!buscandoGps" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
            </svg>
            <svg x-show="buscandoGps" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="buscandoGps ? 'Obteniendo GPS...' : 'Usar mi ubicación actual'"></span>
        </button>
    </div>

    <div
        id="{{ $mapId }}"
        x-init="initMapa('{{ $mapId }}')"
        class="w-full h-56 rounded-xl overflow-hidden border border-slate-700/60 bg-slate-800"
    ></div>

    <div class="flex items-center justify-between mt-2">
        <p class="text-[11px] text-slate-500">
            <span x-show="lat && lng">
                <span class="font-mono" x-text="Number(lat).toFixed(6) + ', ' + Number(lng).toFixed(6)"></span>
                <span x-show="precision"> · precisión ±<span x-text="precision"></span>m</span>
            </span>
            <span x-show="!lat || !lng">Toca el mapa, arrastra el pin, o usa tu ubicación actual</span>
        </p>
    </div>
    <p x-show="errorGps" x-text="errorGps" class="text-amber-400 text-xs mt-1"></p>

    <input type="hidden" name="latitud" x-model="lat">
    <input type="hidden" name="longitud" x-model="lng">
    @isset($incluirPrecision)
    @if($incluirPrecision)
    <input type="hidden" name="precision_metros" x-model="precision">
    @endif
    @endisset
</div>
