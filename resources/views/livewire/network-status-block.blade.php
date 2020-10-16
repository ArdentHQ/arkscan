<div class="network-status-block">
    <div class="space-x-5 network-status-block-entries">
        <div>@lang('general.height'): {{ $height }}</div>
        <div class="hidden md:block">@lang('general.network'): {{ $network }}</div>
        <div class="hidden md:block">@lang('general.supply'): {{ $supply }}</div>
        <div class="hidden sm:block">@lang('general.market_cap'): {{ $marketCap }}</div>

        <div class="md:hidden">
            <livewire:price-ticker />
        </div>
    </div>
</div>
