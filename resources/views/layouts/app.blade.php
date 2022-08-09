<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-ark-pages-includes-layout-head
        :default-name="trans('metatags.home.title')"
        mask-icon-color="#de5846"
        microsoft-tile-color="#de5846"
        theme-color="#ffffff"
    />

    <x-ark-pages-includes-layout-body
        class="table-compact"
        x-data="{ 'expanded': {{ Settings::usesExpandedTables() ? 'true' : 'false' }} }"
        x-bind:class="{ 'table-compact-until-md': expanded }"
        @toggle-expanded-table="expanded = ($event.detail === true)"
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
            <x-ark-footer
                :creator="[
                    'url' => trans('general.urls.ardent'),
                    'label' => trans('general.ardent'),
                    'newWindow' => true,
                ]"
                :socials="config('social.networks')"
            >
                <span class="inline-flex items-center space-x-1 whitespace-nowrap">
                    <span>@lang ('general.market_data_by')</span>

                    <a href="@lang ('general.urls.coingecko')" target="_blank" rel="noopener nofollow noreferrer">
                        <x-ark-icon name="app-coingecko" />
                    </a>
                </span>
            </x-ark-footer>

            <livewire:search-modal />
        </x-slot>
    </x-ark-pages-includes-layout-body>
</html>
