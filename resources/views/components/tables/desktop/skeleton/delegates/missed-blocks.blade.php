@props([
    'rowCount' => 10,
])

@php
    $canSort = config('database.default') !== 'sqlite';
    $items = [
        'tables.missed-blocks.height'       => [
            'type' => 'text',
            'sortingId' => $canSort ? 'height' : null,
        ],
        'tables.missed-blocks.age'          => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'sortingId' => $canSort ? 'age' : null,
        ],
        'tables.missed-blocks.delegate' => [
            'type' => 'text',
            'sortingId' => $canSort ? 'name' : null,
        ],
        'tables.missed-blocks.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
            'sortingId' => $canSort ? 'no_of_voters' : null,
        ],
        'tables.missed-blocks.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
            'sortingId' => $canSort ? 'votes' : null,
        ],
        'tables.missed-blocks.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'tooltip' => trans('tables.missed-blocks.info.percentage'),
            'sortingId' => $canSort ? 'percentage_votes' : null,
        ],
    ];
@endphp

<x-table-skeleton
    device="desktop"
    :items="$items"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
