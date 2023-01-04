<div class="flex flex-col xl:flex-row xl:max-w-7xl xl:space-x-5 space-y-5 xl:space-y-0 m-auto px-8 md:px-10">
    <div class="flex md:space-x-4 xl:w-164">
        <div class="flex flex-1 flex-col sm:flex-row space-y-5 sm:space-y-0 justify-between font-semibold sm:bg-theme-hint-800 sm:rounded-xl">
            <div class="flex bg-theme-hint-800 md:space-x-4 px-8 sm:pr-0 py-4 rounded-xl sm:bg-transparent sm:rounded-none">
                <x-ark-icon
                    name="app-transactions.migration"
                    size="w-11 h-11"
                    class="hidden md:block migration-icon-dark"
                />

                <div class="flex flex-col justify-between space-y-2">
                    <span class="text-sm text-theme-hint-300 leading-none">
                        @lang('pages.migration.stats.migration_wallet')
                    </span>

                    <div class="flex items-center space-x-1 text-white leading-tight">
                        <a href="{{ route('wallet', config('explorer.migration_address')) }}">
                            <x-truncate-middle length="20">
                                {{ config('explorer.migration_address') }}
                            </x-truncate-middle>
                        </a>

                        <x-ark-icon
                            name="hand-touch"
                            size="sm"
                            class="text-theme-primary-400"
                        />
                    </div>
                </div>
            </div>

            <div class="flex space-x-6 justify-between divide-x divide-theme-hint-700 bg-theme-hint-800 px-8 sm:pl-0 py-4 rounded-xl sm:bg-transparent sm:rounded-none">
                <div class="flex flex-col justify-between sm:text-right space-y-2">
                    <span class="text-sm text-theme-hint-300 leading-none">
                        @lang('pages.migration.stats.amount_migrated')
                    </span>

                    <div class="text-white leading-tight">
                        <x-currency
                            :currency="Network::currency()"
                            decimals="3"
                        >
                            {{ $amountMigrated }}
                        </x-currency>
                    </div>
                </div>

                <div class="flex flex-col justify-between text-right space-y-2 pl-6">
                    <span class="text-sm text-theme-hint-300 leading-none">
                        @lang('pages.migration.stats.supply')
                    </span>

                    <div class="text-white leading-tight">
                        {{ $percentage }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:space-x-5 space-y-5 sm:space-y-0">
        <x-stats.stat
            :label="trans('pages.migration.stats.remaining_supply')"
            padding="py-4 px-8"
            text-class="space-y-2"
            icon="app-coins"
            icon-class="hidden md:flex"
            container-spacing="md:space-x-4"
            class="flex-1"
        >
            <x-currency
                :currency="Network::currency()"
                decimals="3"
            >
                {{ $remainingSupply }}
            </x-currency>
        </x-stats.stat>

        <x-stats.stat
            :label="trans('pages.migration.stats.wallets_migrated')"
            padding="py-4 px-8"
            text-class="space-y-2"
            icon="wallet"
            icon-class="hidden md:flex"
            container-spacing="md:space-x-4"
            class="flex-1"
        >
            {{ $walletsMigrated }}
        </x-stats.stat>
    </div>
</div>
