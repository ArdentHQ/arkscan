<div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
    <x-stats.fee-card
        icon="app-gas.low"
        :title="trans('pages.statistics.gas-tracker.low')"
        :amount="$lowFee['amount']"
        :duration="$lowFee['duration']"
    />

    <x-stats.fee-card
        icon="app-gas.average"
        :title="trans('pages.statistics.gas-tracker.average')"
        :amount="$averageFee['amount']"
        :duration="$averageFee['duration']"
    />

    <x-stats.fee-card
        icon="app-gas.high"
        :title="trans('pages.statistics.gas-tracker.high')"
        :amount="$highFee['amount']"
        :duration="$highFee['duration']"
    />
</div>
