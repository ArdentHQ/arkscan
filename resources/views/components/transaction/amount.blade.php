@props([
    'amount',
    'smallAmount' => 0.0001,
    'hideTooltip' => false,
])

<x-general.amount-small
    :amount="$amount"
    :small-amount="$smallAmount"
    :hide-tooltip="$hideTooltip"
/>
