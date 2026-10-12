<x-app-layout>
    @php
        $anulada = $sale->status === 'anulada'
            || ! $sale->active
            || $sale->trashed();
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Factura {{ $sale->invoice_number ?? 'Venta #'.$sale->id }}
                    </h1>

                    @if ($anulada)
                        <span class="mt-2 inline-block rounded bg-red-100 px-3 py-1 font-semibold text-red-800">
                            Venta anulada
                        </span>
                    @endif
                </div>

                <a href="{{ route('sales.index') }}" class="text-gray-600 underline">
                    ← Volver a ventas
                </a>
            </div>

            <div class="mb-4 grid grid-cols-1 gap-4 rounded bg-white p-4 text-sm shadow sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="text-gray-500">Fecha</div>
                    {{ $sale->sale_date->format('d/m/Y') }}
                </div>

                <div>
                    <div class="text-gray-500">Cliente</div>
                    {{ $sale->client?->name ?? 'Cliente no disponible' }}
                </div>

                <div>
                    <div class="text-gray-500">Vendedor</div>
                    {{ $sale->user?->name ?? 'No registrado' }}
                </div>

                <div>
                    <div class="text-gray-500">Medio de pago</div>
                    {{ $sale->paymentMethod?->name ?? '—' }}
                </div>
            </div>

            <div class="overflow-x-auto rounded bg-white shadow">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="p-3 text-left">Producto</th>
                            <th class="p-3 text-right">Cantidad</th>
                            <th class="p-3 text-right">Precio</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sale->details as $line)
                            <tr class="border-b">
                                <td class="p-3">
                                    {{ $line->product?->name ?? 'Producto no disponible' }}
                                </td>
                                <td class="p-3 text-right">{{ $line->quantity }}</td>
                                <td class="p-3 text-right">
                                    ${{ number_format((float) $line->price, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right">
                                    ${{ number_format((float) $line->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="3" class="p-3 text-right">Subtotal</td>
                            <td class="p-3 text-right">
                                ${{ number_format((float) $sale->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="p-3 text-right">IVA (19 %)</td>
                            <td class="p-3 text-right">
                                ${{ number_format((float) $sale->tax, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="border-t font-bold">
                            <td colspan="3" class="p-3 text-right">Total</td>
                            <td class="p-3 text-right">
                                ${{ number_format((float) $sale->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>