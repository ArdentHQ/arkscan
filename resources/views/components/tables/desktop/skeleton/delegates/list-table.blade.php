@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.delegates.rank' => [
            'type' => 'text',
            'width' => '70',
            'sortingId' => 'rank',
            'livewireSort' => true,
        ],
        'tables.delegates.delegate' => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
        ],
        'tables.delegates.status' => 'text',
        'tables.delegates.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
            'sortingId' => 'no_of_voters',
            'livewireSort' => true,
        ],
        'tables.delegates.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
            'sortingId' => 'votes',
            'livewireSort' => true,
        ],
        'tables.delegates.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'sortingId' => 'percentage_votes',
            'livewireSort' => true,
            'tooltip' => trans('tables.delegates.info.percentage'),
        ],
        'tables.delegates.missed_blocks' => [
            'type' => 'number',
            'sortingId' => 'missed_blocks',
            'livewireSort' => true,
        ],
        '' => [
            'type'  => 'text',
            'width' => '70',
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
