@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_summary')">
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.to')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.address
            :address="\App\Services\ContractPayload::decodeAddress($transaction->methodArguments()[0])"
            class="inline-block"
        />
    </x-transaction.page.section-detail.row>

    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.amount')"
        :value="ExplorerNumberFormatter::weiToArk(\App\Services\ContractPayload::decodeUnsignedInt($transaction->methodArguments()[1]))"
    >
        <x-transaction.amount :transaction="$transaction" />
    </x-transaction.page.section-detail.row>

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
