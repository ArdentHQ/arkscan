@php
    $isProduction = config('arkscan.network') === 'production';
@endphp

<x-navbar.dropdown
    dropdown-background="bg-white dark:bg-theme-dark-900 border border-white dark:border-theme-dark-700 px-1 py-[0.125rem]"
    dropdown-padding=""
>
    <x-slot name="button">
        <span>
            @if ($isProduction)
                @lang('general.navbar.mainnet')
            @else
                @lang('general.navbar.testnet')
            @endif
        </span>
    </x-slot>

    <x-general.dropdown.list-item
        :url="Network::mainnetExplorerUrl()"
        :is-active="$isProduction"
        class="inline-flex justify-between items-center"
    >
        <span>@lang('general.navbar.mainnet')</span>

        @if ($isProduction)
            <span>
                <x-ark-icon
                    name="double-check-mark"
                    size="sm"
                    class="text-theme-primary-600 dark:text-theme-dark-50"
                />
            </span>
        @endif
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item
        :url="Network::testnetExplorerUrl()"
        :is-active="! $isProduction"
        class="inline-flex justify-between items-center"
    >
        <span>@lang('general.navbar.testnet')</span>

        @if (! $isProduction)
            <span>
                <x-ark-icon
                    name="double-check-mark"
                    size="sm"
                    class="text-theme-primary-600 dark:text-theme-dark-50"
                />
            </span>
        @endif
    </x-general.dropdown.list-item>
</x-navbar.dropdown>
