@if(isset($isSent))
    <span class="py-1 px-2 font-bold whitespace-nowrap rounded border fiat-tooltip-sent text-theme-danger-400 border-theme-danger-100 dark:border-theme-danger-400">
        -
@elseif(isset($isReceived))
    <span class="py-1 px-2 font-bold whitespace-nowrap rounded border fiat-tooltip-received text-theme-success-600 border-theme-success-200 dark:border-theme-success-600">
        +
@else
    <span>
@endif
    @if(Network::canBeExchanged())
        <span @if ($amount ?? false) data-tippy-content="{{ $fiat }}" @endif>
            <x-currency :currency="Network::currency()">{{ $amount }}</x-currency>
        </span>
    @else
        <span>
            <x-currency :currency="Network::currency()">{{ $amount }}</x-currency>
        </span>
    @endif
</span>
