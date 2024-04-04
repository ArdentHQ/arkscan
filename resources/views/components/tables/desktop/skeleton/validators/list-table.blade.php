@props([
    'rowCount' => 10,
    'paginator' => null,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.validators.rank' => [
            'type' => 'rank',
            'width' => '60',
            'sortingId' => 'rank',
            'livewireSort' => true,
        ],
        'tables.validators.validator' => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
        ],
        'tables.validators.status' => 'text',
        'tables.validators.no_of_voters' => [
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
        'tables.validators.votes' => [
            'type'       => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'responsive' => true,
            'sortingId' => 'votes',
            'livewireSort' => true,
        ],
        'tables.validators.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'sortingId' => 'percentage_votes',
            'livewireSort' => true,
            'tooltip' => trans('tables.validators.info.percentage'),
        ],
        'tables.validators.missed_blocks' => [
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
    :paginator="$paginator"
    encapsulated
/>
