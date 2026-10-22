<x-app-layout>
    <div class="py-8">
        <div
            class="mx-auto max-w-6xl px-4"
            x-data="{
                products: @js($products),
                rows: @js(old('items', [])).length
                    ? @js(old('items', []))
                    : [{ product_id: '', quantity: 1 }],

                add() {
                    this.rows.push({ product_id: '', quantity: 1 });
                },

                remove(i) {
                    if (this.rows.length > 1) {
                        this.rows.splice(i, 1);
                    }
                },

                product(row) {
                    return this.products.find(p => p.id == row.product_id);
                },

                price(row) {
                    const product = this.product(row);
                    return product ? Number(product.price) : 0;
                },

                ivaPercentage(row) {
                    const product = this.product(row);
                    return product ? Number(product.iva_percentage || 0) : 0;
                },

                ivaLabel(row) {
                    const percentage = this.ivaPercentage(row);
                    return percentage > 0 ? `${percentage}% incluido` : 'No aplica';
                },

                stock(row) {
                    const product = this.product(row);
                    return product ? product.stock : '';
                },

                round2(value) {
                    return Math.round((Number(value) + Number.EPSILON) * 100) / 100;
                },

                lineTotal(row) {
                    return this.round2(this.price(row) * (Number(row.quantity) || 0));
                },

                lineIva(row) {
                    const rate = this.ivaPercentage(row);
                    const total = this.lineTotal(row);

                    if (rate <= 0 || total <= 0) {
                        return 0;
                    }

                    return this.round2(total * rate / (100 + rate));
                },

                lineSubtotal(row) {
                    return this.round2(this.lineTotal(row) - this.lineIva(row));
                },

                fmt(value) {
                    return '$' + Number(value).toLocaleString('es-CO', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                },

                get subtotal() {
                    return this.round2(
                        this.rows.reduce((sum, row) => sum + this.lineSubtotal(row), 0)
                    );
                },

                get tax() {
                    return this.round2(
                        this.rows.reduce((sum, row) => sum + this.lineIva(row), 0)
                    );
                },

                get total() {
                    return this.round2(
                        this.rows.reduce((sum, row) => sum + this.lineTotal(row), 0)
                    );
                }
            }"
        >
            <h1 class="mb-6 text-2xl font-bold text-rose-950">Nueva venta</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-2xl bg-rose-100 p-4 text-rose-900 shadow">
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
                        <select name="client_id" class="w-full rounded-xl border-0 bg-white p-3 shadow" required>
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
                        <select name="payment_method_id" class="w-full rounded-xl border-0 bg-white p-3 shadow" required>
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
                            class="w-full rounded-xl border-0 bg-white p-3 shadow"
                            required
                        >
                    </label>
                </div>

                <div class="overflow-x-auto rounded-2xl bg-white shadow">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left">
                                <th class="p-3">Producto</th>
                                <th class="p-3">Cantidad</th>
                                <th class="p-3 text-right">Precio público (IVA incluido)</th>
                                <th class="p-3 text-center">IVA</th>
                                <th class="p-3 text-right">Base sin IVA</th>
                                <th class="p-3 text-right">IVA incluido</th>
                                <th class="p-3 text-right">Total línea</th>
                                <th class="p-3 text-right">Stock</th>
                                <th class="p-3"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <template x-for="(row, i) in rows" :key="i">
                                <tr>
                                    <td class="p-3">
                                        <select
                                            :name="`items[${i}][product_id]`"
                                            x-model="row.product_id"
                                            class="w-full rounded-lg border-0 bg-rose-50 p-2 shadow-sm"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option
                                                    :value="product.id"
                                                    x-text="product.name + (Number(product.iva_percentage) > 0
                                                        ? ' — IVA incluido ' + product.iva_percentage + '%'
                                                        : ' — No aplica IVA')"
                                                ></option>
                                            </template>
                                        </select>
                                    </td>

                                    <td class="p-3">
                                        <input
                                            type="number"
                                            min="1"
                                            :max="stock(row)"
                                            :name="`items[${i}][quantity]`"
                                            x-model.number="row.quantity"
                                            class="w-24 rounded-lg border-0 bg-rose-50 p-2 shadow-sm"
                                            required
                                        >
                                    </td>

                                    <td class="p-3 text-right" x-text="fmt(price(row))"></td>
                                    <td class="p-3 text-center" x-text="ivaLabel(row)"></td>
                                    <td class="p-3 text-right" x-text="fmt(lineSubtotal(row))"></td>
                                    <td class="p-3 text-right" x-text="fmt(lineIva(row))"></td>
                                    <td class="p-3 text-right font-semibold" x-text="fmt(lineTotal(row))"></td>
                                    <td class="p-3 text-right" x-text="stock(row)"></td>

                                    <td class="p-3 text-right">
                                        <button
                                            type="button"
                                            @click="remove(i)"
                                            class="font-semibold text-rose-700 hover:text-rose-900"
                                        >
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <button
                    type="button"
                    @click="add()"
                    class="mt-4 font-semibold text-rose-700 hover:text-rose-900"
                >
                    + Agregar producto
                </button>

                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-sm space-y-2 rounded-2xl bg-white p-5 shadow">
                        <div class="flex justify-between">
                            <span>Subtotal antes de IVA:</span>
                            <span x-text="fmt(subtotal)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA incluido:</span>
                            <span x-text="fmt(tax)"></span>
                        </div>
                        <div class="flex justify-between border-t border-rose-100 pt-3 text-xl font-bold text-rose-950">
                            <span>Total a pagar:</span>
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
                        class="w-full rounded-xl border-0 bg-white p-3 shadow"
                    >{{ old('notes') }}</textarea>
                </label>

                <div class="mt-6">
                    <button class="rounded-xl bg-rose-600 px-5 py-3 font-bold text-white shadow transition hover:bg-rose-700">
                        Registrar venta
                    </button>
                    <a href="{{ route('sales.index') }}" class="ml-3 font-medium text-rose-700">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>