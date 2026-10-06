<label class="block mb-3">
    Nombre:
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full border rounded p-2" required>
</label>

<label class="block mb-3">
    Descripción:
    <textarea name="description" class="w-full border rounded p-2">{{ old('description', $product->description ?? '') }}</textarea>
</label>

<div class="grid grid-cols-2 gap-4">
    <label class="block mb-3">
        Precio:
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? '') }}" class="w-full border rounded p-2" required>
    </label>
    <label class="block mb-3">
        Stock:
        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="w-full border rounded p-2" required>
    </label>
</div>

<label class="block mb-3">
    Categoría:
    <select name="category_id" class="w-full border rounded p-2" required>
        <option value="">Seleccione...</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
</label>

<label class="flex items-center gap-2 mb-4">
    <input type="hidden" name="active" value="0">
    <input type="checkbox" name="active" value="1" @checked(old('active', $product->active ?? true))>
    Activo
</label>
