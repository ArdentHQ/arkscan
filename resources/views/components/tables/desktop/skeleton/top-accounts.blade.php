@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'general.wallet.rank'       => 'rank',
        'general.wallet.address'    => [
            'type' => 'address',
            'class' => 'md:w-[220px] xl:w-[490px]',
        ],
        'general.wallet.name'       => 'text',
        'general.wallet.type'       => 'icon',
        'general.wallet.voting'     => 'icon',
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
