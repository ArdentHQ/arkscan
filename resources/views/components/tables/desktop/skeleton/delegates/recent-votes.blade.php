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
        ],
        'tables.recent-votes.addressing' => [
            'type' => 'encapsulated.addressing',
            'header' => 'address',
            'sortingId' => 'address',
            'livewireSort' => true,
        ],
        'tables.recent-votes.type'       => [
            'type' => 'text',
            'sortingId' => 'type',
            'livewireSort' => true,
        ],
        'tables.recent-votes.delegate'   => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
