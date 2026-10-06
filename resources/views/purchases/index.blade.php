<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Compras</h1>
                <a href="{{ route('purchases.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nueva compra</a>
            </div>
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Fecha</th>
                        <th class="p-3 text-left">Proveedor</th>
                        <th class="p-3 text-left">Pago</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-left">Estado</th>
                        <th class="p-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $d)
                        <tr class="border-b {{ $d->active ? '' : 'text-gray-400 line-through' }}">
                            <td class="p-3">{{ $d->id }}</td>
                            <td class="p-3">{{ $d->purchase_date->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $d->provider->name }}</td>
                            <td class="p-3">{{ $d->paymentMethod?->name }}</td>
                            <td class="p-3 text-right">${{ number_format($d->total, 0, ',', '.') }}</td>
                            <td class="p-3">{{ $d->active ? 'Registrada' : 'Anulada' }}</td>
                            <td class="p-3">
                                <a href="{{ route('purchases.show', $d) }}" class="text-blue-600">Ver</a>
                                @if (Auth::user()->isAdmin() && $d->active)
                                <form action="{{ route('purchases.destroy', $d) }}" method="POST" class="inline" onsubmit="return confirm('¿Anular esta compra? El stock se descontará.')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 ms-3">Anular</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-3 text-gray-500">Aún no hay registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $purchases->links() }}</div>
        </div>
    </div>
</x-app-layout>
