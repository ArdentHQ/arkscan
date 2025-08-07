@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'sortingId' => null,
    'livewireSort' => false,
    'componentId' => '',
])

@if ($sortingId)
    <x-tables.headers.desktop.sortable-header
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :name="$name"
        :livewire-sort="$livewireSort"
        :sorting-id="$sortingId"
        :component-id="$componentId"
        :class="Arr::toCssClasses(['text-left',
            $class,
        ])"
    />
@else
    <x-ark-tables.header
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :class="Arr::toCssClasses(['text-left',
            $class,
        ])"
        :name="$name"
        :attributes="$attributes"
    />
@endif
