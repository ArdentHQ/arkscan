<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-ark-pages-includes-layout-head
        :default-name="trans('metatags.home.title')"
        mask-icon-color="#c9292c"
        microsoft-tile-color="#da532c"
        theme-color="#ffffff"
    />

    <x-ark-pages-includes-layout-body
        class="table-compact"
        x-data="{ 'compact': {{ Settings::usesCompactTables() ? 'true' : 'false' }} }"
        x-bind:class="{ 'table-compact-until-md': !compact, }"
        @toggle-compact-table="compact = ! $event.detail.expand"
    >
        <x-navbar.navbar
            :navigation="[
                ['route' => 'delegates',  'label' => trans('menus.delegates')],
                ['route' => 'wallets',    'label' => trans('menus.wallets')],
                ['route' => 'statistics', 'label' => trans('menus.statistics')],
            ]"
        >
            <x-slot name="logo">
                <x-navbar.logo />
            </x-slot>
        </x-navbar.navbar>

        <x-slot name="content">
            <main class="container flex flex-1 w-full mx-auto @unless($isLanding ?? false) pb-14 mt-16 @endif sm:max-w-full @unless($fullWidth ?? false) px-4 sm:px-8 lg:max-w-7xl @endif">
                <div class="{{ $containerChildClass ?? 'w-full bg-white dark:bg-theme-secondary-900' }}">
                    @yield('content')
                </div>
            </main>
        </x-slot>

        <x-slot name="footer">
            <x-ark-footer>
                <x-slot name="copyrightSlot">
                    <div class="flex">
                        <span class="mx-1 sm:inline"> | </span>
                        <span class="flex items-center space-x-2 whitespace-nowrap">
                            <span>@lang('general.market_data_by')</span>

                            <a
                                href="@lang('general.urls.coingecko')"
                                target="_blank"
                            >
                                <x-ark-icon
                                    name="app-coingecko"
                                    class="inline-block -mt-1"
                                />
                            </a>
                        </span>
                    </div>
                </x-slot>
            </x-ark-footer>

            <livewire:search-module is-modal />
        </x-slot>
    </x-ark-pages-includes-layout-body>
</html>
