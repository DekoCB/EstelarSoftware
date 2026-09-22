{{--
    Assets compartidos de Leaflet + factory Alpine para captura de ubicación precisa
    (GPS del navegador + mapa interactivo con pin ajustable manualmente).
    Se incluye desde _form.blade.php (evento) y _lead_form.blade.php (lead).
    @once evita duplicar el <link>/<script> aunque ambos partials aparezcan en la misma página.
--}}
@once
@push('scripts')
@vite(['resources/js/leaflet-setup.js'])
<script>
    function ubicacionPicker(latInit, lngInit) {
        return {
            lat: latInit || null,
            lng: lngInit || null,
            precision: null,
            buscandoGps: false,
            errorGps: '',
            map: null,
            marker: null,

            initMapa(mapId) {
                this.$nextTick(() => {
                    const startLat = this.lat ?? -12.0464;
                    const startLng = this.lng ?? -77.0428;

                    this.map = L.map(mapId).setView([startLat, startLng], this.lat ? 16 : 5);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        maxZoom: 19,
                    }).addTo(this.map);

                    if (this.lat && this.lng) {
                        this.colocarMarcador(this.lat, this.lng);
                    }

                    this.map.on('click', (e) => this.setUbicacion(e.latlng.lat, e.latlng.lng));

                    setTimeout(() => this.map.invalidateSize(), 150);
                });
            },

            colocarMarcador(lat, lng) {
                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                    return;
                }
                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                this.marker.on('dragend', () => {
                    const pos = this.marker.getLatLng();
                    this.lat = pos.lat;
                    this.lng = pos.lng;
                    this.precision = null;
                });
            },

            setUbicacion(lat, lng) {
                this.lat = lat;
                this.lng = lng;
                this.colocarMarcador(lat, lng);
                this.map.setView([lat, lng], Math.max(this.map.getZoom(), 16));
            },

            obtenerGps() {
                if (!navigator.geolocation) {
                    this.errorGps = 'Tu navegador no soporta geolocalización.';
                    return;
                }
                this.buscandoGps = true;
                this.errorGps = '';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.precision = Math.round(pos.coords.accuracy);
                        this.setUbicacion(pos.coords.latitude, pos.coords.longitude);
                        this.buscandoGps = false;
                    },
                    (err) => {
                        this.errorGps = err.code === 1
                            ? 'Debes permitir el acceso a tu ubicación para capturarla automáticamente.'
                            : 'No se pudo obtener tu ubicación GPS. Ajusta el pin manualmente en el mapa.';
                        this.buscandoGps = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            },
        };
    }
</script>
@endpush
@endonce
