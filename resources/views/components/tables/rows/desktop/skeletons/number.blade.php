@isset($responsive)
    <td class="hidden text-right lg:table-cell">
        <x-loading.text />
    </td>
@else
    <td class="text-right">
        <x-loading.text />
    </td>
@endisset
