@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.recent-votes.id'         => 'text',
        'tables.recent-votes.age'        => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'xl',
            'sortingId' => 'age',
            'livewireSort' => true,
        ],
        'tables.recent-votes.addressing' => [
            'type' => 'text',
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
    encapsulated
/>
