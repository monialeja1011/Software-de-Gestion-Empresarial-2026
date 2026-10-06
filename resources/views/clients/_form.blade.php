<label class="block mb-3">
    Nombre:
    <input type="text" name="name" value="{{ old('name', $client->name ?? '') }}" class="w-full border rounded p-2" required>
</label>
<label class="block mb-3">
    Email:
    <input type="email" name="email" value="{{ old('email', $client->email ?? '') }}" class="w-full border rounded p-2">
</label>
<label class="block mb-3">
    Teléfono:
    <input type="text" name="phone" value="{{ old('phone', $client->phone ?? '') }}" class="w-full border rounded p-2">
</label>
<label class="block mb-3">
    Dirección:
    <input type="text" name="address" value="{{ old('address', $client->address ?? '') }}" class="w-full border rounded p-2">
</label>
<label class="flex items-center gap-2 mb-4">
    <input type="hidden" name="active" value="0">
    <input type="checkbox" name="active" value="1" @checked(old('active', $client->active ?? true))>
    Activo
</label>
