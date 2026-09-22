<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-white">Configurar Ubicación de Oficina</h1>
            <p class="text-sm text-slate-400 mt-0.5">Define el punto GPS y el radio de verificación de asistencia</p>
        </div>
    </x-slot>

    <div class="max-w-2xl" x-data="geoConfig()">

        <div class="card p-6 space-y-5">

            {{-- Mapa Leaflet --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Haz clic en el mapa para fijar la ubicación de la oficina
                </label>
                <div id="mapa" class="w-full h-72 rounded-xl overflow-hidden border border-slate-700"></div>
                <p class="text-xs text-slate-500 mt-1.5">También puedes arrastrar el marcador azul para ajustar la posición exacta.</p>
            </div>

            {{-- Coordenadas --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Latitud</label>
                    <input type="text" x-model="lat" readonly
                           class="input-field w-full font-mono text-sm bg-slate-800/50 cursor-not-allowed"
                           placeholder="Haz clic en el mapa">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Longitud</label>
                    <input type="text" x-model="lng" readonly
                           class="input-field w-full font-mono text-sm bg-slate-800/50 cursor-not-allowed"
                           placeholder="Haz clic en el mapa">
                </div>
            </div>

            {{-- Radio --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-3">
                    Radio permitido:
                    <span class="text-sky-400 font-bold" x-text="radio + ' metros'"></span>
                </label>
                <input type="range" x-model="radio" min="5" max="500" step="5"
                       @input="actualizarCirculo()"
                       class="w-full accent-sky-500">
                <div class="flex justify-between text-xs text-slate-600 mt-1">
                    <span>5m</span>
                    <span>250m</span>
                    <span>500m</span>
                </div>
            </div>

            {{-- Botón usar mi ubicación --}}
            <button type="button" @click="usarMiUbicacion()"
                    class="w-full py-2.5 rounded-xl text-sm font-semibold border border-slate-600
                           text-slate-300 hover:bg-slate-800 transition-colors duration-150">
                <svg class="w-4 h-4 inline mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                </svg>
                Usar mi ubicación actual
            </button>

            {{-- Guardar --}}
            <form method="POST" action="{{ route('asistencias.config.save') }}" @submit.prevent="guardar($event)">
                @csrf
                <input type="hidden" name="asistencia_latitud"      x-model="lat">
                <input type="hidden" name="asistencia_longitud"      x-model="lng">
                <input type="hidden" name="asistencia_radio_metros"  x-model="radio">
                <button type="submit"
                        :disabled="!lat || !lng"
                        class="w-full py-3 rounded-xl text-sm font-bold bg-sky-500 hover:bg-sky-400 text-white
                               transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                    Guardar configuración
                </button>
            </form>

            @if($config->tieneGeoConfigurado())
            <div class="border-t border-slate-800/60 pt-4">
                <p class="text-xs text-slate-500 mb-1">Configuración actual</p>
                <p class="text-sm text-slate-300 font-mono">
                    {{ $config->asistencia_latitud }}, {{ $config->asistencia_longitud }}
                    — Radio: {{ $config->radioMetros() }}m
                </p>
            </div>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('asistencias.index') }}" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">
                ← Volver a asistencias
            </a>
        </div>
    </div>

    @push('scripts')
    @vite(['resources/js/leaflet-setup.js'])
    <script>
    function geoConfig() {
        return {
            lat: '{{ $config->asistencia_latitud ?? "" }}',
            lng: '{{ $config->asistencia_longitud ?? "" }}',
            radio: {{ $config->asistencia_radio_metros ?? 15 }},
            mapa: null,
            marcador: null,
            circulo: null,

            init() {
                const lat = parseFloat(this.lat) || -12.0640;
                const lng = parseFloat(this.lng) || -75.2049;

                this.mapa = L.map('mapa').setView([lat, lng], 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(this.mapa);

                if (this.lat && this.lng) {
                    this.ponerMarcador(parseFloat(this.lat), parseFloat(this.lng));
                }

                this.mapa.on('click', (e) => {
                    this.lat = e.latlng.lat.toFixed(7);
                    this.lng = e.latlng.lng.toFixed(7);
                    this.ponerMarcador(e.latlng.lat, e.latlng.lng);
                });
            },

            ponerMarcador(lat, lng) {
                if (this.marcador) this.mapa.removeLayer(this.marcador);
                if (this.circulo)  this.mapa.removeLayer(this.circulo);

                this.marcador = L.marker([lat, lng], {draggable: true}).addTo(this.mapa);
                this.circulo  = L.circle([lat, lng], {
                    radius: parseInt(this.radio),
                    color: '#38bdf8', fillColor: '#38bdf8', fillOpacity: 0.15
                }).addTo(this.mapa);

                this.marcador.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.lat = pos.lat.toFixed(7);
                    this.lng = pos.lng.toFixed(7);
                    this.actualizarCirculo();
                });
            },

            actualizarCirculo() {
                if (this.circulo) {
                    this.circulo.setRadius(parseInt(this.radio));
                }
            },

            usarMiUbicacion() {
                if (!navigator.geolocation) return alert('Geolocalización no soportada.');
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.lat = pos.coords.latitude.toFixed(7);
                    this.lng = pos.coords.longitude.toFixed(7);
                    this.ponerMarcador(pos.coords.latitude, pos.coords.longitude);
                    this.mapa.setView([pos.coords.latitude, pos.coords.longitude], 18);
                }, () => alert('No se pudo obtener tu ubicación.'), {enableHighAccuracy: true});
            },

            guardar(event) {
                if (!this.lat || !this.lng) {
                    alert('Primero selecciona una ubicación en el mapa.');
                    return;
                }
                event.target.submit();
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
