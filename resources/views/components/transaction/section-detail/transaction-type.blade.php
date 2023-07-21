@props(['transaction'])

<x-transaction.page-section :title="trans('pages.transaction.transaction_type')">
    <x-transaction.section-detail.row
        :title="trans('pages.transaction.header.category')"
        :transaction="$transaction"
        valueClass="inline"
    >
        <x-general.encapsulated.transaction-type-badge
            :transaction="$transaction"
            class="inline text-theme-secondary-700 dark:text-theme-dark-200"
        />
    </x-transaction.section-detail.row>

    @if ($transaction->isVoteCombination())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.old_delegate')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.delegate :delegate="$transaction->unvoted()" />
        </x-transaction.section-detail.row>

        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.new_delegate')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.delegate :delegate="$transaction->voted()" />
        </x-transaction.section-detail.row>
    @elseif ($transaction->isVote() || $transaction->isUnvote())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.delegate')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.delegate :delegate="$transaction->isVote() ? $transaction->voted() : $transaction->unvoted()" />
        </x-transaction.section-detail.row>
    @elseif ($transaction->isMultisignature())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.address')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.address :address="$transaction->multiSignatureWallet()->address()" />
        </x-transaction.section-detail.row>

        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.signatures')"
            :value="trans('general.x_of_y', [
                $transaction->multiSignatureMinimum(),
                $transaction->multiSignatureParticipantCount(),
            ])"
            :transaction="$transaction"
        />
    @elseif ($transaction->isIpfs())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.hash')"
            :transaction="$transaction"
        >
            <x-transaction.section-detail.ipfs-link :hash="$transaction->ipfsHash()" />
        </x-transaction.section-detail.row>
    @elseif ($transaction->isDelegateRegistration() || $transaction->isDelegateResignation())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.delegate')"
            :value="$transaction->sender()->username()"
            :transaction="$transaction"
        />
    @elseif ($transaction->isLegacy())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.sub_category')"
            :value="trans('pages.transaction.types.'.$this->typeName())"
            :transaction="$transaction"
        />
    @endif
</x-transaction.page-section>
