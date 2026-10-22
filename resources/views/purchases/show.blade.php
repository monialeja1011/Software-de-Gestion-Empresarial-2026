<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
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

                $baseTotal = $purchase->details->sum(
                    fn ($detail) => (float) $detail->base_subtotal
                );

                $ivaTotal = $purchase->details->sum(
                    fn ($detail) => (float) $detail->iva_amount
                );
            @endphp

            <header class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-pink-700">Abastecimiento</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">
                        Orden {{ $purchase->order_number ?? 'OC-'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT) }}
                    </h1>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('purchases.pdf', $purchase) }}"
                        class="rounded-xl bg-pink-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-pink-700"
                    >
                        Descargar orden PDF
                    </a>

                    <a
                        href="{{ route('purchases.index') }}"
                        class="rounded-xl bg-white px-4 py-2 text-slate-700 shadow-sm hover:bg-pink-50"
                    >
                        Volver a compras
                    </a>
                </div>
            </header>

            @if (session('success'))
                <div class="rounded-2xl bg-green-50 p-4 text-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
                    <p class="font-semibold">No se pudo completar la operación:</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-slate-900">Datos de la orden</h2>

                    <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $statusStyles[$purchase->status] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $statusLabels[$purchase->status] ?? ucfirst($purchase->status) }}
                    </span>
                </div>

                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-slate-500">Proveedor</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $purchase->provider?->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Fecha de la orden</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $purchase->purchase_date?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Llegada prometida</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $purchase->expected_delivery_date?->format('d/m/Y') ?? 'No indicada' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Medio de pago</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $purchase->paymentMethod?->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Plazo de pago</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $purchase->payment_term_days ?? 0 }} días
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Fecha límite de pago</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $purchase->due_date?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Estado del pago</dt>
                        <dd class="mt-1 font-medium capitalize text-slate-900">
                            {{ $purchase->payment_status ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Total de la orden</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">
                            ${{ number_format($purchase->total, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>

                @if ($purchase->observations)
                    <div class="mt-5 rounded-xl bg-pink-50 p-4">
                        <p class="text-sm font-semibold text-slate-800">Observaciones de la orden</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                            {{ $purchase->observations }}
                        </p>
                    </div>
                @endif
            </section>

            <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-slate-900">Productos de la orden</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        El inventario aumenta únicamente por las unidades recibidas.
                    </p>
                </div>

                {{-- Tarjetas para celular --}}
                <div class="space-y-4 md:hidden">
                    @foreach ($purchase->details as $detail)
                        <article class="rounded-2xl bg-pink-50/70 p-4 shadow-sm">
                            <h3 class="font-semibold text-slate-900">
                                {{ $detail->product?->name ?? 'Producto eliminado' }}
                            </h3>

                            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                                <dt class="text-slate-600">Solicitado</dt>
                                <dd class="text-right font-medium">{{ $detail->quantity }}</dd>

                                <dt class="text-slate-600">Recibido</dt>
                                <dd class="text-right font-medium">{{ $detail->received_quantity }}</dd>

                                <dt class="text-slate-600">Pendiente</dt>
                                <dd class="text-right font-medium">
                                    {{ max(0, $detail->quantity - $detail->received_quantity) }}
                                </dd>

                                <dt class="text-slate-600">Costo cotizado</dt>
                                <dd class="text-right">
                                    ${{ number_format($detail->price, 0, ',', '.') }}
                                </dd>

                                <dt class="text-slate-600">IVA de compra</dt>
                                <dd class="text-right">
                                    @if ((float) $detail->iva_percentage > 0)
                                        {{ number_format($detail->iva_percentage, 2, ',', '.') }}%
                                        ({{ $detail->iva_included ? 'incluido' : 'adicional' }})
                                    @else
                                        No aplica
                                    @endif
                                </dd>

                                <dt class="text-slate-600">Base</dt>
                                <dd class="text-right">
                                    ${{ number_format($detail->base_subtotal, 0, ',', '.') }}
                                </dd>

                                <dt class="text-slate-600">Valor IVA</dt>
                                <dd class="text-right">
                                    ${{ number_format($detail->iva_amount, 0, ',', '.') }}
                                </dd>

                                <dt class="font-semibold text-slate-800">Total de línea</dt>
                                <dd class="text-right font-bold text-slate-900">
                                    ${{ number_format($detail->subtotal, 0, ',', '.') }}
                                </dd>
                            </dl>
                        </article>
                    @endforeach
                </div>

                {{-- Tabla para tabletas y PC --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[900px] text-sm">
                        <thead class="bg-pink-50 text-left text-slate-700">
                            <tr>
                                <th class="p-3">Producto</th>
                                <th class="p-3 text-right">Solicitado</th>
                                <th class="p-3 text-right">Recibido</th>
                                <th class="p-3 text-right">Pendiente</th>
                                <th class="p-3 text-right">Costo cotizado</th>
                                <th class="p-3 text-right">Base</th>
                                <th class="p-3 text-right">IVA</th>
                                <th class="p-3 text-right">Total de línea</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase->details as $detail)
                                <tr class="border-t border-pink-100">
                                    <td class="p-3 font-medium text-slate-900">
                                        {{ $detail->product?->name ?? 'Producto eliminado' }}
                                        <span class="block text-xs text-slate-500">
                                            @if ((float) $detail->iva_percentage > 0)
                                                IVA {{ number_format($detail->iva_percentage, 2, ',', '.') }}%
                                                {{ $detail->iva_included ? 'incluido' : 'adicional' }}
                                            @else
                                                No aplica IVA
                                            @endif
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">{{ $detail->quantity }}</td>
                                    <td class="p-3 text-right">{{ $detail->received_quantity }}</td>
                                    <td class="p-3 text-right">
                                        {{ max(0, $detail->quantity - $detail->received_quantity) }}
                                    </td>
                                    <td class="p-3 text-right">
                                        ${{ number_format($detail->price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">
                                        ${{ number_format($detail->base_subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">
                                        ${{ number_format($detail->iva_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right font-semibold">
                                        ${{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-pink-100 bg-pink-50/70">
                            <tr>
                                <td colspan="5" class="p-3 text-right font-semibold">Totales</td>
                                <td class="p-3 text-right font-semibold">
                                    ${{ number_format($baseTotal, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right font-semibold">
                                    ${{ number_format($ivaTotal, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right font-bold">
                                    ${{ number_format($purchase->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-2 rounded-2xl bg-pink-50 p-4 text-sm sm:ml-auto sm:max-w-sm">
                    <p class="flex justify-between gap-4">
                        <span>Base de compra:</span>
                        <strong>${{ number_format($baseTotal, 0, ',', '.') }}</strong>
                    </p>
                    <p class="flex justify-between gap-4">
                        <span>IVA:</span>
                        <strong>${{ number_format($ivaTotal, 0, ',', '.') }}</strong>
                    </p>
                    <p class="flex justify-between gap-4 border-t border-pink-200 pt-2 text-lg font-bold text-pink-900">
                        <span>Total:</span>
                        <span>${{ number_format($purchase->total, 0, ',', '.') }}</span>
                    </p>
                </div>
            </section>

            @if (
                $purchase->status !== 'anulada'
                && $purchase->details->contains(fn ($detail) => $detail->received_quantity < $detail->quantity)
                && auth()->user()->can('recibir-compras')
            )
                <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Registrar recepción</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Escribe cuántas unidades llegaron en esta entrega. Puedes registrar una recepción parcial.
                    </p>

                    <form
                        action="{{ route('purchases.receive', $purchase) }}"
                        method="POST"
                        class="mt-5 space-y-4"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="space-y-3">
                            @foreach ($purchase->details as $detail)
                                @php
                                    $pending = max(0, $detail->quantity - $detail->received_quantity);
                                @endphp

                                @if ($pending > 0)
                                    <label class="grid grid-cols-1 items-center gap-2 rounded-xl bg-pink-50/70 p-4 sm:grid-cols-[1fr_auto_auto]">
                                        <span class="font-medium text-slate-800">
                                            {{ $detail->product?->name ?? 'Producto eliminado' }}
                                            <span class="block text-xs font-normal text-slate-500">
                                                Pendiente: {{ $pending }} unidades
                                            </span>
                                        </span>

                                        <span class="text-sm text-slate-600">Llegaron:</span>

                                        <input
                                            type="number"
                                            name="items[{{ $detail->id }}][quantity]"
                                            min="0"
                                            max="{{ $pending }}"
                                            value="{{ old('items.'.$detail->id.'.quantity', 0) }}"
                                            class="w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500 sm:w-28"
                                        >
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        <label class="block text-sm font-medium text-slate-700">
                            Observaciones de la recepción
                            <textarea
                                name="observations"
                                rows="3"
                                maxlength="5000"
                                class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                placeholder="Indica si faltó producto, llegó averiado o hubo alguna novedad"
                            >{{ old('observations') }}</textarea>
                        </label>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-pink-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-pink-700 sm:w-auto"
                        >
                            Guardar recepción y actualizar stock
                        </button>
                    </form>
                </section>
            @endif

            @if ($purchase->receipts->isNotEmpty())
                <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <h2 class="mb-4 text-lg font-semibold text-slate-900">Historial de recepciones</h2>

                    <div class="space-y-4">
                        @foreach ($purchase->receipts->sortByDesc('received_at') as $receipt)
                            <article class="rounded-2xl bg-pink-50/70 p-4">
                                <div class="flex flex-wrap justify-between gap-2">
                                    <p class="font-semibold text-slate-900">
                                        Recepción del {{ $receipt->received_at->format('d/m/Y H:i') }}
                                    </p>
                                    <span class="text-sm text-slate-600">
                                        Registró: {{ $receipt->user?->name ?? 'Usuario no disponible' }}
                                    </span>
                                </div>

                                @if ($receipt->observations)
                                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">
                                        {{ $receipt->observations }}
                                    </p>
                                @endif

                                <ul class="mt-3 space-y-1 text-sm text-slate-700">
                                    @foreach ($receipt->details as $receiptDetail)
                                        <li>
                                            {{ $receiptDetail->purchaseDetail?->product?->name ?? 'Producto eliminado' }}:
                                            {{ $receiptDetail->quantity }} unidades
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>