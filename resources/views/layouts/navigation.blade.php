@php
    $branding = \App\Models\CompanySetting::current();
    $user = auth()->user();

    $sections = [
        'Inventario' => [
            ['ver-productos', 'products.index', 'products.*', 'Productos'],
            ['ver-categorias', 'categories.index', 'categories.*', 'Categorías'],
            ['ver-inventario', 'inventory.index', 'inventory.*', 'Kardex'],
        ],
        'Comercial' => [
            ['ver-ventas', 'sales.index', 'sales.*', 'Ventas'],
            ['ver-clientes', 'clients.index', 'clients.*', 'Clientes'],
        ],
        'Abastecimiento' => [
            ['ver-compras', 'purchases.index', 'purchases.*', 'Compras'],
            ['ver-proveedores', 'providers.index', 'providers.*', 'Proveedores'],
        ],
        'Administración' => [
            ['ver-metodos-pago', 'payment-methods.index', 'payment-methods.*', 'Métodos de pago'],
            ['gestionar-usuarios', 'users.index', 'users.*', 'Usuarios y roles'],
            ['ver-auditoria', 'audit.index', 'audit.*', 'Auditoría'],
            ['ver-identidad', 'company-settings.edit', 'company-settings.*', 'Identidad corporativa'],
        ],
    ];

    $linkBase = 'relative flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors';
    $linkActive = 'bg-pink-50 font-semibold text-pink-800 before:absolute before:bottom-2 before:left-0 before:top-2 before:w-1 before:rounded-full before:bg-pink-600';
    $linkIdle = 'text-slate-600 hover:bg-pink-50 hover:text-pink-800';
@endphp

<div>
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"
    ></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-pink-100 bg-gradient-to-b from-pink-50 via-white to-white shadow-xl lg:translate-x-0"
        :class="{
            'transition-transform duration-200': ready,
            '!translate-x-0': sidebarOpen,
            'lg:!-translate-x-full': sidebarCollapsed
        }"
    >
        <div class="flex items-center justify-between gap-3 border-b border-pink-100 px-5 py-5">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                @if ($branding->logoUrl())
                    <img src="{{ $branding->logoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-xl border border-pink-100 bg-white object-contain p-1.5">
                @else
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-700 text-sm font-semibold tracking-wide text-white">
                        {{ $branding->initials() }}
                    </span>
                @endif
                <span class="min-w-0 leading-tight">
                    <span class="block line-clamp-2 text-[15px] font-semibold text-slate-900">{{ $branding->trade_name }}</span>
                    <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $branding->slogan ?: 'Control de tu negocio' }}</span>
                </span>
            </a>

            <button
                type="button"
                @click="sidebarOpen = false"
                class="rounded-lg p-2 text-slate-500 hover:bg-pink-100 lg:hidden"
                aria-label="Cerrar menú"
            >
                ✕
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
            <a href="{{ route('dashboard') }}"
               @click="sidebarOpen = false"
               class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $linkActive : $linkIdle }}">
                Panel principal
            </a>

            @foreach ($sections as $title => $items)
                @php
                    $visible = collect($items)->filter(fn ($item) => $user->can($item[0]));
                @endphp

                @if ($visible->isNotEmpty())
                    <section>
                        <p class="mb-1.5 px-3 text-xs font-semibold text-slate-400">{{ $title }}</p>
                        <div class="space-y-0.5">
                            @foreach ($visible as [$permission, $route, $pattern, $label])
                                <a href="{{ route($route) }}"
                                   @click="sidebarOpen = false"
                                   class="{{ $linkBase }} {{ request()->routeIs($pattern) ? $linkActive : $linkIdle }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-pink-100 p-4">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-sm font-semibold text-pink-800">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                Rol: <span class="font-medium text-pink-800">{{ $user->getRoleNames()->join(', ') ?: 'Sin rol asignado' }}</span>
            </p>
        </div>
    </aside>
</div>