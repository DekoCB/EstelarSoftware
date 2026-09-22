<section class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6">
    <header class="mb-5">
        <h2 class="text-sm font-semibold text-white">
            {{ __('Actualizar contraseña') }}
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            {{ __('Usa una contraseña larga y aleatoria para mantener tu cuenta segura.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4 max-w-md">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-medium text-slate-400 mb-1.5">{{ __('Contraseña actual') }}</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="input-dark @error('current_password', 'updatePassword') error @enderror" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-medium text-slate-400 mb-1.5">{{ __('Nueva contraseña') }}</label>
            <input id="update_password_password" name="password" type="password"
                   class="input-dark @error('password', 'updatePassword') error @enderror" autocomplete="new-password">
            @error('password', 'updatePassword')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-medium text-slate-400 mb-1.5">{{ __('Confirmar contraseña') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="input-dark @error('password_confirmation', 'updatePassword') error @enderror" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
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

            @if (session('status') === 'password-updated')
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
