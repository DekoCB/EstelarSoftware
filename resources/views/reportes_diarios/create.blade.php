<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Nuevo Reporte Diario</h1>
                <p class="text-sm text-slate-400 mt-0.5">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>
            <a href="{{ route('reportes_diarios.index') }}"
               class="text-sm text-slate-400 hover:text-white transition-colors">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div x-data="reporteForm()" class="max-w-4xl">
        <form method="POST" action="{{ route('reportes_diarios.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- ── 1. DATOS DEL COLABORADOR ──────────────────────────────── --}}
            <div class="card p-6 mb-5">
                <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-5 pb-3 border-b border-slate-800">
                    1. Datos del colaborador
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Área --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Área <span class="text-red-400">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="area" value="desarrollo" x-model="area" class="sr-only" required>
                                <div :class="area === 'desarrollo'
                                        ? 'border-sky-500 bg-sky-500/10 text-sky-400'
                                        : 'border-slate-700 text-slate-400 hover:border-slate-600'"
                                     class="flex items-center gap-2 p-3 rounded-xl border transition-all duration-150">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/>
                                    </svg>
                                    <span class="text-sm font-semibold">Desarrollo</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="area" value="ventas" x-model="area" class="sr-only">
                                <div :class="area === 'ventas'
                                        ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400'
                                        : 'border-slate-700 text-slate-400 hover:border-slate-600'"
                                     class="flex items-center gap-2 p-3 rounded-xl border transition-all duration-150">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                                    </svg>
                                    <span class="text-sm font-semibold">Ventas</span>
                                </div>
                            </label>
                        </div>
                        @error('area')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-slate-300 mb-1.5">
                            Fecha del reporte <span class="text-red-400">*</span>
                        </label>
                        <input id="fecha" name="fecha" type="date"
                               value="{{ old('fecha', today()->toDateString()) }}"
                               class="input-field w-full" required>
                        @error('fecha')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Proyectos asignados --}}
                    <div>
                        <label for="proyectos_asignados" class="block text-sm font-medium text-slate-300 mb-1.5">
                            Proyectos asignados
                        </label>
                        <input id="proyectos_asignados" name="proyectos_asignados" type="text"
                               value="{{ old('proyectos_asignados') }}"
                               placeholder="Ej: EstelarERP, Sistema Nueva-era"
                               class="input-field w-full">
                        @error('proyectos_asignados')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Sprint / Iteración --}}
                    <div>
                        <label for="sprint_iteracion" class="block text-sm font-medium text-slate-300 mb-1.5">
                            Sprint / Iteración
                        </label>
                        <input id="sprint_iteracion" name="sprint_iteracion" type="text"
                               value="{{ old('sprint_iteracion') }}"
                               placeholder="Ej: Sprint 3, Iteración de corrección"
                               class="input-field w-full">
                        @error('sprint_iteracion')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Módulo / Componente --}}
                    <div>
                        <label for="modulo_componente" class="block text-sm font-medium text-slate-300 mb-1.5">
                            Módulo / Componente
                        </label>
                        <input id="modulo_componente" name="modulo_componente" type="text"
                               value="{{ old('modulo_componente') }}"
                               placeholder="Ej: Cotización, Facturación, Login"
                               class="input-field w-full">
                        @error('modulo_componente')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Horas trabajadas --}}
                    <div>
                        <label for="horas_trabajadas" class="block text-sm font-medium text-slate-300 mb-1.5">
                            Horas trabajadas <span class="text-red-400">*</span>
                        </label>
                        <input id="horas_trabajadas" name="horas_trabajadas" type="number"
                               step="0.5" min="0.5" max="24"
                               value="{{ old('horas_trabajadas', 8) }}"
                               class="input-field w-full" required>
                        @error('horas_trabajadas')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ── 2. TAREAS DEL DÍA ─────────────────────────────────────── --}}
            <div class="card p-6 mb-5">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">
                        2. Tareas del día
                    </h2>
                    <button type="button" @click="agregarTarea"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg
                                   bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 border border-sky-500/30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar tarea
                    </button>
                </div>

                @error('tareas')<p class="mb-3 text-xs text-red-400">{{ $message }}</p>@enderror

                <div class="space-y-3">
                    <template x-for="(tarea, i) in tareas" :key="i">
                        <div class="grid grid-cols-12 gap-2 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">

                            {{-- Descripción --}}
                            <div class="col-span-12 sm:col-span-5">
                                <label class="block text-xs text-slate-500 mb-1">Descripción</label>
                                <input type="text"
                                       :name="`tareas[${i}][descripcion]`"
                                       x-model="tarea.descripcion"
                                       placeholder="Describe la tarea realizada"
                                       class="input-field w-full text-sm" required>
                            </div>

                            {{-- Tipo --}}
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs text-slate-500 mb-1">Tipo</label>
                                <select :name="`tareas[${i}][tipo]`" x-model="tarea.tipo"
                                        class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 w-full px-2 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                    @foreach(\App\Models\ReporteDiario::TIPOS_TAREA as $tipo)
                                        <option value="{{ $tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Estado --}}
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs text-slate-500 mb-1">Estado</label>
                                <select :name="`tareas[${i}][estado]`" x-model="tarea.estado"
                                        class="bg-slate-800 border border-slate-700 rounded-xl text-sm w-full px-2 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                        :class="{
                                            'text-emerald-400': tarea.estado === 'Completado',
                                            'text-amber-400':   tarea.estado === 'En Progreso',
                                            'text-red-400':     tarea.estado === 'Incompleto',
                                        }">
                                    @foreach(\App\Models\ReporteDiario::ESTADOS_TAREA as $estado)
                                        <option value="{{ $estado }}">{{ $estado }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tiempo --}}
                            <div class="col-span-10 sm:col-span-2">
                                <label class="block text-xs text-slate-500 mb-1">Horas</label>
                                <input type="number" step="0.5" min="0.5" max="24"
                                       :name="`tareas[${i}][tiempo_horas]`"
                                       x-model="tarea.tiempo_horas"
                                       class="input-field w-full text-sm" required>
                            </div>

                            {{-- Eliminar --}}
                            <div class="col-span-2 sm:col-span-1 flex items-end justify-center pb-1">
                                <button type="button" @click="quitarTarea(i)"
                                        x-show="tareas.length > 1"
                                        class="text-slate-600 hover:text-red-400 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Resumen de métricas en tiempo real --}}
                <div class="mt-4 grid grid-cols-4 gap-3">
                    <div class="text-center p-3 rounded-xl bg-slate-800/60 border border-slate-700/50">
                        <p class="text-2xl font-bold text-white font-mono" x-text="tareas.length"></p>
                        <p class="text-xs text-slate-500 mt-0.5">Planificadas</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-emerald-500/5 border border-emerald-500/20">
                        <p class="text-2xl font-bold text-emerald-400 font-mono" x-text="contarEstado('Completado')"></p>
                        <p class="text-xs text-slate-500 mt-0.5">Completadas</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-amber-500/5 border border-amber-500/20">
                        <p class="text-2xl font-bold text-amber-400 font-mono" x-text="contarEstado('En Progreso')"></p>
                        <p class="text-xs text-slate-500 mt-0.5">En Progreso</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-red-500/5 border border-red-500/20">
                        <p class="text-2xl font-bold text-red-400 font-mono" x-text="contarEstado('Incompleto')"></p>
                        <p class="text-xs text-slate-500 mt-0.5">Incompletas</p>
                    </div>
                </div>
            </div>

            {{-- ── 3. LOGROS DESTACADOS ──────────────────────────────────── --}}
            <div class="card p-6 mb-5">
                <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-5 pb-3 border-b border-slate-800">
                    3. Logros destacados del día
                </h2>
                <textarea name="logros_destacados" rows="3"
                          placeholder="Describe los logros más relevantes que completaste hoy..."
                          class="input-dark w-full resize-none">{{ old('logros_destacados') }}</textarea>
                @error('logros_destacados')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- ── 4. IMPEDIMENTOS / BLOQUEOS ───────────────────────────── --}}
            <div class="card p-6 mb-5">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">
                        4. Impedimentos / Bloqueos
                    </h2>
                    <button type="button" @click="agregarImpedimento"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg
                                   bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar impedimento
                    </button>
                </div>

                <div class="space-y-3" x-show="impedimentos.length > 0">
                    <template x-for="(imp, i) in impedimentos" :key="i">
                        <div class="grid grid-cols-12 gap-2 p-3 rounded-xl bg-amber-500/5 border border-amber-500/20">

                            {{-- Descripción --}}
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs text-slate-500 mb-1">Descripción del impedimento</label>
                                <input type="text"
                                       :name="`impedimentos[${i}][descripcion]`"
                                       x-model="imp.descripcion"
                                       placeholder="Describe el bloqueo o problema encontrado"
                                       class="input-field w-full text-sm">
                            </div>

                            {{-- Impacto --}}
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs text-slate-500 mb-1">Impacto</label>
                                <select :name="`impedimentos[${i}][impacto]`" x-model="imp.impacto"
                                        class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 w-full px-2 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                    @foreach(\App\Models\ReporteDiario::IMPACTOS as $impacto)
                                        <option value="{{ $impacto }}">{{ $impacto }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Requiere apoyo --}}
                            <div class="col-span-4 sm:col-span-3 flex flex-col justify-start pt-1">
                                <label class="block text-xs text-slate-500 mb-2">¿Requiere apoyo?</label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" :name="`impedimentos[${i}][requiere_apoyo]`" value="0">
                                    <input type="checkbox"
                                           :name="`impedimentos[${i}][requiere_apoyo]`"
                                           value="1"
                                           x-model="imp.requiere_apoyo"
                                           class="rounded border-slate-600 bg-slate-800 text-sky-500 focus:ring-sky-500">
                                    <span class="text-sm text-slate-300">Sí</span>
                                </label>
                            </div>

                            {{-- Eliminar --}}
                            <div class="col-span-2 sm:col-span-1 flex items-start justify-center pt-6">
                                <button type="button" @click="quitarImpedimento(i)"
                                        class="text-slate-600 hover:text-red-400 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <p x-show="impedimentos.length === 0" class="text-sm text-slate-500 italic">
                    Sin impedimentos — haz clic en "Agregar impedimento" si tienes alguno.
                </p>
            </div>

            {{-- ── 5. PLAN PARA MAÑANA ───────────────────────────────────── --}}
            <div class="card p-6 mb-5">
                <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-5 pb-3 border-b border-slate-800">
                    5. Plan para mañana
                </h2>
                <textarea name="plan_siguiente_dia" rows="3"
                          placeholder="¿Qué planeas hacer mañana?"
                          class="input-dark w-full resize-none">{{ old('plan_siguiente_dia') }}</textarea>
                @error('plan_siguiente_dia')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- ── 6. ARCHIVO ADJUNTO (opcional) ────────────────────────── --}}
            <div class="card p-6 mb-6">
                <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-5 pb-3 border-b border-slate-800">
                    6. Archivo adjunto (opcional)
                </h2>
                <div class="flex items-center gap-4">
                    <label class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-slate-600
                                  hover:border-sky-500/60 cursor-pointer transition-colors group">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-sky-400 flex-shrink-0 transition-colors"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors truncate"
                               x-text="archivoNombre || 'Adjuntar PDF, imagen (máx. 5 MB)'"></p>
                        </div>
                        <input type="file" name="archivo_adjunto" accept=".pdf,.jpg,.jpeg,.png" class="sr-only"
                               @change="archivoNombre = $event.target.files[0]?.name || ''">
                    </label>
                </div>
                @error('archivo_adjunto')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('reportes_diarios.index') }}"
                   class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white transition-colors duration-150">
                    Guardar reporte
                </button>
            </div>
        </form>
    </div>

    @php
        $tareasDefault = [['descripcion' => '', 'tipo' => 'Desarrollo', 'estado' => 'Completado', 'tiempo_horas' => 2]];
        $impedimentosDefault = [];
    @endphp
    <script>
        function reporteForm() {
            return {
                area: '{{ old('area', 'desarrollo') }}',
                archivoNombre: '',
                tareas: @json(old('tareas', $tareasDefault)),
                impedimentos: @json(old('impedimentos', $impedimentosDefault)),

                agregarTarea() {
                    this.tareas.push({ descripcion: '', tipo: 'Desarrollo', estado: 'Completado', tiempo_horas: 1 });
                },
                quitarTarea(i) {
                    if (this.tareas.length > 1) this.tareas.splice(i, 1);
                },
                contarEstado(estado) {
                    return this.tareas.filter(t => t.estado === estado).length;
                },
                agregarImpedimento() {
                    this.impedimentos.push({ descripcion: '', impacto: 'Bajo', requiere_apoyo: false });
                },
                quitarImpedimento(i) {
                    this.impedimentos.splice(i, 1);
                },
            };
        }
    </script>
</x-app-layout>
