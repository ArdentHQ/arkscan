@php ($isDisabled = app()->isDownForMaintenance() || ! Network::canBeExchanged() || config('arkscan.network') !== 'production' || ! $isAvailable)

<div
    {{--
        @TODO: use events and websockets to handle when price ticker (and all other areas) are updated instead of polling

        https://app.clickup.com/t/86dqjdxjm
    --}}
    @if (! $isDisabled && config('broadcasting.default') !== 'reverb')
        wire:poll.visible.30s
    @endif
    class="w-full md:w-auto"
    :class="{ 'opacity-50': busy }"
    x-data="{ busy: false }"
    x-init="Livewire.on('currencyChanged', () => busy = true);"
    @has-loaded-price-data="busy = false"
>
    <div @class([
        'flex rounded items-center md:bg-theme-secondary-200 md:dark:bg-theme-dark-700 md:border border-theme-secondary-300 dark:border-transparent justify-between',
        'dark:text-theme-dark-200 md:text-theme-secondary-500 md:dark:text-theme-dark-500 cursor-not-allowed select-none' => $isDisabled,
        'dark:text-theme-dark-200 md:text-theme-secondary-700 md:dark:text-theme-dark-50 cursor-default' => ! $isDisabled,
    ])>
        <div class="font-semibold md:py-1.5 md:pr-2 md:pl-3 md:text-sm transition-default">
            <span>@lang('general.navbar.price'):</span>

            @if ($isDisabled)
                @lang('general.na')
            @else
                <div class="inline-flex items-center">
                    {{ ExplorerNumberFormatter::currency($price, Settings::currency()) }}
                </div>
            @endif
        </div>

        <x-general.dropdown.dropdown
            active-button-class=""
            button-class="rounded-r rounded-l md:bg-white md:rounded-l-none bg-theme-secondary-200 text-theme-secondary-700 dim:hover:bg-theme-dark-700 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-600 md:hover:text-theme-secondary-900 dark:bg-theme-dark-800 dark:hover:bg-theme-secondary-800 dark:text-theme-dark-200 hover:bg-theme-secondary-200"
            dropdown-class="right-0 min-w-[125px]"
            scroll-class="max-h-[246px] md:max-h-[332px]"
            :disabled="$isDisabled"
        >
            <x-slot
                name="button"
                class="rounded-r rounded-l md:rounded-l-none"
            >
                <div
                    @class([
                        'flex justify-center items-center py-2 pr-3 space-x-2 text-sm font-semibold leading-4 group transition-default',
                        'cursor-not-allowed' => $isDisabled,
                        'dark:text-theme-dark-50 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50 md:dark:text-theme-dark-50' => ! $isDisabled,
                    ])
                    @if ($isDisabled)
                        disabled
                    @endif
                >
                    <div @class([
                        'md:w-px h-3.5 md:block',
                        'bg-theme-secondary-300 dark:bg-theme-dark-500' => $isDisabled,
                        'bg-transparent md:group-hover:bg-theme-secondary-300 md:group-hover:dark:bg-theme-dark-700' => ! $isDisabled,
                    ])></div>

                    <span>
                        {{ $to }}
                    </span>

                    <span
                        class="transition-default"
                        :class="{ 'rotate-180': dropdownOpen }"
                    >
                        <x-ark-icon
                            name="arrows.chevron-down-small"
                            size="w-2.5 h-2.5 md:w-3 md:h-3"
                        />
                    </span>
                </div>
            </x-slot>

            @foreach (config('currencies') as $currency)
                <x-general.dropdown.list-item
                    :is-active="$currency['currency'] === $to"
                    wire:click="setCurrency('{{ $currency['currency'] }}')"
                >
                    {{ $currency['currency'] }}

                    @if ($currency['symbol'] !== null)
                        <span class="text-theme-secondary-500 dark:text-theme-dark-200">
                            ({{ $currency['symbol'] }})
                        </span>
                    @endif
                </x-general.dropdown.list-item>
            @endforeach
        </x-general.dropdown>
    </div>
</div>
