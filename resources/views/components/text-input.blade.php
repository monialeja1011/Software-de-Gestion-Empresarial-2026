@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm focus:border-pink-400 focus:ring-pink-200 disabled:bg-pink-50/50']) }}>
