{{--
    Partial compartido por create.blade.php y edit.blade.php
    Variables esperadas: $evento (opcional, para edición), $usuarios
--}}
@include('eventos._leaflet-assets')
@include('eventos._ticket-preview-script')

<div
    x-data="{
        ...ubicacionPicker({{ $evento->latitud ?? 'null' }}, {{ $evento->longitud ?? 'null' }}),
        ...eventoPreview(
            '{{ addslashes(old('nombre', $evento->nombre ?? '')) }}',
            '{{ addslashes(old('descripcion', $evento->descripcion ?? '')) }}',
            '{{ addslashes(old('lugar', $evento->lugar ?? '')) }}',
            '{{ old('fecha_inicio', isset($evento) ? $evento->fecha_inicio->format('Y-m-d') : '') }}',
            '{{ old('hora_inicio', isset($evento) && $evento->hora_inicio ? substr($evento->hora_inicio, 0, 5) : '') }}',
            {{ isset($evento) && $evento->imagenUrl() ? "'".$evento->imagenUrl()."'" : 'null' }}
        )
    }"
    class="space-y-6"
>

    {{-- ── Datos del evento ───────────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">1</span>
            Datos del evento
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">
                    Nombre del evento <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    name="nombre"
                    x-model="pvNombre"
                    class="input-dark @error('nombre') error @enderror"
                    placeholder="Ej. Feria Tecnológica Huancayo 2026"
                >
                @error('nombre')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Descripción</label>
                <textarea
                    name="descripcion"
                    rows="2"
                    x-model="pvDescripcion"
                    class="input-dark"
                    placeholder="Objetivo de la campaña o feria (opcional)"
                ></textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Imagen del evento</label>
                <p class="text-slate-600 text-xs mb-2">Imagen vertical para el ticket que descargan los asistentes. Recomendado 800×1200px.</p>

                <div class="bg-slate-800/40 border border-slate-700/30 rounded-xl flex items-center justify-center overflow-hidden mb-2" style="min-height:110px">
                    <img x-show="pvImagen" :src="pvImagen" class="max-h-32 max-w-full object-contain p-2">
                    <div x-show="!pvImagen" class="flex flex-col items-center gap-1 py-6">
                        <svg class="w-7 h-7 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/>
                        </svg>
                        <p class="text-xs text-slate-600">Sin imagen</p>
                    </div>
                </div>

                <input type="file" id="input_imagen" name="imagen" accept="image/*" class="hidden" @change="onPvImagenChange($event)">
                @if(isset($evento))
                <input type="hidden" name="delete_imagen" :value="pvImagenEliminar ? '1' : '0'">
                @endif

                <div class="flex gap-2">
                    <label for="input_imagen"
                           class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                                  text-xs font-medium cursor-pointer text-slate-300
                                  bg-slate-800 border border-slate-700/60
                                  hover:border-sky-500/30 hover:text-sky-400 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                        </svg>
                        Subir
                    </label>
                    <button type="button" x-show="pvImagen && !pvImagenEliminar" @click="onPvImagenQuitar()"
                            class="px-3 py-2 rounded-lg text-xs text-red-400 bg-slate-800
                                   border border-slate-700/60 hover:bg-red-500/10 transition-all">
                        Quitar
                    </button>
                    <button type="button" x-show="pvImagenEliminar" @click="onPvImagenCancelarQuitar()"
                            class="px-3 py-2 rounded-lg text-xs text-slate-400 bg-slate-800
                                   border border-slate-700/60 transition-all">
                        Cancelar
                    </button>
                </div>
                @error('imagen')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha de inicio <span class="text-red-400">*</span></label>
                <input
                    type="date"
                    name="fecha_inicio"
                    x-model="pvFechaInicio"
                    class="input-dark @error('fecha_inicio') error @enderror"
                >
                @error('fecha_inicio')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Fecha de fin</label>
                <input
                    type="date"
                    name="fecha_fin"
                    class="input-dark @error('fecha_fin') error @enderror"
                    value="{{ old('fecha_fin', isset($evento) && $evento->fecha_fin ? $evento->fecha_fin->format('Y-m-d') : '') }}"
                >
                @error('fecha_fin')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Hora</label>
                <input
                    type="time"
                    name="hora_inicio"
                    x-model="pvHoraInicio"
                    class="input-dark @error('hora_inicio') error @enderror"
                >
                @error('hora_inicio')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-slate-600 text-xs mt-1">Se muestra en el ticket de los asistentes</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estado</label>
                <select name="estado" class="input-dark">
                    @foreach(\App\Models\Event::ESTADOS as $val)
                    <option value="{{ $val }}" {{ old('estado', $evento->estado ?? 'planificado') === $val ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $val)) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Responsable</label>
                <select name="responsable_id" class="input-dark">
                    <option value="">— Sin asignar —</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ (string) old('responsable_id', $evento->responsable_id ?? '') === (string) $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    {{-- ── Ubicación del evento ───────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">2</span>
            Ubicación
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Lugar / Local</label>
                <input
                    type="text"
                    name="lugar"
                    x-model="pvLugar"
                    class="input-dark"
                    placeholder="Ej. Cámara de Comercio de Huancayo"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Dirección</label>
                <input
                    type="text"
                    name="direccion"
                    class="input-dark"
                    placeholder="Referencia de la dirección"
                    value="{{ old('direccion', $evento->direccion ?? '') }}"
                >
            </div>
        </div>

        @include('eventos._mapa-picker', ['mapId' => 'mapa-evento'])
    </div>

    {{-- ── Vista previa del ticket ──────────────────────────────────── --}}
    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-sky-500/20 flex items-center justify-center text-sky-400 text-xs font-bold">3</span>
            Vista previa del ticket
        </h3>
        <p class="text-slate-600 text-xs mb-5 ml-7">Así se verá la entrada que reciban los asistentes. Los datos de "Juan Pérez" son de ejemplo.</p>

        <div class="max-w-lg mx-auto relative bg-slate-950 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col sm:flex-row shadow-[0_0_40px_rgba(0,0,0,0.4)]">

            <div class="relative h-32 sm:h-auto sm:w-32 flex-shrink-0 overflow-hidden">
                <template x-if="pvImagen">
                    <img :src="pvImagen" alt="" class="w-full h-full object-cover">
                </template>
                <template x-if="!pvImagen">
                    <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-900 to-sky-950"></div>
                </template>
            </div>

            <div class="hidden sm:block w-4 h-4 rounded-full bg-slate-900 absolute left-32 -top-2 -translate-x-1/2 z-10"></div>
            <div class="hidden sm:block w-4 h-4 rounded-full bg-slate-900 absolute left-32 -bottom-2 -translate-x-1/2 z-10"></div>

            <div class="flex-1 bg-white border-t sm:border-t-0 sm:border-l border-dashed border-slate-300 flex flex-col p-4 gap-3">
                <div>
                    <p class="text-[9px] font-semibold text-sky-600 uppercase tracking-widest">Entrada general</p>
                    <p class="text-sm font-bold text-slate-900 leading-snug" x-text="pvNombre || 'Nombre del evento'"></p>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-show="pvDescripcion" x-text="pvDescripcion"></p>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-2 text-[11px]">
                    <div x-show="pvFechaFormateada">
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Fecha</p>
                        <p class="text-slate-800 font-semibold font-mono" x-text="pvFechaFormateada"></p>
                    </div>
                    <div x-show="pvHoraFormateada">
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Hora</p>
                        <p class="text-slate-800 font-semibold font-mono" x-text="pvHoraFormateada"></p>
                    </div>
                    <div class="col-span-2" x-show="pvLugar">
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Lugar</p>
                        <p class="text-slate-800 font-semibold" x-text="pvLugar"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Asistente</p>
                        <p class="text-slate-800 font-semibold">Juan Pérez</p>
                        <p class="text-slate-500">Empresa Ejemplo SAC</p>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-200 pt-3 flex items-center gap-3">
                    <div class="bg-white p-0.5 flex-shrink-0 opacity-90">
                        {!! \App\Support\QrGenerator::svg('vista-previa', 70) !!}
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">N.º de entrada</p>
                        <p class="text-xs text-slate-700 font-mono font-semibold tracking-wider">EV0-000000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
