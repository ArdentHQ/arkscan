@props(['transaction'])

<x-transaction.page-section :title="trans('pages.transaction.transaction_summary')">
    @if ($transaction->isTransfer() || $transaction->isMultiPayment())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.amount')"
            :value="ExplorerNumberFormatter::currency($transaction->amount(), Network::currency())"
            :transaction="$transaction"
        />
    @endif

    <x-transaction.section-detail.row
        :title="trans('pages.transaction.header.fee')"
        :value="ExplorerNumberFormatter::currency($transaction->fee(), Network::currency())"
        :transaction="$transaction"
    />

    @if (Network::canBeExchanged())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.value')"
            :value="ExplorerNumberFormatter::currency($transaction->totalFiat(), Network::currency())"
            :transaction="$transaction"
        />
    @endif
</x-transaction.page-section>
