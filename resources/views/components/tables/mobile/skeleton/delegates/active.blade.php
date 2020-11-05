@if (Network::usesMarketSquare())
    <x-table-skeleton
        device="mobile"
        :items="['text', 'address', 'status', 'number', 'text', 'text', 'number']"
    />
@else
    <x-table-skeleton
        device="mobile"
        :items="['text', 'address', 'status', 'number', 'text', 'text']"
    />
@endif
