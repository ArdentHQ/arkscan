@if (Network::usesMarketSquare())
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.delegates.rank'       => 'text',
            'general.delegates.name'       => 'adress',
            'general.delegates.votes'      => 'number',
            'general.delegates.profile'    => 'text',
            'general.delegates.commission' => 'text'
        ]"
    />
@else
    <x-table-skeleton
        device="desktop"
        :items="[
            'general.delegates.rank'  => 'text',
            'general.delegates.name'  => 'address',
            'general.delegates.votes' => 'number'
        ]"
    />
@endif
