<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1220">
    <title>Iniciar sesión | SGE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 p-12 lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-cyan-400/10 blur-3xl"></div>

            <a href="/" class="relative flex w-fit items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-400 font-black text-slate-950">
                    SGE
                </span>
                <span>
                    <span class="block font-bold">Gestión Empresarial</span>
                    <span class="block text-xs text-slate-400">Control claro para tu negocio</span>
                </span>
            </a>

            <div class="relative max-w-xl py-12">
                <p class="mb-5 text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">
                    Sistema de Gestión Empresarial
                </p>
                <h1 class="text-5xl font-black leading-tight">
                    Todo tu negocio,
                    <span class="text-cyan-300">en un solo lugar.</span>
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-slate-300">
                    Administra inventario, clientes, ventas y compras desde una plataforma organizada y segura.
                </p>
            </div>

            <p class="relative text-sm text-slate-500">
                SGE · Acceso seguro para usuarios autorizados
            </p>
        </section>

        <section class="flex items-center justify-center bg-slate-100 px-6 py-12 text-slate-900 sm:px-10">
            <div class="w-full max-w-md">
                <a href="/" class="mb-10 flex w-fit items-center gap-3 lg:hidden">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-400 font-black text-slate-950">
                        SGE
                    </span>
                    <span class="font-bold">Gestión Empresarial</span>
                </a>

                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-widest text-cyan-700">
                        Bienvenida/o
                    </p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight">Inicia sesión</h2>
                    <p class="mt-2 text-slate-600">
                        Ingresa tus datos para acceder al sistema.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <p class="font-semibold">No se pudo iniciar sesión.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Correo electrónico
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-600/10"
                            placeholder="nombre@empresa.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                            Contraseña
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-600/10"
                            placeholder="Ingresa tu contraseña"
                        >
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember" class="flex items-center gap-2 text-sm text-slate-600">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-600"
                            >
                            Recuérdame
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-medium text-cyan-800 hover:text-cyan-600">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-900 px-5 py-3 font-bold text-white transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-700/20"
                    >
                        Ingresar al sistema
                    </button>
                </form>

                <p class="mt-8 text-center text-xs text-slate-500">
                    Si no tienes una cuenta, solicita acceso al administrador de tu empresa.
                </p>
            </div>
        </section>
    </main>
</body>
</html>