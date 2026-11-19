<div>
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"
    ></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-pink-100 bg-gradient-to-b from-pink-100 via-white to-rose-50 shadow-xl transition-transform duration-200"
        :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0']"
    >
        <div class="flex h-20 items-center justify-between border-b border-pink-100 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-600 font-bold text-white shadow-lg shadow-pink-200">
                    SGE
                </span>
                <span>
                    <span class="block font-bold text-slate-900">Gestión Empresarial</span>
                    <span class="block text-xs text-pink-700">Control de tu negocio</span>
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
               class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-pink-600 text-white shadow-lg shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">
                Panel principal
            </a>

            @if (auth()->user()->can('ver-productos') || auth()->user()->can('ver-categorias') || auth()->user()->can('ver-inventario'))
                <section>
                    <p class="mb-2 px-4 text-xs font-bold uppercase tracking-widest text-pink-700">Inventario</p>
                    <div class="space-y-1">
                        @can('ver-productos')
                            <a href="{{ route('products.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('products.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Productos</a>
                        @endcan
                        @can('ver-categorias')
                            <a href="{{ route('categories.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('categories.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Categorías</a>
                        @endcan
                        @can('ver-inventario')
                            <a href="{{ route('inventory.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('inventory.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Kardex</a>
                        @endcan
                    </div>
                </section>
            @endif

            @if (auth()->user()->can('ver-ventas') || auth()->user()->can('ver-clientes'))
                <section>
                    <p class="mb-2 px-4 text-xs font-bold uppercase tracking-widest text-pink-700">Comercial</p>
                    <div class="space-y-1">
                        @can('ver-ventas')
                            <a href="{{ route('sales.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('sales.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Ventas</a>
                        @endcan
                        @can('ver-clientes')
                            <a href="{{ route('clients.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('clients.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Clientes</a>
                        @endcan
                    </div>
                </section>
            @endif

            @if (auth()->user()->can('ver-compras') || auth()->user()->can('ver-proveedores'))
                <section>
                    <p class="mb-2 px-4 text-xs font-bold uppercase tracking-widest text-pink-700">Abastecimiento</p>
                    <div class="space-y-1">
                        @can('ver-compras')
                            <a href="{{ route('purchases.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('purchases.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Compras</a>
                        @endcan
                        @can('ver-proveedores')
                            <a href="{{ route('providers.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('providers.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Proveedores</a>
                        @endcan
                    </div>
                </section>
            @endif

            @if (auth()->user()->can('ver-metodos-pago') || auth()->user()->can('gestionar-usuarios') || auth()->user()->can('ver-auditoria'))
                <section>
                    <p class="mb-2 px-4 text-xs font-bold uppercase tracking-widest text-pink-700">Administración</p>
                    <div class="space-y-1">
                        @can('ver-metodos-pago')
                            <a href="{{ route('payment-methods.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('payment-methods.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Métodos de pago</a>
                        @endcan
                        @can('gestionar-usuarios')
                            <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('users.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Usuarios y roles</a>
                        @endcan
                        @can('ver-auditoria')
                            <a href="{{ route('audit.index') }}" @click="sidebarOpen = false"
                               class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('audit.*') ? 'bg-pink-600 font-semibold text-white shadow-md shadow-pink-200' : 'text-slate-700 hover:bg-pink-100 hover:text-pink-800' }}">Auditoría</a>
                        @endcan
                    </div>
                </section>
            @endif
        </nav>

        <div class="border-t border-pink-100 p-4">
            <p class="truncate font-semibold text-slate-800">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
            <p class="mt-1 text-xs text-pink-700">
                Rol: {{ Auth::user()->getRoleNames()->join(', ') ?: 'Sin rol asignado' }}
            </p>
        </div>
    </aside>
</div>