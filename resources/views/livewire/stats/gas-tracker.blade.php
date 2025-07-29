<div class="p-6 md:rounded-xl bg-theme-secondary-200 md:p-[9px] dark:bg-theme-dark-950">
    <h3 class="text-sm font-semibold text-theme-secondary-700 mb-[9px] md:px-[15px] dark:text-theme-dark-200">
        @lang('pages.statistics.gas-tracker.current_gas_prices')
    </h3>
    
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