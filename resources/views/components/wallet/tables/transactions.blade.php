@props([
    'transactions',
    'wallet',
])

<div
    x-show="tab === 'transactions'"
    id="transaction-list"
    class="w-full"
>
    <x-tables.toolbars.transactions :transactions="$transactions" />

    <x-skeletons.wallet-transactions>
        <x-tables.desktop.wallet-transactions
            :transactions="$transactions"
            :wallet="$wallet"
            :state="$this->state()"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.wallet-transactions
            :transactions="$transactions"
            :wallet="$wallet"
            :state="$this->state()"
            :no-results-message="$this->noResultsMessage"
        />

        <x-general.pagination.table
            :results="$transactions"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.transactions>
</div>
