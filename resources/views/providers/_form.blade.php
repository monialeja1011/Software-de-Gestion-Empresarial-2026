<div class="grid gap-4 sm:grid-cols-2">
    <x-ui.field name="name" label="Nombre" :value="$provider->name ?? ''" :required="true" class="sm:col-span-2" />
    <x-ui.field name="email" label="Correo electrónico" type="email" :value="$provider->email ?? ''" />
    <x-ui.field name="phone" label="Teléfono" type="tel" :value="$provider->phone ?? ''" />
    <x-ui.field name="address" label="Dirección" :value="$provider->address ?? ''" class="sm:col-span-2" />
    <x-ui.checkbox name="active" label="Proveedor activo" :checked="old('active', $provider->active ?? true)" class="sm:col-span-2" />
</div>
