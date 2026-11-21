<!DOCTYPE html>
<html lang="es">
@php
    $company = \App\Models\CompanySetting::current();
    $tagline = $company->slogan ?: 'Control claro para tu negocio';
@endphp
<head>
    <x-brand-icon />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf8f4">
    <title>Iniciar sesión | {{ $company->trade_name }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-brand-style :neutral="true" />
    @include('partials.motion')
</head>
<body class="min-h-screen bg-pink-50 text-pink-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-pink-900 via-pink-800 to-pink-600 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="sge-drift absolute -right-24 -top-24 h-96 w-96 rounded-full bg-pink-200/20 blur-3xl"></div>
            <div class="sge-drift absolute -bottom-32 -left-24 h-[28rem] w-[28rem] rounded-full bg-pink-300/20 blur-3xl" style="animation-delay: -9s"></div>
            <div class="pointer-events-none absolute inset-0 opacity-40"
                 style="background-image: radial-gradient(rgb(var(--brand-200) / .35) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: linear-gradient(to bottom right, #000, transparent 70%); mask-image: linear-gradient(to bottom right, #000, transparent 70%);"></div>

            <a href="/" class="sge-fade relative flex w-fit items-center gap-3">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="" class="h-11 w-11 rounded-2xl bg-white object-contain p-1.5 shadow-[0_8px_20px_rgba(58,47,39,0.25)]">
                @else
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-sm font-extrabold text-pink-700 shadow-[0_8px_20px_rgba(58,47,39,0.25)]">
                        {{ $company->initials() }}
                    </span>
                @endif
                <span class="leading-tight">
                    <span class="block font-bold">{{ $company->trade_name }}</span>
                    <span class="block text-xs text-pink-100">{{ $tagline }}</span>
                </span>
            </a>

            <div class="relative max-w-xl">
                <h1 class="text-5xl font-extrabold leading-[1.08] tracking-tight">
                    <span class="sge-rise block" style="--d: 150ms">Todo tu negocio,</span>
                    <span class="sge-rise block" style="--d: 300ms"><span class="sge-shine-dark">en un solo lugar.</span></span>
                </h1>
                <p class="sge-rise mt-6 max-w-lg text-lg leading-8 text-pink-100" style="--d: 450ms">
                    Administra inventario, clientes, ventas y compras desde una plataforma organizada y segura.
                </p>

                <div class="sge-fade relative mt-12 h-60 max-w-md" style="--d: 700ms" aria-hidden="true">
                    <div class="sge-float absolute left-0 top-0 w-56 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-md">
                        <p class="text-xs font-medium text-pink-100">Ventas</p>
                        <div class="mt-3 flex h-16 items-end gap-1.5">
                            @foreach ([40, 65, 50, 80, 60, 95, 75] as $i => $height)
                                <span class="sge-bar w-full rounded-t bg-pink-100/80" style="height: {{ $height }}%; --d: {{ $i * 220 }}ms"></span>
                            @endforeach
                        </div>
                    </div>

                    <div class="sge-float absolute right-0 top-10 w-52 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-md" style="--d: -2.5s">
                        <p class="text-xs font-medium text-pink-100">Inventario</p>
                        <div class="mt-4 space-y-3">
                            @foreach ([92, 68, 80] as $i => $width)
                                <span class="block h-1.5 rounded-full bg-pink-100/10">
                                    <span class="sge-line block h-1.5 rounded-full bg-pink-100/80" style="width: {{ $width }}%; --d: {{ $i * 500 }}ms"></span>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="sge-float absolute bottom-0 left-20 w-52 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-md" style="--d: -4.5s">
                        <p class="text-xs font-medium text-pink-100">Compras</p>
                        <ul class="mt-3 space-y-2 text-xs text-pink-50">
                            <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-pink-200"></span>Orden creada</li>
                            <li class="flex items-center gap-2"><span class="sge-dot h-2 w-2 rounded-full bg-pink-100"></span>En camino</li>
                            <li class="flex items-center gap-2 text-pink-200/70"><span class="h-2 w-2 rounded-full border border-pink-200/60"></span>Recibida</li>
                        </ul>
                    </div>
                </div>
            </div>

            <p class="relative text-sm text-pink-100/80">
                © {{ date('Y') }} {{ $company->trade_name }} · Acceso seguro para usuarios autorizados
            </p>
        </section>

        <section class="relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-pink-50 via-pink-50 to-pink-100 px-6 py-12 sm:px-10">
            <div class="sge-drift absolute -left-20 top-10 h-72 w-72 rounded-full bg-pink-200/50 blur-3xl"></div>
            <div class="sge-drift absolute -bottom-24 right-0 h-80 w-80 rounded-full bg-pink-300/30 blur-3xl" style="animation-delay: -7s"></div>

            <div class="sge-rise relative w-full max-w-md rounded-[2rem] bg-white/85 p-7 shadow-[0_24px_55px_rgba(87,72,56,0.16),0_8px_18px_rgba(58,47,39,0.07)] backdrop-blur-sm sm:p-10">
                <a href="/" class="mb-10 flex w-fit items-center gap-3 lg:hidden">
                    @if ($company->logoUrl())
                        <img src="{{ $company->logoUrl() }}" alt="" class="h-11 w-11 rounded-2xl border border-pink-100 bg-white object-contain p-1.5">
                    @else
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-600 text-sm font-extrabold text-white shadow-[0_8px_18px_rgba(87,72,56,0.25)]">
                            {{ $company->initials() }}
                        </span>
                    @endif
                    <span class="font-bold text-pink-900">{{ $company->trade_name }}</span>
                </a>

                <div class="sge-rise mb-8" style="--d: 120ms">
                    <p class="text-sm font-semibold text-pink-600">Bienvenida/o</p>
                    <h2 class="mt-1 text-3xl font-extrabold tracking-tight text-pink-900">Inicia sesión</h2>
                    <p class="mt-2 text-pink-800">Ingresa tus datos para acceder al sistema.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-2xl bg-rose-50 p-4 text-sm text-rose-800 shadow-[0_5px_14px_rgba(87,72,56,0.08)]">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="sge-shake mb-5 rounded-2xl bg-rose-100 p-4 text-sm text-rose-900 shadow-[0_5px_14px_rgba(87,72,56,0.10)]" role="alert">
                        <p class="font-semibold">No se pudo iniciar sesión.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5"
                      x-data="{ show: false, loading: false }"
                      @submit="loading = true"
                      @pageshow.window="loading = false">
                    @csrf

                    <div class="sge-rise" style="--d: 220ms">
                        <label for="email" class="mb-2 block text-sm font-semibold text-pink-900">Correo electrónico</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-pink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/>
                            </svg>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="w-full rounded-xl border-0 bg-pink-50/70 py-3 pl-12 pr-4 text-pink-900 shadow-[inset_0_2px_5px_rgba(87,72,56,0.06),0_4px_12px_rgba(87,72,56,0.07)] outline-none transition duration-200 placeholder:text-pink-400 focus:bg-white focus:ring-4 focus:ring-pink-300/60"
                                placeholder="nombre@empresa.com"
                            >
                        </div>
                    </div>

                    <div class="sge-rise" style="--d: 320ms">
                        <label for="password" class="mb-2 block text-sm font-semibold text-pink-900">Contraseña</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-pink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/>
                            </svg>
                            <input
                                id="password"
                                name="password"
                                :type="show ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                class="w-full rounded-xl border-0 bg-pink-50/70 py-3 pl-12 pr-12 text-pink-900 shadow-[inset_0_2px_5px_rgba(87,72,56,0.06),0_4px_12px_rgba(87,72,56,0.07)] outline-none transition duration-200 placeholder:text-pink-400 focus:bg-white focus:ring-4 focus:ring-pink-300/60"
                                placeholder="Ingresa tu contraseña"
                            >
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-pink-500 transition hover:text-pink-800 focus:outline-none focus:ring-2 focus:ring-pink-300"
                                    :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
                                    <path x-show="show" x-cloak d="M4 4l16 16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="sge-rise flex items-center justify-between gap-4" style="--d: 400ms">
                        <label for="remember" class="flex items-center gap-2 text-sm text-pink-800">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="rounded border-pink-300 text-pink-600 focus:ring-pink-400"
                            >
                            Recuérdame
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-semibold text-pink-700 transition hover:text-pink-900">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <div class="sge-rise" style="--d: 480ms">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="group relative w-full overflow-hidden rounded-xl bg-pink-600 px-5 py-3 font-bold text-white shadow-[0_9px_20px_rgba(87,72,56,0.26)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_13px_26px_rgba(87,72,56,0.32)] focus:outline-none focus:ring-4 focus:ring-pink-300/70 disabled:cursor-wait disabled:opacity-80"
                        >
                            <span class="pointer-events-none absolute inset-y-0 left-0 w-1/3 -translate-x-full -skew-x-12 bg-white/25 transition-transform duration-700 group-hover:translate-x-[400%]"></span>

                            <span x-show="!loading" class="relative inline-flex items-center justify-center gap-2">
                                Ingresar al sistema
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>

                            <span x-show="loading" x-cloak class="relative inline-flex items-center justify-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M21 12a9 9 0 00-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                                Ingresando…
                            </span>
                        </button>
                    </div>
                </form>

                <p class="sge-rise mt-8 text-center text-xs leading-5 text-pink-700" style="--d: 560ms">
                    Si no tienes una cuenta, solicita acceso al administrador de tu empresa.
                </p>
            </div>
        </section>
    </main>
</body>
</html>