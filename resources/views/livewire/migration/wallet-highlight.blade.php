<div class="overflow-auto dark:bg-black bg-theme-secondary-100">
    <div class="py-8 content-container-full-width">
        <div class="px-8 md:px-10 md:w-full">
            <div class="flex justify-between py-4 px-8 space-x-12 font-semibold bg-white rounded-xl dark:bg-theme-secondary-900">
                <div class="flex flex-shrink-0 space-x-4 min-w-0 whitespace-nowrap">
                    <x-ark-icon
                        name="app-transactions.migration"
                        size="w-11 h-11"
                        class="migration-wallet-icon-dark"
                    />

                    <div class="flex flex-col justify-between space-y-2 min-w-0 whitespace-nowrap">
                        <span class="text-sm leading-none text-theme-secondary-500 dark:text-theme-secondary-600">
                            @lang('general.address')
                        </span>

                        <div class="flex items-center space-x-1 leading-tight text-theme-secondary-500 dark:text-theme-secondary-600">
                            <a class="link" href="{{ route('migration') }}">@lang('pages.migration.stats.migration_wallet')</a>

                            <a href="{{ route('wallet', config('explorer.migration_address')) }}" class="min-w-0">
                                <x-truncate-middle length="10">
                                    {{ config('explorer.migration_address') }}
                                </x-truncate-middle>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-6 divide-x divide-theme-secondary-300 dark:divide-theme-secondary-800">
                    <div class="flex flex-col justify-between space-y-2 text-right whitespace-nowrap">
                        <span class="text-sm leading-none text-theme-secondary-500 dark:text-theme-secondary-600">
                            @lang('pages.migration.stats.amount_migrated')
                        </span>

                        <div class="leading-tight dark:text-white text-theme-secondary-900">
                            <x-currency
                                :currency="Network::currency()"
                                :decimals="0"
                            >
                                {{ $amountMigrated }}
                            </x-currency>
                        </div>
                    </div>

                    <div class="flex flex-col justify-between pl-6 space-y-2 text-right">
                        <span class="text-sm leading-none text-theme-secondary-500 dark:text-theme-secondary-600">
                            @lang('pages.migration.stats.supply')
                        </span>

                        <div class="leading-tight dark:text-white text-theme-secondary-900">
                            {{ $percentage }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
