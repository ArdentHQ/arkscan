{{-- @TODO: do we really need +/- symbols and color coding? Seems overkill --}}

@if(isset($isSent))
    <span class="p-2 font-bold border rounded text-theme-danger-400 border-theme-danger-100 dark:text-theme-danger-600 dark:border-theme-danger-600">
@elseif(isset($isReceived))
    <span class="p-2 font-bold border rounded text-theme-success-400 border-theme-success-100 dark:text-theme-success-600 dark:border-theme-success-600">
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
