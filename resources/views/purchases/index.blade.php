<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-pink-700">Abastecimiento</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">Órdenes de compra</h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Consulta pedidos, fechas de llegada y estado de recepción.
                    </p>
                </div>

                @can('crear-compras')
                    <a
                        href="{{ route('purchases.create') }}"
                        class="rounded-lg bg-pink-600 px-5 py-3 font-semibold text-white hover:bg-pink-700"
                    >
                        Nueva orden
                    </a>
                @endcan
            </div>

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $statusLabels = [
                    'enviada' => 'Enviada al proveedor',
                    'parcialmente_recibida' => 'Parcialmente recibida',
                    'recibida' => 'Recibida completa',
                    'anulada' => 'Anulada',
                ];

                $statusStyles = [
                    'enviada' => 'bg-blue-100 text-blue-800',
                    'parcialmente_recibida' => 'bg-amber-100 text-amber-800',
                    'recibida' => 'bg-green-100 text-green-800',
                    'anulada' => 'bg-red-100 text-red-800',
                ];
            @endphp

            <section class="overflow-hidden rounded-xl border border-pink-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-sm">
                        <thead class="bg-pink-50 text-left text-slate-700">
                            <tr>
                                <th class="p-3">Orden</th>
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Proveedor</th>
                                <th class="p-3">Llegada prometida</th>
                                <th class="p-3">Pago hasta</th>
                                <th class="p-3 text-right">Total</th>
                                <th class="p-3">Estado</th>
                                <th class="p-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr class="border-t border-slate-100 {{ $purchase->status === 'anulada' ? 'bg-red-50/50' : '' }}">
                                    <td class="p-3 font-semibold text-slate-900">
                                        {{ $purchase->order_number ?? 'OC-'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        {{ $purchase->purchase_date?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        {{ $purchase->provider?->name ?? 'Proveedor no disponible' }}
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        {{ $purchase->expected_delivery_date?->format('d/m/Y') ?? 'No indicada' }}
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        {{ $purchase->due_date?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="p-3 text-right font-medium text-slate-900">
                                        ${{ number_format($purchase->total, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$purchase->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $statusLabels[$purchase->status] ?? ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <a
                                            href="{{ route('purchases.show', $purchase) }}"
                                            class="font-semibold text-pink-700 hover:text-pink-900"
                                        >
                                            Ver orden
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-slate-500">
                                        Todavía no hay órdenes de compra.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div>
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</x-app-layout>