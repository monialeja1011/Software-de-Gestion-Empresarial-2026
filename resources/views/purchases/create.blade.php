<x-app-layout>
    <div class="py-8">
        <div
            class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8"
            x-data="{
                products: @js($products),
                rows: @js(old('items', [])),

                init() {
                    if (!this.rows.length) {
                        this.rows = [{
                            product_id: '',
                            quantity: 1,
                            price: 0,
                            iva_percentage: 0,
                            iva_included: '1'
                        }];
                    }
                },

                add() {
                    this.rows.push({
                        product_id: '',
                        quantity: 1,
                        price: 0,
                        iva_percentage: 0,
                        iva_included: '1'
                    });
                },

                remove(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },

                productFor(row) {
                    return this.products.find(product => product.id == row.product_id);
                },

                setProduct(row) {
                    const product = this.productFor(row);

                    if (product) {
                        row.price = Number(product.cost_price || 0);
                        row.iva_percentage = Number(product.iva_percentage || 0);
                        row.iva_included = '1';
                    }
                },

                quantity(row) {
                    return Math.max(0, Number(row.quantity) || 0);
                },

                price(row) {
                    return Math.max(0, Number(row.price) || 0);
                },

                rate(row) {
                    return Math.max(0, Number(row.iva_percentage) || 0);
                },

                taxIncluded(row) {
                    return String(row.iva_included) === '1';
                },

                round2(value) {
                    return Math.round((Number(value) + Number.EPSILON) * 100) / 100;
                },

                baseUnit(row) {
                    const price = this.price(row);
                    const rate = this.rate(row);

                    if (this.taxIncluded(row) && rate > 0) {
                        return this.round2(price / (1 + rate / 100));
                    }

                    return price;
                },

                ivaUnit(row) {
                    const price = this.price(row);
                    const rate = this.rate(row);

                    if (rate <= 0) {
                        return 0;
                    }

                    if (this.taxIncluded(row)) {
                        return this.round2(price - this.baseUnit(row));
                    }

                    return this.round2(price * rate / 100);
                },

                baseSubtotal(row) {
                    return this.round2(this.baseUnit(row) * this.quantity(row));
                },

                ivaAmount(row) {
                    return this.round2(this.ivaUnit(row) * this.quantity(row));
                },

                lineTotal(row) {
                    return this.round2(this.baseSubtotal(row) + this.ivaAmount(row));
                },

                format(value) {
                    return '$' + Number(value || 0).toLocaleString('es-CO', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                },

                get totalBase() {
                    return this.round2(this.rows.reduce((sum, row) => sum + this.baseSubtotal(row), 0));
                },

                get totalIva() {
                    return this.round2(this.rows.reduce((sum, row) => sum + this.ivaAmount(row), 0));
                },

                get total() {
                    return this.round2(this.totalBase + this.totalIva);
                }
            }"
        >
            <header>
                <p class="text-sm font-medium text-pink-700">Abastecimiento</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Nueva orden de compra</h1>
                <p class="mt-2 text-sm text-slate-600">
                    Crear la orden no modifica las existencias. El stock cambia al registrar la recepción.
                </p>
            </header>

            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
                    <p class="font-semibold">Revisa estos datos:</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
                @csrf

                <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <h2 class="mb-4 text-lg font-semibold text-slate-900">Datos de la orden</h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Proveedor
                            <select name="provider_id" class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500" required>
                                <option value="">Selecciona un proveedor</option>
                                @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
                                        {{ $provider->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block text-sm font-medium text-slate-700">
                            Medio de pago
                            <select name="payment_method_id" class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500" required>
                                <option value="">Selecciona un medio de pago</option>
                                @foreach ($methods as $method)
                                    <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                                        {{ $method->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block text-sm font-medium text-slate-700">
                            Fecha de la orden
                            <input
                                type="date"
                                name="purchase_date"
                                value="{{ old('purchase_date', now()->toDateString()) }}"
                                max="{{ now()->toDateString() }}"
                                class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                required
                            >
                        </label>

                        <label class="block text-sm font-medium text-slate-700">
                            Fecha prometida de llegada
                            <input
                                type="date"
                                name="expected_delivery_date"
                                value="{{ old('expected_delivery_date') }}"
                                min="{{ old('purchase_date', now()->toDateString()) }}"
                                class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                            >
                        </label>

                        <label class="block text-sm font-medium text-slate-700 sm:col-span-2">
                            Plazo de pago en días
                            <input
                                type="number"
                                name="payment_term_days"
                                min="0"
                                max="365"
                                value="{{ old('payment_term_days', 0) }}"
                                class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500 sm:max-w-xs"
                                required
                            >
                            <span class="mt-1 block text-xs text-slate-500">Usa 0 si el pago es de contado.</span>
                        </label>
                    </div>

                    <label class="mt-4 block text-sm font-medium text-slate-700">
                        Observaciones de la orden
                        <textarea
                            name="observations"
                            rows="3"
                            maxlength="5000"
                            class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                            placeholder="Instrucciones para el proveedor o detalles del pedido"
                        >{{ old('observations') }}</textarea>
                    </label>
                </section>

                <section class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-slate-900">Productos solicitados</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Indica la tarifa de la cotización y si el precio del proveedor ya incluye el IVA.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(row, index) in rows" :key="index">
                            <article class="rounded-2xl bg-pink-50/70 p-4 shadow-sm sm:p-5">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="font-semibold text-slate-900">
                                        Producto <span x-text="index + 1"></span>
                                    </h3>
                                    <button
                                        type="button"
                                        @click="remove(index)"
                                        class="text-sm font-semibold text-rose-700 hover:text-rose-900"
                                    >
                                        Quitar
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <label class="block text-sm font-medium text-slate-700 sm:col-span-2 lg:col-span-3">
                                        Producto
                                        <select
                                            :name="`items[${index}][product_id]`"
                                            x-model="row.product_id"
                                            @change="setProduct(row)"
                                            class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                            required
                                        >
                                            <option value="">Selecciona un producto</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option :value="product.id" x-text="product.name"></option>
                                            </template>
                                        </select>
                                    </label>

                                    <label class="block text-sm font-medium text-slate-700">
                                        Cantidad solicitada
                                        <input
                                            type="number"
                                            min="1"
                                            :name="`items[${index}][quantity]`"
                                            x-model.number="row.quantity"
                                            class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                            required
                                        >
                                    </label>

                                    <label class="block text-sm font-medium text-slate-700">
                                        Precio unitario cotizado
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            :name="`items[${index}][price]`"
                                            x-model.number="row.price"
                                            class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                            required
                                        >
                                    </label>

                                    <label class="block text-sm font-medium text-slate-700">
                                        IVA de compra (%)
                                        <input
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            :name="`items[${index}][iva_percentage]`"
                                            x-model.number="row.iva_percentage"
                                            class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                            required
                                        >
                                    </label>

                                    <label class="block text-sm font-medium text-slate-700 sm:col-span-2 lg:col-span-1">
                                        Tratamiento del precio
                                        <select
                                            :name="`items[${index}][iva_included]`"
                                            x-model="row.iva_included"
                                            class="mt-1 w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500"
                                            required
                                        >
                                            <option value="1">El precio ya incluye IVA</option>
                                            <option value="0">Agregar IVA al precio</option>
                                        </select>
                                    </label>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-white p-4 text-sm sm:grid-cols-3">
                                    <p>Base: <strong x-text="format(baseSubtotal(row))"></strong></p>
                                    <p>IVA: <strong x-text="format(ivaAmount(row))"></strong></p>
                                    <p>Total de línea: <strong x-text="format(lineTotal(row))"></strong></p>
                                </div>
                            </article>
                        </template>
                    </div>

                    <div class="mt-5 flex flex-col gap-4 border-t border-pink-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <button
                            type="button"
                            @click="add()"
                            class="rounded-xl bg-pink-100 px-4 py-2.5 font-semibold text-pink-800 hover:bg-pink-200"
                        >
                            + Agregar producto
                        </button>

                        <div class="w-full space-y-2 rounded-xl bg-pink-50 p-4 sm:max-w-sm">
                            <p class="flex justify-between gap-4">
                                <span>Base de compra:</span>
                                <strong x-text="format(totalBase)"></strong>
                            </p>
                            <p class="flex justify-between gap-4">
                                <span>IVA:</span>
                                <strong x-text="format(totalIva)"></strong>
                            </p>
                            <p class="flex justify-between gap-4 border-t border-pink-200 pt-2 text-lg">
                                <span>Total orden:</span>
                                <strong x-text="format(total)"></strong>
                            </p>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-pink-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-pink-700 sm:w-auto"
                    >
                        Crear orden de compra
                    </button>
                    <a
                        href="{{ route('purchases.index') }}"
                        class="text-center font-medium text-pink-800 hover:text-pink-950"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>