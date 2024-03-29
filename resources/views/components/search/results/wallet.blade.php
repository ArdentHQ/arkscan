@props([
    'wallet',
    'truncate' => false,
    'truncateLength' => null,
])

<x-search.results.result :model="$wallet">
    <x-tables.rows.mobile class="md:hidden">
        <x-slot name="header">
            <x-general.identity
                :model="$wallet"
                without-link
                :without-truncate="! $truncate"
                :truncate-length="10"
                address-visible
                class="text-theme-secondary-700 dark:text-theme-dark-200"
                content-class="truncate"
                container-class="min-w-0"
                link-class="link group-hover/result:no-underline hover:text-theme-primary-600"
            />
        </x-slot>

        <x-search.results.mobile.detail :title="trans('general.search.balance_currency', ['currency' => Network::currency()])">
            {{ ExplorerNumberFormatter::currencyWithoutSuffix($wallet->balance(), Network::currency()) }}
        </x-search.results.mobile.detail>
    </x-tables.rows.mobile>

    <div class="hidden flex-col space-y-2 md:flex">
        <div class="flex overflow-auto items-center space-x-2 isolate">
            <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                @lang('general.search.address')
            </div>

            <x-general.identity
                :model="$wallet"
                without-link
                :without-truncate="! $truncate"
                :truncate-length="$truncateLength"
                address-visible
                class="text-theme-secondary-700 dark:text-theme-dark-200"
                content-class="truncate"
                container-class="min-w-0"
                link-class="link group-hover/result:no-underline hover:text-theme-primary-600"
            />
        </div>

        <div class="flex items-center space-x-1 text-xs">
            <div class="text-theme-secondary-700 dark:text-theme-dark-200">
                @lang('general.search.balance')
            </div>

            <div class="truncate text-theme-secondary-900 dark:text-theme-dark-50">
                <x-currency :currency="Network::currency()">
                    {{ $wallet->balance() }}
                </x-currency>
            </div>
        </div>
    </div>
</x-search.results.result>
