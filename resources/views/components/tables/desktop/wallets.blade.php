@props([
    'wallets',
    'hideVoting' => false,
    'useVoteWeight' => false,
])

<x-tables.encapsulated-table sticky class="hidden w-full md:block">
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.number name="general.wallet.rank" />
            <x-tables.headers.desktop.address name="general.wallet.address" />
            <x-tables.headers.desktop.text name="general.wallet.name" />
            <x-tables.headers.desktop.icon name="general.wallet.type" class="text-center" />
            @unless($hideVoting)
                <x-tables.headers.desktop.icon name="general.wallet.voting" class="text-center" responsive breakpoint="lg" />
            @endunless
            <x-tables.headers.desktop.number name="general.wallet.balance" last-on="lg">
                <span>({{ Network::currency()}})</span>
            </x-tables.headers.desktop.number>
            <x-tables.headers.desktop.number name="general.wallet.percentage" class="text-right" responsive breakpoint="md-lg">
                <x-ark-info :tooltip="trans('pages.wallets.supply_tooltip', ['symbol' => Network::currency()])" type="info" />
            </x-tables.headers.desktop.number>
        </tr>
    </thead>
    <tbody>
        @foreach($wallets as $wallet)
            <x-ark-tables.row  wire:key="wallet-{{ $wallet->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.rank :results="$wallets" :index="$loop->index + 1" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address :model="$wallet" without-username />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.username :model="$wallet" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-center">
                    <x-tables.rows.desktop.wallet-type :model="$wallet" />
                </x-ark-tables.cell>

                @unless($hideVoting)
                    <x-ark-tables.cell class="text-center" responsive breakpoint="lg" >
                        <x-tables.rows.desktop.encapsulated.voting :model="$wallet" />
                    </x-ark-tables.cell>
                @endunless

                <x-ark-tables.cell class="text-right">
                    <div class="flex flex-col font-semibold text-theme-secondary-900 dark:text-theme-secondary-200">
                        <x-tables.rows.desktop.encapsulated.balance :model="$wallet" />

                        <span class="mt-1 text-xs font-semibold lg:hidden text-theme-secondary-500">
                            @if($useVoteWeight)
                                <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                            @else
                                <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                            @endif
                        </span>
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right" responsive breakpoint="md-lg">
                    <span class="font-semibold">
                        @if($useVoteWeight)
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
