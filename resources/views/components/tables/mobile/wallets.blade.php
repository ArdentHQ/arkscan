<div class="divide-y table-list-mobile">
    @foreach ($wallets as $wallet)
        <div class="space-y-3 table-list-mobile-row">
            <x-tables.rows.mobile.address :model="$wallet" />

            <x-tables.rows.mobile.wallet-type :model="$wallet" />

            <x-tables.rows.mobile.balance :model="$wallet" />

            <x-tables.rows.mobile.vote-percentage :model="$wallet" />
        </div>
    @endforeach
</div>
