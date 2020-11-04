<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.address name="general.wallet.address" />
                <x-tables.headers.desktop.text name="general.wallet.info" />
                <x-tables.headers.desktop.number name="general.wallet.balance" />
                <x-tables.headers.desktop.number name="general.wallet.supply" responsive />
            </tr>
        </thead>
        <tbody>
            @foreach($wallets as $wallet)
                <tr>
                    <td wire:key="{{ $wallet->address() }}-address">
                        <x-tables.rows.desktop.address :model="$wallet" :without-truncate="$withoutTruncate ?? false" />
                    </td>
                    <td class="text-center" wire:key="{{ $wallet->address() }}-type">
                        <x-tables.rows.desktop.wallet-type :model="$wallet" />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.balance :model="$wallet" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        @isset($useVoteWeight)
                            <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                        @else
                            <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
