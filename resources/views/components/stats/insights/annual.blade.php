@props([
    'years',
])

<div
    :class="{
        'hidden md:block': tab !== 'annual',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.annual.title')
    </div>

    <div>
        {{-- Mobile --}}
        <div class="flex flex-col md:hidden">
            @foreach ($years as $year)
                <x-stats.insights.container :title="$year['year']" full-width>
                    <div class="flex flex-col flex-1 justify-between pt-3 space-y-3 w-full divide-y divide-dashed sm:flex-row sm:space-y-0 sm:divide-none divide-theme-secondary-300 dark:divide-theme-dark-700">
                        <div class="flex flex-col space-y-2">
                            <span>@lang('pages.statistics.insights.annual.header.transaction')</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                <x-number>{{ $year['transactions'] }}</x-number>
                            </span>
                        </div>

                        <div class="flex flex-col pt-3 space-y-2 w-full sm:pt-0 sm:w-[170px]">
                            <span>@lang('pages.statistics.insights.annual.header.volume')</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                {{ ExplorerNumberFormatter::currencyWithDecimals($year['volume'], Network::currency(), 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col flex-1 justify-between pt-3 space-y-3 w-full divide-y divide-dashed sm:flex-row sm:space-y-0 sm:divide-none divide-theme-secondary-300 dark:divide-theme-dark-700">
                        <div class="flex flex-col space-y-2">
                            <span>@lang('pages.statistics.insights.annual.header.fees')</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                {{ ExplorerNumberFormatter::currencyWithDecimals($year['fees'], Network::currency(), 2) }}
                            </span>
                        </div>

                        <div class="flex flex-col pt-3 space-y-2 w-full sm:pt-0 sm:w-[170px]">
                            <span>@lang('pages.statistics.insights.annual.header.blocks')</span>
                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                <x-number>{{ $year['blocks'] }}</x-number>
                            </span>
                        </div>
                    </div>
                </x-stats.insights.container>
            @endforeach
        </div>

        {{-- Desktop Medium --}}
        <div class="hidden md:block xl:hidden">
            <x-stats.insights.container full-width>
                <div class="flex flex-col space-y-4 divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-dark-700">
                    @foreach ($years as $year)
                        <div @class(["flex", "pt-4" => !$loop->first])>
                            <div class="flex flex-1 text-theme-secondary-900 dark:text-theme-dark-50">{{ $year['year'] }}</div>
                            <div class="flex flex-col flex-1 space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                                <div class="flex flex-col flex-1 space-y-3">
                                    <div class="flex justify-between space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.transaction'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            <x-number>{{ $year['transactions'] }}</x-number>
                                        </span>
                                    </div>
                                    <div class="flex justify-between space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.blocks'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            <x-number>{{ $year['blocks'] }}</x-number>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col flex-1 space-y-3 md-lg:pl-16">
                                    <div class="flex justify-between space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.volume'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            {{ ExplorerNumberFormatter::currencyWithDecimals($year['volume'], Network::currency(), 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.fees'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            {{ ExplorerNumberFormatter::currencyWithDecimals($year['fees'], Network::currency(), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-stats.insights.container>
        </div>

        {{-- Desktop Large --}}
        <div class="hidden xl:block">
            <x-stats.insights.container full-width>
                <table class="justify-between w-full border-separate border-spacing-y-3">
                    <tbody>
                        @foreach ($years as $year)
                            <tr>
                                <td>
                                    <div class="pr-8 text-theme-secondary-900 dark:text-theme-dark-50">
                                        {{ $year['year'] }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-between px-8 space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.transaction'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            <x-number>{{ $year['transactions'] }}</x-number>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-between px-8 space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.volume'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            {{ ExplorerNumberFormatter::currencyWithDecimals($year['volume'], Network::currency(), 0) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-between px-8 space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.fees'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            {{ ExplorerNumberFormatter::currencyWithDecimals($year['fees'], Network::currency(), 2) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-between pl-8 space-x-3">
                                        <span>@lang('pages.statistics.insights.annual.header.blocks'):</span>
                                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                            <x-number>{{ $year['blocks'] }}</x-number>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-stats.insights.container>
        </div>
    </div>
</div>
