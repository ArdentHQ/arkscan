@props(['transaction'])

<x-transaction.page-section :title="trans('pages.transaction.transaction_summary')">
    @if ($transaction->isTransfer() || $transaction->isMultiPayment())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.amount')"
            :value="ExplorerNumberFormatter::networkCurrency($transaction->amount(), withSuffix: true)"
            :transaction="$transaction"
        />
    @endif

    <x-transaction.section-detail.row
        :title="trans('pages.transaction.header.fee')"
        :value="ExplorerNumberFormatter::networkCurrency($transaction->fee(), withSuffix: true)"
        :transaction="$transaction"
    />

    @if (Network::canBeExchanged())
        @if (ExchangeRate::convertNumerical($transaction->amountWithFee(), $transaction->model()->timestamp) < 0.01)
            <x-transaction.section-detail.row
                :title="trans('pages.transaction.header.value')"
                :value="'<'.ExplorerNumberFormatter::currency(0.01, Settings::currency())"
                :transaction="$transaction"
                :tooltip="$transaction->totalFiat(true)"
            />
        @else
            <x-transaction.section-detail.row
                :title="trans('pages.transaction.header.value')"
                :value="$transaction->totalFiat()"
                :transaction="$transaction"
            />
        @endif
    @endif
</x-transaction.page-section>
