@isset($responsive)
    <th class="hidden text-right xl:table-cell">@lang($name)</th>
@else
    <th class="text-right">@lang($name)</th>
@endisset
