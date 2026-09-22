<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($title ?? null) ? $title . ' — ' : '' }}{{ config('app.name') }}</title>

    @php $empresa = \App\Models\EmpresaConfig::config(); @endphp
    @if($empresa->logo_sidebar)
        <link rel="icon" href="{{ $empresa->logoSidebarUrl() }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        * {
            scrollbar-width: thin;
            scrollbar-color: #0EA5E9 rgba(255, 255, 255, 0.03);
        }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.03); border-radius: 999px; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #38BDF8, #0EA5E9 45%, #0284C7);
            border-radius: 999px;
            border: 3px solid transparent;
            background-clip: padding-box;
            min-height: 40px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #7DD3FC, #38BDF8 45%, #0EA5E9);
            background-clip: padding-box;
            box-shadow: 0 0 12px rgba(14, 165, 233, 0.55);
        }
        ::-webkit-scrollbar-corner { background: transparent; }
    </style>
</head>
<body
    class="font-sans antialiased bg-slate-950 text-slate-100"
    x-data="{ mobileMenuOpen: false }"
>

{{-- ══ OVERLAY MÓVIL ══════════════════════════════════════════════ --}}
<div
    x-show="mobileMenuOpen"
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="mobileMenuOpen = false"
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-20 lg:hidden"
    style="display:none"
></div>

