<x-stats.stat wire:ignore.self :label="trans('general.price')" icon="app-price" :disabled="! Network::canBeExchanged() || $price === null" class="flex-grow">
    <x-slot name="side">
        <div wire:ignore>
            <livewire:price-stats />
        </div>
    </x-slot>

    <div wire:poll.60s class="flex space-x-3">
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

