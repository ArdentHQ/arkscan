@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        '' => [
            'type' => 'icon',
            'badgeWidth' => 'w-5',
            'badgeHeight' => 'h-5',
            'class' => ' ',
            'width' => 20,
        ],
        'tables.delegate-monitor.order' => [
            'type' => 'rank',
        ],
        'tables.delegate-monitor.delegate' => [
            'type' => 'text',
            'width' => 190,
        ],
        'tables.delegate-monitor.status' => [
            'type' => 'badge',
            'badgeWidth' => 'w-[10rem]',
        ],
        'tables.delegate-monitor.time_to_forge' => [
            'type' => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'class' => 'whitespace-nowrap',
        ],
        'tables.delegate-monitor.block_height' => [
            'type'  => 'number',
            'class' => 'whitespace-nowrap',
        ],
    ]"
    :row-count="$rowCount"
    encapsulated
/>
