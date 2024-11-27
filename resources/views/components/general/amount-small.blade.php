@props([
    'amount',
    'smallAmount' => 0.0001,
    'hideTooltip' => false,
])

@unless ($hideTooltip)
    @if ($amount === 0.00)
        <span>
            0 {{ Network::currency() }}
        </span>
    @elseif ($amount < $smallAmount)
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 18) }}">
            &lt;{{ $smallAmount }} {{ Network::currency() }}
        </span>
    @else
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 18) }}">
            {{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 2) }}
        </span>
    @endif
@else
    @if ($amount === 0.00)
        0 {{ Network::currency() }}
    @elseif ($amount < $smallAmount)
        &lt;{{ $smallAmount }} {{ Network::currency() }}
    @else
        {{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 2) }}
    @endif
@endif
