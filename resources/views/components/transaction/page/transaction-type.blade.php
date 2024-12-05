@props(['transaction'])

<x-general.page-section.container
    :title="trans('pages.transaction.action')"
    wrapper-class="flex flex-col flex-1 space-y-3 w-full whitespace-nowrap"
>
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
                <x-general.page-section.data.validator :validator="$votedValidator" />
            @endif
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isValidatorRegistration())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.validator')"
            :transaction="$transaction"
            value-class="min-w-0"
        >
            <x-truncate-dynamic>{{ $transaction->validatorPublicKey() }}</x-truncate-dynamic>
        </x-transaction.page.section-detail.row>
    @endif
</x-general.page-section.container>
