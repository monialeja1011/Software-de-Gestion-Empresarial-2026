<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-lg border border-pink-200 bg-white px-4 py-2 text-sm font-semibold text-pink-800 transition-colors hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-pink-200 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
