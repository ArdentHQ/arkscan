<div
    id="transactions-list"
    class="w-full"
    wire:init="setIsReady"
>
    <x-tables.toolbars.transactions-generic :transactions="$transactions" />

    <x-skeletons.transactions
        :row-count="$this->perPage"
        :paginator="$transactions"
    >
        <x-tables.desktop.transactions
            :transactions="$transactions"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.transactions
            :transactions="$transactions"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.transactions>

    <x-general.pagination.table :results="$transactions" />

    <x-script.onload-scroll-to-query selector="#transactions-list" />
</div>
