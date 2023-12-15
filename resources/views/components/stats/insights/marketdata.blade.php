@props([
    'prices',
    'volumes',
    'caps',
])

<div
    :class="{
        'hidden md:block': tab !== 'market_data',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.market_data.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.price')" full-width>
            @foreach (['daily', 'atl', 'ath'] as $item) {{-- Mind the lack of "52w" here --}}
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
                        <div class="flex flex-col space-y-2">
                            <span>@lang('pages.statistics.insights.market_data.header.'.$item)</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                @if($item === 'daily')
                                    {{ ExplorerNumberFormatter::currencyWithDecimals($prices[$item.'_low'], Settings::currency(), 2) }}
                                    -
                                    {{ ExplorerNumberFormatter::currencyWithDecimals($prices[$item.'_high'], Settings::currency(), 2) }}
                                @else
                                    {{ ExplorerNumberFormatter::currencyWithDecimals($prices[$item], Settings::currency(), 2) }}
                                @endif
                            </span>
                        </div>

                        <div class="flex flex-col space-y-2 w-[130px]">
                            @if($item === 'daily')
                                <span>@lang('pages.statistics.insights.market_data.header.52w')</span>
                                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ ExplorerNumberFormatter::currencyWithDecimals($prices['52w_low'], Settings::currency(), 2) }}
                                    -
                                    {{ ExplorerNumberFormatter::currencyWithDecimals($prices['52w_high'], Settings::currency(), 2) }}
                                </span>
                            @elseif($item === 'atl' || $item === 'ath')
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </span>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ Carbon\Carbon::createFromTimestamp($prices[$item.'_date'])->format(DateFormat::DATE) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Desktop --}}
                <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                    <div class="flex flex-1">
                        @lang('pages.statistics.insights.market_data.header.'.$item)
                    </div>
                    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                            @if($item === 'daily' || $item === '52w')
                                {{ ExplorerNumberFormatter::currencyWithDecimals($prices[$item.'_low'], Settings::currency(), 2) }}
                                -
                                {{ ExplorerNumberFormatter::currencyWithDecimals($prices[$item.'_high'], Settings::currency(), 2) }}
                            @else
                                {{ ExplorerNumberFormatter::currencyWithDecimals($prices[$item], Settings::currency(), 2) }}
                            @endif
                        </div>

                        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                            @if($item === 'atl' || $item === 'ath')
                                <div>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </div>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ Carbon\Carbon::createFromTimestamp($prices[$item.'_date'])->format(DateFormat::DATE) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.exchanges_volume')" full-width>
            @foreach (['today_volume', 'atl', 'ath'] as $item)
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
                        <div class="flex flex-col space-y-2">
                            <span>@lang('pages.statistics.insights.market_data.header.'.$item)</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                {{ ExplorerNumberFormatter::currencyForViews($volumes[$item], Settings::currency()) }}
                            </span>
                        </div>

                        <div class="flex flex-col space-y-2 w-[130px]">
                            @if($item === 'atl' || $item === 'ath')
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </span>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ Carbon\Carbon::createFromTimestamp($volumes[$item.'_date'])->format(DateFormat::DATE) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Desktop --}}
                <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                    <div class="flex flex-1">
                        @lang('pages.statistics.insights.market_data.header.'.$item)
                    </div>
                    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ ExplorerNumberFormatter::currencyForViews($volumes[$item], Settings::currency()) }}
                        </div>

                        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                            @if($item === 'atl' || $item === 'ath')
                                <div>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </div>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ Carbon\Carbon::createFromTimestamp($volumes[$item.'_date'])->format(DateFormat::DATE) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.market_cap')" full-width>
            @foreach (['today_value', 'atl', 'ath'] as $item)
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
                        <div class="flex flex-col space-y-2">
                            <span>@lang('pages.statistics.insights.market_data.header.'.$item)</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                {{ ExplorerNumberFormatter::currencyForViews($caps[$item], Settings::currency()) }}
                            </span>
                        </div>

                        <div class="flex flex-col space-y-2 w-[130px]">
                            @if($item === 'atl' || $item === 'ath')
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </span>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ Carbon\Carbon::createFromTimestamp($caps[$item.'_date'])->format(DateFormat::DATE) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Desktop --}}
                <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                    <div class="flex flex-1">
                        @lang('pages.statistics.insights.market_data.header.'.$item)
                    </div>
                    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ ExplorerNumberFormatter::currencyForViews($caps[$item], Settings::currency()) }}
                        </div>

                        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                            @if($item === 'atl' || $item === 'ath')
                                <div>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </div>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {{ Carbon\Carbon::createFromTimestamp($caps[$item.'_date'])->format(DateFormat::DATE) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>
    </div>
</div>
