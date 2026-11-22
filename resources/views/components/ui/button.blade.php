@props(['variant' => 'primary', 'href' => null, 'type' => 'submit'])
@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50';

    $variants = [
        'primary' => 'bg-pink-700 text-white shadow-sm hover:bg-pink-800 focus:ring-pink-300',
        'secondary' => 'border border-pink-200 bg-white text-pink-800 hover:bg-pink-50 focus:ring-pink-200',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-300',
        'ghost' => 'text-pink-800 hover:bg-pink-50 focus:ring-pink-200',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
