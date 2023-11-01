<x-table-skeleton
    device="desktop"
    :items="[
        'tables.exchanges.name'      => [
            'type' => 'exchange',
            'header' => 'text',
            'sortingId' => 'name',
            'sortDisabled' => true,
        ],
        'tables.exchanges.top_pairs' => [
            'type' => 'text',
            'sortingId' => 'top_pairs',
            'sortDisabled' => true,
        ],
        'tables.exchanges.price_currency' => [
            'type' => 'number',
            'nameProperties' => ['currency' => Settings::currency()],
            'sortingId' => 'price_currency',
            'sortDisabled' => true,
        ],
        'tables.exchanges.volume_currency'=> [
            'type' => 'number',
            'responsive' => true,
            'breakpoint' => 'md-lg',
            'nameProperties' => ['currency' => Settings::currency()],
            'sortingId' => 'volume_currency',
            'sortDisabled' => true,
        ],
    ]"
    class="rounded-b-xl"
    encapsulated
/>
