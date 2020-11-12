<div
    class="border-b-20 border-theme-secondary-100 dark:border-black"
    x-data="{
        usesPriceChart: {{ Settings::usesPriceChart() ? 'true' : 'false' }},
        usesFeeChart: {{ Settings::usesFeeChart() ? 'true' : 'false' }},
    }"
    x-show="usesPriceChart || usesFeeChart"
    x-on:toggle-price-chart.window="usesPriceChart = ! usesPriceChart"
    x-on:toggle-fee-chart.window="usesFeeChart = ! usesFeeChart"
>
    <div class="content-container">
        <div class="flex-col hidden w-full divide-y sm:flex divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
            <div class="flex flex-col w-full pt-16 space-x-0 lg:flex-row lg:space-x-10">
                @if(Network::canBeExchanged())
                    <x-chart
                        :data="$prices"
                        identifier="price"
                        colours-scheme="#339A51"
                        alpine-show="toggle-price-chart"
                        :is-visible="Settings::usesPriceChart()"
                    />
                @endif

                <x-chart
                    :data="$fees"
                    identifier="fees"
                    colours-scheme="#FFAE10"
                    alpine-show="toggle-fee-chart"
                    :is-visible="Settings::usesFeeChart()"
                />
            </div>

            <div class="grid w-full grid-flow-row grid-cols-2 gap-6 pt-8 mt-5 mb-16 {{ Network::canbeExchanged() ?  'xl:grid-cols-4' : 'xl:grid-cols-3'}} gap-y-12 xl:gap-y-4 chart-details">
                @if(Network::canBeExchanged())
                    <x-details-box :title="trans('pages.home.network-details.price')" icon="app-price">
                        {{ $aggregates['price'] }}
                    </x-details-box>
                @endif

                <x-details-box :title="trans('pages.home.network-details.lifetime_transactions_volume')" icon="app-volume">
                    <x-currency>{{ $aggregates['volume'] }}</x-currency>
                </x-details-box>

                <x-details-box :title="trans('pages.home.network-details.lifetime_transactions')" icon="app-transactions-amount">
                    <x-number>{{ $aggregates['transactionsCount'] }}</x-number>
                </x-details-box>

                <x-details-box :title="trans('pages.home.network-details.total_votes')" icon="app-votes">
                    <x-currency>{{ $aggregates['votesCount'] }}</x-currency>
                    <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                        <x-percentage>{{ $aggregates['votesPercentage'] }}</x-percentage>
                    </span>
                </x-details-box>
            </div>
        </div>
    </div>
</div>
