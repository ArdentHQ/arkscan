<div class="flex flex-col px-8 m-auto space-y-5 md:px-10 xl:flex-row xl:space-y-0 xl:space-x-5 xl:max-w-7xl">
    <div class="flex md:space-x-4 xl:w-164 min-w-0">
        <div class="flex flex-col flex-1 justify-between space-y-5 font-semibold sm:flex-row sm:space-x-4 sm:space-y-0 sm:rounded-xl sm:bg-theme-hint-800 w-full">
            <div class="flex flex-1 py-4 px-8 rounded-xl sm:pr-0 sm:bg-transparent sm:rounded-none md:space-x-4 bg-theme-hint-800 min-w-0">
                <x-ark-icon
                    name="app-transactions.migration"
                    size="w-11 h-11"
                    class="hidden md:block migration-icon-dark"
                />

                <div class="flex flex-col justify-between space-y-2 min-w-0">
                    <span class="text-sm leading-none text-theme-hint-300">
                        @lang('pages.migration.stats.migration_wallet')
                    </span>

                    <div class="flex items-center space-x-1 leading-tight text-white">
                        <a href="{{ route('wallet', config('explorer.migration_address')) }}" class="min-w-0">
                            <x-truncate-dynamic>
                                {{ config('explorer.migration_address') }}
                            </x-truncate-dynamic>
                        </a>

                        <x-ark-icon
                            name="hand-touch"
                            size="sm"
                            class="text-theme-primary-400"
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-between py-4 px-8 space-x-6 rounded-xl divide-x sm:pl-0 sm:bg-transparent sm:rounded-none divide-theme-hint-700 bg-theme-hint-800 w-62">
                <div class="flex flex-col justify-between space-y-2 sm:text-right whitespace-nowrap">
                    <span class="text-sm leading-none text-theme-hint-300">
                        @lang('pages.migration.stats.amount_migrated')
                    </span>

                    <div class="leading-tight text-white">
                        <x-currency
                            :currency="Network::currency()"
                            decimals="3"
                        >
                            {{ $amountMigrated }}
                        </x-currency>
                    </div>
                </div>

                <div class="flex flex-col justify-between pl-6 space-y-2 text-right">
                    <span class="text-sm leading-none text-theme-hint-300">
                        @lang('pages.migration.stats.supply')
                    </span>

                    <div class="leading-tight text-white">
                        {{ $percentage }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col space-y-5 sm:flex-row sm:space-y-0 sm:space-x-5">
        <x-stats.stat
            :label="trans('pages.migration.stats.remaining_supply')"
            padding="py-4 px-8"
            text-class="space-y-2"
            icon="coins-stack"
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
