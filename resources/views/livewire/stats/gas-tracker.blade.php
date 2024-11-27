<div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
    <x-stats.fee-card
        icon="app-gas.low"
        :title="trans('pages.statistics.gas.low')"
        :amount="$fees['low']"
    />

    <x-stats.fee-card
        icon="app-gas.average"
        :title="trans('pages.statistics.gas.average')"
        :amount="$fees['average']"
    />

    <x-stats.fee-card
        icon="app-gas.high"
        :title="trans('pages.statistics.gas.high')"
        :amount="$fees['high']"
    />
</div>
