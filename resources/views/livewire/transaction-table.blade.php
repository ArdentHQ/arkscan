<div>
    @isset($showTitle)
        <div class="w-full md:mb-5">
            <div class="flex relative flex-col justify-between md:flex-row md:items-end">
                <h1 class="mb-8 md:mb-0">@lang('pages.transactions.title')</h1>

                <div class="-my-3 mb-5 md:mb-0 md:-mr-8">
                    <x-transaction-table-filter />
                </div>
            </div>
        </div>
    @endisset

    <div id="transaction-list" class="w-full">
        <x-skeletons.transactions>
            <x-tables.desktop.transactions :transactions="$transactions" />

            <x-tables.mobile.transactions :transactions="$transactions" />

            <x-general.pagination :results="$transactions" class="mt-8" />

            <x-script.onload-scroll-to-query selector="#transaction-list" />
        </x-skeletons.transactions>
    </div>

</div>
