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
        'flex rounded items-center md:bg-theme-secondary-200 md:dark:bg-theme-secondary-800 md:border border-theme-secondary-300 dark:border-transparent justify-between',
        'md:text-theme-secondary-500 md:dark:text-theme-secondary-700 cursor-not-allowed select-none' => $isDisabled,
        'md:text-theme-secondary-700 md:dark:text-theme-secondary-200 cursor-default' => ! $isDisabled,
    ])>
        <div class="font-semibold md:py-1.5 md:pr-2 md:pl-3 md:text-sm transition-default">
            <span>@lang('general.navbar.price'):</span>

            @if ($isDisabled)
                @lang('general.na')
            @else
                <div class="inline-flex items-center">
                    @if (config('currencies.'.Str::lower($to).'.symbol'))
                        <span>
                            {{ config('currencies.'.Str::lower($to).'.symbol') }}
                        </span>
                    @endif

                    <span>
                        {{ $price }}
                    </span>
                </div>
            @endif
        </div>

        <x-general.dropdown.dropdown
            scroll-class="max-h-[246px] md:max-h-[332px]"
            :disabled="$isDisabled"
        >
            <x-slot
                name="button"
                class="rounded-r rounded-l md:rounded-l-none"
            >
                <div
                    @class([
                        'flex justify-center items-center py-2 pr-3 space-x-2 text-sm font-semibold leading-4 group',
                        'cursor-not-allowed' => $isDisabled,
                        'hover:text-theme-secondary-900 hover:dark:text-theme-secondary-200 md:dark:text-theme-secondary-200' => ! $isDisabled,
                    ])
                    @if ($isDisabled)
                        disabled
                    @endif
                >
                    <div @class([
                        'md:w-px h-3.5 md:block transition-default',
                        'bg-theme-secondary-300 dark:bg-theme-secondary-700' => $isDisabled,
                        'bg-transparent md:group-hover:bg-theme-secondary-300 md:group-hover:dark:bg-theme-secondary-700' => ! $isDisabled,
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
                            size="w-3 h-3"
                        />
                    </span>
                </div>
            </x-slot>

            <x-slot
                name="content"
                class="right-0 top-full"
            >
                @foreach (config('currencies') as $currency)
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
