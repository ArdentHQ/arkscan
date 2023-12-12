<div>
    <x-stats.insights.transactions
        :details="$transactionDetails"
        :averages="$transactionAverages"
        :records="$transactionRecords"
    />

    <x-stats.insights.delegates :details="$delegateDetails" />

    <x-stats.insights.addresses
        :holdings="$addressHoldings"
        :unique="$uniqueAddresses"
    />
</div>
