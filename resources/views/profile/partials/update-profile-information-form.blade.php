<section class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
    <header class="mb-5">
        <h2 class="text-sm font-semibold text-white">
            {{ __('Información del perfil') }}
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            {{ __('Actualiza la información de tu perfil y tu correo electrónico.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4 max-w-md">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-medium text-slate-400 mb-1.5">{{ __('Nombre') }}</label>
            <input id="name" name="name" type="text" class="input-dark @error('name') error @enderror"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-slate-400 mb-1.5">{{ __('Correo electrónico') }}</label>
            <input id="email" name="email" type="email" class="input-dark @error('email') error @enderror"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-xs text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-lg px-3 py-2">
                    {{ __('Tu correo electrónico no está verificado.') }}
                    <button form="send-verification" class="underline hover:text-amber-300 transition-colors">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-emerald-400">
                            {{ __('Se envió un nuevo enlace de verificación a tu correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-1">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                           bg-gradient-to-r from-sky-500 to-cyan-500
                           hover:from-sky-400 hover:to-cyan-400
                           shadow-[0_0_18px_rgba(14,165,233,0.35)]
                           hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                           transition-all duration-200 active:scale-[0.98]">
                {{ __('Guardar') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-emerald-400"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
