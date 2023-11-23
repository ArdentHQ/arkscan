<div class="w-full">
    <x-tables.toolbars.transactions
        :transactions="$transactions"
        :wallet="$wallet"
    />

    <x-skeletons.wallet-transactions
        :row-count="$this->perPage"
        :paginator="$transactions"
    >
        <x-tables.desktop.wallet-transactions
            :transactions="$transactions"
            :wallet="$wallet"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.wallet-transactions
            :transactions="$transactions"
            :wallet="$wallet"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.transactions>

    <x-general.pagination.table :results="$transactions" />
</div>
