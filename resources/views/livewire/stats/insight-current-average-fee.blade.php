<div class="flex w-full" wire:poll.{{ $refreshInterval }}s>
    <x-stats.insight
        id="current-average-fee"
        :mainTitle="$currentAverageFeeTitle"
        :mainValue="$currentAverageFeeValue"
        :secondaryTitle="$minFeeTitle"
        :secondaryValue="$minFeeValue"
        :tertiaryTitle="$maxFeeTitle"
        :tertiaryValue="$maxFeeValue"
        :options="$options"
        :selected="$transactionType"
        model="transactionType"
    />
</div>
