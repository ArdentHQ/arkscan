@props([
    'rowCount' => 10,
    'paginator' => null,
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
            'tooltip' => trans('pages.wallets.percentage_tooltip'),
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
