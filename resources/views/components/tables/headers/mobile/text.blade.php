@isset($responsive)
    <th class="hidden {{ $alignment ?? 'text-right' }} {{ $breakpoint ?? 'sm' }}:table-cell">@lang($name)</th>
@else
    <th class="{{ $alignment ?? 'text-right' }}">@lang($name)</th>
@endisset
