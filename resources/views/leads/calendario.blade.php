<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('leads.index') }}" class="text-slate-600 hover:text-slate-400 transition-colors font-mono">Leads</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Calendario</span>
        </div>
    </x-slot>

    <div
        x-data="leadsCalendario({{ now()->year }}, {{ now()->month }})"
        x-init="cargar()"
        class="space-y-6"
    >
        {{-- ── Header ─────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Calendario de reuniones</h2>
                <p class="text-sm text-slate-500 mt-0.5">Vista mensual de reuniones agendadas con leads</p>
            </div>
            <a href="{{ route('leads.index') }}"
               style="display:inline-flex; align-items:center; gap:0.5rem;"
               class="px-3 py-2 rounded-xl text-sm text-slate-400
                      hover:text-white hover:bg-slate-800 transition-colors border border-slate-700/60">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
        </div>

        {{-- ── Calendario ─────────────────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-4 sm:p-6 relative overflow-hidden">

            {{-- brillo decorativo de fondo --}}
            <div style="position:absolute; top:-80px; right:-80px; width:260px; height:260px; border-radius:9999px;
                        background:radial-gradient(circle, rgba(14,165,233,0.10) 0%, transparent 70%); pointer-events:none;"></div>

            {{-- Navegación de mes --}}
            <div style="display:flex; align-items:center; justify-content:space-between;" class="mb-5 relative">
                <button @click="mesAnterior()" style="display:flex; align-items:center; justify-content:center;"
                        class="w-9 h-9 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors border border-slate-700/60">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                    </svg>
                </button>
                <h3 class="text-base font-bold text-white tracking-wide" x-text="nombreMes"></h3>
                <button @click="mesSiguiente()" style="display:flex; align-items:center; justify-content:center;"
                        class="w-9 h-9 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors border border-slate-700/60">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>
            </div>

            {{-- Días de la semana --}}
            <div style="display:grid; grid-template-columns:repeat(7, minmax(0,1fr)); gap:4px;" class="mb-1.5 relative">
                <template x-for="d in ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']" :key="d">
                    <div class="text-center text-[10px] font-bold text-slate-600 uppercase tracking-wider py-1" x-text="d"></div>
                </template>
            </div>

            {{-- Celdas de días --}}
            <div style="display:grid; grid-template-columns:repeat(7, minmax(0,1fr)); gap:4px;" class="relative">
                <template x-for="n in offsetInicio" :key="'off-'+n">
                    <div style="min-height:88px;"></div>
                </template>
                <template x-for="dia in diasEnMes" :key="dia">
                    <button
                        @click="seleccionarDia(dia)"
                        type="button"
                        style="min-height:88px; display:flex; flex-direction:column; align-items:stretch; text-align:left;"
                        class="rounded-xl p-1.5 transition-colors duration-150 border"
                        :class="diaSeleccionado === dia
                            ? 'bg-sky-500/10 border-sky-500/50'
                            : (esHoy(dia) ? 'bg-slate-800/40 border-sky-500/30' : 'bg-slate-800/20 border-transparent hover:border-slate-700')"
                    >
                        <span class="text-xs font-mono"
                              :class="esHoy(dia) ? 'text-sky-400 font-bold' : 'text-slate-400'"
                              x-text="dia"></span>
                        <span class="flex flex-col gap-1 mt-1.5 overflow-hidden">
                            <template x-for="ev in (eventosPorDia[dia] || []).slice(0, 2)" :key="ev.id">
                                <span class="flex items-center gap-1 overflow-hidden text-ellipsis whitespace-nowrap px-1.5 py-1 rounded-md text-[11px] font-medium"
                                      :style="'background-color:' + colorHex(ev.color, 15) + '; color:' + colorHex(ev.color, 'text')"
                                      :title="ev.hora + ' — ' + ev.lead_nombre + (ev.label ? ' (' + ev.label + ')' : '')">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                          :style="'background-color:' + colorHex(ev.color)"></span>
                                    <span class="font-mono font-bold" x-text="ev.hora"></span>
                                    <span class="overflow-hidden text-ellipsis" x-text="ev.lead_nombre"></span>
                                </span>
                            </template>
                            <span x-show="(eventosPorDia[dia] || []).length > 2" class="flex items-center gap-1 flex-wrap pl-0.5">
                                <template x-for="ev in (eventosPorDia[dia] || []).slice(2)" :key="'extra-'+ev.id">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                          :style="'background-color:' + colorHex(ev.color)"
                                          :title="ev.hora + ' — ' + ev.lead_nombre"></span>
                                </template>
                            </span>
                        </span>
                    </button>
                </template>
            </div>

            <p x-show="cargando" class="text-xs text-slate-600 mt-4 relative">Cargando reuniones...</p>
        </div>

        {{-- ── Modal: agenda del día (24 horas) ──────────────────── --}}
        <template x-teleport="body">
            <div
                x-show="diaModalAbierto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display:none"
            >
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="cerrarDiaModal()"></div>

                <div class="relative min-h-full flex items-start justify-center p-4 pt-12">
                    <div
                        x-show="diaModalAbierto"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="w-full max-w-lg bg-slate-900 border border-slate-700/60 rounded-2xl
                               shadow-[0_0_80px_rgba(0,0,0,0.6)] flex flex-col max-h-[calc(100vh-6rem)]"
                        @click.stop
                    >
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/80 flex-shrink-0">
                            <div>
                                <h2 class="text-base font-bold text-white">Agenda del día</h2>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="diaModalLabel"></p>
                            </div>
                            <button
                                @click="cerrarDiaModal()"
                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                       text-slate-500 hover:text-white hover:bg-slate-800
                                       transition-colors duration-150"
                                aria-label="Cerrar"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="overflow-y-auto flex-1 px-2 py-3">
                            <div class="divide-y divide-slate-800/60">
                                <button
                                    type="button"
                                    @click="fueraHorarioExpandido = !fueraHorarioExpandido"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-[11px] font-semibold
                                           text-slate-500 hover:text-slate-300 transition-colors duration-150"
                                >
                                    <svg class="w-3 h-3 transition-transform duration-150 flex-shrink-0"
                                         :class="fueraHorarioExpandido ? '-rotate-90' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                    00:00 – 05:00 · fuera de horario
                                </button>

                                <template x-for="bloque in horasDia" :key="bloque.hora">
                                    <div x-show="bloque.hora >= 6 || fueraHorarioExpandido || bloque.eventos.length > 0"
                                         class="flex gap-3 px-3 py-2.5">
                                        <span class="w-12 flex-shrink-0 text-xs font-mono text-slate-500 pt-1"
                                              x-text="String(bloque.hora).padStart(2, '0') + ':00'"></span>
                                        <div class="flex-1 min-w-0">
                                            <button
                                                type="button"
                                                x-show="bloque.eventos.length === 0"
                                                @click="irACrearLead(String(bloque.hora).padStart(2, '0') + ':00')"
                                                class="w-full text-left text-xs text-slate-700 hover:text-sky-400
                                                       pt-1 pb-1 transition-colors duration-150"
                                            >
                                                Libre — clic para agendar
                                            </button>
                                            <div class="space-y-1.5">
                                                <template x-for="ev in bloque.eventos" :key="ev.id">
                                                    <a :href="'{{ url('/leads') }}/' + ev.lead_id"
                                                       :style="'background-color:' + colorHex(ev.color, 6) + '; border-color:' + colorHex(ev.color, 25)"
                                                       class="block border px-3 py-2 rounded-lg hover:brightness-125 transition-all">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="text-sm font-semibold text-white truncate" x-text="ev.lead_nombre"></span>
                                                            <span class="text-xs font-mono font-bold flex-shrink-0" :style="'color:' + colorHex(ev.color, 'text')" x-text="ev.hora"></span>
                                                        </div>
                                                        <p x-show="ev.nota" class="text-xs text-slate-500 mt-0.5 truncate" x-text="ev.nota"></p>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @once
    <script>
        function leadsCalendario(anioInicial, mesInicial) {
            return {
                anio: anioInicial,
                mes: mesInicial,
                cargando: false,
                eventosPorDia: {},
                diaSeleccionado: null,
                diaModalAbierto: false,
                diaModalFecha: null,
                diaModalLabel: '',
                horasDia: [],

                get nombreMes() {
                    const nombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    return nombres[this.mes - 1] + ' ' + this.anio;
                },
                get diasEnMes() {
                    return new Date(this.anio, this.mes, 0).getDate();
                },
                get offsetInicio() {
                    // día de la semana del día 1, ajustado para que Lunes = 0
                    const dow = new Date(this.anio, this.mes - 1, 1).getDay();
                    return (dow === 0) ? 6 : dow - 1;
                },

                esHoy(dia) {
                    const hoy = new Date();
                    return hoy.getFullYear() === this.anio && (hoy.getMonth() + 1) === this.mes && hoy.getDate() === dia;
                },

                // Mismos colores que LeadResponseOptions::COLORES_DISPONIBLES (paleta Tailwind).
                // Se resuelven a hex en JS (en vez de clases bg-{color}-500 dinámicas) porque
                // el color viene del backend en tiempo de ejecución y Tailwind no puede
                // purgar/generar clases construidas por JS.
                coloresHex: {
                    red:     { 400: '#F87171', dot: '#EF4444' },
                    slate:   { 400: '#94A3B8', dot: '#64748B' },
                    orange:  { 400: '#FB923C', dot: '#F97316' },
                    purple:  { 400: '#C084FC', dot: '#A855F7' },
                    teal:    { 400: '#2DD4BF', dot: '#14B8A6' },
                    sky:     { 400: '#38BDF8', dot: '#0EA5E9' },
                    rose:    { 400: '#FB7185', dot: '#F43F5E' },
                    amber:   { 400: '#FBBF24', dot: '#F59E0B' },
                    violet:  { 400: '#A78BFA', dot: '#8B5CF6' },
                    yellow:  { 400: '#FACC15', dot: '#EAB308' },
                    emerald: { 400: '#34D399', dot: '#10B981' },
                },
                colorHex(nombre, variante) {
                    const c = this.coloresHex[nombre] || this.coloresHex.slate;
                    if (variante === 'text') return c[400];
                    if (variante === undefined) return c.dot;
                    // variante numérica = opacidad porcentual sobre el tono principal (dot)
                    const hex = c.dot.replace('#', '');
                    const r = parseInt(hex.substring(0, 2), 16);
                    const g = parseInt(hex.substring(2, 4), 16);
                    const b = parseInt(hex.substring(4, 6), 16);
                    return `rgba(${r}, ${g}, ${b}, ${variante / 100})`;
                },

                async cargar() {
                    this.cargando = true;
                    this.diaSeleccionado = null;
                    try {
                        const res = await fetch(`{{ route('leads.calendario.eventos') }}?anio=${this.anio}&mes=${this.mes}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const eventos = await res.json();
                        const agrupado = {};
                        eventos.forEach(ev => {
                            const dia = parseInt(ev.fecha.split('-')[2], 10);
                            if (!agrupado[dia]) agrupado[dia] = [];
                            agrupado[dia].push(ev);
                        });
                        this.eventosPorDia = agrupado;
                    } finally {
                        this.cargando = false;
                    }
                },

                mesAnterior() {
                    this.mes--;
                    if (this.mes < 1) { this.mes = 12; this.anio--; }
                    this.cargar();
                },
                mesSiguiente() {
                    this.mes++;
                    if (this.mes > 12) { this.mes = 1; this.anio++; }
                    this.cargar();
                },
                seleccionarDia(dia) {
                    this.diaSeleccionado = dia;
                    this.horasDia = Array.from({ length: 24 }, (_, h) => ({ hora: h, eventos: [] }));
                    (this.eventosPorDia[dia] || []).forEach(ev => {
                        const hora = parseInt(ev.hora.split(':')[0], 10);
                        this.horasDia[hora].eventos.push(ev);
                    });
                    this.diaModalFecha = `${this.anio}-${String(this.mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                    this.diaModalLabel = dia + ' de ' + this.nombreMes;
                    this.diaModalAbierto = true;
                },
                cerrarDiaModal() {
                    this.diaModalAbierto = false;
                },
                irACrearLead(hora) {
                    window.location.href = `{{ url('/leads') }}?agendar_fecha=${this.diaModalFecha}&agendar_hora=${hora}`;
                },
            }
        }
    </script>
    @endonce

</x-app-layout>
