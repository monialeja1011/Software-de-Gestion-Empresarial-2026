<label class="block mb-3">
    Nombre:
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="w-full border rounded p-2" required>
</label>

<label class="block mb-3">
    Descripción:
    <textarea name="description" class="w-full border rounded p-2">{{ old('description', $category->description ?? '') }}</textarea>
</label>

<label class="flex items-center gap-2 mb-4">
    <input type="hidden" name="active" value="0">
    <input type="checkbox" name="active" value="1" @checked(old('active', $category->active ?? true))>
    Activa
</label>
