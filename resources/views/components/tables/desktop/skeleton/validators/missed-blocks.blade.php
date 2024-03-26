@props([
    'rowCount' => 10,
    'paginator' => null,
])

@php
    $canSort = config('database.default') !== 'sqlite';
    $items = [
        'tables.missed-blocks.height'       => [
            'type' => 'text',
            'sortingId' => $canSort ? 'height' : null,
            'livewireSort' => $canSort,
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'md-lg',
            ],
        ],
        'tables.missed-blocks.age'          => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'sortingId' => $canSort ? 'age' : null,
            'livewireSort' => $canSort,
        ],
        'tables.missed-blocks.validator' => [
            'type' => 'text',
            'sortingId' => $canSort ? 'name' : null,
            'livewireSort' => $canSort,
        ],
        'tables.missed-blocks.no_of_voters' => [
            'type'       => 'number',
            'sortingId' => $canSort ? 'no_of_voters' : null,
            'livewireSort' => $canSort,
        ],
        'tables.missed-blocks.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'sortingId' => $canSort ? 'votes' : null,
            'livewireSort' => $canSort,
        ],
        'tables.missed-blocks.percentage' => [
            'type'       => 'number',
            'tooltip' => trans('tables.missed-blocks.info.percentage'),
            'sortingId' => $canSort ? 'percentage_votes' : null,
            'livewireSort' => $canSort,
        ],
    ];
@endphp

<x-table-skeleton
    device="desktop"
    :items="$items"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
