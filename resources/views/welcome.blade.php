<!DOCTYPE html>
<html lang="es">
@php
    $company = \App\Models\CompanySetting::current();
    $tagline = $company->slogan ?: 'Control claro para tu negocio';

    $icons = [
        'box' => '<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
        'receipt' => '<path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2V3z"/><path d="M9 8h6M9 12h6"/>',
        'truck' => '<path d="M2 6h11v10H2z"/><path d="M13 9h4l4 4v3h-8z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
    ];

    $summary = [
        ['Inventario', 'Productos', 'Existencias y categorías', 'box'],
        ['Comercial', 'Ventas', 'Clientes y operaciones', 'receipt'],
        ['Abastecimiento', 'Compras', 'Proveedores y registros', 'truck'],
        ['Acceso', 'Permisos', 'Funciones por usuario', 'shield'],
    ];

    $modules = [
        ['Productos', 'Gestiona inventario, precios y categorías.', 'box'],
        ['Clientes', 'Centraliza la información de tus clientes.', 'user'],
        ['Ventas', 'Registra operaciones y consulta su detalle.', 'receipt'],
        ['Compras', 'Organiza pedidos y proveedores.', 'truck'],
    ];
@endphp
<head>
    <x-brand-icon />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf8f4">
    <title>{{ $company->trade_name }} | Plataforma de gestión empresarial</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-brand-style :neutral="true" />
    @include('partials.motion')
</head>

