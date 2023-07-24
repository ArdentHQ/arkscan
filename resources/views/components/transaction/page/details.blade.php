@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_details')">
    <x-general.page-section.row
        :title="trans('pages.transaction.header.timestamp')"
        :value="$transaction->timestamp()"
        :transaction="$transaction"
    />

    <x-general.page-section.row
        :title="trans('pages.transaction.header.block')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.block-height-link
            :id="$transaction->blockId()"
            :height="$transaction->blockHeight()"
        />
    </x-general.page-section.row>

    <x-general.page-section.row
        :title="trans('pages.transaction.header.nonce')"
        :transaction="$transaction"
    >
        <x-number>{{ $transaction->nonce() }}</x-number>
    </x-general.page-section.row>
</x-general.page-section.container>
