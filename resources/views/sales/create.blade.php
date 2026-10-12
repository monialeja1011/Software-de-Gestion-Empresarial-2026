<x-app-layout>
    <div class="py-8">
        <div
            class="mx-auto max-w-5xl px-4"
            x-data="{
                products: @js($products),
                rows: @js(old('items', [])).length
                    ? @js(old('items', []))
                    : [{ product_id: '', quantity: 1 }],
                add() {
                    this.rows.push({ product_id: '', quantity: 1 });
                },
                remove(i) {
                    if (this.rows.length > 1) this.rows.splice(i, 1);
                },
                price(row) {
                    const product = this.products.find(p => p.id == row.product_id);
                    return product ? Number(product.price) : 0;
                },
                stock(row) {
                    const product = this.products.find(p => p.id == row.product_id);
                    return product ? product.stock : '';
                },
                lineSubtotal(row) {
                    return this.price(row) * (Number(row.quantity) || 0);
                },
                fmt(value) {
                    return '$' + Number(value).toLocaleString('es-CO', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                },
                get subtotal() {
                    return this.rows.reduce((sum, row) => sum + this.lineSubtotal(row), 0);
                },
                get tax() {
                    return Math.round(this.subtotal * 0.19 * 100) / 100;
                },
                get total() {
                    return this.subtotal + this.tax;
                }
            }"
        >
            <h1 class="mb-6 text-2xl font-bold">Nueva venta</h1>

            @if ($errors->any())
                <div class="mb-4 rounded bg-red-100 p-4 text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sales.store') }}" method="POST">
                @csrf

                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <label class="block">
                        Cliente
                        <select name="client_id" class="w-full rounded border p-2" required>
                            <option value="">Seleccione...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}"
                                    @selected(old('client_id') == $client->id)>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        Medio de pago
                        <select name="payment_method_id" class="w-full rounded border p-2" required>
                            <option value="">Seleccione...</option>
                            @foreach ($methods as $method)
                                <option value="{{ $method->id }}"
                                    @selected(old('payment_method_id') == $method->id)>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        Fecha
                        <input
                            type="date"
                            name="sale_date"
                            value="{{ old('sale_date', now()->toDateString()) }}"
                            max="{{ now()->toDateString() }}"
                            required
                        >
                    </label>
                </div>

                <div class="overflow-x-auto">
                    <table class="mb-4 w-full rounded bg-white shadow">
                        <thead>
                            <tr class="border-b">
                                <th class="p-2 text-left">Producto</th>
                                <th class="p-2 text-left">Cantidad</th>
                                <th class="p-2 text-right">Precio</th>
                                <th class="p-2 text-right">Subtotal</th>
                                <th class="p-2 text-right">Stock</th>
                                <th class="p-2"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <template x-for="(row, i) in rows" :key="i">
                                <tr class="border-b">
                                    <td class="p-2">
                                        <select
                                            :name="`items[${i}][product_id]`"
                                            x-model="row.product_id"
                                            class="w-full rounded border p-1"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option :value="product.id" x-text="product.name"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <td class="p-2">
                                        <input
                                            type="number"
                                            min="1"
                                            :max="stock(row)"
                                            :name="`items[${i}][quantity]`"
                                            x-model.number="row.quantity"
                                            class="w-24 rounded border p-1"
                                            required
                                        >
                                    </td>

                                    <td class="p-2 text-right" x-text="fmt(price(row))"></td>
                                    <td class="p-2 text-right" x-text="fmt(lineSubtotal(row))"></td>
                                    <td class="p-2 text-right" x-text="stock(row)"></td>

                                    <td class="p-2 text-right">
                                        <button
                                            type="button"
                                            @click="remove(i)"
                                            class="text-red-600"
                                        >
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <button type="button" @click="add()" class="text-blue-600">
                    + Agregar producto
                </button>

                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-sm space-y-2 rounded bg-white p-4 shadow">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span x-text="fmt(subtotal)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA (19 %):</span>
                            <span x-text="fmt(tax)"></span>
                        </div>
                        <div class="flex justify-between border-t pt-2 text-xl font-bold">
                            <span>Total:</span>
                            <span x-text="fmt(total)"></span>
                        </div>
                    </div>
                </div>

                <label class="mt-6 block">
                    Notas (opcional)
                    <textarea
                        name="notes"
                        rows="3"
                        maxlength="500"
                        class="w-full rounded border p-2"
                    >{{ old('notes') }}</textarea>
                </label>

                <div class="mt-6">
                    <button class="rounded bg-green-600 px-4 py-2 text-white">
                        Registrar venta
                    </button>
                    <a href="{{ route('sales.index') }}" class="ml-3 text-gray-600">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>