<body class="min-h-screen bg-gradient-to-br from-pink-50 via-pink-50 to-pink-100 text-pink-900 antialiased">
    <header class="sticky top-0 z-40 transition-all duration-300"
            x-data="{ scrolled: false }"
            @scroll.window="scrolled = window.scrollY > 8"
            :class="scrolled ? 'bg-white/90 shadow-[0_8px_28px_rgba(87,72,56,0.10)] backdrop-blur-md' : 'bg-transparent'">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-3">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="" class="h-11 w-11 rounded-2xl border border-pink-100 bg-white object-contain p-1.5">
                @else
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-600 text-sm font-extrabold text-white shadow-[0_8px_18px_rgba(87,72,56,0.30)]">
                        {{ $company->initials() }}
                    </span>
                @endif
                <span class="leading-tight">
                    <span class="block font-bold text-pink-900">{{ $company->trade_name }}</span>
                    <span class="block text-xs text-pink-700">{{ $tagline }}</span>
                </span>
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-xl bg-pink-600 px-5 py-2.5 font-semibold text-white shadow-[0_7px_16px_rgba(87,72,56,0.25)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_11px_22px_rgba(87,72,56,0.30)]">
                    Ir al panel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-xl bg-white px-5 py-2.5 font-semibold text-pink-800 shadow-[0_6px_16px_rgba(87,72,56,0.13)] transition hover:-translate-y-0.5 hover:bg-pink-100 hover:shadow-[0_10px_20px_rgba(87,72,56,0.18)]">
                    Iniciar sesión
                </a>
            @endauth
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="sge-drift absolute -left-24 top-0 h-96 w-96 rounded-full bg-pink-200/50 blur-3xl"></div>
            <div class="sge-drift absolute -right-20 bottom-0 h-[26rem] w-[26rem] rounded-full bg-pink-300/30 blur-3xl" style="animation-delay: -8s"></div>
            <div class="pointer-events-none absolute inset-0 opacity-50"
                 style="background-image: radial-gradient(rgb(var(--brand-300) / .55) 1px, transparent 1px); background-size: 24px 24px; -webkit-mask-image: linear-gradient(to bottom, #000, transparent 75%); mask-image: linear-gradient(to bottom, #000, transparent 75%);"></div>

            <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-6 py-16 md:grid-cols-2 md:py-24">
                <div>
                    <p class="sge-rise mb-6 inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-pink-700 shadow-[0_5px_14px_rgba(87,72,56,0.12)]">
                        <span class="sge-dot h-2 w-2 rounded-full bg-pink-500"></span>
                        Plataforma de gestión empresarial
                    </p>

                    <h1 class="max-w-2xl text-4xl font-extrabold leading-[1.08] tracking-tight text-pink-900 sm:text-5xl lg:text-6xl">
                        <span class="sge-rise block" style="--d: 120ms">Administra tu negocio desde</span>
                        <span class="sge-rise block" style="--d: 260ms"><span class="sge-shine-light">un solo lugar.</span></span>
                    </h1>

                    <p class="sge-rise mt-6 max-w-xl text-lg leading-8 text-pink-800" style="--d: 400ms">
                        Organiza productos, clientes, ventas y compras con información centralizada
                        y accesos según el rol de cada usuario.
                    </p>

                    <div class="sge-rise mt-9 flex flex-wrap gap-4" style="--d: 520ms">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="group inline-flex items-center gap-2 rounded-xl bg-pink-600 px-6 py-3 font-bold text-white shadow-[0_8px_18px_rgba(87,72,56,0.28)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_12px_24px_rgba(87,72,56,0.32)]">
                                Abrir mi panel
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="group inline-flex items-center gap-2 rounded-xl bg-pink-600 px-6 py-3 font-bold text-white shadow-[0_8px_18px_rgba(87,72,56,0.28)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_12px_24px_rgba(87,72,56,0.32)]">
                                Ingresar al sistema
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endauth

                        <a href="#modulos"
                           class="rounded-xl bg-white/90 px-6 py-3 font-semibold text-pink-800 shadow-[0_7px_16px_rgba(87,72,56,0.13)] transition hover:-translate-y-0.5 hover:bg-pink-100 hover:shadow-[0_10px_20px_rgba(87,72,56,0.18)]">
                            Ver módulos
                        </a>
                    </div>

                    <ul class="sge-rise mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-pink-700" style="--d: 640ms">
                        @foreach (['Acceso según el rol', 'Registro de cambios', 'Reportes en PDF'] as $feature)
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-pink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l4 4 10-10"/></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sge-rise" style="--d: 300ms">
                    <div class="sge-float rounded-[2rem] bg-gradient-to-br from-white via-pink-50 to-pink-100 p-6 shadow-[0_24px_55px_rgba(87,72,56,0.18),0_8px_18px_rgba(58,47,39,0.08)] sm:p-8">
                        <div class="flex items-center justify-between pb-4">
                            <div>
                                <p class="text-sm font-medium text-pink-700">Resumen del sistema</p>
                                <h2 class="mt-1 text-xl font-bold text-pink-900">Operación organizada</h2>
                            </div>
                            <span class="inline-flex items-center gap-2 rounded-full bg-pink-100 px-3 py-1 text-sm font-semibold text-pink-700 shadow-sm">
                                <span class="sge-dot h-2 w-2 rounded-full bg-pink-500"></span>
                                Activo
                            </span>
                        </div>

                        <div class="flex h-10 items-end gap-1.5 pb-1" aria-hidden="true">
                            @foreach ([35, 55, 42, 70, 52, 85, 60, 92, 68, 78, 58, 88] as $i => $height)
                                <span class="sge-bar w-full rounded-t bg-pink-300/70" style="height: {{ $height }}%; --d: {{ $i * 180 }}ms"></span>
                            @endforeach
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($summary as $i => [$area, $name, $detail, $icon])
                                <div class="sge-rise group rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(87,72,56,0.10)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(87,72,56,0.16)]" style="--d: {{ 600 + $i * 120 }}ms">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-pink-700">{{ $area }}</p>
                                        <svg class="h-5 w-5 text-pink-400 transition-colors duration-300 group-hover:text-pink-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$icon] !!}</svg>
                                    </div>
                                    <p class="mt-3 text-2xl font-bold text-pink-900">{{ $name }}</p>
                                    <p class="mt-2 text-sm text-pink-600">{{ $detail }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="modulos" class="scroll-mt-20 bg-white/55">
            <div class="mx-auto max-w-7xl px-6 py-20">
                <div class="sge-reveal max-w-2xl">
                    <p class="text-sm font-semibold text-pink-600">Módulos</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-pink-900">Una base para trabajar mejor</h2>
                </div>

                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($modules as $i => [$title, $description, $icon])
                        <article class="sge-reveal group rounded-2xl bg-white/90 p-6 shadow-[0_10px_24px_rgba(87,72,56,0.12)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_18px_34px_rgba(87,72,56,0.18)]" style="--d: {{ $i * 110 }}ms">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-100 text-pink-700 transition duration-300 group-hover:bg-pink-600 group-hover:text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$icon] !!}</svg>
                            </span>
                            <h3 class="mt-5 text-lg font-bold text-pink-900">{{ $title }}</h3>
                            <p class="mt-2 leading-6 text-pink-800">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-16">
            <div class="sge-reveal relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-pink-900 via-pink-800 to-pink-600 px-8 py-12 text-white sm:px-14">
                <div class="sge-drift absolute -right-16 -top-16 h-64 w-64 rounded-full bg-pink-200/20 blur-3xl"></div>
                <div class="relative flex flex-wrap items-center justify-between gap-6">
                    <div class="max-w-xl">
                        <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Entra y sigue con tu trabajo</h2>
                        <p class="mt-2 text-pink-100">Ingresa con tu usuario para ver el estado de tu negocio.</p>
                    </div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-white px-6 py-3 font-bold text-pink-800 shadow-[0_8px_18px_rgba(58,47,39,0.28)] transition hover:-translate-y-0.5 hover:bg-pink-50">Abrir mi panel</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl bg-white px-6 py-3 font-bold text-pink-800 shadow-[0_8px_18px_rgba(58,47,39,0.28)] transition hover:-translate-y-0.5 hover:bg-pink-50">Iniciar sesión</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="px-6 py-8 text-center text-sm text-pink-700">
        © {{ date('Y') }} {{ $company->trade_name }}
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('.sge-reveal');

            if (!('IntersectionObserver' in window)) {
                items.forEach((el) => el.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            items.forEach((el) => observer.observe(el));
        });
    </script>
</body>
</html>