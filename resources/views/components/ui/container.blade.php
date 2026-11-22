@props(['width' => 'max-w-7xl'])
<div class="min-h-screen bg-pink-50 py-8">
    <div {{ $attributes->merge(['class' => 'mx-auto space-y-6 px-4 sm:px-6 lg:px-8 '.$width]) }}>
        {{ $slot }}
    </div>
</div>
