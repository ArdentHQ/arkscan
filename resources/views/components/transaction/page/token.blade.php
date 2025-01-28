@props(['transaction'])

@php ($methodArguments = $transaction->methodArguments())

@if (count($methodArguments) > 0)
    <x-general.page-section.container :title="trans('pages.transaction.tokens_transferred')">
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.to')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.address class="inline-block">
                <x-slot name="address">
                    <x-payload.address :argument="$methodArguments[TokenTransferArgument::RECIPIENT]" />
                </x-slot>
            </x-transaction.page.section-detail.address>
        </x-transaction.page.section-detail.row>

        @if (count($methodArguments) > 1)
            <x-transaction.page.section-detail.row
                :title="trans('pages.transaction.header.amount')"
                :transaction="$transaction"
            >
                <x-payload.amount
                    :argument="$methodArguments[TokenTransferArgument::AMOUNT]"
                    :suffix="$transaction->tokenName()"
                />
            </x-transaction.page.section-detail.row>
        @endif
    </x-general.page-section.container>
@endif
