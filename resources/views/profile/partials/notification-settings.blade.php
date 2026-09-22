<div
    x-data="{
        permiso: (window.Notification ? Notification.permission : 'unsupported'),
        pedir() {
            if (!window.Notification) return;
            Notification.requestPermission().then(p => this.permiso = p);
        },
    }"
    class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6"
>
    <h3 class="text-sm font-semibold text-white mb-1">Notificaciones de reuniones</h3>
    <p class="text-xs text-slate-500 mb-4">Avisa en este navegador cuando se acerque la hora de una reunión, aunque estés en otra pestaña.</p>

    <template x-if="permiso === 'unsupported'">
        <p class="text-xs text-slate-500">Este navegador no soporta notificaciones.</p>
    </template>

    <template x-if="permiso !== 'unsupported'">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2 text-sm">
                <template x-if="permiso === 'granted'">
                    <span class="inline-flex items-center gap-1.5 text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Activadas — permiso permitido
                    </span>
                </template>
                <template x-if="permiso === 'denied'">
                    <span class="inline-flex items-center gap-1.5 text-red-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Bloqueadas por el navegador
                    </span>
                </template>
                <template x-if="permiso === 'default'">
                    <span class="inline-flex items-center gap-1.5 text-slate-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-slate-600"></span> No configuradas
                    </span>
                </template>
            </div>

            <button type="button" x-show="permiso === 'default'" @click="pedir()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-white
                           bg-gradient-to-r from-sky-500 to-cyan-500
                           hover:from-sky-400 hover:to-cyan-400 transition-all duration-200">
                Activar notificaciones
            </button>
        </div>
    </template>

    <template x-if="permiso === 'denied'">
        <p class="text-xs text-slate-500 mt-3">
            Las bloqueaste antes y el navegador ya no permite volver a preguntar desde aquí.
            Actívalas manualmente desde el ícono de candado/información junto a la dirección del sitio, en la configuración de notificaciones.
        </p>
    </template>

    <div class="flex items-center justify-between gap-4 flex-wrap mt-4 pt-4 border-t border-slate-800/60"
         x-data="{ sonando: false }">
        <div>
            <p class="text-xs font-medium text-slate-300">Sonido de la alarma</p>
            <p class="text-xs text-slate-500 mt-0.5">
                El navegador bloquea el audio hasta que hagas clic aquí una vez. Sin esto, el aviso puede aparecer en pantalla pero sin sonar.
            </p>
        </div>
        <button type="button"
                @click="window.__leadAlarmTestSound?.(); sonando = true; setTimeout(() => sonando = false, 1000)"
                class="px-4 py-2 rounded-xl text-xs font-semibold text-white
                       bg-gradient-to-r from-sky-500 to-cyan-500
                       hover:from-sky-400 hover:to-cyan-400 transition-all duration-200
                       flex-shrink-0">
            <span x-show="!sonando">🔊 Probar sonido</span>
            <span x-show="sonando" x-cloak>♪ Sonando…</span>
        </button>
    </div>
</div>
