<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-white">Marcar Asistencia</h1>
            <p class="text-sm text-slate-400 mt-0.5">{{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}</p>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto" x-data="asistencia()">

        {{-- Estado del día --}}
        <div class="card p-6 mb-4">
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm font-semibold text-slate-300">Estado de hoy</p>
                <span class="text-2xl font-mono font-bold text-white" x-text="horaActual"></span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- Entrada --}}
                <div class="rounded-xl p-4 {{ $entrada ? 'bg-emerald-500/10 border border-emerald-500/30' : ($justEntrada ? 'bg-amber-500/10 border border-amber-500/30' : 'bg-slate-800/50 border border-slate-700/50') }}">
                    <p class="text-xs text-slate-400 mb-1">Entrada</p>
                    @if($entrada)
                        <p class="text-lg font-bold font-mono text-emerald-400">{{ substr($entrada->hora, 0, 5) }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ round($entrada->distancia_metros) }}m de distancia</p>
                    @elseif($justEntrada)
                        <p class="text-sm font-bold text-amber-400">Justificado</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $justEntrada->estado === 'aprobado' ? 'Aprobado' : 'Pendiente de revisión' }}</p>
                    @else
                        <p class="text-slate-500 text-sm">—</p>
                    @endif
                </div>
                {{-- Salida --}}
                <div class="rounded-xl p-4 {{ $salida ? 'bg-sky-500/10 border border-sky-500/30' : ($justSalida ? 'bg-amber-500/10 border border-amber-500/30' : 'bg-slate-800/50 border border-slate-700/50') }}">
                    <p class="text-xs text-slate-400 mb-1">Salida</p>
                    @if($salida)
                        <p class="text-lg font-bold font-mono text-sky-400">{{ substr($salida->hora, 0, 5) }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ round($salida->distancia_metros) }}m de distancia</p>
                    @elseif($justSalida)
                        <p class="text-sm font-bold text-amber-400">Justificado</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $justSalida->estado === 'aprobado' ? 'Aprobado' : 'Pendiente de revisión' }}</p>
                    @else
                        <p class="text-slate-500 text-sm">—</p>
                    @endif
                </div>
            </div>
        </div>

        @php
            $config = \App\Models\EmpresaConfig::config();
            $tieneConfig = $config->tieneGeoConfigurado();
            $entradaHecha = $entrada || $justEntrada;
            $salidaHecha = $salida || $justSalida;
            $yaCompleto = $entradaHecha && $salidaHecha;
            $proximoTipo = $entradaHecha ? 'salida' : 'entrada';
        @endphp

        @if(!$tieneConfig)
            <div class="card p-6 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                </svg>
                <p class="text-slate-300 font-medium">Ubicación de oficina no configurada</p>
                <p class="text-slate-500 text-sm mt-1">El administrador debe configurar la ubicación primero.</p>
            </div>

        @elseif($yaCompleto)
            <div class="card p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500/15 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </div>
                <p class="text-white font-semibold text-lg">¡Asistencia completa!</p>
                <p class="text-slate-400 text-sm mt-1">Registraste entrada y salida hoy.</p>
            </div>

        @else
            {{-- Botón de registro --}}
            <div class="card p-6 text-center space-y-4">
                <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center
                            {{ $proximoTipo === 'entrada' ? 'bg-emerald-500/15' : 'bg-sky-500/15' }}">
                    <svg class="w-9 h-9 {{ $proximoTipo === 'entrada' ? 'text-emerald-400' : 'text-sky-400' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                    </svg>
                </div>

                {{-- Estado de ubicación --}}
                <div>
                    <p x-show="estado === 'idle'" class="text-slate-400 text-sm">Toca el botón para obtener tu ubicación</p>
                    <p x-show="estado === 'buscando'" class="text-amber-400 text-sm animate-pulse">Obteniendo ubicación GPS...</p>
                    <p x-show="estado === 'dentro'" class="text-emerald-400 text-sm font-semibold">
                        ✓ Estás a <span x-text="distancia"></span>m · Dentro del rango
                    </p>
                    <p x-show="estado === 'fuera'" class="text-red-400 text-sm font-semibold">
                        ✗ Estás a <span x-text="distancia"></span>m de la oficina (radio: {{ $config->radioMetros() }}m)
                    </p>
                    <p x-show="estado === 'error'" class="text-red-400 text-sm" x-text="mensajeError"></p>
                </div>

                {{-- Form oculto --}}
                <form method="POST" action="{{ route('asistencias.registrar') }}" id="formAsistencia">
                    @csrf
                    <input type="hidden" name="latitud"  x-model="lat">
                    <input type="hidden" name="longitud" x-model="lng">
                </form>

                <button @click="obtenerUbicacion()"
                        :disabled="estado === 'buscando'"
                        class="w-full py-4 rounded-2xl text-base font-bold transition-all duration-200
                               {{ $proximoTipo === 'entrada'
                                   ? 'bg-emerald-500 hover:bg-emerald-400 text-white'
                                   : 'bg-sky-500 hover:bg-sky-400 text-white' }}
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="estado !== 'buscando'">
                        Registrar {{ ucfirst($proximoTipo) }}
                    </span>
                    <span x-show="estado === 'buscando'">Detectando ubicación...</span>
                </button>

                <p class="text-xs text-slate-600">
                    Radio permitido: {{ $config->radioMetros() }} metros desde la oficina
                </p>
            </div>
        @endif

        <div class="mt-4 text-center">
            <a href="{{ route('asistencias.index') }}" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">
                Ver mi reporte mensual →
            </a>
        </div>
    </div>

    @push('scripts')
    <script>
    function asistencia() {
        return {
            estado: 'idle',
            lat: '', lng: '',
            distancia: 0,
            mensajeError: '',
            horaActual: '',

            init() {
                this.actualizarHora();
                setInterval(() => this.actualizarHora(), 1000);
            },

            actualizarHora() {
                const now = new Date();
                this.horaActual = now.toLocaleTimeString('es-PE', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
            },

            haversine(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2)**2
                        + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            },

            obtenerUbicacion() {
                if (!navigator.geolocation) {
                    this.estado = 'error';
                    this.mensajeError = 'Tu navegador no soporta geolocalización.';
                    return;
                }
                this.estado = 'buscando';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.lat = pos.coords.latitude;
                        this.lng = pos.coords.longitude;
                        const dist = this.haversine(
                            {{ $config->asistencia_latitud }},
                            {{ $config->asistencia_longitud }},
                            this.lat, this.lng
                        );
                        this.distancia = Math.round(dist);
                        if (dist <= {{ $config->radioMetros() }}) {
                            this.estado = 'dentro';
                            setTimeout(() => document.getElementById('formAsistencia').submit(), 600);
                        } else {
                            this.estado = 'fuera';
                        }
                    },
                    (err) => {
                        this.estado = 'error';
                        this.mensajeError = err.code === 1
                            ? 'Debes permitir el acceso a tu ubicación.'
                            : 'No se pudo obtener tu ubicación. Intenta de nuevo.';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
