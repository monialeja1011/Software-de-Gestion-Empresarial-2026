<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">
                Sistema de Gestión Empresarial
            </p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">
                Panel de control
            </h1>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            <section class="rounded-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-cyan-950 p-7 text-white shadow-lg sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-widest text-cyan-300">
                    Resumen de tu empresa
                </p>
                <h2 class="mt-3 text-3xl font-black">
                    Hola, {{ auth()->user()->name }}
                </h2>
                <p class="mt-2 max-w-2xl text-slate-300">
                    Consulta la actividad del negocio y accede rápidamente a los módulos disponibles.
                </p>
            </section>

            <section>
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Indicadores</h2>
                    <p class="text-sm text-slate-500">Resumen del mes actual</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @can('ver-ventas')
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Ventas del mes</p>
                            <p class="mt-3 text-3xl font-black text-slate-900">
                                ${{ number_format($salesMonth, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-sm text-cyan-700">Total registrado</p>
                        </article>
                    @endcan

                    @can('ver-compras')
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Compras del mes</p>
                            <p class="mt-3 text-3xl font-black text-slate-900">
                                ${{ number_format($purchasesMonth, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-sm text-cyan-700">Total registrado</p>
                        </article>
                    @endcan

                    @can('ver-clientes')
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Clientes activos</p>
                            <p class="mt-3 text-3xl font-black text-slate-900">{{ $clientsCount }}</p>
                            <p class="mt-2 text-sm text-cyan-700">En el sistema</p>
                        </article>
                    @endcan

                    @can('ver-productos')
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Productos activos</p>
                            <p class="mt-3 text-3xl font-black text-slate-900">{{ $productsCount }}</p>
                            <p class="mt-2 text-sm text-cyan-700">En inventario</p>
                        </article>
                    @endcan
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-2">
                @can('ver-ventas')
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-lg font-bold text-slate-900">Últimas ventas</h2>
                            <p class="text-sm text-slate-500">Operaciones recientes</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[520px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500">
                                        <th class="pb-3 font-medium">Venta</th>
                                        <th class="pb-3 font-medium">Cliente</th>
                                        <th class="pb-3 font-medium">Pago</th>
                                        <th class="pb-3 text-right font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestSales as $sale)
                                        <tr class="border-b border-slate-100 last:border-0">
                                            <td class="py-3 font-semibold text-cyan-800">
                                                #{{ $sale->id }}
                                            </td>
                                            <td class="py-3 text-slate-700">
                                                {{ $sale->client->name }}
                                            </td>
                                            <td class="py-3 text-slate-500">
                                                {{ $sale->paymentMethod?->name ?? 'Sin método' }}
                                            </td>
                                            <td class="py-3 text-right font-semibold text-slate-900">
                                                ${{ number_format($sale->total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-slate-500">
                                                Todavía no hay ventas registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                @endcan

                @can('ver-productos')
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-lg font-bold text-slate-900">Stock bajo</h2>
                            <p class="text-sm text-slate-500">Productos con 5 unidades o menos</p>
                        </div>

                        <div class="space-y-3">
                            @forelse ($lowStock as $product)
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                    <span class="font-medium text-slate-700">{{ $product->name }}</span>
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-bold text-red-700">
                                        {{ $product->stock }} unidades
                                    </span>
                                </div>
                            @empty
                                <p class="rounded-xl bg-emerald-50 p-4 text-emerald-800">
                                    Todo el inventario está bien.
                                </p>
                            @endforelse
                        </div>
                    </article>
                @endcan
            </section>

        </div>
    </div>
</x-app-layout>