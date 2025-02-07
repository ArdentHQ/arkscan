@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_summary')">
    @if ($transaction->isTransfer() || $transaction->isTokenTransfer() || $transaction->isMultiPayment())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.amount')"
            :transaction="$transaction"
        >
            <x-transaction.amount :transaction="$transaction" />
        </x-transaction.page.section-detail.row>
    @endif

    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.fee')"
        :value="ExplorerNumberFormatter::networkCurrency($transaction->fee(), withSuffix: true)"
        :transaction="$transaction"
    />

    @if (Network::canBeExchanged())
        @if (ExplorerNumberFormatter::isFiat(Settings::currency()) && ExchangeRate::convertNumerical($transaction->amountWithFee(), $transaction->model()->timestamp) < 0.01)
            <x-transaction.page.section-detail.row
                :title="trans('pages.transaction.header.value')"
                :value="'<'.ExplorerNumberFormatter::currency(0.01, Settings::currency())"
                :transaction="$transaction"
                :tooltip="$transaction->totalFiat(true)"
            />
        @else
            <x-transaction.page.section-detail.row
                :title="trans('pages.transaction.header.value')"
                :transaction="$transaction"
            >
                <livewire:fiat-value
                    :amount="$transaction->amountWithFee()"
                    :timestamp="$transaction->model()->timestamp"
                />
            </x-transaction.page.section-detail.row>
        @endif
    @endif
</x-general.page-section.container>
