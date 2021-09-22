<div class="divide-y table-list-mobile">
    @foreach ($wallets as $wallet)
        <div class="table-list-mobile-row">
            <x-tables.rows.mobile.address :model="$wallet" />

            <x-tables.rows.mobile.wallet-type :model="$wallet" />

            <x-tables.rows.mobile.balance :model="$wallet" />

            @isset($useVoteWeight)
                <x-tables.rows.mobile.vote-percentage :model="$wallet" />
            @else
                <x-tables.rows.mobile.balance-percentage :model="$wallet" />
            @endif
        </div>
    @endforeach
</div>
