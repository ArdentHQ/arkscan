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
    'sortDisabled' => false,
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
        :sort-disabled="$sortDisabled"
        :initial-sort="$initialSort"
        :hide-sorting="$hideSorting"
        :component-id="$componentId"
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
