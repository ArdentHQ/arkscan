<div
    class="flex flex-col space-y-3 divide-y sm:flex-row sm:space-y-0 sm:space-x-6 sm:divide-y-0 divide-theme-secondary-300 dark:divide-theme-dark-700"
    wire:poll.{{ Network::blockTime() }}s
>
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
            :title="trans('pages.home.statistics.exchange_volume')"
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
