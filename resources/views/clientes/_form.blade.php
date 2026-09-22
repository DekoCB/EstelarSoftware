{{--
    Partial compartido por create.blade.php y edit.blade.php
    Variables esperadas: $cliente (opcional, para edición)
--}}
<div
    x-data="{
        tipoDocumento: '{{ old('tipo_documento', $cliente->tipo_documento ?? 'RUC') }}',
        numeroDocumento: '{{ old('numero_documento', $cliente->numero_documento ?? '') }}',
        razonSocial: '{{ old('razon_social', addslashes($cliente->razon_social ?? '')) }}',
        direccion: '{{ old('direccion', addslashes($cliente->direccion ?? '')) }}',

        buscando: false,
        encontrado: false,
        errorBusqueda: '',

        get longitud() {
            return { RUC: 11, DNI: 8 }[this.tipoDocumento] ?? null;
        },
        get puedeConsultar() {
            return ['RUC','DNI'].includes(this.tipoDocumento) && this.longitud !== null;
        },
        get listaParaBuscar() {
            return this.puedeConsultar && this.numeroDocumento.replace(/\D/g,'').length === this.longitud;
        },

        onNumeroInput() {
            this.encontrado = false;
            this.errorBusqueda = '';
            if (this.listaParaBuscar) this.consultar();
        },

        onTipoChange() {
            this.numeroDocumento = '';
            this.razonSocial = '';
            this.direccion = '';
            this.encontrado = false;
            this.errorBusqueda = '';
        },

        async consultar() {
            this.buscando = true;
            this.errorBusqueda = '';
            this.encontrado = false;

            try {
                const res = await fetch(
                    `{{ url('/api/consulta-documento') }}?tipo=${this.tipoDocumento}&numero=${this.numeroDocumento}`,
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
                        ? 'No se encontró el documento. Puedes rellenar los datos manualmente.'
                        : 'Error al consultar la API. Rellena los datos manualmente.';
                    return;
                }

                // Auto-rellenar campos
                if (data.nombre) {
                    this.razonSocial = data.nombre;
                    this.encontrado = true;
                }

                // Para RUC también traemos dirección si existe
                if (this.tipoDocumento === 'RUC' && data.direccion && data.direccion !== '-') {
                    this.direccion = data.direccion;
                }

            } catch {
                this.errorBusqueda = 'Sin conexión con la API. Rellena los datos manualmente.';
            } finally {
                this.buscando = false;
            }
        }
    }"
    class="space-y-6"
>

    {{-- ── Identificación ─────────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">1</span>
            Identificación
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Tipo documento --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Tipo de documento</label>
                <select
                    name="tipo_documento"
                    x-model="tipoDocumento"
                    @change="onTipoChange()"
                    class="input-dark"
                >
                    <option value="RUC">RUC — Empresa</option>
                    <option value="DNI">DNI — Persona natural</option>
                    <option value="CE">C.E. — Extranjero</option>
                    <option value="PASAPORTE">Pasaporte</option>
                </select>
                @error('tipo_documento')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Número de documento --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">
                    Número de documento
                    <span x-show="longitud" class="text-slate-600 font-normal"
                          x-text="'(' + longitud + ' dígitos)'"></span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="numero_documento"
                        x-model="numeroDocumento"
                        @input.debounce.600ms="onNumeroInput()"
                        :maxlength="longitud ?? 20"
                        class="input-dark font-mono pr-10 @error('numero_documento') error @enderror"
                        placeholder="Ej. 74093841"
                        value="{{ old('numero_documento', $cliente->numero_documento ?? '') }}"
                    >
                    {{-- Indicador de estado de búsqueda --}}
                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        {{-- Spinner --}}
                        <svg x-show="buscando" class="w-4 h-4 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{-- Check verde --}}
                        <svg x-show="encontrado && !buscando" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        {{-- Error --}}
                        <svg x-show="errorBusqueda && !buscando" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </div>
                </div>

                {{-- Mensajes bajo el campo --}}
                <p x-show="encontrado && !buscando"
                   class="text-emerald-400 text-xs mt-1.5 flex items-center gap-1">
                    <span x-text="tipoDocumento === 'RUC' ? '✓ RUC encontrado en SUNAT' : '✓ DNI encontrado'"></span>
                </p>
                <p x-show="errorBusqueda && !buscando"
                   x-text="errorBusqueda"
                   class="text-amber-400 text-xs mt-1.5">
                </p>
                <p x-show="!puedeConsultar && tipoDocumento"
                   class="text-slate-600 text-xs mt-1.5">
                    Rellena los datos manualmente
                </p>

                @error('numero_documento')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estado</label>
                <select name="estado" class="input-dark">
                    @foreach(['prospecto' => 'Prospecto', 'activo' => 'Activo', 'inactivo' => 'Inactivo', 'bloqueado' => 'Bloqueado'] as $val => $label)
                    <option value="{{ $val }}" {{ old('estado', $cliente->estado ?? 'prospecto') === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('estado')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── Datos del cliente ───────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">2</span>
                Datos del cliente
            </h3>
            <span x-show="encontrado"
                  class="text-xs text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                Completado desde la API
            </span>
            <span x-show="errorBusqueda || !puedeConsultar"
                  class="text-xs text-slate-500 bg-slate-800 px-2.5 py-1 rounded-lg">
                Relleno manual
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Razón social --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">
                    Razón social / Nombre completo <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    name="razon_social"
                    x-model="razonSocial"
                    class="input-dark @error('razon_social') error @enderror"
                    placeholder="Se autocompleta al ingresar el documento, o escríbelo manualmente"
                    value="{{ old('razon_social', $cliente->razon_social ?? '') }}"
                >
                @error('razon_social')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nombre comercial --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Nombre comercial</label>
                <input
                    type="text"
                    name="nombre_comercial"
                    class="input-dark"
                    placeholder="Nombre de fantasía (opcional)"
                    value="{{ old('nombre_comercial', $cliente->nombre_comercial ?? '') }}"
                >
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Correo electrónico</label>
                <input
                    type="email"
                    name="email"
                    class="input-dark @error('email') error @enderror"
                    placeholder="contacto@empresa.com"
                    value="{{ old('email', $cliente->email ?? '') }}"
                >
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Teléfono</label>
                <input
                    type="text"
                    name="telefono"
                    class="input-dark font-mono"
                    placeholder="+51 999 999 999"
                    value="{{ old('telefono', $cliente->telefono ?? '') }}"
                >
            </div>

            {{-- Dirección --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Dirección</label>
                <input
                    type="text"
                    name="direccion"
                    x-model="direccion"
                    class="input-dark"
                    placeholder="Se autocompleta con RUC, o escríbela manualmente"
                    value="{{ old('direccion', $cliente->direccion ?? '') }}"
                >
            </div>

        </div>
    </div>

</div>
