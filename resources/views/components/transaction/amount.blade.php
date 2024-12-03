@props([
    'transaction',
    'smallAmount' => 0.0001,
    'hideTooltip' => false,
])

<x-general.amount-small
    :amount="$transaction->amount()"
    :small-amount="$smallAmount"
    :hide-tooltip="$hideTooltip"
/>
