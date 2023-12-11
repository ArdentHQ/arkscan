@props([
    'holdings',
    'unique',
])

<div
    :class="{
        'hidden md:block': tab !== 'addresses',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.addresses.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.addresses.holdings')">
            @foreach($holdings as $key => $values)
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col pt-3 space-y-2">
                        <span>&gt; <x-number>{{ $values['grouped'] }}</x-number> {{ Network::currency() }}</span>

                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                            <x-number>{{ $values['count'] }}</x-number>
                        </span>
                    </div>
                </div>
                {{-- Desktop --}}
                <div class="hidden justify-between w-full md:flex">
                    <div class="flex flex-1">
                        <span>&gt; <x-number>{{ $values['grouped'] }}</x-number> {{ Network::currency() }}</span>
                    </div>
                    <div class="flex flex-1 justify-between">
                        <span>@lang('pages.statistics.insights.addresses.header.addresses'):</span>
                        <span class="text-theme-secondary-900 dark:text-theme-dark-50"><x-number>{{ $values['count'] }}</x-number></span>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.addresses.unique')" full-width>
            {{-- Mobile --}}
            @foreach (['genesis', 'newest', 'most_transactions', 'largest'] as $item)
                @if($unique[$item] !== null)
                    <div class="flex md:hidden">
                        <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
                            <div class="flex flex-col space-y-2">
                                <span>@lang('pages.statistics.insights.addresses.header.'.$item)</span>
                                <a
                                    href="{{ route('wallet', $unique[$item]['address']) }}"
                                    class="link"
                                >
                                    <x-truncate-middle>{{ $unique[$item]['address'] }}</x-truncate-middle>
                                </a>
                            </div>

                            <div class="flex flex-col space-y-2 w-[90px]">
                                <span>
                                    @if ($item === 'most_transactions')
                                        @lang('pages.statistics.insights.addresses.header.transactions')
                                    @elseif ($item === 'largest')
                                        @lang('pages.statistics.insights.addresses.header.balance')
                                    @else
                                        @lang('pages.statistics.insights.addresses.header.date')
                                    @endif
                                </span>
                                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    @if ($item === 'most_transactions')
                                        <x-number>{{ $unique[$item]['value'] }}</x-number>
                                    @elseif ($item === 'largest')
                                        {{ ExplorerNumberFormatter::currencyShort($unique[$item]['value'], Network::currency(), 2) }}
                                    @else
                                        {{ $unique[$item]['value'] }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Desktop --}}
            @foreach (['genesis', 'newest', 'most_transactions', 'largest'] as $item)
                @if($unique[$item] !== null)
                    <div class="hidden justify-between w-full md:flex xl:w-[770px]">
                        <div class="flex flex-1">
                            @lang('pages.statistics.insights.addresses.header.'.$item)
                        </div>
                        <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                            <div class="flex flex-1 justify-between">
                                <span>@lang('pages.statistics.insights.addresses.header.address'):</span>
                                <a
                                    href="{{ route('wallet', $unique[$item]['address']) }}"
                                    class="link"
                                >
                                    <x-truncate-middle>{{ $unique[$item]['address'] }}</x-truncate-middle>
                                </a>
                            </div>
                            <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                                <div>
                                    @if ($item === 'most_transactions')
                                        @lang('pages.statistics.insights.addresses.header.transactions'):
                                    @elseif ($item === 'largest')
                                        @lang('pages.statistics.insights.addresses.header.balance'):
                                    @else
                                        @lang('pages.statistics.insights.addresses.header.date'):
                                    @endif
                                </div>
                                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                                    @if ($item === 'most_transactions')
                                        <x-number>{{ $unique[$item]['value'] }}</x-number>
                                    @elseif ($item === 'largest')
                                        {{ ExplorerNumberFormatter::currencyWithDecimals($unique[$item]['value'], Network::currency(), 2) }}
                                    @else
                                        {{ $unique[$item]['value'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </x-stats.insights.container>
    </div>
</div>