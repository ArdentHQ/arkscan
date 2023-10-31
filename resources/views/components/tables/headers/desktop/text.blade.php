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
    'hideSorting' => false,
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
        :hide-sorting="$hideSorting"
    />
@else
    <x-ark-tables.header
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :width="$width"
        :class="$class"
        :name="$name"
    />
@endif
