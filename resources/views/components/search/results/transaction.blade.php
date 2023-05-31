@props(['transaction'])

<x-search.results.result :model="$transaction">
    <div class="flex items-center space-x-2">
        <div class="dark:text-theme-secondary-500">
            @lang('general.search.transaction')
        </div>

        <div class="min-w-0 link">
            <x-truncate-dynamic>
                {{ $transaction->id() }}
            </x-truncate-dynamic>
        </div>
    </div>

    <div class="flex flex-col space-y-2 md:flex-row md:items-center md:space-y-0 md:space-x-4">
        <div class="flex items-center space-x-2 text-xs">
            <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('general.search.from')
            </div>

            <x-general.identity
                :model="$transaction->sender()"
                without-reverse
                without-reverse-class="space-x-2"
                without-link
                class="text-theme-secondary-700 dark:text-theme-secondary-500"
            >
                <x-slot name="icon">
                    <x-general.avatar-small
                        :identifier="$transaction->sender()->address"
                        size="w-4 h-4 md:w-5 md:h-5"
                    />
                </x-slot>
            </x-general.identity>
        </div>

        <x-search.results.transaction-type :transaction="$transaction" />

        <div class="flex items-center space-x-2 text-xs md:flex-1 md:justify-end md:space-x-0">
            <div class="md:hidden text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('general.search.amount')
            </div>

            <div class="text-theme-secondary-700 dark:text-theme-secondary-500">
                <x-currency :currency="Network::currency()">
                    {{ ExplorerNumberFormatter::number($transaction->amountWithFee()) }}
                </x-currency>
            </div>
        </div>
    </div>
</x-search.results.result>
