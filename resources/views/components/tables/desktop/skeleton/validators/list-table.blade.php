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
            'componentId' => 'validators',
        ],
        'tables.validators.validator' => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
            'componentId' => 'validators',
        ],
        'tables.validators.status' => 'text',
        'tables.validators.no_of_voters' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
            'sortingId' => 'no_of_voters',
            'livewireSort' => true,
            'componentId' => 'validators',
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
            'componentId' => 'validators',
        ],
        'tables.validators.percentage' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'sortingId' => 'percentage_votes',
            'livewireSort' => true,
            'componentId' => 'validators',
            'tooltip' => trans('tables.validators.info.percentage'),
        ],
        'tables.validators.missed_blocks' => [
            'type' => 'badge',
            'sortingId' => 'missed_blocks',
            'livewireSort' => true,
            'componentId' => 'validators',
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
