@if (Network::usesMarketSquare())
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.delegates.rank'         => 'text',
            'general.delegates.name'         => 'address',
            'general.delegates.status'       => 'status',
            'general.delegates.votes'        => 'number',
            'general.delegates.profile'      => 'text',
            'general.delegates.commission'   => 'text',
            'general.delegates.productivity' => 'number'
        ]"
    />
@else
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.delegates.rank'         => 'text',
            'general.delegates.name'         => 'address',
            'general.delegates.status'       => 'status',
            'general.delegates.votes'        => 'number',
            'general.delegates.productivity' => 'number'
        ]"
    />
@endif
