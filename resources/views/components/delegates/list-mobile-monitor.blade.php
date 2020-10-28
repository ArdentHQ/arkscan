<div class="space-y-8 divide-y md:hidden">
    @foreach ($delegates as $delegate)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.slot-id :model="$delegate" />

            <x-tables.rows.mobile.username-with-avatar :model="$delegate" />

            <x-tables.rows.mobile.slot-time :model="$delegate" />

            <x-tables.rows.mobile.round-status :model="$delegate" />

            <x-tables.rows.mobile.wallet-last-block :model="$delegate" />
        </div>
    @endforeach
</div>
