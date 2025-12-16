@props([
    'amount',
    'smallAmount' => 0.0001,
    'hideTooltip' => false,
    'withoutCurrency' => false,
])

@unless ($hideTooltip)
    @if ($amount === 0.00)
        <span>
            0.00
            @if ($withoutCurrency === false)
                {{ Network::currency() }}
            @endif
        </span>
    @elseif ($amount < $smallAmount)
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 18) }}">
            &lt;{{ $smallAmount }}
            @if ($withoutCurrency === false)
                {{ Network::currency() }}
            @endif
        </span>
    @else
        <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 18) }}">
            @if ($withoutCurrency)
                {{ ExplorerNumberFormatter::networkCurrency($amount) }}
            @else
                {{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 2) }}
            @endif
        </span>
    @endif
@else
    @if ($amount === 0.00)
        0.00
        @if ($withoutCurrency === false)
            {{ Network::currency() }}
        @endif
    @elseif ($amount < $smallAmount)
        &lt;{{ $smallAmount }}
        @if ($withoutCurrency === false)
            {{ Network::currency() }}
        @endif
    @else
        @if ($withoutCurrency)
            {{ ExplorerNumberFormatter::networkCurrency($amount) }}
        @else
            {{ ExplorerNumberFormatter::currencyWithDecimals($amount, Network::currency(), 2) }}
        @endif
    @endif
@endif
