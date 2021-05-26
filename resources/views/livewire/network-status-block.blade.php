<div class="overflow-auto bg-theme-secondary-100 dark:bg-black ">
    <div class="py-10 content-container-full-width">
        <div class="px-8 md:w-full">
            <div
                class="flex grid-cols-2 gap-3 w-full md:grid xl:space-x-3 xl:gap-0 xl:flex"
                wire:poll.{{ Network::blockTime() }}s
            >
                <x-stats.stat :label="trans('general.height')" icon="app-block_height">
                    <x-number>{{ $height }}</x-number>
                </x-stats.stat>

                <x-stats.stat :label="trans('general.total_supply')" icon="app-supply">
                    <x-currency :currency="Network::currency()">{{ $supply }}</x-currency>
                </x-stats.stat>

                <x-stats.stat :label="trans('general.market_cap')" icon="app-market_cap" :disabled="! Network::canBeExchanged()">
                    {{ $marketCap }}
                </x-stats.stat>

                <x-stats.stat class="flex-grow justify-between" :label="trans('general.price')" icon="app-price" :disabled="! Network::canBeExchanged()">
                    {{ $price }}

                    <x-slot name="side">
                        <livewire:price-stats :placeholder=" ! Network::canBeExchanged()" />
                    </x-slot>
                </x-stats.stat>

            </div>
        </div>
    </div>
</div>
