@props([
    'name',
    'label',
    'as' => 'input',
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
])
@php
    $control = 'mt-1 block w-full rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-pink-400 focus:ring-pink-200 read-only:bg-pink-50/50 disabled:bg-pink-50/50 disabled:text-slate-500';
    $id = $attributes->get('id', $name);
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="text-sm font-medium text-slate-700">
        {{ $label }}@if ($required)<span class="text-pink-700" aria-hidden="true"> *</span>@endif
    </label>

    @if ($as === 'select')
        <select id="{{ $id }}" name="{{ $name }}" @required($required) {{ $attributes->except(['class', 'id']) }} class="{{ $control }}">
            {{ $slot }}
        </select>
    @elseif ($as === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" @required($required) {{ $attributes->except(['class', 'id']) }} class="{{ $control }}">{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required($required) {{ $attributes->except(['class', 'id']) }} class="{{ $control }}">
    @endif

    @if ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
    @enderror
</div>
