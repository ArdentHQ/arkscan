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
        'tables.validator-monitor.order' => [
            'type' => 'rank',
        ],
        'tables.validator-monitor.validator' => [
            'type' => 'text',
            'width' => 190,
        ],
        'tables.validator-monitor.status' => [
            'type' => 'badge',
            'badgeWidth' => 'w-[8.75rem]',
        ],
        'tables.validator-monitor.time_to_forge' => [
            'type' => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'class' => 'whitespace-nowrap',
        ],
        'tables.validator-monitor.block_height' => [
            'type'  => 'number',
            'class' => 'whitespace-nowrap',
        ],
    ]"
    :row-count="$rowCount"
    encapsulated
/>
