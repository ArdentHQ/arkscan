<div
    x-show="showWrongNetworkMessage"
    class="flex fixed bottom-6 z-20 justify-center px-2 w-full md:px-9 lg:px-14 xl:px-0 sm:px-15 md-lg:px-[130px]"
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
