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
        ],
        'tables.recent-votes.addressing' => 'text',
        'tables.recent-votes.type'       => 'text',
        'tables.recent-votes.delegate'   => 'text',
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
