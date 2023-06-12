@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'width' => null,
    'initialSort' => 'asc',
    'sortingId' => null,
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :class="$class"
    :width="$width"
    :attributes="$attributes"
    :class="Arr::toCssClasses([
        'group/header cursor-pointer' => $sortingId !== null,
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => $sortingId,
        'x-on:click' => $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => $sortingId !== null ? $initialSort : null,
    ])"
>
    <div @class([
        'flex items-center space-x-2' => $sortingId !== null,
    ])>
        <span>@lang($name)</span>

        <x-tables.headers.desktop.includes.sort-icon
            :id="$sortingId"
            :initial-direction="$initialSort"
        />
    </div>
</x-ark-tables.header>
