@props(['transaction'])

<x-general.page-section.container :title="trans('pages.transaction.transaction_type')">
    <x-transaction.page.section-detail.row
        :title="trans('pages.transaction.header.category')"
        :transaction="$transaction"
        valueClass="inline"
    >
        <x-general.encapsulated.transaction-type-badge
            :transaction="$transaction"
            class="inline text-theme-secondary-700 dark:text-theme-dark-200"
        />
    </x-transaction.page.section-detail.row>

    @if ($transaction->isVoteCombination())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.old_delegate')"
            :transaction="$transaction"
        >
            <x-general.page-section.data.delegate :delegate="$transaction->unvoted()" />
        </x-transaction.page.section-detail.row>

        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.new_delegate')"
            :transaction="$transaction"
        >
            <x-general.page-section.data.delegate :delegate="$transaction->voted()" />
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isVote() || $transaction->isUnvote())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.delegate')"
            :transaction="$transaction"
        >
            <x-general.page-section.data.delegate :delegate="$transaction->isVote() ? $transaction->voted() : $transaction->unvoted()" />
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isMultisignature())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.address')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.address :address="$transaction->multiSignatureWallet()->address()" />
        </x-transaction.page.section-detail.row>

        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.signatures')"
            :value="trans('general.x_of_y', [
                $transaction->multiSignatureMinimum(),
                $transaction->multiSignatureParticipantCount(),
            ])"
            :transaction="$transaction"
        />
    @elseif ($transaction->isIpfs())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.hash')"
            :transaction="$transaction"
        >
            <x-transaction.page.section-detail.ipfs-link :hash="$transaction->ipfsHash()" />
        </x-transaction.page.section-detail.row>
    @elseif ($transaction->isDelegateRegistration() || $transaction->isDelegateResignation())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.delegate')"
            :value="$transaction->sender()->username()"
            :transaction="$transaction"
        />
    @elseif ($transaction->isBlsRegistration())
        @php ($oldBlsPublicKey = $transaction->oldBlsPublicKey())
        @php ($newBlsPublicKey = $transaction->blsPublicKey())

        @if ($oldBlsPublicKey)
            <x-transaction.page.section-detail.row
                :title="trans('pages.transaction.header.old-bls-public-key')"
                :transaction="$transaction"
            >
                <x-transaction.page.section-detail.bls-public-key :public-key="$oldBlsPublicKey" />
            </x-transaction.page.section-detail.row>

            <x-transaction.page.section-detail.row
                :title="trans('pages.transaction.header.new-bls-public-key')"
                :transaction="$transaction"
            >
                <x-transaction.page.section-detail.bls-public-key :public-key="$newBlsPublicKey" />
            </x-transaction.page.section-detail.row>
        @else
            <x-transaction.page.section-detail.row
                :title="trans('pages.transaction.header.bls-public-key')"
                :transaction="$transaction"
            >
                <x-transaction.page.section-detail.bls-public-key :public-key="$newBlsPublicKey" />
            </x-transaction.page.section-detail.row>
        @endif
    @elseif ($transaction->isLegacy())
        <x-transaction.page.section-detail.row
            :title="trans('pages.transaction.header.sub_category')"
            :value="trans('pages.transaction.types.'.$transaction->typeName())"
            :transaction="$transaction"
        />
    @endif
</x-general.page-section.container>
