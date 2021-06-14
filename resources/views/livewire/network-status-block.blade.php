<div class="overflow-auto dark:bg-black bg-theme-secondary-100">
    <div class="py-8 content-container-full-width">
        <div class="px-10 md:w-full">
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

                <x-stats.stat :label="trans('general.price')" icon="app-price" :disabled="! Network::canBeExchanged() || $price === null" class="flex-grow">
                    <x-slot name="side">
                        <div wire:ignore>
                            <livewire:price-stats />
                        </div>
                    </x-slot>

                    <div class="flex space-x-3">
                        <span class="font-semibold leading-none whitespace-nowrap dark:text-white text-theme-secondary-900">
                            {{ $price }}
                        </span>

                        @if ($priceChange < 0)
                            <span class="flex items-center space-x-1 text-sm font-semibold leading-none text-theme-danger-400">
                                <span>
                                    <x-ark-icon name="triangle-down" size="2xs" />
                                </span>
                                <span>
                                    <x-percentage>{{ $priceChange * 100 * -1 }}</x-percentage>
                                </span>
                            </span>
                        @else
                            <span class="flex items-center space-x-1 text-sm font-semibold leading-none text-theme-success-600">
                                <span>
                                    <x-ark-icon name="triangle-up" size="2xs" />
                                </span>
                                <span>
                                    <x-percentage>{{ $priceChange * 100 }}</x-percentage>
                                </span>
                            </span>
                        @endif
                    </div>
                </x-stats.stat>
            </div>
        </div>
    </div>
</div>
