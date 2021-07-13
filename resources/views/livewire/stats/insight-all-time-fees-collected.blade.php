<div class="flex w-full" wire:poll.{{ $refreshInterval }}s>
    <x-stats.insight
        id="all-time-fees-collected"
        :mainTitle="$allTimeFeesCollectedTitle"
        :mainValue="$allTimeFeesCollectedValue"
        :secondaryTitle="$feesTitle"
        :secondaryValue="$feesValue"
        :secondaryTooltip="$feesTooltip"
        :chart="$chartValues"
        :chart-theme="$chartTheme"
        :options="$options"
        :selected="$period"
        model="period"
    />
</div>
