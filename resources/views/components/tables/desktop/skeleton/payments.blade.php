<x-table-skeleton
    device="desktop"
    :items="[
        'general.transaction.recipient' => [
            'type' => 'address',
            'lastOn' => 'md',
        ],
        'general.transaction.amount'  => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ],
    ]"
/>
