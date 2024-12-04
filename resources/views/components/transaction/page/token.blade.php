@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.tokens_transferred')">
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
</x-general.page-section.container>
