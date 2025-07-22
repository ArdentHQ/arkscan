@props([
    'rowCount' => 10,
    'paginator' => null,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.recent-votes.id'         => [
            'type' => 'text',
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'xl',
            ],
        ],
        'tables.recent-votes.age'        => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'xl',
            'sortingId' => 'age',
            'livewireSort' => true,
            'componentId' => 'recent-votes',
        ],
        'tables.recent-votes.addressing' => [
            'type' => 'encapsulated.addressing',
            'header' => 'address',
            'sortingId' => 'address',
            'livewireSort' => true,
            'componentId' => 'recent-votes',
        ],
        'tables.recent-votes.type'       => [
            'type' => 'text',
            'sortingId' => 'type',
            'livewireSort' => true,
            'componentId' => 'recent-votes',
        ],
        'tables.recent-votes.validator'   => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
            'componentId' => 'recent-votes',
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
