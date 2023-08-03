@props([
    'rowCount' => 10,
])

@php
    $items = [
        'tables.delegates.rank' => [
            'type' => 'rank',
            'width' => '70',
        ],
        'tables.delegates.delegate' => 'text',
        'tables.delegates.status' => 'text',
        'tables.delegates.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
        ],
        'tables.delegates.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
        ],
        'tables.delegates.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ],
    ];

    if ($this->showMissedBlocks) {
        $items['tables.delegates.missed_blocks'] = [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ];
    }

    $items[''] = [
        'type'  => 'text',
        'width' => '70',
    ];
@endphp

<x-table-skeleton
    device="desktop"
    :items="$items"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
