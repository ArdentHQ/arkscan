@isset($responsive)
    <th class="hidden lg:table-cell">@lang($name)</th>
@else
    <th>@lang($name)</th>
@endisset
