<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Solicitudes Internas</h1>
                <p class="text-sm text-slate-400 mt-0.5">
                    {{ $esGestor ? 'Gestión de solicitudes del equipo' : 'Mis solicitudes de recursos' }}
                </p>
            </div>
            @can('solicitudes.crear')
            <a href="{{ route('solicitudes.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-sky-500 hover:bg-sky-400 text-white transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nueva solicitud
            </a>
            @endcan
        </div>
    </x-slot>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="estado" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
            <option value="">Todos los estados</option>
            @foreach(\App\Models\Solicitud::ESTADOS as $val => $label)
                <option value="{{ $val }}" @selected(request('estado') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="tipo" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500">
            <option value="">Todos los tipos</option>
            <option value="dinero" @selected(request('tipo') === 'dinero')>Dinero</option>
            <option value="objeto" @selected(request('tipo') === 'objeto')>Objeto</option>
        </select>
        @if(request('estado') || request('tipo'))
            <a href="{{ route('solicitudes.index') }}"
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
                    <th class="th-cell">Solicitante</th>
                    @endif
                    <th class="th-cell">Título</th>
                    <th class="th-cell">Tipo</th>
                    <th class="th-cell">Monto</th>
                    <th class="th-cell">Estado</th>
                    <th class="th-cell">Fecha</th>
                    <th class="th-cell w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($solicitudes as $sol)
                <tr class="hover:bg-slate-800/30 transition-colors duration-100">
                    @if($esGestor)
                    <td class="td-cell">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500
                                        flex items-center justify-center text-[10px] font-bold text-white uppercase flex-shrink-0">
                                {{ substr($sol->solicitante->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-white font-medium leading-tight">{{ $sol->solicitante->name }}</p>
                                <p class="text-slate-500 text-xs leading-tight">{{ $sol->solicitante->getRoleNames()->first() }}</p>
                            </div>
                        </div>
                    </td>
                    @endif
                    <td class="td-cell">
                        <a href="{{ route('solicitudes.show', $sol) }}"
                           class="text-white hover:text-sky-400 font-medium transition-colors">
                            {{ $sol->titulo }}
                        </a>
                    </td>
                    <td class="td-cell">
                        @if($sol->tipo === 'dinero')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Dinero
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-400">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                                Objeto
                            </span>
                        @endif
                    </td>
                    <td class="td-cell font-mono">
                        {{ $sol->monto ? 'S/ ' . number_format($sol->monto, 2) : '—' }}
                    </td>
                    <td class="td-cell">
                        @php $color = $sol->estadoColor(); @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg
                                     bg-{{ $color }}-500/15 text-{{ $color }}-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-400"></span>
                            {{ $sol->estadoLabel() }}
                        </span>
                    </td>
                    <td class="td-cell text-slate-400">
                        {{ $sol->created_at->format('d/m/Y') }}
                    </td>
                    <td class="td-cell">
                        <a href="{{ route('solicitudes.show', $sol) }}"
                           class="text-slate-400 hover:text-sky-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $esGestor ? 7 : 6 }}" class="td-cell text-center py-16 text-slate-500">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                        </svg>
                        No hay solicitudes
                        @can('solicitudes.crear')
                            — <a href="{{ route('solicitudes.create') }}" class="text-sky-400 hover:underline">crear una</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($solicitudes->hasPages())
    <div class="mt-4">{{ $solicitudes->links() }}</div>
    @endif
</x-app-layout>
