<section
    x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
    class="bg-slate-900 border border-red-500/20 rounded-2xl p-6"
>
    <header class="mb-4">
        <h2 class="text-sm font-semibold text-white">
            {{ __('Eliminar cuenta') }}
        </h2>

        <p class="mt-1 text-xs text-slate-500 max-w-md">
            {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminar tu cuenta, descarga cualquier dato o información que quieras conservar.') }}
        </p>
    </header>

    <button type="button" @click="open = true"
            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-400
                   border border-red-500/30 hover:bg-red-500/10 hover:text-red-300
                   transition-all duration-200">
        {{ __('Eliminar cuenta') }}
    </button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 overflow-y-auto"
            style="display:none; z-index:60;"
            @keydown.escape.window="open = false"
        >
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="open = false"></div>

            <div class="relative min-h-full flex items-center justify-center p-4">
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="w-full max-w-md bg-slate-900 border border-red-500/30 rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)]"
                    @click.stop
                >
                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                        @csrf
                        @method('delete')

                        <h2 class="text-base font-bold text-white">
                            {{ __('¿Estás seguro de que quieres eliminar tu cuenta?') }}
                        </h2>

                        <p class="mt-2 text-xs text-slate-500">
                            {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Ingresa tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.') }}
                        </p>

                        <div class="mt-4">
                            <label for="password" class="sr-only">{{ __('Contraseña') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="input-dark @error('password', 'userDeletion') error @enderror"
                                placeholder="{{ __('Contraseña') }}"
                            >
                            @error('password', 'userDeletion')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-5 flex justify-end gap-3">
                            <button type="button" @click="open = false"
                                    class="px-4 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white
                                           border border-slate-700/60 hover:bg-slate-800 transition-colors">
                                {{ __('Cancelar') }}
                            </button>

                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                                           bg-red-600 hover:bg-red-500
                                           shadow-[0_0_18px_rgba(239,68,68,0.35)]
                                           hover:shadow-[0_0_28px_rgba(239,68,68,0.55)]
                                           transition-all duration-200 active:scale-[0.98]">
                                {{ __('Eliminar cuenta') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</section>
