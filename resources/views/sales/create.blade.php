<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Nueva venta</h1>
            <span class="text-sm text-slate-400">El precio de cada producto ya incluye el IVA</span>
        </div>
    </x-slot>

    @php
        $control = 'mt-1 block w-full rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-pink-400 focus:ring-pink-200 disabled:bg-pink-50/50 disabled:text-slate-400';
        $cell = 'w-full rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm focus:border-pink-400 focus:ring-pink-200';
    @endphp

    <x-ui.container width="max-w-6xl">
        <form
            action="{{ route('sales.store') }}"
            method="POST"
            class="space-y-6"
            x-data="{
                lookupUrl: @js(route('clients.lookup')),

                clientDoc: @js(old('client_document', '')),
                clientName: @js(old('client_name', '')),
                clientPhone: @js(old('client_phone', '')),
                clientEmail: @js(old('client_email', '')),
                clientStatus: 'idle',
                lookupFailed: false,
                lookupTimer: null,
                lookupAbort: null,

                get digits() {
                    return String(this.clientDoc || '').replace(/\D/g, '');
                },

                get clientLocked() {
                    return this.clientStatus === 'found' || this.clientStatus === 'inactive';
                },

                get clientEnabled() {
                    return ['found', 'new', 'inactive'].includes(this.clientStatus);
                },

                onDocInput() {
                    clearTimeout(this.lookupTimer);
                    this.lookupFailed = false;

                    if (this.clientLocked) {
                        this.clientName = '';
                    }

                    if (this.digits.length < 5) {
                        this.clientStatus = 'idle';
                        return;
                    }

                    if (this.digits.length > 15) {
                        this.clientStatus = 'invalid';
                        return;
                    }

                    this.clientStatus = 'checking';
                    this.lookupTimer = setTimeout(() => this.lookup(), 350);
                },

                async lookup() {
                    const digits = this.digits;

                    if (this.lookupAbort) {
                        this.lookupAbort.abort();
                    }
                    this.lookupAbort = new AbortController();

                    try {
                        const response = await fetch(`${this.lookupUrl}?document=${encodeURIComponent(digits)}`, {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                            signal: this.lookupAbort.signal
                        });

                        if (!response.ok) {
                            throw new Error('lookup');
                        }

                        const data = await response.json();

                        // Si siguió escribiendo mientras llegaba la respuesta, se ignora.
                        if (digits !== this.digits) {
                            return;
                        }

                        if (!data.valid) {
                            this.clientStatus = 'invalid';
                        } else if (data.found) {
                            this.clientName = data.name;
                            this.clientStatus = data.active ? 'found' : 'inactive';
                        } else {
                            this.clientStatus = 'new';
                        }
                    } catch (error) {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        // Sin conexión con el servidor: se deja escribir el nombre; si la
                        // cédula ya existe, al guardar se usa el cliente registrado.
                        this.lookupFailed = true;
                        this.clientStatus = 'new';
                    }
                },

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
            x-init="if (digits.length >= 5) { clientStatus = 'checking'; lookup(); }"
        >
            @csrf

            <div class="grid gap-6 lg:grid-cols-5">
                {{-- Cliente: se busca por cédula --}}
                <x-ui.card title="Cliente" class="lg:col-span-3">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="client_document" class="text-sm font-medium text-slate-700">Cédula <span class="text-pink-700" aria-hidden="true">*</span></label>
                            <input
                                id="client_document"
                                name="client_document"
                                type="text"
                                inputmode="numeric"
                                maxlength="20"
                                autocomplete="off"
                                placeholder="Escribe la cédula"
                                x-model="clientDoc"
                                @input="onDocInput()"
                                required
                                class="{{ $control }}"
                            >
                            @error('client_document')
                                <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="client_name" class="text-sm font-medium text-slate-700">Nombre del cliente <span x-show="clientStatus === 'new'" x-cloak class="text-pink-700" aria-hidden="true">*</span></label>
                            <input
                                id="client_name"
                                name="client_name"
                                type="text"
                                maxlength="150"
                                autocomplete="off"
                                x-model="clientName"
                                :readonly="clientLocked"
                                :disabled="!clientEnabled"
                                :required="clientStatus === 'new'"
                                :placeholder="clientEnabled ? 'Nombre completo' : 'Primero escribe la cédula'"
                                :class="clientLocked ? '!bg-pink-50/60' : ''"
                                class="{{ $control }}"
                            >
                            @error('client_name')
                                <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Cliente nuevo: se piden sus datos de contacto --}}
                    <div x-show="clientStatus === 'new'" x-cloak x-transition.opacity
                         class="mt-4 grid gap-4 border-t border-pink-50 pt-4 sm:grid-cols-2">
                        <div>
                            <label for="client_phone" class="text-sm font-medium text-slate-700">Teléfono <span class="text-pink-700" aria-hidden="true">*</span></label>
                            <input
                                id="client_phone"
                                name="client_phone"
                                type="tel"
                                inputmode="tel"
                                maxlength="20"
                                autocomplete="off"
                                placeholder="Número de contacto"
                                x-model="clientPhone"
                                :disabled="clientStatus !== 'new'"
                                :required="clientStatus === 'new'"
                                class="{{ $control }}"
                            >
                            @error('client_phone')
                                <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="client_email" class="text-sm font-medium text-slate-700">Correo electrónico <span class="text-pink-700" aria-hidden="true">*</span></label>
                            <input
                                id="client_email"
                                name="client_email"
                                type="email"
                                maxlength="255"
                                autocomplete="off"
                                placeholder="nombre@correo.com"
                                x-model="clientEmail"
                                :disabled="clientStatus !== 'new'"
                                :required="clientStatus === 'new'"
                                class="{{ $control }}"
                            >
                            @error('client_email')
                                <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <p class="mt-3 min-h-[1.25rem] text-sm" aria-live="polite">
                        <span x-show="clientStatus === 'checking'" x-cloak class="text-slate-500">Buscando cliente…</span>
                        <span x-show="clientStatus === 'found'" x-cloak class="text-emerald-700">Cliente registrado: el nombre se completó solo.</span>
                        <span x-show="clientStatus === 'new'" x-cloak class="text-amber-700">
                            <template x-if="lookupFailed"><span>No se pudo verificar la cédula. Completa los datos; si ya existe, se usará el cliente registrado.</span></template>
                            <template x-if="!lookupFailed"><span>Esta cédula no está registrada. Completa sus datos y el cliente se creará con esta venta.</span></template>
                        </span>
                        <span x-show="clientStatus === 'inactive'" x-cloak class="text-rose-700">Este cliente está inactivo. Actívalo en Clientes para poder venderle.</span>
                        <span x-show="clientStatus === 'invalid'" x-cloak class="text-rose-700">La cédula debe tener entre 5 y 15 números.</span>
                    </p>
                </x-ui.card>

                <x-ui.card title="Datos de la venta" class="lg:col-span-2">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                        <x-ui.field as="select" name="payment_method_id" label="Medio de pago" :required="true">
                            <option value="">Seleccione...</option>
                            @foreach ($methods as $method)
                                <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </x-ui.field>

                        <x-ui.field name="sale_date" label="Fecha" type="date" :value="now()->toDateString()" :required="true" max="{{ now()->toDateString() }}" />
                    </div>
                </x-ui.card>
            </div>

            {{-- Productos --}}
            <x-ui.card flush title="Productos">
                <x-slot name="actions">
                    <x-ui.button type="button" variant="secondary" @click="add()">+ Agregar producto</x-ui.button>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left text-sm">
                        <thead class="text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Producto</th>
                                <th class="px-4 py-3 font-medium">Cantidad</th>
                                <th class="px-4 py-3 text-right font-medium">Precio (IVA incluido)</th>
                                <th class="px-4 py-3 text-center font-medium">IVA</th>
                                <th class="px-4 py-3 text-right font-medium">Base sin IVA</th>
                                <th class="px-4 py-3 text-right font-medium">IVA incluido</th>
                                <th class="px-4 py-3 text-right font-medium">Total línea</th>
                                <th class="px-4 py-3 text-right font-medium">Stock</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <template x-for="(row, i) in rows" :key="i">
                                <tr class="border-t border-pink-50">
                                    <td class="px-4 py-3">
                                        <select
                                            :name="`items[${i}][product_id]`"
                                            x-model="row.product_id"
                                            class="{{ $cell }}"
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

                                    <td class="px-4 py-3">
                                        <input
                                            type="number"
                                            min="1"
                                            :max="stock(row)"
                                            :name="`items[${i}][quantity]`"
                                            x-model.number="row.quantity"
                                            class="{{ $cell }} w-24"
                                            required
                                        >
                                    </td>

                                    <td class="px-4 py-3 text-right tabular-nums text-slate-700" x-text="fmt(price(row))"></td>
                                    <td class="px-4 py-3 text-center text-slate-600" x-text="ivaLabel(row)"></td>
                                    <td class="px-4 py-3 text-right tabular-nums text-slate-700" x-text="fmt(lineSubtotal(row))"></td>
                                    <td class="px-4 py-3 text-right tabular-nums text-slate-700" x-text="fmt(lineIva(row))"></td>
                                    <td class="px-4 py-3 text-right font-semibold tabular-nums text-slate-900" x-text="fmt(lineTotal(row))"></td>
                                    <td class="px-4 py-3 text-right tabular-nums text-slate-600" x-text="stock(row)"></td>

                                    <td class="px-4 py-3 text-right">
                                        <button type="button" @click="remove(i)"
                                                class="text-sm font-medium text-rose-700 underline-offset-2 hover:underline">
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <div class="grid gap-6 lg:grid-cols-5">
                <div class="lg:col-span-3">
                    <x-ui.field as="textarea" name="notes" label="Notas (opcional)" rows="4" maxlength="500" />
                </div>

                <x-ui.card class="lg:col-span-2">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-600">Subtotal antes de IVA</dt>
                            <dd class="tabular-nums text-slate-800" x-text="fmt(subtotal)"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-600">IVA incluido</dt>
                            <dd class="tabular-nums text-slate-800" x-text="fmt(tax)"></dd>
                        </div>
                        <div class="flex items-baseline justify-between border-t border-pink-100 pt-3">
                            <dt class="text-base font-semibold text-slate-900">Total a pagar</dt>
                            <dd class="text-2xl font-semibold tabular-nums text-pink-800" x-text="fmt(total)"></dd>
                        </div>
                    </dl>
                </x-ui.card>
            </div>

            <div class="flex items-center gap-4">
                <x-ui.button x-bind:disabled="clientStatus === 'inactive' || clientStatus === 'checking'">Registrar venta</x-ui.button>
                <x-ui.link :href="route('sales.index')">Cancelar</x-ui.link>
            </div>
        </form>
    </x-ui.container>
</x-app-layout>
