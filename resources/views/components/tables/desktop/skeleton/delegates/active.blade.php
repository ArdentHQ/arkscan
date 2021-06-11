<x-table-skeleton
    class="block"
    device="desktop"
    :items="[
        'general.delegates.rank'         => 'rank',
        'general.delegates.name' => 'address',
        'general.delegates.status'       => [
            'type' => 'status',
            'lastOn' => 'md',
        ],
        'general.delegates.votes'  => [
            'type' => 'number',
            'responsive' => true,
        ],
        'general.delegates.productivity' => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ]
    ]"
/>
