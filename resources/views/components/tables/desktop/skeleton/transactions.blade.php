@isset($useConfirmations)
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.transaction.id'            => 'icon',
            'general.transaction.timestamp'     => 'text',
            'general.transaction.sender'        => 'address',
            'general.transaction.recipient'     => 'address',
            'general.transaction.amount'        => [
                'type' => 'number',
                'lastOn' => 'xl',
            ],
            'general.transaction.fee' => [
                'type' => 'number',
                'responsive' => true,
                'breakpoint' => 'xl',
            ],
            'general.transaction.confirmations' => [
                'type' => 'number',
                'responsive' => true,
                'breakpoint' => 'xl',
            ]
        ]"
    />
@else
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.transaction.id'            => 'icon',
            'general.transaction.timestamp'     => 'text',
            'general.transaction.sender'        => 'address',
            'general.transaction.recipient'     => 'address',
            'general.transaction.amount'        => [
                'type' => 'number',
                'lastOn' => 'xl',
            ],
            'general.transaction.fee' => [
                'type' => 'number',
                'responsive' => true,
                'breakpoint' => 'xl',
            ],
        ]"
    />
@endif
