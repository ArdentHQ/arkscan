@if(Network::canBeExchanged())
    <div class="inline" @if ($amount ?? false) data-tippy-content="{{ $fiat }}" @endif>
        {{ $amount }}
    </div>
@else
    <div class="inline">
        {{ $amount }}
    </div>
@endif
