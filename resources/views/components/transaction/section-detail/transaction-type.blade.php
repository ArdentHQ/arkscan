@props([
    'transaction',
    'width' => null,
])

@php
    $items = [
        trans('pages.transaction.header.category') => [
            'component' => '<x-general.encapsulated.transaction-type-badge
                :transaction="$transaction"
                class="inline-block"
            />',

            'data' => [
                'transaction' => $transaction,
            ],
        ],
    ];

    if ($transaction->isVoteCombination()) {
        $items[trans('pages.transaction.header.old_delegate')] = [
            'class' => '!mt-2',
            'component' => '<x-transaction.section-detail.delegate :delegate="$delegate" />',

            'data' => [
                'delegate' => $transaction->unvoted(),
            ],
        ];

        $items[trans('pages.transaction.header.new_delegate')] = [
            'component' => '<x-transaction.section-detail.delegate :delegate="$delegate" />',

            'data' => [
                'delegate' => $transaction->voted(),
            ],
        ];
    } elseif ($transaction->isVote() || $transaction->isUnvote()) {
        $items[trans('pages.transaction.header.delegate')] = [
            'class' => '!mt-2',
            'component' => '<x-transaction.section-detail.delegate :delegate="$delegate" />',

            'data' => [
                'delegate' => $transaction->isVote() ? $transaction->voted() : $transaction->unvoted(),
            ],
        ];
    } elseif ($transaction->isMultisignature()) {
        $items[trans('pages.transaction.header.address')] = [
            'class' => '!mt-2',
            'component' => '<x-transaction.section-detail.address :address="$address" />',

            'data' => [
                'address' => $transaction->multiSignatureWallet()->address(),
            ],
        ];

        $items[trans('pages.transaction.header.signatures')] = trans('general.x_of_y', [
            $transaction->multiSignatureMinimum(),
            $transaction->multiSignatureParticipantCount(),
        ]);
    } elseif ($transaction->isIpfs()) {
        $items[trans('pages.transaction.header.hash')] = [
            'class' => '!mt-2',
            'component' => '<x-transaction.section-detail.ipfs-link :hash="$hash" />',

            'data' => [
                'hash' => $transaction->ipfsHash(),
            ],
        ];
    } elseif ($transaction->isDelegateRegistration() || $transaction->isDelegateResignation()) {
        $items[trans('pages.transaction.header.delegate')] = [
            'content' => $transaction->delegateUsername(),
            'class' => '!mt-2',
        ];
    } elseif ($transaction->isLegacy()) {
        $items[trans('pages.transaction.header.sub_category')] = [
            'content' => $transaction->typeLabel(),
            'class' => '!mt-2',
        ];
    }
@endphp

<x-transaction.page-section :title="trans('pages.transaction.transaction_type')">
    <x-transaction.section-detail.row
        :title="trans('pages.transaction.header.category')"
        :transaction="$transaction"
        valueClass="inline"
    >
        <x-general.encapsulated.transaction-type-badge
            :transaction="$transaction"
            class="inline"
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
            :value="$transaction->delegateUsername()"
            :transaction="$transaction"
        />
    @elseif ($transaction->isLegacy())
        <x-transaction.section-detail.row
            :title="trans('pages.transaction.header.sub_category')"
            :value="$transaction->typeLabel()"
            :transaction="$transaction"
        />
    @endif
</x-transaction.page-section>
