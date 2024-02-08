<div>
    <x-stats.insights.transactions :data="$transactionDetails" />

    @if (Network::canBeExchanged())
        <x-stats.insights.marketdata :data="$marketData" />
    @endif

    <x-stats.insights.delegates :details="$delegateDetails" />

    <x-stats.insights.addresses
        :holdings="$addressHoldings"
        :unique="$uniqueAddresses"
    />

    <x-stats.insights.annual
        :years="$annualData"
    />
</div>
