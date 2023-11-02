@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.delegates.rank' => [
            'type' => 'rank',
            'width' => '60',
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
            'class' => 'whitespace-nowrap',
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'lg',
                'height' => 'h-[15px]',
            ],
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
            'type' => 'badge',
            'sortingId' => 'missed_blocks',
            'livewireSort' => true,
            'class' => 'whitespace-nowrap text-right',
        ],
        '' => [
            'type'  => 'text',
            'width' => '60',
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
