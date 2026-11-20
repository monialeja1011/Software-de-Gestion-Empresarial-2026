<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-brand-icon />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\CompanySetting::current()->trade_name ?: config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-brand-style />
    </head>
    <body class="font-sans antialiased">
        <div
            class="min-h-screen bg-gray-100"
            x-data="{
                ready: false,
                sidebarOpen: false,
                sidebarCollapsed: (() => {
                    try { return localStorage.getItem('sge.sidebarCollapsed') === '1'; } catch (e) { return false; }
                })(),
                toggleSidebar() {
                    if (window.matchMedia('(min-width: 1024px)').matches) {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                    } else {
                        this.sidebarOpen = !this.sidebarOpen;
                    }
                }
            }"
            x-init="
                $watch('sidebarCollapsed', value => {
                    try { localStorage.setItem('sge.sidebarCollapsed', value ? '1' : '0'); } catch (e) {}
                });
                $nextTick(() => ready = true);
            "
        >
            @include('layouts.navigation')

            <div
                class="min-h-screen lg:pl-72"
                :class="{ 'transition-[padding] duration-200': ready, 'lg:!pl-0': sidebarCollapsed }"
            >
                <header class="sticky top-0 z-30 flex h-20 items-center justify-between gap-4 border-b border-pink-100 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-4">
                        <button
                            type="button"
                            @click="toggleSidebar()"
                            class="shrink-0 rounded-lg bg-pink-100 p-2.5 text-pink-700 transition-colors hover:bg-pink-200 focus:outline-none focus:ring-2 focus:ring-pink-300"
                            aria-label="Mostrar u ocultar el menú"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <path d="M3 5h14M3 10h14M3 15h14" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        <span class="hidden text-sm text-slate-600 sm:inline">{{ Auth::user()->name }}</span>

                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <button
                                type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                class="flex items-center gap-1.5 rounded-lg bg-pink-100 px-4 py-2 text-sm font-medium text-pink-800 transition-colors hover:bg-pink-200 focus:outline-none focus:ring-2 focus:ring-pink-300"
                            >
                                Cuenta
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition.opacity.duration.100ms
                                class="absolute right-0 mt-2 w-52 rounded-xl border border-pink-100 bg-white py-1 shadow-lg"
                            >
                                <a href="{{ route('active-role.select') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-800">Cambiar rol activo</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-800">Perfil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-800">Cerrar sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-2">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded">
                            <ul class="list-disc ms-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <div
            x-data="{
                open: false,
                message: '',
                form: null,
                get danger() { return /elimin|anul|borr/i.test(this.message); },
                ask(detail) {
                    this.form = detail.form;
                    this.message = detail.message;
                    this.open = true;
                },
                accept() {
                    const form = this.form;
                    this.open = false;
                    if (!form) return;
                    form.dataset.confirmed = 'true';
                    if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
                }
            }"
            @sge-confirm.window="ask($event.detail)"
            @keydown.escape.window="open = false"
            x-show="open"
            x-cloak
            x-transition.opacity.duration.100ms
            class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 px-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-title"
        >
            <div @click.outside="open = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 id="confirm-title" class="text-lg font-semibold text-slate-900">Confirmar operación</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600" x-text="message"></p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-pink-50">
                        Cancelar
                    </button>
                    <button type="button" @click="accept()"
                            :class="danger ? 'bg-red-600 hover:bg-red-700' : 'bg-pink-700 hover:bg-pink-800'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </body>
</html>
