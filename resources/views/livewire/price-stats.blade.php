<div
    wire:poll.60s
    class="hidden flex-grow justify-end lg:flex"
>
    <div
        wire:key="{{ Settings::currency() }}-{{ $isPositive ? 'positive' : 'negative' }}"
        class="ml-6"
        x-data="PriceChart(
            {{ $historical->values()->toJson() }},
            {{ $historical->keys()->toJson() }},
            {{ ! Network::canBeExchanged() ? 'true' : 'false' }},
            {{ Settings::usesDarkTheme() ? 'true' : 'false' }},
            '{{ time() }}',
            {{ $isPositive ? 'true' : 'false' }}
        )"
        x-init="init"
        @toggle-dark-mode.window="toggleDarkMode"
    >
        <div class="block" wire:ignore>
            <canvas
                x-ref="chart"
                class="w-full h-full"
                width="{{ \App\Services\NumberFormatter::isFiat(Settings::currency()) ? 210 : 120 }}"
                height="40"
            ></canvas>
        </div>
    </div>
</div>
