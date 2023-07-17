@props([
    'transaction',
    'width' => null,
])

@php
    $items = [
        trans('pages.transaction.header.from') => [
            'component' => '<x-transaction.section-detail.address
                :address="$address"
                class="inline-block"
            />',

            'data' => [
                'address' => $transaction->sender()->address(),
            ],
        ],
    ];

    if ($transaction->isTransfer()) {
        $items[trans('pages.transaction.header.to')] = [
            'component' => '<x-transaction.section-detail.address
                :address="$address"
                class="inline-block"
            />',

            'data' => [
                'address' => $transaction->recipient()->address(),
            ],
        ];
    } elseif ($transaction->isMultiPayment()) {
        $items[trans('pages.transaction.header.to')] = [
            'component' => '<x-transaction.section-detail.recipients
                :transaction="$transaction"
                class="inline-block"
            />',

            'data' => [
                'transaction' => $transaction,
            ],
        ];
    }
@endphp

<x-transaction.page-section
    :title="trans('pages.transaction.addressing')"
    :items="$items"
    :width="$width"
/>
