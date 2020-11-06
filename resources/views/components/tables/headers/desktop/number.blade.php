@isset($responsive)
    <th class="hidden {{ $alignment ?? 'text-right' }} {{ $breakpoint ?? 'lg'}}:table-cell">@lang($name)</th>
@else
    <th class="{{ $alignment ?? 'text-right' }}">@lang($name)</th>
@endisset
