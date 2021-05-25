<x-table-skeleton
    device="desktop"
    :items="[
        'general.delegates.rank'  => 'text',
        'general.delegates.name'  => [
            'type' => 'address',
            'lastOn' => 'lg',
        ],
        'general.delegates.votes'  => [
            'type' => 'number',
            'responsive' => true,
        ],
    ]"
/>
