@props([
    'rowCount' => 10,
    'paginator' => null,
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
        'tables.transactions.method'     => 'text',
        'tables.transactions.addressing' => [
            'type' => 'encapsulated.addressing',
            'header' => 'address',
        ],
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
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
