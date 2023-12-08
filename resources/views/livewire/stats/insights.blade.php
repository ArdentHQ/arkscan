<div>
    <x-stats.insights.transactions
        :details="$transactionDetails"
        :averages="$transactionAverages"
    />

    <x-stats.insights.delegates :details="$delegateDetails" />
</div>
