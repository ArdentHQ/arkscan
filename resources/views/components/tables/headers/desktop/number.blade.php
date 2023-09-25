@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'nameProperties' => [],
    'sortingId' => null,
    'initialSort' => 'asc',
    'livewireSort' => false,
])

@if ($sortingId)
    <x-tables.headers.desktop.sortable-header
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :name="$name"
        :name-properties="$nameProperties"
        :livewire-sort="$livewireSort"
        :sorting-id="$sortingId"
        :initial-sort="$initialSort"
        sort-icon-alignment="left"
        :class="Arr::toCssClasses([
            'leading-4.25 items-center',
            'text-right' => $livewireSort || $sortingId === null,
            $class,
        ])"
    >
        {{ $slot }}
    </x-tables.headers.desktop.sortable-header>
@else
    <x-ark-tables.header
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :class="Arr::toCssClasses([
            'text-right',
            $class,
        ])"
    >
        @isset ($slot)
            <div class="inline-flex items-center space-x-2">
                <div>@lang($name, $nameProperties)</div>

                {{ $slot }}
            </div>
        @else
            <span>@lang($name, $nameProperties)</span>
        @endif
    </x-ark-tables.header>
@endif
