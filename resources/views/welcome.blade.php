<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset("favicon.svg") }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fff1f7">
    <title>SGE | Sistema de Gestión Empresarial</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-fuchsia-100 text-rose-950 antialiased">
    <header class="bg-white/80 shadow-[0_8px_28px_rgba(190,24,93,0.10)] backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="/" class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-600 font-black text-white shadow-[0_8px_18px_rgba(190,24,93,0.30)]">
                    SGE
                </span>

                <span>
                    <span class="block font-bold tracking-wide text-rose-950">
                        Gestión Empresarial
                    </span>
                    <span class="block text-xs text-rose-700">
                        Control claro para tu negocio
                    </span>
                </span>
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-xl bg-rose-600 px-5 py-3 font-semibold text-white shadow-[0_7px_16px_rgba(190,24,93,0.25)] transition hover:-translate-y-0.5 hover:bg-rose-700 hover:shadow-[0_11px_22px_rgba(190,24,93,0.30)]">
                    Ir al panel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-xl bg-white px-5 py-3 font-semibold text-rose-800 shadow-[0_6px_16px_rgba(190,24,93,0.13)] transition hover:-translate-y-0.5 hover:bg-rose-100 hover:shadow-[0_10px_20px_rgba(190,24,93,0.18)]">
                    Iniciar sesión
                </a>
            @endauth
        </div>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 md:grid-cols-2 md:py-28">
            <div>
                <p class="mb-5 inline-flex rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-rose-700 shadow-[0_5px_14px_rgba(190,24,93,0.12)]">
                    Plataforma de gestión empresarial
                </p>

                <h1 class="max-w-2xl text-4xl font-black leading-tight tracking-tight text-rose-950 sm:text-5xl lg:text-6xl">
                    Administra tu negocio desde
                    <span class="text-rose-600">un solo lugar.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-rose-800">
                    Organiza productos, clientes, ventas y compras con información centralizada
                    y accesos según el rol de cada usuario.
                </p>

                <div class="mt-9 flex flex-wrap gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="rounded-xl bg-rose-600 px-6 py-3 font-bold text-white shadow-[0_8px_18px_rgba(190,24,93,0.28)] transition hover:-translate-y-0.5 hover:bg-rose-700 hover:shadow-[0_12px_24px_rgba(190,24,93,0.32)]">
                            Abrir mi panel
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="rounded-xl bg-rose-600 px-6 py-3 font-bold text-white shadow-[0_8px_18px_rgba(190,24,93,0.28)] transition hover:-translate-y-0.5 hover:bg-rose-700 hover:shadow-[0_12px_24px_rgba(190,24,93,0.32)]">
                            Ingresar al sistema
                        </a>
                    @endauth

                    <a href="#modulos"
                       class="rounded-xl bg-white/90 px-6 py-3 font-semibold text-rose-800 shadow-[0_7px_16px_rgba(190,24,93,0.13)] transition hover:-translate-y-0.5 hover:bg-rose-100 hover:shadow-[0_10px_20px_rgba(190,24,93,0.18)]">
                        Ver módulos
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] bg-gradient-to-br from-white via-rose-50 to-pink-100 p-6 shadow-[0_24px_55px_rgba(190,24,93,0.18),0_8px_18px_rgba(74,36,57,0.08)] sm:p-8">
                <div class="flex items-center justify-between pb-5">
                    <div>
                        <p class="text-sm font-medium text-rose-700">Resumen del sistema</p>
                        <h2 class="mt-1 text-xl font-bold text-rose-950">
                            Operación organizada
                        </h2>
                    </div>

                    <span class="rounded-full bg-rose-100 px-3 py-1 text-sm font-semibold text-rose-700 shadow-sm">
                        ● Activo
                    </span>
                </div>

                <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(190,24,93,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(190,24,93,0.16)]">
                        <p class="text-sm font-medium text-rose-700">Inventario</p>
                        <p class="mt-3 text-2xl font-bold text-rose-950">Productos</p>
                        <p class="mt-2 text-sm text-rose-600">Existencias y categorías</p>
                    </div>

                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(190,24,93,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(190,24,93,0.16)]">
                        <p class="text-sm font-medium text-rose-700">Comercial</p>
                        <p class="mt-3 text-2xl font-bold text-rose-950">Ventas</p>
                        <p class="mt-2 text-sm text-rose-600">Clientes y operaciones</p>
                    </div>

                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(190,24,93,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(190,24,93,0.16)]">
                        <p class="text-sm font-medium text-rose-700">Abastecimiento</p>
                        <p class="mt-3 text-2xl font-bold text-rose-950">Compras</p>
                        <p class="mt-2 text-sm text-rose-600">Proveedores y registros</p>
                    </div>

                    <div class="rounded-2xl bg-white/90 p-5 shadow-[0_8px_18px_rgba(190,24,93,0.10)] transition hover:-translate-y-1 hover:shadow-[0_13px_24px_rgba(190,24,93,0.16)]">
                        <p class="text-sm font-medium text-rose-700">Acceso</p>
                        <p class="mt-3 text-2xl font-bold text-rose-950">Permisos</p>
                        <p class="mt-2 text-sm text-rose-600">Funciones por usuario</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="modulos" class="bg-white/55">
            <div class="mx-auto max-w-7xl px-6 py-16">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-rose-600">
                    Módulos del SGE
                </p>

                <h2 class="mt-3 text-3xl font-bold text-rose-950">
                    Una base para trabajar mejor
                </h2>

                <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Productos', 'Gestiona inventario, precios y categorías.'],
                        ['Clientes', 'Centraliza la información de tus clientes.'],
                        ['Ventas', 'Registra operaciones y consulta su detalle.'],
                        ['Compras', 'Organiza pedidos y proveedores.'],
                    ] as [$title, $description])
                        <article class="rounded-2xl bg-white/90 p-6 shadow-[0_10px_24px_rgba(190,24,93,0.12)] transition hover:-translate-y-1 hover:shadow-[0_16px_30px_rgba(190,24,93,0.18)]">
                            <h3 class="text-lg font-bold text-rose-950">{{ $title }}</h3>
                            <p class="mt-2 leading-6 text-rose-800">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="px-6 py-6 text-center text-sm text-rose-700">
        SGE · Sistema de Gestión Empresarial
    </footer>
</body>
</html>