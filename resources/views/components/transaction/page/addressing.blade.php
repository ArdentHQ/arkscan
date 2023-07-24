@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.addressing')">
    <x-general.page-section.row
        :title="trans('pages.transaction.header.from')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.address
            :address="$transaction->sender()->address()"
            class="inline-block"
        />
    </x-general.page-section.row>

    @if ($transaction->isTransfer())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.to')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.address
                :address="$transaction->recipient()->address()"
                class="inline-block"
            />
        </x-general.page-section.row>
    @elseif ($transaction->isMultiPayment())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.to')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.recipients
                :transaction="$transaction"
                class="inline-block"
            />
        </x-general.page-section.row>
    @endif
</x-general.page-section.container>
