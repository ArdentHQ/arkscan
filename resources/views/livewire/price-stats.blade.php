<div
    class="flex flex-grow justify-between ml-3 h-full"
    @unless ($placeholder)
        wire:poll.60s
    @endunless
>
    @unless ($placeholder)
        <div class="flex flex-col justify-end h-full">
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
    @endunless

    <div class="hidden flex-grow justify-end lg:flex">
        <div
            wire:key="{{ Settings::currency() }}"
            class="ml-6"
            x-data="PriceChart(
                {{ $historical->values()->toJson() }},
                {{ $historical->keys()->toJson() }},
                {{ $priceChange === null ? 0 : $priceChange }},
                {{ $placeholder ? 'true' : 'false' }},
                {{ Settings::usesDarkTheme() ? 'true' : 'false' }},
                '{{ time() }}'
            )"
            x-init="init"
            @toggle-dark-mode.window="toggleDarkMode"
            wire:loading.class="hidden"
        >
            <div wire:ignore>
                <canvas
                    x-ref="chart"
                    class="w-full h-full"
                    width="{{ ExchangeRate::isFiat(Settings::currency()) ? 210 : 120 }}"
                    height="40"
                ></canvas>
            </div>
        </div>
    </div>
</div>
