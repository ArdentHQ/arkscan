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
/>
