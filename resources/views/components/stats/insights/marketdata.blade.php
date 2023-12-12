@props([
    'prices',
    'volumes',
    'caps',
])

<div
    :class="{
        'hidden md:block': tab !== 'marketData',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.market_data.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.price')" full-width>
            {{-- Desktop --}}
            <div class="hidden justify-between w-full md:flex xl:w-[482px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.daily_range')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $prices['daily_low'] }} - {{ $prices['daily_high'] }}
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[482px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.52_week_range')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $prices['52w_low'] }} - {{ $prices['52w_high'] }}
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.atl')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $prices['atl'] }}
                    </div>

                    <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                        <div>
                            @lang('pages.statistics.insights.market_data.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $prices['atl_date']}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.ath')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $prices['ath'] }}
                    </div>

                    <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                        <div>
                            @lang('pages.statistics.insights.market_data.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $prices['ath_date']}}
                        </div>
                    </div>
                </div>
            </div>
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.exchanges_volume')" full-width>
            {{-- Desktop --}}
            <div class="hidden justify-between w-full md:flex xl:w-[482px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.today_volume')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $volumes['value'] }}
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.atl')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $volumes['atl'] }}
                    </div>

                    <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                        <div>
                            @lang('pages.statistics.insights.market_data.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $volumes['atl_date']}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.ath')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $volumes['ath'] }}
                    </div>

                    <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                        <div>
                            @lang('pages.statistics.insights.market_data.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $volumes['ath_date']}}
                        </div>
                    </div>
                </div>
            </div>
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.market_cap')" full-width>
            {{-- Desktop --}}
            <div class="hidden justify-between w-full md:flex xl:w-[482px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.today_value')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $caps['value'] }}
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.atl')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $caps['atl'] }}
                    </div>

                    <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                        <div>
                            @lang('pages.statistics.insights.market_data.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $caps['atl_date']}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                <div class="flex flex-1">
                    @lang('pages.statistics.insights.market_data.header.ath')
                </div>
                <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                    <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $caps['ath'] }}
                    </div>

                    <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                        <div>
                            @lang('pages.statistics.insights.market_data.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $caps['ath_date']}}
                        </div>
                    </div>
                </div>
            </div>
        </x-stats.insights.container>
    </div>
</div>
