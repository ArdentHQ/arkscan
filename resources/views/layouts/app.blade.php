<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ trim(View::yieldContent('title', trans("metatags.home.title"))) }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#c9292c">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Meta --->
        <x-ark-metadata-tags>
            <x-slot name="title">@yield('meta-title', trans('metatags.home.title'))</x-slot>
            <x-slot name="description">@yield('meta-description', trans('metatags.home.description'))</x-slot>
            <x-slot name="image">@yield('meta-image', trans('metatags.home.image'))</x-slot>
        </x-ark-metadata-tags>

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        @livewireStyles

        <!-- Scripts -->
        @stack('scripts')
    </head>
    <body
        class="@if(Settings::usesDarkTheme()) dark @endif @if(Settings::usesCompactTables()) table-compact @endif"
        x-data="{ 'theme': '{{ Settings::theme() }}', 'compact': {{ Settings::usesCompactTables() ? 'true' : 'false' }} }"
        :class="{ 'dark': theme === 'dark', 'table-compact': compact }"
        @toggle-dark-mode.window="theme === 'dark' ? theme = 'light' : theme = 'dark'"
        @toggle-compact-table="compact = ! compact"
    >
        <div id="app" class="flex flex-col antialiased bg-white dark:bg-theme-secondary-900">
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

            <main class="container flex flex-1 w-full mx-auto @unless($isLanding ?? false) pb-14 mt-16 @endif sm:max-w-full @unless($fullWidth ?? false) px-4 sm:px-8 lg:max-w-7xl @endif">
                <div class="{{ $containerChildClass ?? 'w-full bg-white dark:bg-theme-secondary-900' }}">
                    @yield('content')
                </div>
            </main>
        </div>

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

        @livewireScripts()

        @stack('scripts')

        <!-- Scripts -->
        <script src="{{ mix('js/manifest.js') }}" defer></script>
        <script src="{{ mix('js/vendor.js') }}" defer></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
    </body>
</html>
