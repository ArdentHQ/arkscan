<div class="divide-y table-list-mobile table-list-encapsulated">
    @foreach ($wallets as $wallet)
        <div class="table-list-mobile-row">
            <x-tables.rows.mobile.encapsulated.rank :results="$wallets" :index="$loop->index + 1" />

            <x-tables.rows.mobile.encapsulated.address :model="$wallet" />

            @if ($wallet->username())
                <x-tables.rows.mobile.encapsulated.username :model="$wallet" />
            @endif

            <x-tables.rows.mobile.encapsulated.balance :model="$wallet" />

            @isset($useVoteWeight)
                <x-tables.rows.mobile.encapsulated.vote-percentage :model="$wallet" />
            @else
                <x-tables.rows.mobile.encapsulated.balance-percentage :model="$wallet" />
            @endif
        </div>
    @endforeach
</div>
