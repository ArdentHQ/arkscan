<div
    x-show="isConnected && !isOnSameNetwork"
    data-tippy-content="@lang('general.arkconnect.wrong_network.'.Network::alias())"
>
    {{ $slot }}
</div>

<div
    x-show="!isConnected"
    data-tippy-content="@lang('general.arkconnect.connect_wallet_tooltip')"
>
    {{ $slot }}
</div>
