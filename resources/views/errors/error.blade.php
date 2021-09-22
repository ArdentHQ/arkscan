<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ Network::explorerTitle() }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#c9292c">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Meta --->
        <meta property="og:title" content="{{ $errorType }} - Error | ARK Documentation" />
        <meta property="og:image" content="{{ url('/') }}/images/meta-image.png" />
        <meta property="og:url" content="{{ url()->full() }}" />
        <meta property="og:type" content="website" />

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script src="{{ mix('js/manifest.js') }}" defer></script>
        <script src="{{ mix('js/vendor.js') }}" defer></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body
        class="min-h-screen bg-white dark"
        x-data="{ 'theme': '{{ Settings::theme() }}' }"
        :class="{ 'dark bg-theme-secondary-900': theme === 'dark', 'bg-white': theme !== 'dark' }"
    >
        <div id="app" class="flex flex-col h-full antialiased">
            <main class="container flex flex-1 items-center px-4 mx-auto w-full sm:px-8 sm:max-w-full lg:max-w-7xl">
                <div class="w-full rounded-lg">
                    <div class="flex flex-col justify-center items-center space-y-8">
                        <div class="flex justify-center w-full">
                            <img src="/images/errors/{{ $errorType }}.svg" class="inline w-full max-w-4xl dark:hidden"/>
                            <img src="/images/errors/{{ $errorType }}_dark.svg" class="hidden w-full max-w-4xl dark:inline"/>
                        </div>

                        <div class="text-lg font-semibold text-center text-theme-secondary-900 dark:text-theme-secondary-600">
                            {{ ARKEcosystem\UserInterface\UI::getErrorMessage($errorType) }}
                        </div>
                        <div class="space-x-3">
                            <a href="/" class="button-primary">@lang('menus.home')</a>
                            <a href="{{ url()->current() }}" class="button-secondary">@lang('general.reload')</a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
