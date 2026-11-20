<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Ajuste de inventario</h1>
            <span class="text-sm text-slate-400">{{ $product->name }}</span>
        </div>
    </x-slot>

    @php
        $fieldClass = 'mt-1 rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm focus:border-pink-400 focus:ring-pink-200';
    @endphp

    <div class="min-h-screen bg-[#fdf5f9] py-8">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form id="adjust-form" method="POST" action="{{ route('inventory.adjust.store', $product) }}"
                  class="rounded-2xl border border-pink-100 bg-white">
                @csrf

                <div class="space-y-6 px-6 py-6">
                    <p class="text-sm leading-relaxed text-slate-600">
                        Un ajuste se hace después de un <strong class="font-semibold text-slate-800">conteo físico</strong>:
                        escribe la cantidad que realmente hay en bodega y el sistema registrará la diferencia
                        como entrada o salida en el kardex.
                    </p>

                    <div class="flex items-center justify-between rounded-xl bg-pink-50 px-5 py-4">
                        <span class="text-sm text-slate-600">Stock en el sistema</span>
                        <span class="text-2xl font-semibold text-pink-800 tabular-nums">{{ $product->stock }}</span>
                    </div>

                    <div>
                        <label for="counted_stock" class="text-sm font-medium text-slate-700">Cantidad contada en bodega</label>
                        <input id="counted_stock" name="counted_stock" type="number" min="0" step="1" required
                               value="{{ old('counted_stock', $product->stock) }}"
                               class="{{ $fieldClass }} block w-48">
                        @error('counted_stock')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror

                        <p id="difference" class="mt-2 text-sm text-slate-500" aria-live="polite"></p>
                    </div>

                    <div>
                        <label for="reason" class="text-sm font-medium text-slate-700">Motivo del ajuste (obligatorio)</label>
                        <textarea id="reason" name="reason" rows="3" required
                                  placeholder="Ej.: Conteo mensual de octubre: 1 unidad dañada por humedad"
                                  class="{{ $fieldClass }} block w-full">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-5 border-t border-pink-100 px-6 py-4">
                    <button type="submit"
                            class="rounded-lg bg-gradient-to-br from-pink-700 to-fuchsia-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-pink-800 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2">
                        Registrar ajuste
                    </button>
                    <a href="{{ route('inventory.index', ['product_id' => $product->id]) }}"
                       class="text-sm text-slate-500 underline-offset-2 hover:text-pink-800 hover:underline">
                        Cancelar
                    </a>
                </div>
            </form>

            <section class="rounded-2xl border border-pink-100 bg-white">
                <header class="border-b border-pink-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Últimos movimientos</h2>
                    <p class="text-sm text-slate-400">Para que compares con tu conteo</p>
                </header>

                <ul class="divide-y divide-pink-50">
                    @forelse ($lastMovements as $m)
                        <li class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 px-6 py-3.5 text-sm">
                            <div>
                                <span class="text-slate-700">
                                    {{ method_exists($m, 'conceptLabel') ? $m->conceptLabel() : ucfirst(str_replace('_', ' ', $m->concept)) }}
                                </span>
                                <span class="ml-2 text-slate-400">{{ $m->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-4 tabular-nums">
                                @if ($m->type === 'entrada')
                                    <span class="font-semibold text-emerald-700">+{{ $m->quantity }}</span>
                                @else
                                    <span class="font-semibold text-rose-700">−{{ $m->quantity }}</span>
                                @endif
                                <span class="text-slate-500">Saldo: <strong class="font-semibold text-slate-800">{{ $m->balance }}</strong></span>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-10 text-center text-sm text-slate-500">Este producto aún no tiene movimientos.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const systemStock = @json((int) $product->stock);
            const input = document.getElementById('counted_stock');
            const message = document.getElementById('difference');
            const form = document.getElementById('adjust-form');

            const plural = (n) => (n === 1 ? 'unidad' : 'unidades');

            const update = () => {
                if (input.value === '') { message.textContent = ''; return; }
                const diff = parseInt(input.value, 10) - systemStock;

                if (Number.isNaN(diff)) {
                    message.textContent = '';
                } else if (diff === 0) {
                    message.className = 'mt-2 text-sm text-slate-500';
                    message.textContent = 'Sin diferencia: no se registrará ningún movimiento.';
                } else if (diff > 0) {
                    message.className = 'mt-2 text-sm font-medium text-emerald-700';
                    message.textContent = `Se registrará una entrada de ${diff} ${plural(diff)}.`;
                } else {
                    message.className = 'mt-2 text-sm font-medium text-rose-700';
                    message.textContent = `Se registrará una salida de ${Math.abs(diff)} ${plural(Math.abs(diff))}.`;
                }
            };

            input.addEventListener('input', update);
            update();

            form.addEventListener('submit', (event) => {
                if (!confirm('¿Registrar el ajuste de inventario?')) event.preventDefault();
            });
        });
    </script>
</x-app-layout>