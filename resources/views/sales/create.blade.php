<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4"
             x-data="{
                products: @js($products),
                rows: @js(old('items', [])).length ? @js(old('items', [])) : [{ product_id: '', quantity: 1, price: 0 }],
                add() { this.rows.push({ product_id: '', quantity: 1, price: 0 }) },
                remove(i) { if (this.rows.length > 1) this.rows.splice(i, 1) },
                price(r) { const p = this.products.find(x => x.id == r.product_id); return p ? Number(p.price) : 0 },
                stock(r) { const p = this.products.find(x => x.id == r.product_id); return p ? p.stock : '' },
                fmt(n) { return '$' + Number(n).toLocaleString('es-CO') },
                get total() { return rows.reduce((t, r) => t + (Number(r.quantity) || 0) * price(r), 0) }
             }">
            <h1 class="text-2xl font-bold mb-6">Nueva venta</h1>

            <form action="{{ route('sales.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <label class="block">Cliente
                        <select name="client_id" class="w-full border rounded p-2" required>
                            <option value="">Seleccione...</option>
                            @foreach ($clients as $p)
                                <option value="{{ $p->id }}" @selected(old('client_id') == $p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">Medio de pago
                        <select name="payment_method_id" class="w-full border rounded p-2" required>
                            <option value="">Seleccione...</option>
                            @foreach ($methods as $m)
                                <option value="{{ $m->id }}" @selected(old('payment_method_id') == $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">Fecha
                        <input type="date" name="sale_date" value="{{ old('sale_date', now()->toDateString()) }}" class="w-full border rounded p-2" required>
                    </label>
                </div>

                <table class="w-full bg-white shadow rounded mb-4">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2 text-left">Producto</th>
                            <th class="p-2 text-left">Cantidad</th>
                            <th class="p-2 text-right">Precio</th>
                            <th class="p-2 text-right">Stock</th>
                            <th class="p-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in rows" :key="i">
                            <tr class="border-b">
                                <td class="p-2">
                                    <select :name="`items[${i}][product_id]`" x-model="row.product_id"  class="w-full border rounded p-1" required>
                                        <option value="">Seleccione...</option>
                                        <template x-for="p in products" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="p-2"><input type="number" min="1" :max="stock(row)" :name="`items[${i}][quantity]`" x-model.number="row.quantity" class="w-24 border rounded p-1" required></td>
                                <td class="p-2 text-right" x-text="fmt(price(row))"></td>
                                <td class="p-2 text-right" x-text="stock(row)"></td>
                                <td class="p-2 text-right"><button type="button" @click="remove(i)" class="text-red-600">Quitar</button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="flex items-center justify-between">
                    <button type="button" @click="add()" class="text-blue-600">+ Agregar producto</button>
                    <div class="text-xl font-bold">Total: <span x-text="fmt(total)"></span></div>
                </div>

                <div class="mt-6">
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
                    <a href="{{ route('sales.index') }}" class="ms-3 text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
