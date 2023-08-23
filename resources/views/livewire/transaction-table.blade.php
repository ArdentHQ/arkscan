<div
    class="w-full"
    wire:init="setIsReady"
>
    <x-tables.toolbars.transactions-generic :transactions="$transactions" />

    <x-skeletons.transactions :row-count="$this->perPage">
        <x-tables.desktop.transactions
            :transactions="$transactions"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.transactions
            :transactions="$transactions"
            :no-results-message="$this->noResultsMessage"
        />

        <x-general.pagination.table
            :results="$transactions"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.transactions>
</div>
