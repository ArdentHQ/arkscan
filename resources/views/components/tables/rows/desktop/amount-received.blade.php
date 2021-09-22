@props([
    'model',
    'wallet' => null,
])

<x-general.amount-fiat-tooltip
    :amount="$model->amountReceived($wallet?->address())"
    :fiat="$model->amountReceivedFiat($wallet?->address())"
    is-received
/>
