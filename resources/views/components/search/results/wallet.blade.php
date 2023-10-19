@props(['wallet', 'truncate' => false, 'truncateLength' => null])

<x-search.results.result :model="$wallet">
    <div class="flex overflow-auto items-center space-x-2 isolate">
        <div class="dark:text-theme-dark-50">
            @lang('general.search.address')
        </div>

        <x-general.identity
            :model="$wallet"
            without-reverse
            without-reverse-class="space-x-2"
            without-link
            without-icon
            :without-truncate="! $truncate"
            :truncate-length="$truncateLength"
            address-visible
            class="text-theme-secondary-500 dark:text-theme-dark-200"
            content-class="truncate"
            container-class="min-w-0"
            link-class="link group-hover/result:no-underline hover:text-theme-primary-600"
        />
    </div>

    <div class="flex items-center space-x-1 text-xs">
        <div class="text-theme-secondary-500 dark:text-theme-dark-200">
            @lang('general.search.balance')
        </div>

        <div class="truncate dark:text-theme-dark-50">
            <x-currency :currency="Network::currency()">
                {{ ExplorerNumberFormatter::unformattedRawValue($wallet->balance()) }}
            </x-currency>
        </div>
    </div>
</x-search.results.result>
