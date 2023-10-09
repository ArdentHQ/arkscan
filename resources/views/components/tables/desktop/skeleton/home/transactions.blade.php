@props([
    'rowCount' => 10,
])

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
    ]"
    :row-count="$rowCount"
    encapsulated
/>
