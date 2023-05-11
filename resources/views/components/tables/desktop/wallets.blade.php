<x-tables.encapsulated-table sticky class="hidden w-full md:block">
    <thead class="bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.number name="general.wallet.rank" />
            <x-tables.headers.desktop.address name="general.wallet.address" />
            <x-tables.headers.desktop.text name="general.wallet.name" />
            <x-tables.headers.desktop.icon name="general.wallet.type" class="text-center" />
            <x-tables.headers.desktop.icon name="general.wallet.voting" class="text-center" responsive breakpoint="lg" />
            <x-tables.headers.desktop.number name="general.wallet.balance" last-on="lg" />
            <x-tables.headers.desktop.number name="general.wallet.percentage" class="text-right" responsive breakpoint="lg" />
        </tr>
    </thead>
    <tbody>
        @foreach($wallets as $wallet)
            <x-ark-tables.row  wire:key="wallet-{{ $wallet->address() }}">
                <x-ark-tables.cell>
                    <span>1</span>
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <span class="flex justify-between w-full">
                        <span class="hidden lg:inline">
                            <x-tables.rows.desktop.address :model="$wallet" :without-truncate="$withoutTruncate ?? false" without-username />
                        </span>
                        <span class="lg:hidden"> {{-- TODO: truncate earlier at xl already --}}
                            <x-tables.rows.desktop.address :model="$wallet" without-username />
                        </span>
                        <x-ark-clipboard :value="$wallet->address()" class="text-theme-primary-400 hover:text-theme-primary-700 transition mr-3" no-styling />
                    </span>
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    @if ($wallet->username())
                        <span class="font-semibold">{{ $wallet->username() }}</span>
                    @endif
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-center">
                    <x-tables.rows.desktop.wallet-type :model="$wallet" />
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-center" responsive breakpoint="lg" >
                    @if($wallet->isVoting())
                        <div class="flex justify-center items-center w-full">
                            <x-ark-icon name="check-mark-box" size="sm" />
                        </div>
                    @endif
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right">
                    <div class="flex flex-col font-semibold">
                        <span><x-tables.rows.desktop.balance :model="$wallet" /></span>
                        <span class="font-semibold lg:hidden text-theme-secondary-500 text-xs mt-1">
                            @isset($useVoteWeight)
                                <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                            @else
                                <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                            @endif
                        </span>
                    </div>
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right" responsive breakpoint="lg">
                    <span class="font-semibold">
                        @isset($useVoteWeight)
                            <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                        @else
                            <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                        @endif
                    </span>
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
