@props([
    'transaction',
    'smallAmount' => 0.0001,
    'hideTooltip' => false,
])

@unless ($hideTooltip)
    @if ($transaction->amount() === 0.00)
        <span>
            0 {{ Network::currency() }}
        </span>
    @elseif ($transaction->amount() < $smallAmount)
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 18) }}">
            &lt;{{ $smallAmount }} {{ Network::currency() }}
        </span>
    @else
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 18) }}">
            {{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 2) }}
        </span>
    @endif
@else
    @if ($transaction->amount() === 0.00)
        0 {{ Network::currency() }}
    @elseif ($transaction->amount() < $smallAmount)
        &lt;{{ $smallAmount }} {{ Network::currency() }}
    @else
        {{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 2) }}
    @endif
@endif
