@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'sortingId' => null,
    'livewireSort' => false,
])

<x-tables.headers.desktop.sortable-header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :name="$name"
    :livewire-sort="$livewireSort"
    :sorting-id="$sortingId"
    :class="Arr::toCssClasses([
        'text-left',
        $class,
    ])"
/>
