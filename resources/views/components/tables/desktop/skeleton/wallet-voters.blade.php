@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'general.wallet.address'    => [
            'type' => 'address',
            'class' => 'md:w-[220px] xl:w-[490px]',
        ],
        'general.wallet.balance'    => 'number',
        'general.wallet.percentage' => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'width' => '119',
        ],
    ]"
    :row-count="$rowCount"
    encapsulated
/>
