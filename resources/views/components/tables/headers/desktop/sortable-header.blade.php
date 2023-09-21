@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'nameProperties' => [],
    'width' => null,
    'sortingId' => null,
    'initialSort' => 'asc',
    'livewireSort' => false,
    'sortIconAlignment' => 'right',
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :width="$width"
    :class="Arr::toCssClasses([
        'group/header cursor-pointer' => ($livewireSort && $this->isReady && $sortingId !== null) || (! $livewireSort && $sortingId !== null),
        'flex-row-reverse space-x-0' => $livewireSort && $sortingId !== null,
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => $sortingId,
        'x-on:click' => ! $livewireSort && $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => ! $livewireSort && $sortingId !== null ? $initialSort : null,
        'wire:click' => $livewireSort && $this->isReady && $sortingId !== null ? 'sortBy(\''.$sortingId.'\')' : null,
    ])"
>
    <div @class([
        'flex items-center space-x-2' => $sortingId !== null,
        'justify-end' => $sortIconAlignment === 'left' && $sortingId !== null,
    ])>
        @if (! $livewireSort && $sortIconAlignment === 'left')
            <x-tables.headers.desktop.includes.sort-icon
                :id="$sortingId"
                :initial-direction="$initialSort"
            />
        @endif

        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <span>@lang($name, $nameProperties)</span>
        @endif

        @if ($livewireSort)
            <x-tables.headers.desktop.includes.livewire-sort-icon :id="$sortingId" />
        @elseif ($sortIconAlignment === 'right')
            <x-tables.headers.desktop.includes.sort-icon
                :id="$sortingId"
                :initial-direction="$initialSort"
            />
        @endif
    </div>
</x-ark-tables.header>
