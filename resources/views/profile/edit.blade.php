<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 font-mono">Inicio</span>
            <span class="text-slate-700">/</span>
            <h1 class="text-sm font-semibold text-white">Perfil</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        @include('profile.partials.notification-settings')
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        @include('profile.partials.delete-user-form')
    </div>
</x-app-layout>
