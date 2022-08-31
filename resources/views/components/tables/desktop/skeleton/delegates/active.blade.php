<x-table-skeleton
    class="block"
    device="desktop"
    :items="[
        'general.delegates.rank' => [
            'type' => 'rank',
            'width' => '70',
        ],
        'general.delegates.name' => 'address',
        'general.delegates.status' => [
            'type'   => 'status',
        ],
        'general.delegates.votes' => [
            'type'       => 'number',
            'responsive' => true,
        ],
        'general.delegates.productivity' => [
            'type'       => 'number',
            'responsive' => true,
            'breakpoint' => 'md',
        ],
        '' => [
            'type'  => 'text',
            'width' => '70',
        ]
    ]"
/>
