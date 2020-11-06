@if(isset($isSent))
    <span class="px-2 py-1 font-bold whitespace-no-wrap border rounded text-theme-danger-400 border-theme-danger-100 dark:border-theme-danger-400">
        -
@elseif(isset($isReceived))
    <span class="px-2 py-1 font-bold whitespace-no-wrap border rounded text-theme-success-600 border-theme-success-200 dark:border-theme-success-600">
        +
@else
    <span>
@endif
    @if(Network::canBeExchanged())
        <div class="inline" @if ($amount ?? false) data-tippy-content="{{ $fiat }}" @endif>
            <x-currency>{{ $amount }}</x-currency>
        </div>
    @else
        <div class="inline">
            <x-currency>{{ $amount }}</x-currency>
        </div>
    @endif
</span>
