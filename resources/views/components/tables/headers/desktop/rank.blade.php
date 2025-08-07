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

@if ($sortingId)
    <x-tables.headers.desktop.sortable-header
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
@else
    <x-ark-tables.header
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :class="$class"
        :name="$name"
        :width="$width"
    />
@endif
