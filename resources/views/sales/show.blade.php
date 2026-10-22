<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <header class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-pink-700">Ventas</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">
                        Venta {{ $sale->invoice_number ?? '#'.$sale->id }}
                    </h1>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('sales.pdf', $sale) }}"
                        class="rounded-xl bg-pink-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-pink-700"
                    >
                        Descargar comprobante PDF
                    </a>
                    <a
                        href="{{ route('sales.index') }}"
                        class="rounded-xl bg-white px-4 py-2 font-medium text-slate-700 shadow-sm hover:bg-pink-50"
                    >
                        Volver a ventas
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
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($sale->status === 'anulada')
                <div class="rounded-2xl bg-red-50 p-4 font-semibold text-red-800 shadow-sm">
                    Esta venta fue anulada. El stock fue devuelto y el comprobante indica su estado.
                </div>
            @endif

            <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Datos de la venta</h2>

                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-slate-500">Cliente</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $sale->client?->name ?? 'Cliente no disponible' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Fecha</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $sale->sale_date?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Medio de pago</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $sale->paymentMethod?->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Atendió</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $sale->user?->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Estado</dt>
                        <dd class="mt-1 font-semibold {{ $sale->status === 'anulada' ? 'text-red-700' : 'text-green-700' }}">
                            {{ ucfirst($sale->status ?? 'registrada') }}
                        </dd>
                    </div>

                    @if ($sale->deleted_at)
                        <div>
                            <dt class="text-slate-500">Fecha de anulación</dt>
                            <dd class="mt-1 font-medium text-slate-900">
                                {{ $sale->deleted_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </section>

            <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-slate-900">Productos vendidos</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        El precio unitario ya incluye el IVA cuando aplica. El impuesto se muestra desglosado y no se suma otra vez.
                    </p>
                </div>

                {{-- Tarjetas para celular --}}
                <div class="space-y-4 md:hidden">
                    @forelse ($sale->details as $detail)
                        @php
                            $lineTotal = (float) $detail->subtotal + (float) $detail->iva_amount;
                        @endphp

                        <article class="rounded-2xl bg-pink-50/70 p-4 shadow-sm">
                            <h3 class="font-semibold text-slate-900">
                                {{ $detail->product?->name ?? 'Producto eliminado' }}
                            </h3>

                            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                                <dt class="text-slate-600">Cantidad</dt>
                                <dd class="text-right font-medium">{{ $detail->quantity }}</dd>

                                <dt class="text-slate-600">Precio unitario, IVA incluido</dt>
                                <dd class="text-right">
                                    ${{ number_format($detail->price, 0, ',', '.') }}
                                </dd>

                                <dt class="text-slate-600">Base sin IVA</dt>
                                <dd class="text-right">
                                    ${{ number_format($detail->subtotal, 0, ',', '.') }}
                                </dd>

                                <dt class="text-slate-600">Tarifa IVA</dt>
                                <dd class="text-right">
                                    @if ((float) $detail->iva_percentage > 0)
                                        {{ number_format($detail->iva_percentage, 2, ',', '.') }}%
                                    @else
                                        No aplica
                                    @endif
                                </dd>

                                <dt class="text-slate-600">IVA incluido</dt>
                                <dd class="text-right">
                                    ${{ number_format($detail->iva_amount, 0, ',', '.') }}
                                </dd>

                                <dt class="font-semibold text-slate-800">Total de línea</dt>
                                <dd class="text-right font-bold text-slate-900">
                                    ${{ number_format($lineTotal, 0, ',', '.') }}
                                </dd>
                            </dl>
                        </article>
                    @empty
                        <p class="rounded-xl bg-slate-50 p-4 text-center text-slate-500">
                            Esta venta no tiene productos asociados.
                        </p>
                    @endforelse
                </div>

                {{-- Tabla para tabletas y PC --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[900px] text-sm">
                        <thead class="bg-pink-50 text-left text-slate-700">
                            <tr>
                                <th class="p-3">Producto</th>
                                <th class="p-3 text-right">Cantidad</th>
                                <th class="p-3 text-right">Precio unitario (IVA incluido)</th>
                                <th class="p-3 text-right">Base sin IVA</th>
                                <th class="p-3 text-right">Tarifa IVA</th>
                                <th class="p-3 text-right">IVA incluido</th>
                                <th class="p-3 text-right">Total de línea</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sale->details as $detail)
                                @php
                                    $lineTotal = (float) $detail->subtotal + (float) $detail->iva_amount;
                                @endphp
                                <tr class="border-t border-pink-100">
                                    <td class="p-3 font-medium text-slate-900">
                                        {{ $detail->product?->name ?? 'Producto eliminado' }}
                                    </td>
                                    <td class="p-3 text-right">{{ $detail->quantity }}</td>
                                    <td class="p-3 text-right">
                                        ${{ number_format($detail->price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">
                                        ${{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">
                                        @if ((float) $detail->iva_percentage > 0)
                                            {{ number_format($detail->iva_percentage, 2, ',', '.') }}%
                                        @else
                                            No aplica
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        ${{ number_format($detail->iva_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right font-semibold">
                                        ${{ number_format($lineTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-slate-500">
                                        Esta venta no tiene productos asociados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 ml-auto w-full space-y-2 rounded-2xl bg-pink-50 p-4 sm:max-w-sm">
                    <p class="flex justify-between gap-4">
                        <span>Subtotal antes de IVA:</span>
                        <strong>${{ number_format($sale->subtotal, 0, ',', '.') }}</strong>
                    </p>
                    <p class="flex justify-between gap-4">
                        <span>IVA incluido:</span>
                        <strong>${{ number_format($sale->tax, 0, ',', '.') }}</strong>
                    </p>
                    <p class="flex justify-between gap-4 border-t border-pink-200 pt-2 text-lg font-bold text-pink-900">
                        <span>Total pagado:</span>
                        <span>${{ number_format($sale->total, 0, ',', '.') }}</span>
                    </p>
                </div>
            </section>

            @if ($sale->notes)
                <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <h2 class="font-semibold text-slate-900">Notas</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $sale->notes }}</p>
                </section>
            @endif

            @if ($sale->status !== 'anulada' && auth()->user()->can('eliminar-ventas'))
                <form
                    action="{{ route('sales.destroy', $sale) }}"
                    method="POST"
                    onsubmit="return confirm('¿Anular esta venta? El stock será devuelto.')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="rounded-xl bg-red-50 px-4 py-2 font-medium text-red-700 shadow-sm hover:bg-red-100"
                    >
                        Anular venta
                    </button>
                </form>
            @endif

            <p class="text-xs text-slate-500">
                Este es un comprobante interno; no es una factura electrónica validada por la DIAN.
            </p>
        </div>
    </div>
</x-app-layout>