@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.missed-blocks.height'       => [
            'type' => 'text',
            'sortingId' => 'height',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.age'          => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'sortingId' => 'age',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.delegate' => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
            'sortingId' => 'no_of_voters',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
            'sortingId' => 'votes',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'sortingId' => 'percentage_votes',
            'livewireSort' => true,
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
