<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Compra #{{ $purchase->id }}</h1>
                <a href="{{ route('purchases.index') }}" class="text-gray-600">← Volver</a>
            </div>
            <div class="bg-white shadow rounded p-4 mb-4 grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div><div class="text-gray-500">Fecha</div>{{ $purchase->purchase_date->format('d/m/Y') }}</div>
                <div><div class="text-gray-500">Proveedor</div>{{ $purchase->provider->name }}</div>
                <div><div class="text-gray-500">Medio de pago</div>{{ $purchase->paymentMethod?->name ?? '—' }}</div>
                <div><div class="text-gray-500">Estado</div>{{ $purchase->active ? 'Registrada' : 'Anulada' }}</div>
            </div>
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">Producto</th>
                        <th class="p-3 text-right">Cantidad</th>
                        <th class="p-3 text-right">Precio</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->details as $line)
                        <tr class="border-b">
                            <td class="p-3">{{ $line->product->name }}</td>
                            <td class="p-3 text-right">{{ $line->quantity }}</td>
                            <td class="p-3 text-right">${{ number_format($line->price, 0, ',', '.') }}</td>
                            <td class="p-3 text-right">${{ number_format($line->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="p-3 text-right font-semibold">Total</td><td class="p-3 text-right font-bold">${{ number_format($purchase->total, 0, ',', '.') }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
