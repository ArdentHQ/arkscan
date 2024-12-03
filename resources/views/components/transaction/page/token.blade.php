@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_summary')">
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.to')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.address
            :address="\ArkEcosystem\Crypto\Utils\Address::toChecksumAddress(substr($transaction->methodArguments()[0], 22))"
            class="inline-block"
        />
    </x-transaction.page.section-detail.row>

    {{ dd($transaction->methodArguments()) }}
    {{-- <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.amount')"
        :transaction="$transaction"
    >
        <x-transaction.amount :transaction="$transaction" />
    </x-transaction.page.section-detail.row> --}}

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
