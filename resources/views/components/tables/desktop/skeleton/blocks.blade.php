@isset($withoutGenerator)
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.block.id'           => 'icon',
            'general.block.timestamp'    => 'text',
            'general.block.height'       => 'number',
            'general.block.transactions' => 'number',
            'general.block.amount' => [
                'type' => 'number',
                'lastOn' => 'lg',
            ],
            'general.block.fee'  => [
                'type' => 'number',
                'responsive' => true,
            ],
        ]"
    />
@else
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.block.id'           => 'icon',
            'general.block.timestamp'    => 'text',
            'general.block.generated_by' => 'address',
            'general.block.height'       => 'number',
            'general.block.transactions' => 'number',
            'general.block.amount' => [
                'type' => 'number',
                'lastOn' => 'lg',
            ],
            'general.block.fee'  => [
                'type' => 'number',
                'responsive' => true,
            ],
        ]"
    />
@endif
