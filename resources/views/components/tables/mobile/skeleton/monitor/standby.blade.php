@if (Network::usesMarketSquare())
    <x-table-skeleton
        device="mobile"
        :items="['text', 'address', 'number', 'text', 'text']"
    />
@else
    <x-table-skeleton
        device="mobile"
        :items="['text', 'address', 'number']"
    />
@endif
