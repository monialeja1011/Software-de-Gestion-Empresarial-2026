@php
    $brandPalette = \App\Models\CompanySetting::current()->palette();
@endphp

<style>
    :root {
@foreach ($brandPalette as $shade => $rgb)
        --brand-{{ $shade }}: {{ $rgb }};
@endforeach
    }
</style>