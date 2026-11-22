@props(['name', 'label', 'checked' => false])
<label {{ $attributes->merge(['class' => 'flex items-center gap-3 text-sm text-slate-700']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)
           class="h-4 w-4 rounded border-pink-300 text-pink-700 focus:ring-pink-200">
    {{ $label }}
</label>
