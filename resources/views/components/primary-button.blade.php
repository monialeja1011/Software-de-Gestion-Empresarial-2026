<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-pink-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-pink-800 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
