@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.transactions.id'         => [
            'type' => 'text',
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'xl',
            ],
        ],
        'tables.transactions.age'        => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'xl',
        ],
        'tables.transactions.method'     => 'text',
        'tables.transactions.addressing' => [
            'type' => 'encapsulated.addressing',
            'header' => 'address',
            'generic' => true,
        ],
        'tables.transactions.amount'     => [
            'type' => 'number',
            'lastOn' => 'lg',
            'nameProperties' => ['currency' => Network::currency()],
            'class' => 'last-until-lg',
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'lg',
            ],
        ],
        'tables.transactions.fee'        => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'nameProperties' => ['currency' => Network::currency()],
        ],
    ]"
    :row-count="$rowCount"
    encapsulated
/>
