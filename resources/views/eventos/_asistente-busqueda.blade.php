{{--
    Alpine factory compartida para autocompletar nombres/empresa buscando por DNI/RUC.
    Recibe la URL del endpoint porque se usa en dos contextos: el registro manual del
    staff (autenticado, /api/consulta-documento) y la inscripción pública sin login
    (/api/consulta-documento-publico, limitada por throttle).
--}}
@once
@push('scripts')
<script>
    function asistenteBusqueda(endpointUrl, tipoInit, numeroInit, nombresInit, empresaInit, direccionInit) {
        return {
            tipoDocumento: tipoInit || 'DNI',
            numeroDocumento: numeroInit || '',
            nombres: nombresInit || '',
            empresa: empresaInit || '',
            direccion: direccionInit || '',

            buscando: false,
            encontrado: false,
            errorBusqueda: '',

            get longitud() {
                return { RUC: 11, DNI: 8 }[this.tipoDocumento] ?? null;
            },
            get puedeConsultar() {
                return ['RUC', 'DNI'].includes(this.tipoDocumento) && this.longitud !== null;
            },
            get listaParaBuscar() {
                return this.puedeConsultar && this.numeroDocumento.replace(/\D/g, '').length === this.longitud;
            },

            onNumeroInput() {
                this.encontrado = false;
                this.errorBusqueda = '';
                if (this.listaParaBuscar) this.consultar();
            },

            onTipoChange() {
                this.numeroDocumento = '';
                this.encontrado = false;
                this.errorBusqueda = '';
            },

            async consultar() {
                this.buscando = true;
                this.errorBusqueda = '';
                this.encontrado = false;

                try {
                    const res = await fetch(
                        `${endpointUrl}?tipo=${this.tipoDocumento}&numero=${this.numeroDocumento}`,
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            }
                        }
                    );

                    const data = await res.json();

                    if (!res.ok) {
                        this.errorBusqueda = res.status === 404
                            ? 'No se encontró el documento. Puedes completar los datos manualmente.'
                            : res.status === 429
                                ? 'Demasiadas búsquedas seguidas, espera un momento.'
                                : 'Error al consultar la API. Completa los datos manualmente.';
                        return;
                    }

                    if (data.nombre) {
                        if (this.tipoDocumento === 'RUC') {
                            this.empresa = data.nombre;
                        } else {
                            this.nombres = data.nombre;
                        }
                        this.encontrado = true;
                    }

                    if (this.tipoDocumento === 'RUC' && data.direccion && data.direccion !== '-') {
                        this.direccion = data.direccion;
                    }
                } catch {
                    this.errorBusqueda = 'Sin conexión con la API. Completa los datos manualmente.';
                } finally {
                    this.buscando = false;
                }
            }
        };
    }
</script>
@endpush
@endonce
