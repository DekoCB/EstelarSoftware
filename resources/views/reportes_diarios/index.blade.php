<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Reportes Diarios</h1>
                <p class="text-sm text-slate-400 mt-0.5">
                    {{ $esGestor ? 'Reportes del equipo de trabajo' : 'Mis reportes diarios' }}
                </p>
            </div>
            @can('reportes_diarios.crear')
            <a href="{{ route('reportes_diarios.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-sky-500 hover:bg-sky-400 text-white transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo reporte
            </a>
            @endcan
        </div>
    </x-slot>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="area" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
            <option value="">Todas las áreas</option>
            @foreach(\App\Models\ReporteDiario::AREAS as $val => $label)
                <option value="{{ $val }}" @selected(request('area') === $val)>{{ $label }}</option>
            @endforeach
        </select>

        @if($esGestor)
        <select name="user_id" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
            <option value="">Todos los colaboradores</option>
            @foreach($colaboradores as $col)
                <option value="{{ $col->id }}" @selected(request('user_id') == $col->id)>{{ $col->name }}</option>
            @endforeach
        </select>
        @endif

        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" onchange="this.form.submit()"
               class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" onchange="this.form.submit()"
               class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">

        @if(request('area') || request('user_id') || request('fecha_desde') || request('fecha_hasta'))
            <a href="{{ route('reportes_diarios.index') }}"
               class="text-sm text-slate-400 hover:text-white px-3 py-2 transition-colors">
                Limpiar filtros
            </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/60">
                    @if($esGestor)
                    <th class="th-cell">Colaborador</th>
                    @endif
                    <th class="th-cell">Fecha</th>
                    <th class="th-cell">Área</th>
                    <th class="th-cell">Proyectos</th>
                    <th class="th-cell text-center">Tareas</th>
                    <th class="th-cell text-center">Completadas</th>
                    <th class="th-cell text-center">Horas</th>
                    <th class="th-cell text-center">Impedimentos</th>
                    <th class="th-cell w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($reportes as $r)
                <tr class="hover:bg-slate-800/30 transition-colors duration-100">
                    @if($esGestor)
                    <td class="td-cell">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500
                                        flex items-center justify-center text-[10px] font-bold text-white uppercase flex-shrink-0">
                                {{ substr($r->user->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-white font-medium leading-tight">{{ $r->user->name }}</p>
                                <p class="text-slate-500 text-xs leading-tight">{{ $r->user->getRoleNames()->first() }}</p>
                            </div>
                        </div>
                    </td>
                    @endif
                    <td class="td-cell">
                        <a href="{{ route('reportes_diarios.show', $r) }}"
                           class="text-white hover:text-sky-400 font-medium transition-colors">
                            {{ $r->fecha->format('d/m/Y') }}
                        </a>
                        <p class="text-slate-500 text-xs">{{ $r->fecha->translatedFormat('l') }}</p>
                    </td>
                    <td class="td-cell">
                        @php $color = $r->areaColor(); @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg
                                     bg-{{ $color }}-500/15 text-{{ $color }}-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-400"></span>
                            {{ $r->areaLabel() }}
                        </span>
                    </td>
                    <td class="td-cell text-slate-300 max-w-[180px] truncate">
                        {{ $r->proyectos_asignados ?: '—' }}
                    </td>
                    <td class="td-cell text-center text-slate-300 font-mono">
                        {{ count($r->tareas) }}
                    </td>
                    <td class="td-cell text-center">
                        @php $comp = $r->tareasCompletadas(); $total = count($r->tareas); @endphp
                        <span class="text-xs font-semibold {{ $comp === $total ? 'text-emerald-400' : 'text-slate-400' }}">
                            {{ $comp }}/{{ $total }}
                        </span>
                    </td>
                    <td class="td-cell text-center font-mono text-slate-300">
                        {{ number_format($r->horas_trabajadas, 1) }}h
                    </td>
                    <td class="td-cell text-center">
                        @if($r->tieneImpedimentos())
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md bg-amber-500/15 text-amber-400">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                </svg>
                                {{ count($r->impedimentos) }}
                            </span>
                        @else
                            <span class="text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="td-cell">
                        <a href="{{ route('reportes_diarios.show', $r) }}"
                           class="text-slate-400 hover:text-sky-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $esGestor ? 9 : 8 }}" class="td-cell text-center py-16 text-slate-500">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                        </svg>
                        No hay reportes registrados
                        @can('reportes_diarios.crear')
                            — <a href="{{ route('reportes_diarios.create') }}" class="text-sky-400 hover:underline">crear el primero</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reportes->hasPages())
    <div class="mt-4">{{ $reportes->links() }}</div>
    @endif
</x-app-layout>
