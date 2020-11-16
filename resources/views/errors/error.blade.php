<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'ARK Documentation') }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
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
    <body class="antialiased">
        <div id="app" class="flex flex-col antialiased bg-white theme-light">
            <main class="container flex items-center flex-1 w-full px-4 mx-auto sm:max-w-full sm:px-8 lg:max-w-7xl">
                <div class="w-full bg-white rounded-lg">
                    <div class="flex flex-col items-center justify-center space-y-8">
                        <img src="/images/errors/{{ $errorType }}.svg" class="max-w-4xl"/>
                        <div class="text-lg font-semibold text-center text-theme-secondary-900">
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
