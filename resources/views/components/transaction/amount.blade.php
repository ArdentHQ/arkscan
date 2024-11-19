@props(['transaction'])

@if ($transaction->amount() < 0.01)
    <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 18) }}">
        &lt;0.01 {{ Network::currency() }}
    </span>
@else
    <span data-tippy-content="{{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 18) }}">
        {{ ExplorerNumberFormatter::currencyWithDecimals($transaction->amount(), Network::currency(), 2) }}
    </span>

    {{-- {{ ExplorerNumberFormatter::networkCurrency($transaction->amount(), withSuffix: true) }} --}}
@endif
