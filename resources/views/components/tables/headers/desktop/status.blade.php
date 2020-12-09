@isset($responsive)
    <th class="hidden {{ $breakpoint ?? 'lg' }}:table-cell text-left">@lang($name)</th>
@else
    <th class="text-left">
        @isset ($slot)
            <div class="inline-flex items-center space-x-2">
                <div>@lang($name)</div>

                {{ $slot }}
            </div>
        @else
            @lang($name)
        @endisset
    </th>
@endisset
