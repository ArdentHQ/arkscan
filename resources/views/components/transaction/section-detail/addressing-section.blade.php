@props(['transaction'])

<x-transaction.page-section :title="trans('pages.transaction.addressing')">
    <x-transaction.section-detail.row
        :title="trans('pages.transaction.header.from')"
        :transaction="$transaction"
    >
        <x-transaction.section-detail.address
            :address="$transaction->sender()->address()"
            class="inline-block"
        />
    </x-transaction.section-detail.row>

    @if ($transaction->isTransfer())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.to')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.address
                :address="$transaction->recipient()->address()"
                class="inline-block"
            />
        </x-transaction.section-detail.row>
    @elseif ($transaction->isMultiPayment())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.to')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.recipients
                :transaction="$transaction"
                class="inline-block"
            />
        </x-transaction.section-detail.row>
    @endif
</x-transaction.page-section>
