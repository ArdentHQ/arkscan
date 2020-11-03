<div class="network-status-block">
    <div class="space-x-5 network-status-block-entries" wire:poll.{{ Network::blockTime() }}s>
        <div>@lang('general.height'): <x-number>{{ $height }}</x-number></div>
        <div class="hidden md:block">@lang('general.network'): {{ $network }}</div>
        <div class="hidden md:block">@lang('general.supply'): <x-currency>{{ $supply }}</x-currency></div>
        @if(Network::canBeExchanged())
            <div class="hidden sm:block">@lang('general.market_cap'): <x-currency>{{ $marketCap }}</x-currency></div>

            <div class="md:hidden">
                <livewire:price-ticker />
            </div>
        @endif
    </div>
</div>
