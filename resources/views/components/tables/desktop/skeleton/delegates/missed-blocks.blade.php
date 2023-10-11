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
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'md-lg',
            ],
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
            'sortingId' => 'no_of_voters',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'sortingId' => 'votes',
            'livewireSort' => true,
        ],
        'tables.missed-blocks.percentage' => [
            'type'       => 'number',
            'sortingId' => 'percentage_votes',
            'livewireSort' => true,
            'tooltip' => trans('tables.missed-blocks.info.percentage'),
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
