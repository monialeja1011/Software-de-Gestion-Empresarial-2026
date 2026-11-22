<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full min-w-[640px] text-left text-sm [&_thead_th]:px-6 [&_thead_th]:py-3 [&_thead_th]:font-medium [&_thead_th]:text-slate-400 [&_tbody_td]:px-6 [&_tbody_td]:py-3.5 [&_tbody_td]:align-middle [&_tbody_tr]:border-t [&_tbody_tr]:border-pink-50 [&_tbody_tr]:transition-colors [&_tbody_tr:hover]:bg-pink-50/60']) }}>
        {{ $slot }}
    </table>
</div>
