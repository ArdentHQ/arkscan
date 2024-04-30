<div
    x-show="showWrongNetworkMessage"
    class="fixed flex w-full bottom-6 justify-center z-20 px-2 sm:px-15 md:px-9 md-lg:px-[130px] lg:px-14 xl:px-0"
    x-cloak
>
    <x-ark-toast
        class="mx-auto !max-w-7xl"
        type="warning"
        alpine-close="ignoreWrongNetworkAddress(address)"
        hide-spinner
    >
        @if (Network::alias() === 'mainnet')
            @lang('general.arkconnect.mainnet_network')
        @else
            @lang('general.arkconnect.testnet_network')
        @endif
    </x-ark-toast>
</div>
