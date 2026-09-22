<x-guest-layout>

    {{-- Logo para móvil (panel izquierdo oculto) --}}
    <div class="lg:hidden flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500
                    flex items-center justify-center shadow-[0_0_20px_rgba(14,165,233,0.5)]">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375
                         m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375
                         m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375
                         m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
            </svg>
        </div>
        <span class="text-xl font-bold text-white">
            Estelar <span class="text-sky-400">ERP</span>
        </span>
    </div>

    {{-- Encabezado del formulario --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white">Bienvenido</h2>
        <p class="text-slate-400 text-sm mt-1">Ingresa a tu cuenta para continuar</p>
    </div>

    {{-- Estado de sesión (e.g. "enlace de restablecimiento enviado") --}}
    <x-auth-session-status class="mb-5" :status="session('status')" />

    {{-- ── Formulario ─────────────────────────────────────────────── --}}
    <form
        method="POST"
        action="{{ route('login') }}"
        x-data="{ showPassword: false }"
        class="space-y-5"
    >
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                Correo electrónico
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="correo@empresa.com"
                class="input-dark @error('email') error @enderror"
            >
            @error('email')
                <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                Contraseña
            </label>
            <div class="relative">
                <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="input-dark pr-12 @error('password') error @enderror"
                >
                {{-- Toggle mostrar/ocultar contraseña (Alpine.js) --}}
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    tabindex="-1"
                    class="absolute right-3 top-1/2 -translate-y-1/2
                           text-slate-500 hover:text-sky-400 transition-colors duration-200"
                    :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                >
                    {{-- Ojo abierto --}}
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5
                                 c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639
                                 C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    {{-- Ojo tachado --}}
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5
                                 c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5
                                 c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774
                                 M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21
                                 m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Recordarme + Olvidé mi contraseña --}}
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-slate-600 bg-slate-800
                           text-sky-500 focus:ring-sky-500/30 focus:ring-offset-0
                           focus:ring-offset-transparent transition-colors"
                >
                <span class="text-sm text-slate-400">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-sky-400 hover:text-sky-300 transition-colors duration-200"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        {{-- Botón de acceso --}}
        <button type="submit" class="btn-primary-blue mt-2">
            Iniciar sesión
        </button>

    </form>

    {{-- Footer --}}
    <p class="text-center text-xs text-slate-700 mt-8 font-mono">
        © {{ date('Y') }} Estelar Software Empresarial
    </p>

</x-guest-layout>
