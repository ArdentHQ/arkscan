<div
    class="flex flex-grow justify-between ml-3 h-full"
    @unless ($placeholder)
        wire:poll.60s
    @endunless
>
    @unless ($placeholder)
        <div class="flex flex-col justify-between h-full">
            <a class="pl-3 text-sm font-semibold leading-none whitespace-nowrap border-l link border-theme-secondary-300 dark:border-theme-secondary-800" href="#">
                @lang('actions.view_statistics')
            </a>

            @if ($priceChange < 0)
                <span class="flex items-center pl-3 space-x-1 text-sm font-semibold leading-none text-theme-danger-400">
                    <span>
                        <x-ark-icon name="triangle-down" size="2xs" />
                    </span>
                    <span>
                        <x-percentage>{{ $priceChange * 100 * -1 }}</x-percentage>
                    </span>
                </span>
            @else
                <span class="flex items-center pl-3 space-x-1 text-sm font-semibold leading-none text-theme-success-600">
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


    <div class="hidden flex-grow justify-end lg:flex" >
        <div
            wire:key="{{ Settings::currency() }}"
            class="ml-6"
            style="width: 120px; height: 40px;"
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
        >
            <div class="block" wire:ignore style="width: 120px; height: 40px;">
                <canvas x-ref="chart" class="w-full h-full" ></canvas>
            </div>
        </div>
    </div>
</div>
