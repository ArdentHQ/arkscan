@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_summary')">
    @if ($transaction->isTransfer() || $transaction->isTokenTransfer() || $transaction->isMultiPayment())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.amount')"
            :transaction="$transaction"
        >
            <x-transaction.amount :amount="$transaction->amount()" />
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isValidatorRegistration() && $transaction->amount() > 0)
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.locked_amount')"
            :transaction="$transaction"
        >
            <div class="flex justify-end items-center space-x-2 sm:justify-start">
                <x-transaction.amount :amount="$transaction->amount()" />

                <x-tables.headers.desktop.includes.tooltip
                    :text="trans('pages.transaction.locked_amount_tooltip')"
                    type="question"
                />
            </div>
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isValidatorResignation())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.unlocked_amount')"
            :transaction="$transaction"
        >
            <div class="flex justify-end items-center space-x-2 sm:justify-start">
                @php ($registration = $transaction->validatorRegistration())

                <x-transaction.amount :amount="$registration->amount() ?? $transaction->amount()" />

                <x-tables.headers.desktop.includes.tooltip
                    :text="$registration && $registration->amount() > 0 ? trans('pages.transaction.unlocked_amount_tooltip') : trans('pages.transaction.legacy_registration_tooltip')"
                    type="question"
                />
            </div>
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
