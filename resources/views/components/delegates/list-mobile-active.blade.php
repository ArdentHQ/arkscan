<div class="space-y-8 divide-y md:hidden">
    @foreach ($delegates as $delegate)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.rank :model="$delegate" />

            <x-tables.rows.mobile.username-with-avatar :model="$delegate" />

            <x-tables.rows.mobile.round-status-history :model="$delegate" />

            <x-tables.rows.mobile.votes :model="$delegate" />

            @if (Network::usesMarketSquare())
                <x-tables.rows.mobile.marketsquare-profile :model="$delegate" />

                <x-tables.rows.mobile.marketsquare-commission :model="$delegate" />
            @endif

            <x-tables.rows.mobile.productivity :model="$delegate" />
        </div>
    @endforeach
</div>
