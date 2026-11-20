<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Kardex de inventario</h1>
            <span class="text-sm text-slate-400">Entradas, salidas y saldo por producto</span>
        </div>
    </x-slot>

    @php
        $selected = $product ?? ($products->firstWhere('id', (int) request('product_id')));
        $hasAdvanced = isset($concepts);
        $hasTotals = isset($totals) && is_array($totals);
        $fieldClass = 'mt-1 w-full rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm focus:border-pink-400 focus:ring-pink-200';
    @endphp

    <div class="min-h-screen bg-[#fdf5f9] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-pink-100 bg-white p-5">
                <form method="GET" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-4">
                        <label for="product_id" class="text-sm font-medium text-slate-600">Producto</label>
                        <select id="product_id" name="product_id" class="{{ $fieldClass }}">
                            <option value="">Todos los productos</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" @selected((int) request('product_id') === $p->id)>
                                    {{ $p->name }} (stock: {{ $p->stock }}){{ method_exists($p, 'trashed') && $p->trashed() ? ' — en papelera' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($hasAdvanced)
                        <div class="lg:col-span-2">
                            <label for="concept" class="text-sm font-medium text-slate-600">Concepto</label>
                            <select id="concept" name="concept" class="{{ $fieldClass }}">
                                <option value="">Todos</option>
                                @foreach ($concepts as $value => $label)
                                    <option value="{{ $value }}" @selected(request('concept') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label for="from" class="text-sm font-medium text-slate-600">Desde</label>
                            <input id="from" type="date" name="from" value="{{ request('from') }}" class="{{ $fieldClass }}">
                        </div>
                        <div class="lg:col-span-2">
                            <label for="to" class="text-sm font-medium text-slate-600">Hasta</label>
                            <input id="to" type="date" name="to" value="{{ request('to') }}" class="{{ $fieldClass }}">
                        </div>
                    @endif

                    <div class="flex items-center gap-3 lg:col-span-2">
                        <button type="submit"
                                class="rounded-lg bg-gradient-to-br from-pink-700 to-fuchsia-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-pink-800 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2">
                            Consultar
                        </button>
                        @if (request()->hasAny(['product_id', 'concept', 'from', 'to']))
                            <a href="{{ url()->current() }}" class="text-sm text-slate-500 underline-offset-2 hover:text-pink-800 hover:underline">Limpiar</a>
                        @endif
                    </div>
                </form>
            </section>

            @if ($selected)
                <section class="rounded-2xl border border-pink-100 bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $selected->name }}</h2>
                            @if ($hasTotals)
                                @if ($totals['cuadra'])
                                    <p class="mt-1 text-sm text-emerald-700">El saldo del kardex coincide con el stock del sistema.</p>
                                @else
                                    <p class="mt-1 text-sm font-medium text-rose-700">El saldo del kardex no coincide con el stock. Revisa si hubo cambios hechos fuera del sistema.</p>
                                @endif
                            @endif
                        </div>

                        <div class="flex items-center gap-4 text-sm">
                            @if (Route::has('inventory.pdf'))
                                <a href="{{ route('inventory.pdf', ['product' => $selected->id] + request()->only('from', 'to', 'concept')) }}"
                                   target="_blank"
                                   class="font-medium text-pink-800 underline-offset-2 hover:underline">Descargar PDF</a>
                            @endif
                        </div>
                    </div>

                    <dl class="grid divide-y divide-pink-100 border-t border-pink-100 sm:grid-flow-col sm:auto-cols-fr sm:divide-x sm:divide-y-0">
                        <div class="px-6 py-4">
                            <dt class="text-sm text-slate-500">Stock actual</dt>
                            <dd class="mt-1 text-2xl font-semibold text-slate-900 tabular-nums">{{ $selected->stock }}</dd>
                        </div>
                        @if ($hasTotals)
                            <div class="px-6 py-4">
                                <dt class="text-sm text-slate-500">Entradas</dt>
                                <dd class="mt-1 text-2xl font-semibold text-emerald-700 tabular-nums">{{ $totals['entradas'] }}</dd>
                            </div>
                            <div class="px-6 py-4">
                                <dt class="text-sm text-slate-500">Salidas</dt>
                                <dd class="mt-1 text-2xl font-semibold text-rose-700 tabular-nums">{{ $totals['salidas'] }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            @endif

            <section class="rounded-2xl border border-pink-100 bg-white">
                <header class="flex items-center justify-between border-b border-pink-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Movimientos</h2>
                    <p class="text-sm text-slate-400">
                        {{ $movements->total() }} {{ $movements->total() == 1 ? 'registro' : 'registros' }}
                    </p>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead>
                            <tr class="text-slate-400">
                                <th class="px-6 py-3 font-medium">Fecha</th>
                                @unless ($selected)
                                    <th class="px-3 py-3 font-medium">Producto</th>
                                @endunless
                                <th class="px-3 py-3 font-medium">Concepto</th>
                                <th class="px-3 py-3 text-right font-medium">Entrada</th>
                                <th class="px-3 py-3 text-right font-medium">Salida</th>
                                <th class="px-3 py-3 text-right font-medium">Saldo</th>
                                <th class="px-3 py-3 font-medium">Usuario</th>
                                <th class="px-6 py-3 font-medium">Observación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-50">
                            @forelse ($movements as $movement)
                                <tr class="transition-colors hover:bg-pink-50/60">
                                    <td class="whitespace-nowrap px-6 py-3.5 text-slate-700">
                                        {{ $movement->created_at->format('d/m/Y') }}
                                        <span class="ml-1 text-slate-400">{{ $movement->created_at->format('H:i') }}</span>
                                    </td>

                                    @unless ($selected)
                                        <td class="px-3 py-3.5 font-medium text-slate-800">
                                            {{ $movement->product?->name ?? 'Producto eliminado' }}
                                        </td>
                                    @endunless

                                    <td class="px-3 py-3.5 text-slate-600">
                                        {{ method_exists($movement, 'conceptLabel') ? $movement->conceptLabel() : ucfirst(str_replace('_', ' ', $movement->concept)) }}
                                        @if (method_exists($movement, 'referenceLabel') && $movement->referenceLabel())
                                            <span class="block text-xs text-pink-800">{{ $movement->referenceLabel() }}</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3.5 text-right font-semibold text-emerald-700 tabular-nums">
                                        {{ $movement->type === 'entrada' ? '+'.$movement->quantity : '' }}
                                    </td>
                                    <td class="px-3 py-3.5 text-right font-semibold text-rose-700 tabular-nums">
                                        {{ $movement->type === 'salida' ? '−'.$movement->quantity : '' }}
                                    </td>
                                    <td class="px-3 py-3.5 text-right font-semibold text-slate-900 tabular-nums">
                                        {{ $movement->balance }}
                                    </td>

                                    <td class="px-3 py-3.5 text-slate-600">{{ $movement->user?->name ?? 'Sistema' }}</td>
                                    <td class="max-w-xs px-6 py-3.5 text-slate-500">{{ $movement->notes ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selected ? 7 : 8 }}" class="px-6 py-12 text-center text-slate-500">
                                        No hay movimientos para los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($movements->hasPages())
                    <div class="border-t border-pink-100 px-6">
                        {{ $movements->withQueryString()->links() }}
                    </div>
                @endif
            </section>

            @can('ajustar-inventario')
                <section class="rounded-2xl border border-pink-100 bg-white">
                    <details @if ($errors->any()) open @endif class="group">
                        <summary class="flex cursor-pointer list-none items-center justify-between px-6 py-4">
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Ajustar inventario</h2>
                                <p class="text-sm text-slate-400">Corrige el saldo de un producto indicando el motivo</p>
                            </div>
                            <svg class="h-5 w-5 text-pink-700 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </summary>

                        <form id="adjust-form" method="POST" class="grid gap-4 border-t border-pink-100 px-6 py-5 md:grid-cols-12 md:items-end">
                            @csrf

                            <div class="md:col-span-4">
                                <label for="adjust-product" class="text-sm font-medium text-slate-600">Producto</label>
                                <select id="adjust-product" class="{{ $fieldClass }}" required>
                                    <option value="">Selecciona un producto</option>
                                    @foreach ($products as $p)
                                        @unless (method_exists($p, 'trashed') && $p->trashed())
                                            <option value="{{ $p->id }}"
                                                    data-action="{{ route('inventory.adjust', $p) }}"
                                                    data-stock="{{ $p->stock }}"
                                                    @selected($selected && $selected->id === $p->id)>
                                                {{ $p->name }}
                                            </option>
                                        @endunless
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-400">Stock actual: <span id="adjust-current" class="font-semibold text-slate-600">—</span></p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="adjust-stock" class="text-sm font-medium text-slate-600">Nuevo saldo</label>
                                <input id="adjust-stock" type="number" name="stock" min="0" value="{{ old('stock') }}" class="{{ $fieldClass }}" required>
                                @error('stock') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-4">
                                <label for="adjust-notes" class="text-sm font-medium text-slate-600">Motivo (obligatorio)</label>
                                <input id="adjust-notes" type="text" name="notes" minlength="5" maxlength="1000"
                                       value="{{ old('notes') }}" placeholder="Describe la razón del ajuste"
                                       class="{{ $fieldClass }}" required>
                                @error('notes') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <button type="submit"
                                        class="w-full rounded-lg bg-gradient-to-br from-pink-700 to-fuchsia-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-pink-800 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2">
                                    Guardar ajuste
                                </button>
                            </div>
                        </form>
                    </details>
                </section>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const select = document.getElementById('adjust-product');
                        const form = document.getElementById('adjust-form');
                        const stock = document.getElementById('adjust-stock');
                        const current = document.getElementById('adjust-current');
                        if (!select || !form) return;

                        const sync = () => {
                            const option = select.selectedOptions[0];
                            if (!option || !option.dataset.action) {
                                form.removeAttribute('action');
                                current.textContent = '—';
                                return;
                            }
                            form.action = option.dataset.action;
                            current.textContent = option.dataset.stock;
                            if (!stock.value) stock.value = option.dataset.stock;
                        };

                        select.addEventListener('change', () => {
                            stock.value = '';
                            sync();
                        });
                        sync();
                    });
                </script>
            @endcan
        </div>
    </div>
</x-app-layout>