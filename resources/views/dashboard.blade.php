<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de control</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Ventas del mes</div>
                    <div class="text-2xl font-bold">${{ number_format($salesMonth, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Compras del mes</div>
                    <div class="text-2xl font-bold">${{ number_format($purchasesMonth, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Clientes activos</div>
                    <div class="text-2xl font-bold">{{ $clientsCount }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Productos activos</div>
                    <div class="text-2xl font-bold">{{ $productsCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow rounded p-4">
                    <h3 class="font-semibold mb-3">Últimas ventas</h3>
                    <table class="w-full text-sm">
                        @forelse ($latestSales as $s)
                            <tr class="border-b">
                                <td class="py-2"><a href="{{ route('sales.show', $s) }}" class="text-blue-600">#{{ $s->id }}</a></td>
                                <td>{{ $s->client->name }}</td>
                                <td>{{ $s->paymentMethod?->name }}</td>
                                <td class="text-right">${{ number_format($s->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-2 text-gray-500">Sin ventas todavía.</td></tr>
                        @endforelse
                    </table>
                </div>

                <div class="bg-white shadow rounded p-4">
                    <h3 class="font-semibold mb-3">Stock bajo (5 o menos)</h3>
                    <table class="w-full text-sm">
                        @forelse ($lowStock as $p)
                            <tr class="border-b">
                                <td class="py-2">{{ $p->name }}</td>
                                <td class="text-right text-red-600 font-semibold">{{ $p->stock }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-2 text-gray-500">Todo el inventario está bien.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
