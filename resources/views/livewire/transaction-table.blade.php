<div>
    @isset($showTitle)
        <div class="mb-8 w-full">
            <div class="flex relative flex-col justify-between md:items-end md:flex-row">
                <h2 class="mb-8 md:mb-0">@lang('pages.transactions.title')</h2>

                <div class="-my-3 -mr-8">
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

            <script>
                window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
            </script>
        </x-skeletons.transactions>
    </div>

</div>
