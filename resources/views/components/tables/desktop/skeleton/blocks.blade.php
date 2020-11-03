@isset($withoutGenerator)
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.block.id'           => 'text',
            'general.block.timestamp'    => 'text',
            'general.block.height'       => 'number',
            'general.block.transactions' => 'number',
            'general.block.amount'       => 'number',
            'general.block.fee'          => 'number',
        ]"
    />
@else
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.block.id'           => 'text',
            'general.block.timestamp'    => 'text',
            'general.block.generated_by' => 'address',
            'general.block.height'       => 'number',
            'general.block.transactions' => 'number',
            'general.block.amount'       => 'number',
            'general.block.fee'          => 'number',
        ]"
    />
@endif
