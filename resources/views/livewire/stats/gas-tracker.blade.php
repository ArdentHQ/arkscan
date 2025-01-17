<div class="p-6 md:p-[9px] bg-theme-secondary-200 md:rounded-xl dark:bg-theme-dark-950">
    <h3 class="text-sm dark:text-theme-dark-200 text-theme-secondary-700 md:px-[15px] mb-[9px] font-semibold">Current Gas Prices</h3>
    
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
</div>