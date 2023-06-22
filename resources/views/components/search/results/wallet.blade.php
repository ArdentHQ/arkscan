@props(['wallet', 'truncate' => false, 'truncateLength' => null])

<x-search.results.result :model="$wallet">
    <div class="flex overflow-auto items-center space-x-2 isolate">
        <div class="dark:text-theme-secondary-500">
            @lang('general.search.address')
        </div>

        <x-general.identity
            :model="$wallet"
            without-reverse
            without-reverse-class="space-x-2"
            without-link
            :without-truncate="! $truncate"
            :truncate-length="$truncateLength"
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
                {{-- Taken from tables.rows.desktop.encapsulated.balance --}}
                {{ rtrim(rtrim(number_format((float) ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation::execute((float) $wallet->balance()), 8), 0), '.') }}
            </x-currency>
        </div>
    </div>
</x-search.results.result>
