@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'sortingId' => null,
    'initialSort' => 'asc',
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :class="Arr::toCssClasses([
        'text-right' => ! $sortingId !== null,
        'flex items-center justify-end space-x-2 group/header cursor-pointer' => $sortingId !== null,
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => $sortingId,
        'x-on:click' => $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => $sortingId !== null ? $initialSort : null,
    ])"
>
    @isset ($slot)
        <div class="inline-flex items-center space-x-2">
            <x-tables.headers.desktop.includes.sort-icon
                :id="$sortingId"
                :enabled="$sortingId !== null"
                :initial-direction="$initialSort"
            />

            <div>@lang($name)</div>

            {{ $slot }}
        </div>
    @else
        <x-tables.headers.desktop.includes.sort-icon
            :id="$sortingId"
            :initial-direction="$initialSort"
        />

        <span>@lang($name)</span>
    @endisset
</x-ark-tables.header>
