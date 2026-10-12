<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Ventas</h1>

                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}"
                       class="rounded bg-blue-600 px-4 py-2 text-white">
                        Nueva venta
                    </a>
                @endcan
            </div>

            <div class="overflow-x-auto rounded bg-white shadow">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="p-3 text-left">Factura</th>
                            <th class="p-3 text-left">Fecha</th>
                            <th class="p-3 text-left">Cliente</th>
                            <th class="p-3 text-left">Vendedor</th>
                            <th class="p-3 text-left">Pago</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-left">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sales as $sale)
                            @php
                                $anulada = $sale->status === 'anulada'
                                    || ! $sale->active
                                    || $sale->trashed();
                            @endphp

                            <tr class="border-b {{ $anulada ? 'bg-red-50 text-red-800' : '' }}">
                                <td class="p-3">
                                    {{ $sale->invoice_number ?? 'Venta #'.$sale->id }}
                                </td>
                                <td class="p-3">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="p-3">{{ $sale->client?->name ?? 'Cliente no disponible' }}</td>
                                <td class="p-3">{{ $sale->user?->name ?? 'No registrado' }}</td>
                                <td class="p-3">{{ $sale->paymentMethod?->name ?? '—' }}</td>
                                <td class="p-3 text-right">
                                    ${{ number_format((float) $sale->total, 0, ',', '.') }}
                                </td>
                                <td class="p-3 font-semibold">
                                    {{ $anulada ? 'Anulada' : ucfirst($sale->status) }}
                                </td>
                                <td class="p-3">
                                    <a href="{{ route('sales.show', $sale) }}"
                                       class="text-blue-600 underline">
                                        Ver
                                    </a>

                                    @can('eliminar-ventas')
                                        @if (! $anulada)
                                            <form action="{{ route('sales.destroy', $sale) }}"
                                                  method="POST"
                                                  class="ml-3 inline"
                                                  onsubmit="return confirm('¿Anular esta venta y devolver el stock?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-700 underline">Anular</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-3 text-center text-gray-500">
                                    Aún no hay ventas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $sales->links() }}</div>
        </div>
    </div>
</x-app-layout>