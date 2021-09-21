<x-table-skeleton
    class="block"
    device="desktop"
    :items="[
        'pages.delegates.order' => [
            'type'  => 'rank',
            'width' => 70,
        ],
        'pages.delegates.name' => 'address',
        'pages.delegates.forging_at' => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'sm',
        ],
        'pages.delegates.status' => [
            'type'   => 'text',
            'lastOn' => 'md',
        ],
        'pages.delegates.block_id' => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'md',
            'class'      => 'text-right',
        ]
    ]"
/>