{{-- ══ SIDEBAR ═════════════════════════════════════════════════════ --}}
<aside
    class="fixed top-0 left-0 h-full w-64 z-30 flex flex-col
           bg-[#080C18] border-r border-slate-800/70
           transition-transform duration-300 ease-in-out
           -translate-x-full lg:translate-x-0"
    :class="mobileMenuOpen ? 'translate-x-0' : ''"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-slate-800/70 flex-shrink-0">
        <div class="w-8 h-8 rounded-lg flex-shrink-0 overflow-hidden
                    {{ $empresa->logo_sidebar ? '' : 'bg-gradient-to-br from-sky-400 to-cyan-500 shadow-[0_0_18px_rgba(14,165,233,0.45)]' }}
                    flex items-center justify-center">
            @if($empresa->logo_sidebar)
                <img src="{{ $empresa->logoSidebarUrl() }}" alt="Logo" class="w-full h-full object-contain">
            @else
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375
                             m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375
                             m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375
                             m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>
                </svg>
            @endif
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-white leading-tight truncate">
                {{ $empresa->nombre_comercial ?? $empresa->razon_social ?? 'Estelar' }}
                <span class="text-sky-400">ERP</span>
            </p>
            <p class="text-[10px] text-slate-600 truncate leading-tight">Software Empresarial</p>
        </div>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2.5">

        @php
            $link = fn(string $path) =>
                (request()->is(ltrim($path, '/')) || request()->is(ltrim($path, '/') . '/*'))
                    ? 'nav-link-active'
                    : 'nav-link';
        @endphp

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" wire:navigate class="{{ $link('/dashboard') }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- ── OPERACIONES ─────────────────────────────────────────── --}}
        @canany(['clientes.ver','leads.ver','eventos.ver','proyectos.ver','proyectos.ver_asignados'])
            <p class="nav-section">Operaciones</p>

            @can('clientes.ver')
            <a href="{{ url('/clientes') }}" wire:navigate class="{{ $link('/clientes') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </svg>
                <span>Clientes</span>
            </a>
            @endcan

            @can('leads.ver')
            <a href="{{ route('leads.index') }}" wire:navigate class="{{ $link('/leads') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                </svg>
                <span>Leads</span>
            </a>
            @endcan

            @can('eventos.ver')
            <a href="{{ route('eventos.index') }}" wire:navigate class="{{ $link('/eventos') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                </svg>
                <span>Eventos</span>
            </a>
            @endcan

            @canany(['proyectos.ver','proyectos.ver_asignados'])
            <a href="{{ route('proyectos.index') }}" wire:navigate class="{{ $link('/proyectos') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>
                </svg>
                <span>Proyectos</span>
            </a>
            @endcanany

            {{-- Requerimientos se accede desde el detalle de cada proyecto --}}
        @endcanany

        {{-- ── COMERCIAL ────────────────────────────────────────────── --}}
        @canany(['cotizaciones.ver','facturacion.ver','caja.ver','proyectos.ver'])
            <p class="nav-section">Comercial</p>

            @can('cotizaciones.ver')
            <a href="{{ route('cotizaciones.index') }}" wire:navigate class="{{ $link('/ventas') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                <span>Cotizaciones</span>
            </a>
            @endcan

            @can('facturacion.ver')
            <a href="{{ route('facturacion.index') }}" wire:navigate class="{{ $link('/facturacion') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                </svg>
                <span>Facturación</span>
                <span class="ml-auto text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded-md bg-sky-500/20 text-sky-400 leading-none">SUNAT</span>
            </a>
            @endcan

            @can('caja.ver')
            <a href="{{ route('caja.index') }}" wire:navigate class="{{ $link('/caja') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                </svg>
                <span>Caja</span>
            </a>
            @endcan

            @can('proyectos.ver')
            <a href="{{ route('entregas.index') }}" wire:navigate class="{{ $link('/entregas') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375Zm0 4.125h17.25v6.375a2.625 2.625 0 0 1-2.625 2.625H6a2.625 2.625 0 0 1-2.625-2.625V7.125Zm6.375 6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75Z"/>
                </svg>
                <span>Entregas</span>
            </a>
            @endcan
            @can('planilla.ver')
            <a href="{{ route('planilla.index') }}" wire:navigate class="{{ $link('/planilla') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                </svg>
                <span>Planilla</span>
            </a>
            @endcan
        @endcanany

        {{-- ── EQUIPO ───────────────────────────────────────────────── --}}
        @canany(['solicitudes.ver','solicitudes.gestionar','asistencias.ver','asistencias.registrar','asistencias.gestionar','reportes_diarios.ver','reportes_diarios.crear','reportes_diarios.gestionar'])
            <p class="nav-section">Equipo</p>

            @canany(['asistencias.registrar'])
            <a href="{{ route('asistencias.marcar') }}" wire:navigate class="{{ $link('/asistencias/marcar') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                </svg>
                <span>Marcar asistencia</span>
            </a>
            @endcanany

            @canany(['asistencias.ver','asistencias.gestionar'])
            <a href="{{ route('asistencias.index') }}" wire:navigate class="{{ $link('/asistencias') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                </svg>
                <span>Asistencias</span>
            </a>
            @endcanany

            @canany(['reportes_diarios.ver','reportes_diarios.crear','reportes_diarios.gestionar'])
            <a href="{{ route('reportes_diarios.index') }}" wire:navigate class="{{ $link('/reportes-diarios') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                <span>Reportes Diarios</span>
            </a>
            @endcanany

            @canany(['solicitudes.ver','solicitudes.gestionar'])
            <a href="{{ route('solicitudes.index') }}" wire:navigate class="{{ $link('/solicitudes') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                </svg>
                <span>Solicitudes</span>
                @php
                    $pendSolic = \App\Models\Solicitud::when(!auth()->user()->hasPermissionTo('solicitudes.gestionar'), fn($q) => $q->where('user_id', auth()->id()))->where('estado','pendiente')->count();
                @endphp
                @if($pendSolic > 0)
                    <span class="ml-auto text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-400 leading-none">{{ $pendSolic }}</span>
                @endif
            </a>
            @endcanany
        @endcanany

        {{-- ── ANÁLISIS ─────────────────────────────────────────────── --}}
        @can('reportes.ver')
            <p class="nav-section">Análisis</p>
            <a href="{{ route('reportes.index') }}" wire:navigate class="{{ $link('/reportes') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/>
                </svg>
                <span>Reportes</span>
            </a>
        @endcan

        {{-- ── SISTEMA ──────────────────────────────────────────────── --}}
        @canany(['configuracion.ver','usuarios.ver'])
            <p class="nav-section">Sistema</p>

            @can('configuracion.ver')
            <a href="{{ route('configuracion.index') }}" wire:navigate class="{{ $link('/configuracion') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <span>Configuración</span>
            </a>
            @endcan

            @can('usuarios.ver')
            <a href="{{ route('usuarios.index') }}" wire:navigate class="{{ $link('/admin/usuarios') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                </svg>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('roles.index') }}" wire:navigate class="{{ $link('/admin/roles') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                </svg>
                <span>Roles y permisos</span>
            </a>
            @endcan
        @endcanany

    </nav>

    {{-- Tarjeta de usuario --}}
    <div x-data="{ userMenu: false }" class="relative flex-shrink-0 border-t border-slate-800/70 p-2.5">
        <button
            @click="userMenu = !userMenu"
            class="flex items-center gap-3 w-full rounded-xl p-2.5
                   hover:bg-slate-800/60 transition-colors duration-150 text-left"
        >
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500
                        flex items-center justify-center text-xs font-bold text-white flex-shrink-0 uppercase">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate leading-tight">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-slate-500 truncate leading-tight">
                    {{ auth()->user()->email }}
                </p>
            </div>
            <svg class="w-4 h-4 text-slate-600 flex-shrink-0 transition-transform duration-200"
                 :class="userMenu ? '-rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"/>
            </svg>
        </button>

        <div
            x-show="userMenu"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="userMenu = false"
            class="absolute bottom-full left-2.5 right-2.5 mb-1
                   bg-slate-800 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl"
            style="display:none"
        >
            <a href="{{ route('profile.edit') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300
                      hover:bg-slate-700/60 transition-colors duration-150">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                </svg>
                Mi perfil
            </a>
            <div class="border-t border-slate-700/60"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-red-400
                               hover:bg-red-500/10 w-full transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

