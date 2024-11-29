<div wire:poll.{{ Network::blockTime() }}s>
    <div class="flex flex-col space-y-3 divide-y sm:flex-row sm:space-y-0 sm:space-x-6 sm:divide-y-0 divide-theme-secondary-300 dark:divide-theme-dark-700">
        <div class="flex flex-col flex-1 space-y-3 divide-y divide-theme-secondary-300 dark:divide-theme-dark-700">
            <x-home.stat
                :title="trans('pages.home.statistics.market_cap')"
                :disabled="! Network::canBeExchanged() || $marketCap === null"
            >
                {{ $marketCap }}

                @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                    {{ Settings::currency() }}
                @endif
            </x-home.stat>

            <x-home.stat :title="trans('pages.home.statistics.current_supply')" class="pt-3">
                <x-currency :currency="Network::currency()">{{ $supply }}</x-currency>
            </x-home.stat>
        </div>

        <div class="flex flex-col flex-1 pt-3 space-y-3 divide-y sm:pt-0 divide-theme-secondary-300 dark:divide-theme-dark-700">
            <x-home.stat
                :title="trans('pages.home.statistics.volume')"
                :disabled="! Network::canBeExchanged() || $volume === null"
            >
                {{ $volume }}

                @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                    {{ Settings::currency() }}
                @endif
            </x-home.stat>

            <x-home.stat
                :title="trans('pages.home.statistics.block_height')"
                class="pt-3"
            >
                <x-number>{{ $height }}</x-number>
            </x-home.stat>
        </div>
    </div>

    <div class="flex flex-col py-3 px-4 -mx-4 mt-4 -mb-3 space-y-2 font-semibold rounded-b-xl sm:flex-row sm:items-center sm:px-6 sm:-mx-6 sm:space-y-0 sm:space-x-3 md:-mb-6 bg-theme-secondary-100 dark:bg-theme-dark-950 dark:text-theme-dark-200">
        <div class="text-sm">
            @lang('pages.statistics.gas-tracker.gas_tracker')
        </div>

        <div class="flex items-center sm:space-x-2">
            @foreach ($gasTracker as $title => $fee)
                <x-home.includes.gas-badge
                    :title="trans('pages.statistics.gas-tracker.'.$title)"
                    :class="Arr::toCssClasses(['hidden sm:block' => $title !== 'average'])"
                >
                    <x-slot name="value">
                        @if (Network::canBeExchanged())
                            <span>{{ ExchangeRate::convert($fee['amount']) }}</span>
                            <span>{{ Settings::currency() }}</span>
                        @else
                            <span>{{ $fee['amount'] }}</span>
                            <span>@lang('general.gwei')</span>
                        @endif
                    </x-slot>
                </x-home.includes.gas-badge>
            @endforeach

            <div class="ml-2 sm:hidden">
                <x-ark-info
                    type="info"
                    :html-tooltip="view('components.home.includes.gas-tooltip', ['gasTracker' => $gasTracker])->render()"
                />
            </div>
        </div>
    </div>
</div>
