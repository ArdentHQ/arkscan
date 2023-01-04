<div>
    <div class="flex items-center mb-6 space-x-3 md:mb-3">
        <h2>
            <span class="hidden sm:inline">
                @lang('pages.migration.transactions.title')
            </span>

            <span class="sm:hidden">
                @lang('pages.migration.transactions.title_mobile')
            </span>
        </h2>

        <div class="py-0.5 px-1.5 text-sm font-semibold leading-tight rounded md:py-1.5 md:px-2 md:text-lg bg-theme-primary-100 text-theme-secondary-700 transition-default dark:bg-theme-secondary-800 dark:text-theme-secondary-500">
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