</aside>

{{-- ══ CONTENIDO PRINCIPAL ═════════════════════════════════════════ --}}
<div class="lg:ml-64 min-h-screen flex flex-col">

    {{-- Barra superior --}}
    <header class="sticky top-0 z-10 h-16 flex items-center gap-4 px-6
                   bg-slate-950/80 backdrop-blur-md border-b border-slate-800/60">

        {{-- Hamburger (móvil) --}}
        <button
            @click="mobileMenuOpen = true"
            class="lg:hidden text-slate-400 hover:text-white transition-colors"
            aria-label="Abrir menú"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        {{-- Título de página --}}
        <div class="flex-1 min-w-0">
            {{ $header ?? '' }}
        </div>

        {{-- Notificaciones de solicitudes urgentes --}}
        @can('solicitudes.gestionar')
        <livewire:notificaciones-solicitudes />
        @endcan
        @can('leads.ver')
        <livewire:notificaciones-reuniones />
        @persist('lead-alarm')
        <livewire:alarma-reuniones />
        @endpersist
        @endcan

        {{-- Rol del usuario --}}
        @php $roles = auth()->user()->getRoleNames(); @endphp
        @if($roles->isNotEmpty())
        <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold
                     bg-slate-800 text-slate-400 border border-slate-700/60 uppercase tracking-wide font-mono">
            {{ $roles->first() }}
        </span>
        @endif

    </header>

    {{-- Flash messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mx-6 mt-4">
        <div class="flash-success">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mx-6 mt-4">
        <div class="flash-error">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    {{-- Contenido de la página --}}
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>

    {{-- Footer interno --}}
    <footer class="px-6 py-4 border-t border-slate-800/40 text-xs text-slate-700 font-mono flex items-center justify-between">
        <span>Estelar Software Empresarial ERP</span>
        <span>v1.0 · {{ date('Y') }}</span>
    </footer>

</div>

@livewireScripts
@stack('scripts')
</body>
</html>
