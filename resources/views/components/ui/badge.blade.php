@props(['tone' => 'neutral'])
@php
    $tones = [
        'success' => 'bg-emerald-50 text-emerald-700',
        'warning' => 'bg-amber-50 text-amber-700',
        'danger' => 'bg-rose-50 text-rose-700',
        'brand' => 'bg-pink-50 text-pink-800',
        'neutral' => 'bg-zinc-100 text-zinc-600',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold '.($tones[$tone] ?? $tones['neutral'])]) }}>
    {{ $slot }}
</span>
