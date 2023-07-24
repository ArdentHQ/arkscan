@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_type')">
    <x-general.page-section.row
        :title="trans('pages.transaction.header.category')"
        :transaction="$transaction"
        valueClass="inline"
    >
        <x-general.encapsulated.transaction-type-badge
            :transaction="$transaction"
            class="inline text-theme-secondary-700 dark:text-theme-dark-200"
        />
    </x-general.page-section.row>

    @if ($transaction->isVoteCombination())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.old_delegate')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.delegate :delegate="$transaction->unvoted()" />
        </x-general.page-section.row>

        <x-general.page-section.row
            :title="trans('pages.transaction.header.new_delegate')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.delegate :delegate="$transaction->voted()" />
        </x-general.page-section.row>
    @elseif ($transaction->isVote() || $transaction->isUnvote())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.delegate')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.delegate :delegate="$transaction->isVote() ? $transaction->voted() : $transaction->unvoted()" />
        </x-general.page-section.row>
    @elseif ($transaction->isMultisignature())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.address')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.address :address="$transaction->multiSignatureWallet()->address()" />
        </x-general.page-section.row>

        <x-general.page-section.row
            :title="trans('pages.transaction.header.signatures')"
            :value="trans('general.x_of_y', [
                $transaction->multiSignatureMinimum(),
                $transaction->multiSignatureParticipantCount(),
            ])"
            :transaction="$transaction"
        />
    @elseif ($transaction->isIpfs())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.hash')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.ipfs-link :hash="$transaction->ipfsHash()" />
        </x-general.page-section.row>
    @elseif ($transaction->isDelegateRegistration() || $transaction->isDelegateResignation())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.delegate')"
            :value="$transaction->sender()->username()"
            :transaction="$transaction"
        />
    @elseif ($transaction->isLegacy())
        <x-general.page-section.row
            :title="trans('pages.transaction.header.sub_category')"
            :value="trans('pages.transaction.types.'.$transaction->typeName())"
            :transaction="$transaction"
        />
    @endif
</x-general.page-section.container>
