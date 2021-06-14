{{-- @TODO: kept for possible use on statistics page - removed detail boxes as they are not part of the design --}}
<div
    class="hidden sm:block dark:border-black border-b-20 border-theme-secondary-100"
    x-data="{
        usesPriceChart: {{ Settings::usesPriceChart() ? 'true' : 'false' }},
        usesFeeChart: {{ Settings::usesFeeChart() ? 'true' : 'false' }},
    }"
    x-show="usesPriceChart || usesFeeChart"
    x-on:toggle-price-chart.window="usesPriceChart = ! usesPriceChart"
    x-on:toggle-fee-chart.window="usesFeeChart = ! usesFeeChart"
>
    <div class="content-container">
        <div class="flex flex-col w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
            <div class="flex flex-col pt-16 space-x-0 w-full lg:flex-row lg:space-x-10">
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
        </div>
    </div>
</div>
