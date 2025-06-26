@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_details')">
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.timestamp')"
        :value="$transaction->timestamp()"
        :transaction="$transaction"
    />

    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.block')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.block-height-link
            :id="$transaction->blockHash()"
            :height="$transaction->blockHeight()"
        />
    </x-transaction.page.section-detail.row>

    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.nonce')"
        :transaction="$transaction"
    >
        <x-number>{{ $transaction->nonce() }}</x-number>
    </x-transaction.page.section-detail.row>
</x-general.page-section.container>
