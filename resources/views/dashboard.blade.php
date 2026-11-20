<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Panel de control</h1>
            <span class="text-sm text-slate-400">Sistema de Gestión Empresarial</span>
        </div>
    </x-slot>

    @php
        $maxSold = max(1, (int) ($topProducts->max('quantity_sold') ?? 1));
    @endphp

    <div class="min-h-screen bg-pink-50 pb-14">
        <section class="bg-gradient-to-br from-pink-900 via-pink-700 to-pink-600 pb-24 pt-10 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-pink-100/80">
                    {{ now()->translatedFormat('l, j \d\e F \d\e Y') }}
                </p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                    Hola, {{ auth()->user()->name }}
                </h2>
                <p class="mt-3 max-w-xl text-base leading-relaxed text-pink-50/90">
                    Así va tu negocio este mes. Desde aquí puedes revisar las ventas recientes y el estado del inventario.
                </p>
            </div>
        </section>

        <div class="mx-auto -mt-14 max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section aria-label="Indicadores del mes">
                <div class="grid overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-[0_10px_30px_-12px_rgba(157,23,77,0.25)] sm:grid-cols-2 xl:grid-flow-col xl:auto-cols-fr">
                    @can('ver-ventas')
                        <article class="border-b border-pink-100 px-6 py-6 last:border-b-0 xl:border-b-0 xl:border-l xl:first:border-l-0">
                            <p class="text-sm text-slate-500">Ventas del mes</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 tabular-nums">
                                <span class="mr-1 text-base font-medium text-pink-700">COP</span>{{ number_format($salesMonth, 0, ',', '.') }}
                            </p>
                        </article>
                    @endcan

                    @can('ver-compras')
                        <article class="border-b border-pink-100 px-6 py-6 last:border-b-0 xl:border-b-0 xl:border-l xl:first:border-l-0">
                            <p class="text-sm text-slate-500">Compras del mes</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 tabular-nums">
                                <span class="mr-1 text-base font-medium text-pink-700">COP</span>{{ number_format($purchasesMonth, 0, ',', '.') }}
                            </p>
                        </article>
                    @endcan

                    @can('ver-clientes')
                        <article class="border-b border-pink-100 px-6 py-6 last:border-b-0 xl:border-b-0 xl:border-l xl:first:border-l-0">
                            <p class="text-sm text-slate-500">Clientes activos</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 tabular-nums">
                                {{ $clientsCount }}
                            </p>
                        </article>
                    @endcan

                    @can('ver-productos')
                        <article class="border-b border-pink-100 px-6 py-6 last:border-b-0 xl:border-b-0 xl:border-l xl:first:border-l-0">
                            <p class="text-sm text-slate-500">Productos activos</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 tabular-nums">
                                {{ $productsCount }}
                            </p>
                        </article>
                    @endcan
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-3">
                @can('ver-ventas')
                    <article class="rounded-2xl border border-pink-100 bg-white xl:col-span-2">
                        <header class="flex items-center justify-between border-b border-pink-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Últimas ventas</h2>
                            <p class="text-sm text-slate-400">Operaciones recientes</p>
                        </header>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[520px] text-left text-sm">
                                <thead>
                                    <tr class="text-slate-400">
                                        <th class="px-6 py-3 font-medium">Venta</th>
                                        <th class="px-3 py-3 font-medium">Cliente</th>
                                        <th class="px-3 py-3 font-medium">Pago</th>
                                        <th class="px-6 py-3 text-right font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-pink-50">
                                    @forelse ($latestSales as $sale)
                                        <tr class="transition-colors hover:bg-pink-50/60">
                                            <td class="px-6 py-3.5 font-medium text-pink-800">
                                                {{ $sale->invoice_number ?? '#'.$sale->id }}
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-700">
                                                {{ $sale->client?->name ?? 'Cliente no disponible' }}
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-500">
                                                {{ $sale->paymentMethod?->name ?? 'Sin método' }}
                                            </td>
                                            <td class="px-6 py-3.5 text-right font-semibold text-slate-900 tabular-nums">
                                                COP ${{ number_format($sale->total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">
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
                    <article class="rounded-2xl border border-pink-100 bg-white">
                        <header class="border-b border-pink-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Stock bajo</h2>
                            <p class="text-sm text-slate-400">Productos con 5 unidades o menos</p>
                        </header>

                        <ul class="divide-y divide-pink-50">
                            @forelse ($lowStock as $product)
                                <li class="flex items-center justify-between gap-4 px-6 py-3.5">
                                    <span class="truncate text-sm text-slate-700">{{ $product->name }}</span>
                                    <span class="flex shrink-0 items-center gap-2 text-sm font-semibold text-rose-700 tabular-nums">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500" aria-hidden="true"></span>
                                        {{ $product->stock }} {{ $product->stock == 1 ? 'unidad' : 'unidades' }}
                                    </span>
                                </li>
                            @empty
                                <li class="px-6 py-10 text-center text-sm text-emerald-700">
                                    Todo el inventario está en orden.
                                </li>
                            @endforelse
                        </ul>
                    </article>
                @endcan
            </section>

            @can('ver-ventas')
                <section class="rounded-2xl border border-pink-100 bg-white">
                    <header class="flex items-center justify-between border-b border-pink-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Los 5 productos más vendidos</h2>
                        <p class="text-sm text-slate-400">Unidades vendidas este mes</p>
                    </header>

                    <ol class="space-y-5 px-6 py-6">
                        @forelse ($topProducts as $product)
                            <li>
                                <div class="flex items-baseline justify-between gap-4 text-sm">
                                    <span class="font-medium text-slate-800">{{ $product->name }}</span>
                                    <span class="font-semibold text-pink-800 tabular-nums">{{ $product->quantity_sold }} unidades</span>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-pink-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-pink-700 to-pink-500"
                                         style="width: {{ round(($product->quantity_sold / $maxSold) * 100) }}%"></div>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-center text-sm text-slate-500">
                                Todavía no hay ventas para mostrar.
                            </li>
                        @endforelse
                    </ol>
                </section>
            @endcan

            <section>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-slate-900">Análisis del negocio</h2>
                    <p class="text-sm text-slate-400">Indicadores gráficos para apoyar tus decisiones</p>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @foreach ($charts as $chart)
                        <article class="rounded-2xl border border-pink-100 bg-white p-6 {{ ($chart['wide'] ?? false) ? 'lg:col-span-2' : '' }}">
                            <div class="mb-5">
                                <h3 class="text-base font-semibold text-slate-900">{{ $chart['title'] }}</h3>
                                <p class="text-sm text-slate-400">{{ $chart['subtitle'] }}</p>
                            </div>

                            @if ($chart['empty'])
                                <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-pink-200 bg-pink-50/40 text-sm text-slate-500">
                                    Aún no hay datos suficientes para mostrar este gráfico.
                                </div>
                            @else
                                <div class="relative h-64 sm:h-72">
                                    <canvas
                                        data-chart="{{ json_encode($chart, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                                        aria-label="{{ $chart['title'] }}"
                                        role="img"
                                    ></canvas>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-app-layout>