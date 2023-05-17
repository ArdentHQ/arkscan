@php ($isDisabled = ! Network::canBeExchanged() || config('explorer.network') !== 'production' || ! $isAvailable)

<div
    wire:poll.60s
    class="w-full md:w-auto"
    :class="{ 'opacity-50': busy }"
    x-data="{ to: '{{ $to }}', busy: false }"
    x-init="livewire.on('currencyChanged', () => busy = true);"
    @has-loaded-price-data="busy = false"
>
    <div @class([
        'flex rounded items-center md:bg-theme-secondary-200 md:dark:bg-theme-secondary-800 md:border border-theme-secondary-300 dark:border-transparent cursor-default justify-between',
        'md:text-theme-secondary-500 md:dark:text-theme-secondary-700' => $isDisabled,
        'md:text-theme-secondary-700 md:dark:text-theme-secondary-200' => ! $isDisabled,
    ])>
        <div class="font-semibold md:py-1.5 md:pr-2 md:pl-3 md:text-sm transition-default">
            <span>@lang('general.navbar.price'):</span>

            @if ($isDisabled)
                @lang('general.na')
            @else
                {{ $price }}
            @endif
        </div>

        <x-general.dropdown.dropdown dropdown-class="max-h-[246px] md:max-h-[332px]">
            <x-slot
                name="button"
                @class([
                    'inline-flex items-center rounded-l rounded-r md:rounded-l-none',
                    'text-theme-secondary-500 dark:text-theme-secondary-700 bg-theme-secondary-200 dark:bg-theme-secondary-800' => $isDisabled,
                    'bg-theme-secondary-200 dark:bg-theme-secondary-800 md:bg-white md:dark:text-theme-secondary-200 md:hover:text-theme-secondary-900 md:hover:bg-theme-secondary-200 md:dark:bg-theme-secondary-900 dark:hover:bg-theme-secondary-800 text-theme-secondary-700 dark:text-theme-secondary-200' => ! $isDisabled,
                ])
                :disabled="$isDisabled"
            >
                <button
                    class="flex justify-center items-center py-2 pr-3 space-x-2 text-sm font-semibold leading-4 transition-default"
                    @if ($isDisabled)
                        disabled
                    @endif
                >
                    <div class="hidden w-px h-3.5 md:block bg-theme-secondary-300 dark:bg-theme-secondary-700"></div>

                    <span>
                        {{ $to }}
                    </span>

                    <span
                        class="transition-default"
                        :class="{ 'rotate-180': isOpen }"
                    >
                        <x-ark-icon
                            name="arrows.chevron-down-small"
                            size="w-3 h-3"
                        />
                    </span>
                </button>
            </x-slot>

            <x-slot
                name="content"
                class="right-0 top-full"
            >
                @foreach (collect(config('currencies')) as $currency)
                    <x-general.dropdown.list-item
                        :is-active="$currency['currency'] === $to"
                        wire:click="setCurrency('{{ $currency['currency'] }}')"
                    >
                        {{ $currency['currency'] }}

                        @if ($currency['symbol'] !== null)
                            <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                                ({{ $currency['symbol'] }})
                            </span>
                        @endif
                    </x-general.dropdown.list-item>
                @endforeach
            </x-slot>
        </x-general.dropdown>
    </div>
</div>
