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
        @stack('metatags')
        <meta property="og:image" content="{{ url('/') }}/images/meta-image.png" />
        <meta property="og:url" content="{{ url()->full() }}" />
        <meta property="og:type" content="website" />

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        @livewireStyles

        <!-- Scripts -->
        @stack('scripts')
    </head>
    <body>
        <div id="app" class="flex flex-col antialiased bg-white theme-light">
            <x-ark-navbar
                :navigation="[
                    ['route' => 'home', 'label' => trans('menus.delegate')],
                    ['route' => 'home', 'label' => trans('menus.top_accounts')],
                    ['route' => 'home', 'label' => trans('menus.registered')],
                ]"
                {{-- :registered-menu="App\Models\Documentation::productMenu()" --}}
            >
                <x-slot name="logo">
                    <img src="/images/logo.svg" class="h-10 lg:h-12" />

                    <span class="hidden ml-4 sm:flex text-theme-secondary-900 sm:text-2xl">
                        <span class="font-bold">{{ config('app.name', 'ARK') }}</span>
                    </span>
                </x-slot>
            </x-navbar>

            @section('breadcrumbs')
            @show

            <main class="container flex-1 w-full mx-auto @unless($isLanding ?? false) pb-14 mt-16 @endif sm:max-w-full @unless($fullWidth ?? false) px-4 sm:px-8 lg:max-w-7xl @endif">
                <div class="w-full bg-white rounded-lg">
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
