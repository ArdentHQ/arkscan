@isset($responsive)
    <th class="hidden {{ $breakpoint ?? 'lg'}}:table-cell">@lang($name)</th>
@else
    <th>@lang($name)</th>
@endisset
