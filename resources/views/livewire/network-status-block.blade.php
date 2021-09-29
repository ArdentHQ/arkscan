<div class="overflow-auto dark:bg-black bg-theme-secondary-100">
    <div class="py-8 content-container-full-width">
        <div class="px-8 md:px-10 md:w-full">
            <div
                class="flex grid-cols-2 gap-3 w-full md:grid xl:flex xl:gap-0 xl:space-x-3"
                wire:poll.{{ Network::blockTime() }}s
            >
                <x-stats.stat :label="trans('general.height')" icon="app-height">
                    <x-number>{{ $height }}</x-number>
                </x-stats.stat>

                <x-stats.stat :label="trans('general.total_supply')" icon="app-supply">
                    <x-currency :currency="Network::currency()">{{ $supply }}</x-currency>
                </x-stats.stat>

                <x-stats.stat :label="trans('general.market_cap')" icon="app-monitor" :disabled="! Network::canBeExchanged() || $marketCap === null">
                    {{ $marketCap }}
                </x-stats.stat>

                <livewire:network-status-block-price />
            </div>
        </div>
    </div>
</div>
