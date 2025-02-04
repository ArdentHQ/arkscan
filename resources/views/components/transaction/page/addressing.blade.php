@props(['transaction'])

<x-general.page-section.container
    :title="trans('pages.transaction.addressing')"
    wrapper-class="flex flex-col flex-1 space-y-3 w-full whitespace-nowrap"
>
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.from')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.address
            :wallet="$transaction->sender()"
            class="inline-block"
        />
    </x-transaction.page.section-detail.row>

    <x-transaction.page.section-detail.row
        :title="$transaction->recipient()->isContract() ? trans('pages.transaction.header.interacted_with') : trans('pages.transaction.header.to')"
        :transaction="$transaction"
        value-class="min-w-0"
    >
        <x-transaction.page.section-detail.address
            :wallet="$transaction->recipient()"
            :is-contract="$transaction->recipient()->isContract()"
            class="inline-block"
        />
    </x-transaction.page.section-detail.row>
</x-general.page-section.container>
