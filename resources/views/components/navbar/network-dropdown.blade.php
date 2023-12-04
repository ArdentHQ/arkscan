@php
    $isProduction = config('arkscan.network') === 'production';
@endphp

<x-general.dropdown.dropdown
    active-button-class="space-x-1.5"
    button-wrapper-class=""
    button-class="justify-center p-2 space-x-1.5 h-8 text-sm font-semibold rounded md:px-3 md:bg-white md:border bg-theme-secondary-200 text-theme-secondary-700 md:hover:text-theme-secondary-700 md:border-theme-secondary-300 md:dark:border-theme-dark-700 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-200 md:hover:text-theme-secondary-900 dark:bg-theme-dark-800 dark:hover:bg-theme-dark-700 dark:text-theme-dark-50 hover:bg-theme-secondary-200"
>
    <x-slot name="button">
        @if (strlen($slot) > 0)
            <div>{{ $slot }}</div>
        @else
            <span>
                @if ($isProduction)
                    @lang('general.navbar.mainnet')
                @else
                    @lang('general.navbar.testnet')
                @endif
            </span>

            <span
                class="transition-default"
                :class="{ 'rotate-180': dropdownOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-2.5 h-2.5"
                />
            </span>
        @endif
    </x-slot>

    <x-general.dropdown.list-item
        :url="Network::mainnetExplorerUrl()"
        :is-active="$isProduction"
    >
        @lang('general.navbar.mainnet')
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item
        :url="Network::testnetExplorerUrl()"
        :is-active="! $isProduction"
    >
        @lang('general.navbar.testnet')
    </x-general.dropdown.list-item>
</x-general.dropdown>
