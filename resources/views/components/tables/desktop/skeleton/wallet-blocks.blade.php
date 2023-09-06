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
        'tables.blocks.transactions' => 'number',
        'tables.blocks.volume'       => [
            'type' => 'number',
            'nameProperties' => ['currency' => Network::currency()],
        ],
        'tables.blocks.total_reward' => [
            'type' => 'number',
            'nameProperties' => ['currency' => Network::currency()],
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
        ];
    }
@endphp

<x-table-skeleton
    device="desktop"
    :items="$items"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
