<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('reportes_diarios.index') }}"
                       class="text-slate-500 hover:text-slate-300 transition-colors text-sm">
                        Reportes Diarios
                    </a>
                    <span class="text-slate-700">/</span>
                    <span class="text-sm text-slate-400">{{ $reporte->fecha->format('d/m/Y') }}</span>
                </div>
                <h1 class="text-xl font-bold text-white">
                    Reporte — {{ $reporte->fecha->translatedFormat('l, d \d\e F') }}
                </h1>
                <p class="text-sm text-slate-400 mt-0.5">{{ $reporte->user->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                @php $color = $reporte->areaColor(); @endphp
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-xl
                             bg-{{ $color }}-500/15 text-{{ $color }}-400">
                    {{ $reporte->areaLabel() }}
                </span>
                @can('delete', $reporte)
                <form method="POST" action="{{ route('reportes_diarios.destroy', $reporte) }}"
                      onsubmit="return confirm('¿Eliminar este reporte?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-slate-500 hover:text-red-400 transition-colors text-sm">
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-5">

        {{-- ── DATOS DEL COLABORADOR ───────────────────────────────────── --}}
        <div class="card p-6">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
                1. Datos del colaborador
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Nombre</p>
                    <p class="text-sm font-medium text-white">{{ $reporte->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Cargo</p>
                    <p class="text-sm font-medium text-white capitalize">
                        {{ $reporte->user->getRoleNames()->first() ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Área</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2 py-1 rounded-lg
                                 bg-{{ $reporte->areaColor() }}-500/15 text-{{ $reporte->areaColor() }}-400">
                        {{ $reporte->areaLabel() }}
                    </span>
                </div>
                @if($reporte->proyectos_asignados)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Proyectos asignados</p>
                    <p class="text-sm text-slate-300">{{ $reporte->proyectos_asignados }}</p>
                </div>
                @endif
                @if($reporte->sprint_iteracion)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Sprint / Iteración</p>
                    <p class="text-sm text-slate-300">{{ $reporte->sprint_iteracion }}</p>
                </div>
                @endif
                @if($reporte->modulo_componente)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Módulo / Componente</p>
                    <p class="text-sm text-slate-300">{{ $reporte->modulo_componente }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Horas trabajadas</p>
                    <p class="text-sm font-mono font-bold text-white">{{ number_format($reporte->horas_trabajadas, 1) }}h</p>
                </div>
            </div>
        </div>

        {{-- ── TAREAS DEL DÍA ──────────────────────────────────────────── --}}
        <div class="card overflow-hidden">
            <div class="px-6 pt-6 pb-4 border-b border-slate-800">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    2. Tareas del día
                </h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800/60">
                        <th class="th-cell">Descripción</th>
                        <th class="th-cell">Tipo</th>
                        <th class="th-cell text-center">Estado</th>
                        <th class="th-cell text-center">Horas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @foreach($reporte->tareas as $tarea)
                    <tr class="hover:bg-slate-800/20 transition-colors">
                        <td class="td-cell text-slate-300">{{ $tarea['descripcion'] }}</td>
                        <td class="td-cell">
                            <span class="text-xs px-2 py-0.5 rounded-md bg-slate-700/60 text-slate-400">
                                {{ $tarea['tipo'] }}
                            </span>
                        </td>
                        <td class="td-cell text-center">
                            @php
                                $estadoColor = match($tarea['estado']) {
                                    'Completado' => 'emerald',
                                    'En Progreso' => 'amber',
                                    'Incompleto' => 'red',
                                    default => 'slate',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg
                                         bg-{{ $estadoColor }}-500/15 text-{{ $estadoColor }}-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $estadoColor }}-400"></span>
                                {{ $tarea['estado'] }}
                            </span>
                        </td>
                        <td class="td-cell text-center font-mono text-slate-300">
                            {{ number_format($tarea['tiempo_horas'], 1) }}h
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── MÉTRICAS ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-4 gap-4">
            <div class="card p-4 text-center">
                <p class="text-3xl font-bold text-white font-mono">{{ count($reporte->tareas) }}</p>
                <p class="text-xs text-slate-500 mt-1">Planificadas</p>
            </div>
            <div class="card p-4 text-center border border-emerald-500/20">
                <p class="text-3xl font-bold text-emerald-400 font-mono">{{ $reporte->tareasCompletadas() }}</p>
                <p class="text-xs text-slate-500 mt-1">Completadas</p>
            </div>
            <div class="card p-4 text-center border border-amber-500/20">
                <p class="text-3xl font-bold text-amber-400 font-mono">{{ $reporte->tareasEnProgreso() }}</p>
                <p class="text-xs text-slate-500 mt-1">En Progreso</p>
            </div>
            <div class="card p-4 text-center border border-red-500/20">
                <p class="text-3xl font-bold text-red-400 font-mono">{{ $reporte->tareasIncompletas() }}</p>
                <p class="text-xs text-slate-500 mt-1">Incompletas</p>
            </div>
        </div>

        {{-- ── LOGROS DESTACADOS ───────────────────────────────────────── --}}
        @if($reporte->logros_destacados)
        <div class="card p-6">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
                3. Logros destacados del día
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ $reporte->logros_destacados }}</p>
        </div>
        @endif

        {{-- ── IMPEDIMENTOS ─────────────────────────────────────────────── --}}
        @if($reporte->tieneImpedimentos())
        <div class="card overflow-hidden">
            <div class="px-6 pt-6 pb-4 border-b border-slate-800">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    4. Impedimentos / Bloqueos
                </h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800/60">
                        <th class="th-cell">Descripción</th>
                        <th class="th-cell text-center">Impacto</th>
                        <th class="th-cell text-center">¿Requiere apoyo?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @foreach($reporte->impedimentos as $imp)
                    <tr class="hover:bg-slate-800/20 transition-colors">
                        <td class="td-cell text-slate-300">{{ $imp['descripcion'] }}</td>
                        <td class="td-cell text-center">
                            @php
                                $impColor = match($imp['impacto']) {
                                    'Alto'  => 'red',
                                    'Medio' => 'amber',
                                    'Bajo'  => 'slate',
                                    default => 'slate',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md
                                         bg-{{ $impColor }}-500/15 text-{{ $impColor }}-400">
                                {{ $imp['impacto'] }}
                            </span>
                        </td>
                        <td class="td-cell text-center">
                            @if(!empty($imp['requiere_apoyo']))
                                <span class="inline-flex items-center gap-1 text-xs text-amber-400 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                    Sí
                                </span>
                            @else
                                <span class="text-xs text-slate-600">No</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── PLAN PARA MAÑANA ────────────────────────────────────────── --}}
        @if($reporte->plan_siguiente_dia)
        <div class="card p-6">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
                5. Plan para mañana
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ $reporte->plan_siguiente_dia }}</p>
        </div>
        @endif

        {{-- ── ARCHIVO ADJUNTO ─────────────────────────────────────────── --}}
        @if($reporte->archivo_adjunto)
        <div class="card p-6">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
                Archivo adjunto
            </h2>
            <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-8 h-8 text-sky-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/>
                    </svg>
                    <span class="text-sm text-slate-300 truncate">{{ basename($reporte->archivo_adjunto) }}</span>
                </div>
                <a href="{{ route('reportes_diarios.adjunto', $reporte) }}"
                   class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                          bg-sky-500/10 text-sky-400 border border-sky-500/20
                          hover:bg-sky-500/20 hover:text-sky-300 transition-all duration-150 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/>
                    </svg>
                    Descargar
                </a>
            </div>
        </div>
        @endif

        {{-- ── METADATA ─────────────────────────────────────────────────── --}}
        <div class="text-xs text-slate-600 text-right pb-4">
            Registrado el {{ $reporte->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
</x-app-layout>
