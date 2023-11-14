<div wire:poll.60s class="w-full h-full">
    <div
        wire:key="{{ Settings::currency() }}-{{ $isPositive ? 'positive' : 'negative' }}-{{ $usePlaceholder ? 'placeholder' : 'live' }}-{{ time() }}"
        class="w-full h-full"
        x-data="PriceChart(
            {{ $historical->values()->toJson() }},
            {{ $historical->keys()->toJson() }},
            {{ $usePlaceholder ? 'true' : 'false' }},
            window.getThemeMode() === 'dark' ? 'true' : 'false',
            {{ $isPositive ? 'true' : 'false' }}
        )"
        @toggle-dark-mode.window="toggleDarkMode"
    >
        <div
            class="relative w-full h-[39px]"
            wire:ignore
        >
            <canvas
                x-ref="chart"
                class="max-w-full"
            ></canvas>
        </div>
    </div>
</div>
