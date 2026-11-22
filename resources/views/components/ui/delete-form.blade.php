@props(['action', 'confirm' => '¿Eliminar este registro?', 'label' => 'Eliminar'])
<form action="{{ $action }}" method="POST" data-confirm="{{ $confirm }}" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-sm font-medium text-rose-700 underline-offset-2 hover:underline">{{ $label }}</button>
</form>
