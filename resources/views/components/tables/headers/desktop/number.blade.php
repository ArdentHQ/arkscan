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

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :class="Arr::toCssClasses([
        'leading-4.25 items-center',
        'text-right' => $livewireSort || $sortingId === null,
        'group/header cursor-pointer' => $sortingId !== null,
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => ! $livewireSort && $sortingId,
        'x-on:click' => ! $livewireSort && $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => ! $livewireSort && $sortingId !== null ? $initialSort : null,
        'wire:click' => $livewireSort && $sortingId !== null ? 'sortBy(\''.$sortingId.'\')' : null,
    ])"
>
    @isset ($slot)
        <div @class([
            'inline-flex items-center space-x-2 leading-4.25',
            'justify-end' => $sortingId !== null,
        ])>
            @unless ($livewireSort)
                <x-tables.headers.desktop.includes.sort-icon
                    :id="$sortingId"
                    :initial-direction="$initialSort"
                />
            @endunless

            <div>@lang($name, $nameProperties)</div>

            {{ $slot }}

            @if ($livewireSort)
                <x-tables.headers.desktop.includes.livewire-sort-icon :id="$sortingId" />
            @endif
        </div>
    @else
        @unless ($livewireSort)
            <x-tables.headers.desktop.includes.sort-icon
                :id="$sortingId"
                :initial-direction="$initialSort"
            />
        @endif

        <span>@lang($name, $nameProperties)</span>

        @if ($livewireSort)
            <x-tables.headers.desktop.includes.livewire-sort-icon :id="$sortingId" />
        @endif
    @endisset
</x-ark-tables.header>
