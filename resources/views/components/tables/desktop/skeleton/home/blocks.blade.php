@props([
    'rowCount' => 10,
])

@php
    $items = [
        'tables.blocks.height'       => 'text',
        'tables.blocks.age'          => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
        ],
        'tables.blocks.generated_by' => 'text',
        'tables.blocks.transactions' => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md-lg',
        ],
        'tables.blocks.volume'       => [
            'type' => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'tooltip' => trans('pages.wallets.blocks.volume_tooltip'),
        ],
        'tables.blocks.total_reward' => [
            'type' => 'number',
            'nameProperties' => ['currency' => Network::currency()],
            'tooltip' => trans('pages.wallets.blocks.total_reward_tooltip'),
        ],
    ];

    if (Network::canBeExchanged()) {
        $items['tables.blocks.total_reward'] = [
            ...$items['tables.blocks.total_reward'],

            'lastOn' => 'lg',
            'class' => 'last-until-lg',
        ];

        $items['tables.blocks.value'] = [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'lg',
            'nameProperties' => ['currency' => Settings::currency()],
            'tooltip' => trans('pages.wallets.blocks.value_tooltip'),
        ];
    }
@endphp

<x-table-skeleton
    device="desktop"
    :items="$items"
    :row-count="$rowCount"
    encapsulated
/>
