<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset("favicon.svg") }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fff1f7">
    <title>Iniciar sesión | SGE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-rose-50 text-rose-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-rose-950 via-rose-800 to-pink-600 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-pink-200/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-rose-300/20 blur-3xl"></div>

            <a href="/" class="relative flex w-fit items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white font-black text-rose-700 shadow-[0_8px_20px_rgba(74,36,57,0.25)]">
                    SGE
                </span>
                <span>
                    <span class="block font-bold">Gestión Empresarial</span>
                    <span class="block text-xs text-rose-100">Control claro para tu negocio</span>
                </span>
            </a>

            <div class="relative max-w-xl py-12">
                <p class="mb-5 text-sm font-semibold uppercase tracking-[0.2em] text-pink-100">
                    Sistema de Gestión Empresarial
                </p>
                <h1 class="text-5xl font-black leading-tight">
                    Todo tu negocio,
                    <span class="text-pink-200">en un solo lugar.</span>
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-rose-100">
                    Administra inventario, clientes, ventas y compras desde una plataforma organizada y segura.
                </p>
            </div>

            <p class="relative text-sm text-rose-100/80">
                SGE · Acceso seguro para usuarios autorizados
            </p>
        </section>

        <section class="flex items-center justify-center bg-gradient-to-br from-rose-50 via-pink-50 to-fuchsia-100 px-6 py-12 sm:px-10">
            <div class="w-full max-w-md rounded-[2rem] bg-white/85 p-7 shadow-[0_24px_55px_rgba(190,24,93,0.16),0_8px_18px_rgba(74,36,57,0.07)] backdrop-blur-sm sm:p-10">
                <a href="/" class="mb-10 flex w-fit items-center gap-3 lg:hidden">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-600 font-black text-white shadow-[0_8px_18px_rgba(190,24,93,0.25)]">
                        SGE
                    </span>
                    <span class="font-bold text-rose-950">Gestión Empresarial</span>
                </a>

                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-widest text-rose-600">
                        Bienvenida/o
                    </p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-rose-950">
                        Inicia sesión
                    </h2>
                    <p class="mt-2 text-rose-800">
                        Ingresa tus datos para acceder al sistema.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-2xl bg-rose-50 p-4 text-sm text-rose-800 shadow-[0_5px_14px_rgba(190,24,93,0.08)]">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl bg-rose-100 p-4 text-sm text-rose-900 shadow-[0_5px_14px_rgba(190,24,93,0.10)]">
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
                        <label for="email" class="mb-2 block text-sm font-semibold text-rose-900">
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
                            class="w-full rounded-xl border-0 bg-rose-50/70 px-4 py-3 text-rose-950 shadow-[inset_0_2px_5px_rgba(190,24,93,0.06),0_4px_12px_rgba(190,24,93,0.07)] outline-none transition placeholder:text-rose-400 focus:ring-4 focus:ring-rose-300/60"
                            placeholder="nombre@empresa.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-rose-900">
                            Contraseña
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border-0 bg-rose-50/70 px-4 py-3 text-rose-950 shadow-[inset_0_2px_5px_rgba(190,24,93,0.06),0_4px_12px_rgba(190,24,93,0.07)] outline-none transition placeholder:text-rose-400 focus:ring-4 focus:ring-rose-300/60"
                            placeholder="Ingresa tu contraseña"
                        >
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember" class="flex items-center gap-2 text-sm text-rose-800">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="rounded border-rose-300 text-rose-600 focus:ring-rose-400"
                            >
                            Recuérdame
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-semibold text-rose-700 transition hover:text-rose-900">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-rose-600 px-5 py-3 font-bold text-white shadow-[0_9px_20px_rgba(190,24,93,0.26)] transition hover:-translate-y-0.5 hover:bg-rose-700 hover:shadow-[0_13px_26px_rgba(190,24,93,0.32)] focus:outline-none focus:ring-4 focus:ring-rose-300/70"
                    >
                        Ingresar al sistema
                    </button>
                </form>

                <p class="mt-8 text-center text-xs leading-5 text-rose-700">
                    Si no tienes una cuenta, solicita acceso al administrador de tu empresa.
                </p>
            </div>
        </section>
    </main>
</body>
</html>