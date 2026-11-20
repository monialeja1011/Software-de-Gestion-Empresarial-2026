<!DOCTYPE html>
<html lang="es">
<head>
    <x-brand-icon />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf8f4">
    <title>SGE | Sistema de Gestión Empresarial</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-brand-style :neutral="true" />
</head>

<body class="min-h-screen bg-gradient-to-br from-pink-50 via-pink-50 to-pink-100 text-pink-900 antialiased">
    <header class="bg-white/80 shadow-[0_8px_28px_rgba(87,72,56,0.10)] backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="/" class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-600 font-black text-white shadow-[0_8px_18px_rgba(87,72,56,0.30)]">
                    SGE
                </span>

                <span>
                    <span class="block font-bold tracking-wide text-pink-900">
                        Gestión Empresarial
                    </span>
                    <span class="block text-xs text-pink-700">
                        Control claro para tu negocio
                    </span>
                </span>
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-xl bg-pink-600 px-5 py-3 font-semibold text-white shadow-[0_7px_16px_rgba(87,72,56,0.25)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_11px_22px_rgba(87,72,56,0.30)]">
                    Ir al panel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-xl bg-white px-5 py-3 font-semibold text-pink-800 shadow-[0_6px_16px_rgba(87,72,56,0.13)] transition hover:-translate-y-0.5 hover:bg-pink-100 hover:shadow-[0_10px_20px_rgba(87,72,56,0.18)]">
                    Iniciar sesión
                </a>
            @endauth
        </div>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 md:grid-cols-2 md:py-28">
            <div>
                <p class="mb-5 inline-flex rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-pink-700 shadow-[0_5px_14px_rgba(87,72,56,0.12)]">
                    Plataforma de gestión empresarial
                </p>

                <h1 class="max-w-2xl text-4xl font-black leading-tight tracking-tight text-pink-900 sm:text-5xl lg:text-6xl">
                    Administra tu negocio desde
                    <span class="text-pink-600">un solo lugar.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-pink-800">
                    Organiza productos, clientes, ventas y compras con información centralizada
                    y accesos según el rol de cada usuario.
                </p>

                <div class="mt-9 flex flex-wrap gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="rounded-xl bg-pink-600 px-6 py-3 font-bold text-white shadow-[0_8px_18px_rgba(87,72,56,0.28)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_12px_24px_rgba(87,72,56,0.32)]">
                            Abrir mi panel
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="rounded-xl bg-pink-600 px-6 py-3 font-bold text-white shadow-[0_8px_18px_rgba(87,72,56,0.28)] transition hover:-translate-y-0.5 hover:bg-pink-700 hover:shadow-[0_12px_24px_rgba(87,72,56,0.32)]">
                            Ingresar al sistema
                        </a>
                    @endauth

                    <a href="#modulos"
                       class="rounded-xl bg-white/90 px-6 py-3 font-semibold text-pink-800 shadow-[0_7px_16px_rgba(87,72,56,0.13)] transition hover:-translate-y-0.5 hover:bg-pink-100 hover:shadow-[0_10px_20px_rgba(87,72,56,0.18)]">
                        Ver módulos
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] bg-gradient-to-br from-white via-pink-50 to-pink-100 p-6 shadow-[0_24px_55px_rgba(87,72,56,0.18),0_8px_18px_rgba(58,47,39,0.08)] sm:p-8">
                <div class="flex items-center justify-between pb-5">
                    <div>
                        <p class="text-sm font-medium text-pink-700">Resumen del sistema</p>
                        <h2 class="mt-1 text-xl font-bold text-pink-900">
                            Operación organizada
                        </h2>
                    </div>

                    <span class="rounded-full bg-pink-100 px-3 py-1 text-sm font-semibold text-pink-700 shadow-sm">
                        ● Activo
                    </span>
                </div>

                <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(87,72,56,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(87,72,56,0.16)]">
                        <p class="text-sm font-medium text-pink-700">Inventario</p>
                        <p class="mt-3 text-2xl font-bold text-pink-900">Productos</p>
                        <p class="mt-2 text-sm text-pink-600">Existencias y categorías</p>
                    </div>

                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(87,72,56,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(87,72,56,0.16)]">
                        <p class="text-sm font-medium text-pink-700">Comercial</p>
                        <p class="mt-3 text-2xl font-bold text-pink-900">Ventas</p>
                        <p class="mt-2 text-sm text-pink-600">Clientes y operaciones</p>
                    </div>

                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(87,72,56,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(87,72,56,0.16)]">
                        <p class="text-sm font-medium text-pink-700">Abastecimiento</p>
                        <p class="mt-3 text-2xl font-bold text-pink-900">Compras</p>
                        <p class="mt-2 text-sm text-pink-600">Proveedores y registros</p>
                    </div>

                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(87,72,56,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(87,72,56,0.16)]">
                        <p class="text-sm font-medium text-pink-700">Acceso</p>
                        <p class="mt-3 text-2xl font-bold text-pink-900">Permisos</p>
                        <p class="mt-2 text-sm text-pink-600">Funciones por usuario</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="modulos" class="bg-white/55">
            <div class="mx-auto max-w-7xl px-6 py-16">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-pink-600">
                    Módulos del SGE
                </p>

                <h2 class="mt-3 text-3xl font-bold text-pink-900">
                    Una base para trabajar mejor
                </h2>

                <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Productos', 'Gestiona inventario, precios y categorías.'],
                        ['Clientes', 'Centraliza la información de tus clientes.'],
                        ['Ventas', 'Registra operaciones y consulta su detalle.'],
                        ['Compras', 'Organiza pedidos y proveedores.'],
                    ] as [$title, $description])
                        <article class="rounded-2xl bg-white/90 p-6 shadow-[0_10px_24px_rgba(87,72,56,0.12)] transition hover:-translate-y-1 hover:shadow-[0_16px_30px_rgba(87,72,56,0.18)]">
                            <h3 class="text-lg font-bold text-pink-900">{{ $title }}</h3>
                            <p class="mt-2 leading-6 text-pink-800">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="px-6 py-6 text-center text-sm text-pink-700">
        SGE · Sistema de Gestión Empresarial
    </footer>
</body>
</html>