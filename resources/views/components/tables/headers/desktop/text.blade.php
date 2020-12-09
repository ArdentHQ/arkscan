@isset($responsive)
    <th class="hidden {{ $breakpoint ?? 'lg' }}:table-cell text-left">@lang($name)</th>
@else
    <th class="text-left">@lang($name)</th>
@endisset
