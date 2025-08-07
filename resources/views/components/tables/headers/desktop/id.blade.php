@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'width' => '40',
    'sortingId' => null,
    'livewireSort' => false,
    'componentId' => '',
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :width="$width"
    :class="Arr::toCssClasses(['text-left',
        'group/header cursor-pointer' => $sortingId !== null,
        'flex-row-reverse space-x-0' => $livewireSort && $sortingId !== null,
        $class,
    ])"
    :attributes="$attributes->merge([
        'wire:click' => $livewireSort && $sortingId !== null ? 'sortBy(\''.$sortingId.'\')' : null,
    ])"
>
    <div @class([
        'flex items-center space-x-2' => $sortingId !== null,
    ])>
        <span>@lang($name)</span>

        @if ($livewireSort)
            <x-tables.headers.desktop.includes.livewire-sort-icon
                :sort-key="$sortingId"
                :component-id="$componentId"
            />
        @endif
    </div>
</x-ark-tables.header>
