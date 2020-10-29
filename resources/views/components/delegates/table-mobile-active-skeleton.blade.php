<div class="space-y-8 divide-y table-list-mobile">
    <x-skeleton>
        <div class="space-y-3 table-list-mobile-row">
            <x-tables.rows.mobile.skeleton.rank />

            <x-tables.rows.mobile.skeleton.username-with-avatar />

            <x-tables.rows.mobile.skeleton.round-status-history />

            <x-tables.rows.mobile.skeleton.votes />

            @if (Network::usesMarketSquare())
                <x-tables.rows.mobile.skeleton.marketsquare-profile />

                <x-tables.rows.mobile.skeleton.marketsquare-commission />
            @endif

            <x-tables.rows.mobile.skeleton.productivity />
        </div>
    </x-skeleton>
</div>
