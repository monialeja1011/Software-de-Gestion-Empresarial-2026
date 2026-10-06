<label class="block mb-3">
    Nombre:
    <input type="text" name="name" value="{{ old('name', $provider->name ?? '') }}" class="w-full border rounded p-2" required>
</label>
<label class="block mb-3">
    Email:
    <input type="email" name="email" value="{{ old('email', $provider->email ?? '') }}" class="w-full border rounded p-2">
</label>
<label class="block mb-3">
    Teléfono:
    <input type="text" name="phone" value="{{ old('phone', $provider->phone ?? '') }}" class="w-full border rounded p-2">
</label>
<label class="block mb-3">
    Dirección:
    <input type="text" name="address" value="{{ old('address', $provider->address ?? '') }}" class="w-full border rounded p-2">
</label>
<label class="flex items-center gap-2 mb-4">
    <input type="hidden" name="active" value="0">
    <input type="checkbox" name="active" value="1" @checked(old('active', $provider->active ?? true))>
    Activo
</label>
