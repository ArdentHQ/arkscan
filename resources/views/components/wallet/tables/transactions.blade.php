@props([
    'wallet',
    'transactions',
])

<div
    x-show="tab === 'transactions'"
    id="transactions-list"
    {{ $attributes->class('w-full') }}
>
    <div class="w-full">
        <x-tables.toolbars.wallet.transactions
            :transactions="$transactions"
            :wallet="$wallet"
        />

        <x-skeletons.wallet.transactions
            :row-count="$this->perPage"
            :paginator="$transactions"
        >
            <x-tables.desktop.wallet.transactions
                :transactions="$transactions"
                :wallet="$wallet"
                :no-results-message="$this->transactionsNoResultsMessage"
            />

            <x-tables.mobile.wallet.transactions
                :transactions="$transactions"
                :wallet="$wallet"
                :no-results-message="$this->transactionsNoResultsMessage"
            />
        </x-skeletons.transactions>

        <x-general.pagination.table :results="$transactions" />
    </div>

    <x-webhooks.reload-transactions :wallet="$wallet" />
</div>
