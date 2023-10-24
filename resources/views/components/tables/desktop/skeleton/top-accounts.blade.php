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
        'general.wallet.type'       => [
            'type' => 'icon',
            'responsive' => true,
            'breakpoint' => 'md-lg',
        ],
        'general.wallet.voting'     => [
            'type' => 'icon',
            'responsive' => true,
            'breakpoint' => 'lg',
        ],
        'general.wallet.balance_currency'    => [
            'type' => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'lastOn' => 'lg',
            'class' => 'last-until-lg',
        ],
        'general.wallet.percentage' => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'width' => '119',
            'tooltip' => trans('pages.wallets.supply_tooltip', ['symbol' => Network::currency()]),
        ],
    ]"
    :row-count="$rowCount"
    encapsulated
/>
