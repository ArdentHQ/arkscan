<x-table-skeleton
    device="desktop"
    :items="[
        'tables.transactions.id'         => 'text',
        'tables.transactions.age'        => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'xl',
        ],
        'tables.transactions.type'       => 'text',
        'tables.transactions.addressing' => 'text',
        'tables.transactions.amount'     => [
            'type' => 'number',
            'lastOn' => 'md-lg',
            'nameProperties' => ['currency' => Network::currency()],
            'class' => 'last-until-md-lg',
        ],
        'tables.transactions.fee'        => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'nameProperties' => ['currency' => Network::currency()],
        ],





        // 'tables.block.timestamp'       => [
        //     'type' => 'text',
        //     'responsive' => true,
        // ],
        // 'tables.transaction.sender'    => 'address',
        // 'tables.transaction.recipient' => 'address',
        // 'tables.transaction.amount'    => [
        //     'type' => 'number',
        //     'lastOn' => 'xl',
        // ],
        // 'tables.transaction.fee'       => [
        //     'type' => 'number',
        //     'responsive' => true,
        //     'breakpoint' => 'md-lg',
        // ],
    ]"
    :component-properties="['rounded' => false]"
    encapsulated
/>
