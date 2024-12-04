@props(['transaction'])

@php ($methodArguments = $transaction->methodArguments())

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

    <x-transaction.page.section-detail.row :title="trans('pages.transaction.header.amount')">
        <x-payload.number :argument="$methodArguments[TokenTransferArgument::AMOUNT]" />
    </x-transaction.page.section-detail.row>
</x-general.page-section.container>
