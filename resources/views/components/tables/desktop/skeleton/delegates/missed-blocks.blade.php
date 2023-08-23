@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.missed-blocks.height'       => 'text',
        'tables.missed-blocks.age'          => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
        ],
        'tables.missed-blocks.delegate' => 'text',
        'tables.missed-blocks.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ],
        'tables.missed-blocks.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
        ],
        'tables.missed-blocks.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
