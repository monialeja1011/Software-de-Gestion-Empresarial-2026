<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1220">
    <title>SGE | Sistema de Gestión Empresarial</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <header class="border-b border-white/10">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="/" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-400 font-black text-slate-950">
                    SGE
                </span>
                <span>
                    <span class="block font-bold tracking-wide">Gestión Empresarial</span>
                    <span class="block text-xs text-slate-400">Control claro para tu negocio</span>
                </span>
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-lg bg-cyan-400 px-5 py-2.5 font-semibold text-slate-950 hover:bg-cyan-300">
                    Ir al panel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-lg border border-white/20 px-5 py-2.5 font-semibold hover:border-cyan-300 hover:text-cyan-300">
                    Iniciar sesión
                </a>
            @endauth
        </div>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 md:grid-cols-2 md:py-28">
            <div>
                <p class="mb-5 inline-flex rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-sm text-cyan-200">
                    Plataforma de gestión empresarial
                </p>

                <h1 class="max-w-2xl text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Administra tu negocio desde
                    <span class="text-cyan-300">un solo lugar.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">
                    Organiza productos, clientes, ventas y compras con información centralizada
                    y accesos según el rol de cada usuario.
                </p>

                <div class="mt-9 flex flex-wrap gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="rounded-lg bg-cyan-400 px-6 py-3 font-bold text-slate-950 hover:bg-cyan-300">
                            Abrir mi panel
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="rounded-lg bg-cyan-400 px-6 py-3 font-bold text-slate-950 hover:bg-cyan-300">
                            Ingresar al sistema
                        </a>
                    @endauth

                    <a href="#modulos"
                       class="rounded-lg border border-white/20 px-6 py-3 font-semibold hover:border-white/50">
                        Ver módulos
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-800 to-slate-900 p-6 shadow-2xl shadow-cyan-950/40">
                <div class="flex items-center justify-between border-b border-white/10 pb-5">
                    <div>
                        <p class="text-sm text-slate-400">Resumen del sistema</p>
                        <h2 class="mt-1 text-xl font-bold">Operación organizada</h2>
                    </div>
                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-sm text-emerald-300">
                        ● Activo
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-slate-950/70 p-5">
                        <p class="text-sm text-slate-400">Inventario</p>
                        <p class="mt-3 text-2xl font-bold">Productos</p>
                        <p class="mt-2 text-sm text-cyan-200">Existencias y categorías</p>
                    </div>
                    <div class="rounded-2xl bg-slate-950/70 p-5">
                        <p class="text-sm text-slate-400">Comercial</p>
                        <p class="mt-3 text-2xl font-bold">Ventas</p>
                        <p class="mt-2 text-sm text-cyan-200">Clientes y operaciones</p>
                    </div>
                    <div class="rounded-2xl bg-slate-950/70 p-5">
                        <p class="text-sm text-slate-400">Abastecimiento</p>
                        <p class="mt-3 text-2xl font-bold">Compras</p>
                        <p class="mt-2 text-sm text-cyan-200">Proveedores y registros</p>
                    </div>
                    <div class="rounded-2xl bg-slate-950/70 p-5">
                        <p class="text-sm text-slate-400">Acceso</p>
                        <p class="mt-3 text-2xl font-bold">Permisos</p>
                        <p class="mt-2 text-sm text-cyan-200">Funciones por usuario</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="modulos" class="border-t border-white/10 bg-slate-900/60">
            <div class="mx-auto max-w-7xl px-6 py-16">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">
                    Módulos del SGE
                </p>
                <h2 class="mt-3 text-3xl font-bold">Una base para trabajar mejor</h2>

                <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Productos', 'Gestiona inventario, precios y categorías.'],
                        ['Clientes', 'Centraliza la información de tus clientes.'],
                        ['Ventas', 'Registra operaciones y consulta su detalle.'],
                        ['Compras', 'Organiza pedidos y proveedores.'],
                    ] as [$title, $description])
                        <article class="rounded-2xl border border-white/10 bg-slate-950 p-6">
                            <h3 class="text-lg font-bold">{{ $title }}</h3>
                            <p class="mt-2 leading-6 text-slate-400">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 px-6 py-6 text-center text-sm text-slate-500">
        SGE · Sistema de Gestión Empresarial
    </footer>
</body>
</html>