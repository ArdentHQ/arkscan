@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.action')">
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.method')"
        :transaction="$transaction"
        valueClass="inline"
    >
        <x-general.encapsulated.transaction-type-badge
            :transaction="$transaction"
            class="inline text-theme-secondary-700 dark:text-theme-dark-200"
        />
    </x-transaction.page.section-detail.row>

    @if ($transaction->isVote())
        @php($votedValidator = $transaction->voted())

        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.validator')"
            :transaction="$transaction"
        >
            @if ($votedValidator)
                <x-general.page-section.data.validator :validator="$transaction->voted()" />
            @endif
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isValidatorRegistration() || $transaction->isValidatorResignation())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.validator')"
            :value="$transaction->sender()->address()"
            :transaction="$transaction"
        />
    @endif
</x-general.page-section.container>
