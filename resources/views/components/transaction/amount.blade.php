@props([
    'transaction',
    'smallAmount' => 0.01,
    'hideTooltip' => false,
])

@unless ($hideTooltip)
    @if ($transaction->amount() < $smallAmount)
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 18) }}">
            &lt;{{ $smallAmount }} {{ Network::currency() }}
        </span>
    @else
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 18) }}">
            {{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 2) }}
        </span>
    @endif
@else
    @if ($transaction->amount() < $smallAmount)
        &lt;{{ $smallAmount }} {{ Network::currency() }}
    @else
        {{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 2) }}
    @endif
@endif
