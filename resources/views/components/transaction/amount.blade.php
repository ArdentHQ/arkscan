@props([
    'transaction',
    'smallAmount' => 0.0001,
    'hideTooltip' => false,
    'address' => null,
])

<x-general.amount-small
    :amount="$transaction->amount($address)"
    :small-amount="$smallAmount"
    :hide-tooltip="$hideTooltip"
/>
