@props(['neutral' => false])
@php
    $brandPalette = $neutral
        ? [
            50 => '251 248 244',
            100 => '245 239 231',
            200 => '232 221 208',
            300 => '212 195 174',
            400 => '176 154 130',
            500 => '140 118 96',
            600 => '111 91 73',
            700 => '87 72 56',
            800 => '63 52 42',
            900 => '42 34 27',
        ]
        : \App\Models\CompanySetting::current()->palette();
@endphp
<style>
    :root {
@foreach ($brandPalette as $shade => $rgb)
        --brand-{{ $shade }}: {{ $rgb }};
@endforeach
@if ($neutral)
        --beauty-plum: #3a2f27;
@endif
    }
</style>