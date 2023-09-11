@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.delegates.rank' => [
            'type' => 'rank',
            'width' => '70',
        ],
        'tables.delegates.delegate' => 'text',
        'tables.delegates.status' => 'text',
        'tables.delegates.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ],
        'tables.delegates.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
        ],
        'tables.delegates.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
        ],
        'tables.delegates.missed_blocks' => 'number',
        '' => [
            'type'  => 'text',
            'width' => '70',
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
