@php
    $isProduction = config('arkscan.network') === 'production';
@endphp

<x-general.dropdown.dropdown
    active-button-class=""
    buttonClass="justify-center p-2 space-x-1.5 h-8 text-sm font-semibold rounded md:w-8 md:border md:hover:text-theme-secondary-700 md:border-theme-secondary-300 md:dark:border-theme-secondary-800"
>
    <x-slot name="button">
        @if (strlen($slot) > 0)
            <span>{{ $slot }}</span>
        @else
            <span>
                @if ($isProduction)
                    @lang('general.navbar.live')
                @else
                    @lang('general.navbar.test')
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
        <span class="md:hidden">@lang('general.navbar.live')</span>
        <span class="hidden md:block">@lang('general.navbar.live_network')</span>
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item
        :url="Network::testnetExplorerUrl()"
        :is-active="! $isProduction"
    >
        <span class="md:hidden">@lang('general.navbar.test')</span>
        <span class="hidden md:block">@lang('general.navbar.test_network')</span>
    </x-general.dropdown.list-item>
</x-general.dropdown>
