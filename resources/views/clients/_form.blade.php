<div class="grid gap-4 sm:grid-cols-2">
    <x-ui.field name="name" label="Nombre" :value="$client->name ?? ''" :required="true" class="sm:col-span-2" />
    <x-ui.field name="document_number" label="Cédula" :value="$client->document_number ?? ''" :required="true" inputmode="numeric" maxlength="15" placeholder="Solo números" hint="Escríbela sin puntos ni espacios." />
    <x-ui.field name="phone" label="Teléfono" type="tel" :value="$client->phone ?? ''" />
    <x-ui.field name="email" label="Correo electrónico" type="email" :value="$client->email ?? ''" />
    <x-ui.field name="address" label="Dirección" :value="$client->address ?? ''" />
    <x-ui.checkbox name="active" label="Cliente activo" :checked="old('active', $client->active ?? true)" class="sm:col-span-2" />
</div>
