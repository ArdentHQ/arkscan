@php
    $isProduction = config('arkscan.network') === 'production';
@endphp

<x-navbar.dropdown>
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
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item
        :url="Network::testnetExplorerUrl()"
        :is-active="! $isProduction"
        class="inline-flex justify-between items-center"
    >
        <span>@lang('general.navbar.testnet')</span>
    </x-general.dropdown.list-item>
</x-navbar.dropdown>
