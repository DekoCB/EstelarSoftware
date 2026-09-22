<div
    wire:poll.15s.keep-alive
    x-data="leadAlarm()"
    x-init="init()"
    data-avisos="{{ $avisos->map(fn($a) => [
        'id'      => $a['meeting']->id,
        'momento' => $a['momento'],
        'hora'    => $a['meeting']->fecha_hora->toIso8601String(),
        'nota'    => $a['meeting']->nota,
        'nombre'  => $a['meeting']->lead->nombre,
        'empresa' => $a['meeting']->lead->empresa,
        'url'     => route('leads.show', $a['meeting']->lead_id),
    ])->values()->toJson() }}"
    @keydown.escape.window="dismiss()"
>
    <template x-teleport="body">
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 overflow-y-auto"
            style="display:none; z-index:100;"
        >
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

            <div class="relative min-h-full flex items-center justify-center p-4">
                <div
                    x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="w-full max-w-md bg-slate-900 border border-amber-500/30 rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)]"
                    @click.stop
                >
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-800/80">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-sm font-bold text-white" x-text="current?.titulo"></h2>
                            <p class="text-[11px] text-slate-500" x-text="current?.subtitulo"></p>
                        </div>
                    </div>

                    <div class="px-6 py-5 space-y-1">
                        <p class="text-base font-semibold text-slate-100" x-text="current?.nombre"></p>
                        <p class="text-xs text-slate-500" x-text="current?.empresa || 'Sin empresa'"></p>
                        <p class="text-sm font-mono text-amber-400 mt-2" x-text="current?.horaTexto"></p>
                        <p class="text-xs text-slate-400 mt-2" x-show="current?.nota" x-text="current?.nota"></p>
                    </div>

                    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-800/80">
                        <button
                            type="button"
                            @click="dismiss()"
                            class="px-3 py-2 text-xs font-medium text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors duration-150"
                        >
                            Silenciar
                        </button>
                        <a
                            :href="current?.url"
                            @click="dismiss()"
                            class="px-3 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-lg transition-colors duration-150"
                        >
                            Ver lead
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function leadAlarm() {
        const TEXTOS = {
            antes:   { titulo: 'Reunión próxima',           subtitulo: 'Aviso antes de la hora' },
            hora:    { titulo: 'Es la hora de tu reunión',  subtitulo: 'Empieza ahora' },
            despues: { titulo: '¿Se realizó la reunión?',   subtitulo: 'Ya pasó la hora agendada' },
        };

        return {
            modalOpen: false,
            current: null,
            queue: [],
            seen: new Set(),
            dismissed: new Set(),

            init() {
                this.tick();
                setInterval(() => this.tick(), 5000);
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) this.tick();
                });
            },

            // Cada aviso (antes / hora / despues) de cada reunión suena una sola vez
            // por pestaña, gracias a esta clave compuesta id+momento. El servidor no
            // marca nada como "visto" solo por leerlo (ver LeadMeeting::marcarAvisoVisto),
            // así que el mismo aviso puede seguir llegando en polls sucesivos hasta que
            // el usuario lo reconozca en alguna pestaña — pero aquí nunca se repite.
            tick() {
                let avisos = [];
                try {
                    avisos = JSON.parse(this.$el.dataset.avisos || '[]');
                } catch (e) {
                    avisos = [];
                }

                for (const a of avisos) {
                    const key = a.id + '_' + a.momento;
                    if (this.seen.has(key)) continue;
                    this.seen.add(key);
                    this.ring(a);
                }
            },

            ring(a) {
                const key = a.id + '_' + a.momento;
                if (this.dismissed.has(key)) return;

                const textos = TEXTOS[a.momento] || TEXTOS.antes;
                this.queue.push({
                    id: a.id,
                    momento: a.momento,
                    key,
                    nombre: a.nombre,
                    empresa: a.empresa,
                    nota: a.nota,
                    url: a.url,
                    titulo: textos.titulo,
                    subtitulo: textos.subtitulo,
                    horaTexto: new Date(a.hora).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' }),
                });
                if (!this.modalOpen) this.showNext();
            },

            showNext() {
                if (this.queue.length === 0) return;
                this.current = this.queue.shift();
                this.modalOpen = true;
                window.__leadAlarmPlayTone();
                this.notifyDesktop();
            },

            notifyDesktop() {
                if (!window.Notification || Notification.permission !== 'granted') return;
                try {
                    const n = new Notification(this.current.titulo + ': ' + this.current.nombre, {
                        body: this.current.horaTexto + (this.current.nota ? ' — ' + this.current.nota : ''),
                        tag: 'lead-alarm-' + this.current.key,
                        requireInteraction: true,
                    });
                    n.onclick = () => {
                        window.focus();
                        window.location.href = this.current.url;
                        n.close();
                    };
                } catch (e) {}
            },

            dismiss() {
                if (this.current) {
                    this.dismissed.add(this.current.key);
                    this.queue = this.queue.filter(q => q.key !== this.current.key);
                    // Recién aquí se marca visto en el servidor: hasta que el usuario
                    // no reconoce la alarma en alguna pestaña, sigue sonando en todas.
                    this.$wire.marcarVisto(this.current.id, this.current.momento);
                }
                this.modalOpen = false;
                setTimeout(() => this.showNext(), 300);
            },
        };
    }

    // Reutilizable: la secuencia de tonos de la alarma. Separada de leadAlarm()
    // para poder dispararla también desde el botón "Probar sonido" del perfil.
    window.__leadAlarmPlayTone = function () {
        const ctx = window.__leadAlarmCtx;
        if (!ctx) return;
        const now = ctx.currentTime;
        const freqs = [1046.5, 830.6];
        const step = 0.09;
        for (let i = 0; i < 6; i++) {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'square';
            osc.frequency.value = freqs[i % 2];
            osc.connect(gain);
            gain.connect(ctx.destination);
            const start = now + i * step;
            gain.gain.setValueAtTime(0.0001, start);
            gain.gain.exponentialRampToValueAtTime(0.14, start + 0.01);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + step * 0.92);
            osc.start(start);
            osc.stop(start + step);
        }
    };

    // Desbloquea el AudioContext (requiere un gesto del usuario por política de
    // autoplay del navegador) y de paso reproduce un tono, para el botón
    // "Probar sonido" del perfil.
    window.__leadAlarmUnlock = function () {
        if (!window.__leadAlarmCtx) {
            window.__leadAlarmCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        window.__leadAlarmCtx.resume();
    };

    window.__leadAlarmTestSound = function () {
        window.__leadAlarmUnlock();
        window.__leadAlarmPlayTone();
    };

    if (!window.__leadAlarmAudioBootstrapped) {
        window.__leadAlarmAudioBootstrapped = true;

        // El permiso de notificaciones se pide explícitamente desde el botón
        // "Activar notificaciones" del perfil (ver profile/partials/notification-settings.blade.php),
        // nunca automáticamente al cargar la página.
        // El audio sí requiere un gesto del usuario (política de autoplay del navegador).
        const unlock = window.__leadAlarmUnlock;
        document.addEventListener('pointerdown', unlock, { once: true });
        document.addEventListener('keydown', unlock, { once: true });
    }
</script>
