<label class="mb-3 block">
    Nombre:
    <input
        type="text"
        name="name"
        value="{{ old('name', $product->name ?? '') }}"
        class="w-full rounded border p-2"
        required
    >
</label>

<label class="mb-3 block">
    Descripción:
    <textarea
        name="description"
        class="w-full rounded border p-2"
    >{{ old('description', $product->description ?? '') }}</textarea>
</label>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <label class="mb-3 block">
        Precio de venta:
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $product->price ?? '') }}"
            class="w-full rounded border p-2"
            required
        >
    </label>

    <label class="mb-3 block">
        Stock:
        <input
            type="number"
            min="0"
            name="stock"
            value="{{ old('stock', $product->stock ?? 0) }}"
            class="w-full rounded border p-2"
            required
        >
    </label>
</div>

<label class="mb-3 block">
    Categoría:
    <select name="category_id" class="w-full rounded border p-2" required>
        <option value="">Seleccione...</option>
        @foreach ($categories as $cat)
            <option
                value="{{ $cat->id }}"
                @selected(old('category_id', $product->category_id ?? '') == $cat->id)
            >
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</label>

<label class="mb-3 block">
    IVA (%):
    <input
        type="number"
        name="iva_percentage"
        min="0"
        max="100"
        step="0.01"
        value="{{ old('iva_percentage', $product->iva_percentage ?? 0) }}"
        class="w-full rounded border p-2"
        required
    >
    <span class="text-sm text-gray-600">
        Escribe 0 si no aplica IVA.
    </span>
</label>

<label class="mb-4 flex items-center gap-2">
    <input type="hidden" name="active" value="0">
    <input
        type="checkbox"
        name="active"
        value="1"
        @checked(old('active', $product->active ?? true))
    >
    Activo
</label>