<div class="space-y-4">
    <x-ui.field name="name" label="Nombre" :value="$category->name ?? ''" :required="true" />
    <x-ui.field as="textarea" name="description" label="Descripción" :value="$category->description ?? ''" rows="3" />
    <x-ui.checkbox name="active" label="Categoría activa" :checked="old('active', $category->active ?? true)" />
</div>
