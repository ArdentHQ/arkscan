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
    }
@endphp

<x-transaction.page-section
    :title="trans('pages.transaction.transaction_type')"
    :items="$items"
    :width="$width"
/>
