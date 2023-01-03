<div>
    <div class="flex items-center space-x-3 mb-6 md:mb-3">
        <h2>
            <span class="hidden sm:inline">
                @lang('pages.migration.transactions.title')
            </span>

            <span class="sm:hidden">
                @lang('pages.migration.transactions.title_mobile')
            </span>
        </h2>

        <div class="bg-theme-primary-100 text-theme-secondary-700 dark:bg-theme-secondary-800 dark:text-theme-secondary-500 text-sm md:text-lg leading-tight rounded font-semibold px-1.5 py-0.5 md:px-2 md:py-1.5 transition-default">
            {{ $transactions->total() }}
        </div>
    </div>

    <div id="transaction-list" class="w-full">
        <x-skeletons.migration-transactions>
            <x-tables.desktop.migration-transactions :transactions="$transactions" />

            <x-tables.mobile.transactions :transactions="$transactions" />

            <x-general.pagination :results="$transactions" class="mt-8" />

            <x-script.onload-scroll-to-query selector="#transaction-list" />
        </x-skeletons.transactions>
    </div>
</div>
