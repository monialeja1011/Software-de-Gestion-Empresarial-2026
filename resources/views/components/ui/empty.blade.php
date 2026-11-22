@props(['colspan' => 1])
<tr class="hover:!bg-transparent">
    <td colspan="{{ $colspan }}" class="!px-6 !py-12 text-center text-sm text-slate-500">
        {{ $slot }}
    </td>
</tr>
