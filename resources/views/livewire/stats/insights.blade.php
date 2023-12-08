<div>
    <x-stats.insights.transactions
        :details="$transactionDetails"
        :averages="$transactionAverages"
        :records="$transactionRecords"
    />

    <x-stats.insights.addresses
        :holdings="$addressHoldings"
    />
</div>
