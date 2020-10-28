<div class="space-y-8 divide-y md:hidden">
    @foreach ($wallets as $wallet)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.address :model="$wallet" />

            <x-tables.rows.mobile.wallet-type :model="$wallet" />

            <x-tables.rows.mobile.balance :model="$wallet" />

            <x-tables.rows.mobile.balance-percentage :model="$wallet" />
        </div>
    @endforeach
</div>
