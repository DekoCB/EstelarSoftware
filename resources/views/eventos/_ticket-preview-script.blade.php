{{--
    Alpine factory para la vista previa en vivo del ticket, usada en _form.blade.php
    (crear/editar evento). Lee directamente los campos del formulario mientras se escriben.
--}}
@once
@push('scripts')
<script>
    function eventoPreview(nombreInit, descripcionInit, lugarInit, fechaInicioInit, horaInicioInit, imagenInit) {
        return {
            pvNombre: nombreInit || '',
            pvDescripcion: descripcionInit || '',
            pvLugar: lugarInit || '',
            pvFechaInicio: fechaInicioInit || '',
            pvHoraInicio: horaInicioInit || '',
            pvImagen: imagenInit || null,
            pvImagenEliminar: false,

            get pvFechaFormateada() {
                if (!this.pvFechaInicio) return null;
                const [y, m, d] = this.pvFechaInicio.split('-');
                return (y && m && d) ? `${d}/${m}/${y}` : null;
            },

            get pvHoraFormateada() {
                if (!this.pvHoraInicio) return null;
                let [h, min] = this.pvHoraInicio.split(':').map(Number);
                if (isNaN(h) || isNaN(min)) return null;
                const sufijo = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                if (h === 0) h = 12;
                return `${h}:${String(min).padStart(2, '0')} ${sufijo}`;
            },

            onPvImagenChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.pvImagen = URL.createObjectURL(file);
                    this.pvImagenEliminar = false;
                }
            },

            onPvImagenQuitar() {
                this.pvImagenEliminar = true;
                this.pvImagen = null;
            },

            onPvImagenCancelarQuitar() {
                this.pvImagenEliminar = false;
            },
        };
    }
</script>
@endpush
@endonce
