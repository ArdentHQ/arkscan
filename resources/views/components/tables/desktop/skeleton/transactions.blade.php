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
        ],
        'tables.transactions.fee'        => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'nameProperties' => ['currency' => Network::currency()],
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
