@props(['title' => null, 'subtitle' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 'rounded-2xl border border-pink-100 bg-white']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-pink-100 px-6 py-4">
            <div>
                @if ($title)
                    <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="text-sm text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div @class(['px-6 py-5' => ! $flush])>
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="flex flex-wrap items-center gap-4 border-t border-pink-100 px-6 py-4">
            {{ $footer }}
        </footer>
    @endisset
</section>
