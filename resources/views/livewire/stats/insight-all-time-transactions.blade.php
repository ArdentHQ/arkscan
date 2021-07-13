<div class="flex w-full" wire:poll.{{ $refreshInterval }}s>
    <x-stats.insight
        id="all-time-transactions"
        :mainTitle="$allTimeTransactionsTitle"
        :mainValue="$allTimeTransactionsValue"
        :secondaryTitle="$transactionsTitle"
        :secondaryValue="$transactionsValue"
        :chart="$chartValues"
        :chart-theme="$chartTheme"
        :options="$options"
        :selected="$period"
        model="period"
    />
</div>
