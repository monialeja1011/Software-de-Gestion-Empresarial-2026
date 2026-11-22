<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Mi perfil</h1>
            <p class="mt-1 text-sm text-slate-500">Administra tus datos y la seguridad de tu cuenta.</p>
        </div>
    </x-slot>

    @php
        $activeRole = $user->roles->firstWhere('id', (int) session('active_role_id'));
        $roleName = $activeRole?->name ?? $user->getRoleNames()->first() ?? 'Usuario';
        $initials = collect(preg_split('/\s+/', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    @endphp

    <main class="min-h-screen bg-pink-50/60 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <section class="overflow-hidden rounded-3xl border border-pink-100 bg-white shadow-lg shadow-pink-900/5">
                <div class="bg-gradient-to-r from-pink-800 via-pink-700 to-pink-500 px-6 py-8 text-white sm:px-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-2xl font-bold ring-1 ring-white/30">
                            {{ $initials ?: 'U' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-white/75">Cuenta personal</p>
                            <h2 class="mt-1 truncate text-2xl font-semibold">{{ $user->name }}</h2>
                            <p class="mt-1 truncate text-sm text-white/80">{{ $user->email }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-white/15 px-3 py-1.5 text-sm font-medium ring-1 ring-white/25">
                                Rol: {{ ucfirst(str_replace('-', ' ', $roleName)) }}
                            </span>
                            <span class="rounded-full bg-emerald-400/20 px-3 py-1.5 text-sm font-medium ring-1 ring-emerald-100/40">
                                Cuenta activa
                            </span>
                        </div>
                    </div>
                </div>
                <div class="grid gap-4 px-6 py-5 text-sm text-slate-600 sm:grid-cols-2 sm:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nombre de usuario</p>
                        <p class="mt-1 font-medium text-slate-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Correo de acceso</p>
                        <p class="mt-1 font-medium text-slate-800">{{ $user->email }}</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-3xl border border-pink-100 bg-white p-6 shadow-lg shadow-pink-900/5 sm:p-8">
                    <div class="mb-6 flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-100 text-pink-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 0 0-16 0m12-14a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Información personal</h2>
                            <p class="mt-1 text-sm text-slate-500">Actualiza tu nombre y correo electrónico.</p>
                        </div>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </section>

                <section class="rounded-3xl border border-pink-100 bg-white p-6 shadow-lg shadow-pink-900/5 sm:p-8">
                    <div class="mb-6 flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-100 text-pink-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0 1 10 0v4m-11 0h12a2 2 0 0 1 2 2v7H4v-7a2 2 0 0 1 2-2Zm5 4v3" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Seguridad</h2>
                            <p class="mt-1 text-sm text-slate-500">Cambia tu contraseña para proteger el acceso.</p>
                        </div>
                    </div>
                    @include('profile.partials.update-password-form')
                </section>
            </div>

            @if ($user->hasRole('superadmin'))
                <section class="rounded-3xl border border-red-200 bg-red-50/70 p-6 shadow-sm sm:p-8">
                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-red-700">Zona restringida</p>
                        <h2 class="mt-1 text-lg font-semibold text-red-900">Eliminar cuenta</h2>
                        <p class="mt-1 text-sm text-red-800">Esta acción está disponible únicamente para superadministración.</p>
                    </div>
                    @include('profile.partials.delete-user-form')
                </section>
            @endif
        </div>
    </main>
</x-app-layout>
