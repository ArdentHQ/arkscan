<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'ARK'))</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Meta --->
        <meta property="og:image" content="{{ url('/') }}/images/explorer-preview.png" />
        <meta property="og:url" content="{{ url()->full() }}" />
        <meta property="og:type" content="website" />

        @hasSection('metatags')
            @yield('metatags')
        @else
            <x-data-bag key="metatags" resolver="path" view="components.metatags" />
        @endif

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        @livewireStyles

        <!-- Scripts -->
        @stack('scripts')
    </head>
    <body
        @if(Settings::usesDarkTheme())
            class="theme-dark"
        @endif
        x-data="{ 'theme': '{{ Settings::theme() }}' }"
        :class="{ 'theme-dark': theme === 'dark' }"
        @toggle-dark-mode.window="theme === 'dark' ? theme = 'light' : theme = 'dark'"
    >
        <div id="app" class="flex flex-col antialiased bg-white dark:bg-theme-secondary-900">
            <x-navbar.navbar
                :navigation="[
                    ['route' => 'monitor', 'label' => trans('menus.delegates')],
                    ['route' => 'wallets', 'label' => trans('menus.wallets')],
                ]"
            >
                <x-slot name="logo">
                    <img src="/images/logo.svg" class="h-10 lg:h-12" />

                    <span class="hidden ml-4 sm:flex text-theme-secondary-900 dark:text-theme-secondary-200 sm:text-2xl">
                        <span class="font-bold">{{ config('app.name', 'ARK') }}</span>
                    </span>
                </x-slot>
            </x-navbar>

            @section('breadcrumbs')
            @show

            <main class="container flex-1 w-full mx-auto @unless($isLanding ?? false) pb-14 mt-16 @endif sm:max-w-full @unless($fullWidth ?? false) px-4 sm:px-8 lg:max-w-7xl @endif">
                <div class="w-full bg-white dark:bg-theme-secondary-900">
                    @yield('content')
                </div>
            </main>
        </div>

        <x-footer />

        @livewireScripts()

        @stack('extraStyle')

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </body>
</html>
