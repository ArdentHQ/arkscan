<div class="network-status-block">
    <div class="network-status-block-entries space-x-5">
        <div>@lang('general.height'): {{ $height }}</div>
        <div class="hidden md:block">@lang('general.network'): {{ $network }}</div>
        <div class="hidden md:block">@lang('general.supply'): {{ $supply }}</div>
        <div class="hidden sm:block">@lang('general.market_cap'): {{ $marketCap }}</div>

        <div class="md:hidden">
            <livewire:price-ticker />
        </div>
    </div>
</div>
