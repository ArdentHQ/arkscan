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
        @if ($transactions->isEmpty())
            <x-general.no-results :text="trans('pages.home.no_transaction_results', [trans('forms.search.transaction_types.all')])" />
        @else
            <x-tables.desktop.wallet-transactions
                :transactions="$transactions"
                :wallet="$wallet"
                :state="$this->state()"
            />

            <x-tables.mobile.wallet-transactions
                :transactions="$transactions"
                :wallet="$wallet"
                :state="$this->state()"
            />

            <x-general.pagination.table
                :results="$transactions"
                class="mt-4 md:mt-0"
            />
        @endif

        <x-script.onload-scroll-to-query selector="#transaction-list" />
    </x-skeletons.transactions>
</div>
