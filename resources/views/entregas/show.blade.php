<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('entregas.index') }}" class="text-slate-600 hover:text-slate-400">Entregas</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold truncate max-w-[260px]">{{ $entrega->titulo }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Encabezado ─────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="text-[10px] px-2 py-0.5 rounded-md
                                     {{ $entrega->tipo === 'final' ? 'bg-sky-500/10 text-sky-400' : 'bg-slate-500/10 text-slate-400' }}">
                            {{ $entrega->tipoLabel() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $entrega->estadoBadgeClass() }}">
                            {{ $entrega->estadoLabel() }}
                        </span>
                        @if($entrega->estaFirmado())
                        <span class="text-[10px] text-slate-500 font-mono">
                            firmado {{ $entrega->firmado_at->format('d/m/Y') }}
                        </span>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-white">{{ $entrega->titulo }}</h1>
                    @if($entrega->descripcion)
                    <p class="text-sm text-slate-500 mt-1">{{ $entrega->descripcion }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detalles ────────────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Detalles</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Proyecto</dt>
                    <dd>
                        <a href="{{ route('proyectos.show', $entrega->project) }}"
                           class="text-sm text-sky-400 hover:underline">{{ $entrega->project->name }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Cliente</dt>
                    <dd>
                        <a href="{{ route('clientes.show', $entrega->client) }}"
                           class="text-sm text-sky-400 hover:underline">{{ $entrega->client->razon_social }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Fecha de entrega</dt>
                    <dd class="text-sm font-mono text-white">{{ $entrega->fecha_entrega->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Creado por</dt>
                    <dd class="text-sm text-white">{{ $entrega->createdBy->name }}</dd>
                </div>
            </dl>
        </div>

        {{-- Ítems entregados ─────────────────────────────────────────── --}}
        @if($entrega->items_entregados)
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Ítems entregados</h3>
            <ul class="space-y-2">
                @foreach($entrega->items_entregados as $item)
                @if($item)
                <li class="flex items-start gap-2 text-sm text-slate-300">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    {{ $item }}
                </li>
                @endif
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Firmante ────────────────────────────────────────────────── --}}
        @if($entrega->firma_cliente)
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Firmante del cliente</h3>
            <dl class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <dt class="text-xs text-slate-500 mb-0.5">Nombre</dt>
                    <dd class="text-sm text-white font-semibold">{{ $entrega->firma_cliente }}</dd>
                </div>
                @if($entrega->dni_firmante)
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">DNI</dt>
                    <dd class="text-sm font-mono text-white">{{ $entrega->dni_firmante }}</dd>
                </div>
                @endif
                @if($entrega->cargo_firmante)
                <div>
                    <dt class="text-xs text-slate-500 mb-0.5">Cargo</dt>
                    <dd class="text-sm text-slate-300">{{ $entrega->cargo_firmante }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif

        {{-- Observaciones ───────────────────────────────────────────── --}}
        @if($entrega->observaciones)
        <div class="bg-slate-900 border border-amber-500/20 rounded-2xl p-6">
            <h3 class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">Observaciones</h3>
            <p class="text-sm text-slate-300">{{ $entrega->observaciones }}</p>
        </div>
        @endif

        {{-- Acciones ────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('entregas.index') }}"
               class="text-sm text-slate-500 hover:text-white transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
            <div class="flex gap-2">
                <a href="{{ route('entregas.acta', $entrega) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold
                          text-slate-300 bg-slate-800 hover:bg-slate-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Descargar Acta PDF
                </a>
                @can('entregas.editar')
                <a href="{{ route('entregas.edit', $entrega) }}"
                   class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 transition-colors">
                    Editar
                </a>
                @endcan
                @can('entregas.eliminar')
                <form action="{{ route('entregas.destroy', $entrega) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta acta de entrega?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 transition-colors">
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>

    </div>
</x-app-layout>
