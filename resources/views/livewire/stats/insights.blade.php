<div>
    <x-stats.insights.transactions
        :details="$transactionDetails"
        :averages="$transactionAverages"
        :records="$transactionRecords"
    />

    @if (Network::canBeExchanged())
        <x-stats.insights.marketdata
            :prices="$marketDataPrice"
            :volumes="$marketDataVolume"
            :caps="$marketDataCap"
        />
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
