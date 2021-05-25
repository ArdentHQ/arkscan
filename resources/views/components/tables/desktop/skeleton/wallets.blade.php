<x-table-skeleton
    device="desktop"
    :items="[
        'general.wallet.address' => 'address',
        'general.wallet.info'    => 'text',
        'general.wallet.balance' => [
            'type' => 'number',
            'lastOn' => 'lg',
        ],
        'general.wallet.supply'  => [
            'type' => 'number',
            'responsive' => true,
        ],
    ]"
/>
