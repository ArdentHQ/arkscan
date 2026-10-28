<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @if (config('arkscan.arkconnect.enabled'))
        x-data="Wallet({{ json_encode(Network::toArray()) }})"
    @endif
>
    @push('scripts')
        @vite('resources/js/webhooks.js')
    @endpush

    <x-ark-pages-includes-layout-head
        :default-name="trans('metatags.home.title')"
        mask-icon-color="#de5846"
        microsoft-tile-color="#de5846"
        theme-color="#ffffff"
    />

    <x-ark-pages-includes-layout-body class="table-compact">
        <x-navbar.navbar :navigation="$navigationEntries" />

        <x-slot name="footer">
            <x-ark-footer
                :creator="[
                    'url' => trans('general.urls.ardent'),
                    'label' => trans('general.ardent'),
                    'newWindow' => true,
                ]"
                :socials="config('social.networks')"
                wrapper-class="py-5 px-6 max-w-7xl md:px-10"
                class="dark:border-transparent text-[#A4B1BC] dim:text-theme-dark-200"
            >
                <a
                    href="@lang ('general.urls.coingecko')"
                    target="_blank"
                    rel="noopener nofollow noreferrer"
                    class="inline-flex items-center space-x-1 whitespace-nowrap group"
                >
                    <span>@lang ('general.market_data_by')</span>

                    <x-ark-icon name="app-coingecko" size="sm" />
                </a>

                <span class="mx-0.5">|</span>

                <a
                    href="@lang ('general.urls.simpleanalytics', ['domain' => parse_url(config('app.url'), PHP_URL_HOST)])"
                    target="_blank"
                    rel="noopener nofollow"
                    referrerpolicy="origin"
                    class="inline-flex items-center space-x-1 whitespace-nowrap group"
                >
                    <span>@lang ('general.analytics_by')</span>

                    <x-ark-icon name="app-simpleanalytics" size="sm" />
                </a>
            </x-ark-footer>

            <livewire:search-modal />

            <x-webhooks.currency-update :currency="Settings::currency()" />

            @if (config('arkscan.arkconnect.enabled'))
                <x-arkconnect.delegate-toasts />
            @endif

            <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
            <script async src="https://scripts.simpleanalyticscdn.com/auto-events.js"></script>
        </x-slot>
    </x-ark-pages-includes-layout-body>
</html>
