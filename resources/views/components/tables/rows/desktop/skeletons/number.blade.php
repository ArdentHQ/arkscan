@isset($responsive)
    <td class="hidden text-right xl:table-cell">
        <x-loading.text />
    </td>
@else
    <td class="text-right">
        <x-loading.text />
    </td>
@endisset
