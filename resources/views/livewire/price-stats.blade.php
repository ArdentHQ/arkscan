<div
    wire:poll.60s
    class="hidden flex-grow justify-end h-full lg:flex"
>
    <div
        wire:key="{{ Settings::currency() }}-{{ $isPositive ? 'positive' : 'negative' }}-{{ $usePlaceholder ? 'placeholder' : 'live' }}"
        class="ml-6 h-full"
        x-data="PriceChart(
            {{ $historical->values()->toJson() }},
            {{ $historical->keys()->toJson() }},
            {{ $usePlaceholder ? 'true' : 'false' }},
            window.getThemeMode() === 'dark' ? 'true' : 'false',
            '{{ time() }}',
            {{ $isPositive ? 'true' : 'false' }}
        )"
        @toggle-dark-mode.window="toggleDarkMode"
    >
        <div class="block h-full" wire:ignore>
            <canvas
                x-ref="chart"
                class="w-full h-full"
                width="{{ ExplorerNumberFormatter::isFiat(Settings::currency()) ? 210 : 120 }}"
                height="44"
            ></canvas>
        </div>
    </div>
</div>
