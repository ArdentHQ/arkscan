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
            'component' => '<x-transaction.section-detail.delegate :delegate="$delegate" />',

            'data' => [
                'delegate' => $transaction->isVote() ? $transaction->voted() : $transaction->unvoted(),
            ],
        ];
    } elseif ($transaction->isMultisignature()) {
        $items[trans('pages.transaction.header.address')] = [
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
            'component' => '<x-transaction.section-detail.ipfs-link :hash="$hash" />',

            'data' => [
                'hash' => $transaction->ipfsHash(),
            ],
        ];
    } elseif ($transaction->isDelegateRegistration() || $transaction->isDelegateResignation()) {
        $items[trans('pages.transaction.header.delegate')] = $transaction->delegateUsername();
    } elseif ($transaction->isLegacy()) {
        $items[trans('pages.transaction.header.sub_category')] = $transaction->typeLabel();
    }
@endphp

<x-transaction.page-section
    :title="trans('pages.transaction.transaction_type')"
    :items="$items"
    :width="$width"
/>
