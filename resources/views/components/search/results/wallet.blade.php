@props(['wallet'])

<x-search.results.result :model="$wallet">
    <div class="flex overflow-auto items-center space-x-2">
        <div class="dark:text-theme-secondary-500">
            @lang('general.search.address')
        </div>

        <x-general.identity
            :model="$wallet"
            without-reverse
            without-reverse-class="space-x-2"
            without-link
            address-visible
            class="text-theme-secondary-500 dark:text-theme-secondary-700"
            content-class="truncate"
            container-class="min-w-0"
            link-class="link group-hover/result:no-underline hover:text-theme-primary-600"
        >
            <x-slot name="icon">
                <div class="w-5 h-5">
                    <x-general.avatar-small
                        :identifier="$wallet->address()"
                        size="w-5 h-5"
                    />
                </div>
            </x-slot>
        </x-general.identity>
    </div>

    <div class="flex items-center space-x-1 text-xs">
        <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('general.search.balance')
        </div>

        <div class="truncate dark:text-theme-secondary-500">
            <x-currency :currency="Network::currency()">
                {{ ExplorerNumberFormatter::number($wallet->balance()) }}
            </x-currency>
        </div>
    </div>
</x-search.results.result>
