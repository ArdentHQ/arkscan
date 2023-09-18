@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'width' => null,
    'sortingId' => null,
    'initialSort' => 'asc',
    'livewireSort' => false,
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :width="$width"
    :class="Arr::toCssClasses([
        'group/header cursor-pointer' => $sortingId !== null,
        'flex-row-reverse space-x-0' => $livewireSort && $sortingId !== null,
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => ! $livewireSort && $sortingId,
        'x-on:click' => ! $livewireSort && $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => ! $livewireSort && $sortingId !== null ? $initialSort : null,
        'wire:click' => $livewireSort && $sortingId !== null ? 'sortBy(\''.$sortingId.'\')' : null,
    ])"
>
    <div @class([
        'flex items-center space-x-2' => $sortingId !== null,
    ])>
        <span>@lang($name)</span>

        @if ($livewireSort)
            <x-tables.headers.desktop.includes.livewire-sort-icon :id="$sortingId" />
        @else
            <x-tables.headers.desktop.includes.sort-icon
                :id="$sortingId"
                :initial-direction="$initialSort"
            />
        @endif
    </div>
</x-ark-tables.header>
