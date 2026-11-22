@php
    $control = 'mt-1 block w-full rounded-lg border-pink-100 bg-pink-50/50 text-sm text-slate-700 shadow-sm';
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-ui.field name="name" label="Nombre" :value="$product->name ?? ''" :required="true" class="sm:col-span-2" />

    <x-ui.field as="textarea" name="description" label="Descripción" :value="$product->description ?? ''" rows="3" class="sm:col-span-2" />

    <x-ui.field name="price" label="Precio de venta" type="number" step="0.01" min="0" :value="$product->price ?? ''" :required="true" />

    @if (isset($product))
        <div>
            <label class="text-sm font-medium text-slate-700">Stock</label>
            <input type="number" value="{{ $product->stock }}" readonly class="{{ $control }}">
            <p class="mt-1 text-xs text-slate-400">El stock se administra desde el módulo Kardex.</p>
        </div>
    @else
        <x-ui.field name="stock" label="Stock inicial" type="number" min="0" :value="0" :required="true" hint="Este saldo inicial se registrará en el Kardex." />
    @endif

    <x-ui.field as="select" name="category_id" label="Categoría" :required="true">
        <option value="">Seleccione...</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                {{ $cat->name }}
            </option>
        @endforeach
    </x-ui.field>

    <x-ui.field name="iva_percentage" label="IVA (%)" type="number" min="0" max="100" step="0.01" :value="$product->iva_percentage ?? 0" :required="true" hint="Escribe 0 si no aplica IVA." />

    <x-ui.checkbox name="active" label="Producto activo" :checked="old('active', $product->active ?? true)" class="sm:col-span-2" />
</div>
