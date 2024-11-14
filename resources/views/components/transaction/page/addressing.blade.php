@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.addressing')">
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.from')"
        :transaction="$transaction"
    >
        <x-transaction.page.section-detail.address
            :address="$transaction->sender()->address()"
            class="inline-block"
        />
    </x-transaction.page.section-detail.row>

    @if ($transaction->isTransfer())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.to')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.address
                :address="$transaction->recipient()->address()"
                class="inline-block"
            />
        </x-transaction.page.section-detail.row>
    @endif
</x-general.page-section.container>
