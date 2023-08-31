@props([
    'rowCount' => 10,
])

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.blocks.height'       => 'text',
        'tables.blocks.age'          => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md-lg',
        ],
        'tables.blocks.generated_by' => 'text',
        'tables.blocks.transactions' => 'number',
        'tables.blocks.volume'       => [
            'type' => 'number',
            'nameProperties' => ['currency' => Network::currency()],
        ],
        'tables.blocks.total_reward' => [
            'type' => 'number',
            'lastOn' => 'md-lg',
            'nameProperties' => ['currency' => Network::currency()],
            'class' => 'last-until-md-lg',
        ],
        'tables.blocks.value'        => [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'nameProperties' => ['currency' => Settings::currency()],
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    encapsulated
/>
