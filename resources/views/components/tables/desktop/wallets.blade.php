<x-ark-tables.table sticky class="hidden w-full md:block">
    <thead>
        <tr>
            <x-tables.headers.desktop.address name="general.wallet.address" />
            <x-tables.headers.desktop.icon name="general.wallet.info" class="text-center" />
            <x-tables.headers.desktop.number name="general.wallet.balance" />
            <x-tables.headers.desktop.number name="general.wallet.supply" />
        </tr>
    </thead>
    <tbody>
        @foreach($wallets as $wallet)
            <x-ark-tables.row  wire:key="wallet-{{ $wallet->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.address :model="$wallet" :without-truncate="$withoutTruncate ?? false"/>
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-center">
                    <x-tables.rows.desktop.wallet-type :model="$wallet" />
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.balance :model="$wallet" />
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right">
                    @isset($useVoteWeight)
                        <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                    @else
                        <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                    @endif
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
