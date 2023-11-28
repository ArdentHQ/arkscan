@props([
    'rowCount' => 10,
    'paginator' => null,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.transactions.address' => [
            'type' => 'address',
            'class' => 'md:w-[220px] xl:w-[490px]',
        ],
        'tables.transactions.amount_no_currency'     => [
            'type' => 'number',
        ],
    ]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
