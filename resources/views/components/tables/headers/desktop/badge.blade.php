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
    'componentId' => '',
])

<x-tables.headers.desktop.text
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :width="$width"
    :class="$class"
    :name="$name"
    :livewire-sort="$livewireSort"
    :sorting-id="$sortingId"
    :initial-sort="$initialSort"
    :component-id="$componentId"
/>
