<x-table-skeleton
    device="desktop"
    row-count="10"
    :items="[
        'general.transaction.id'  => 'icon',
        'general.block.timestamp' => [
            'type' => 'text',
            'responsive' => true,
        ],
        'general.transaction.sender' => 'address',
        'general.transaction.amount' => [
            'type' => 'number',
            'lastOn' => 'xl',
        ],
        'general.transaction.fee' => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'xl',
        ],
    ]"
/>
