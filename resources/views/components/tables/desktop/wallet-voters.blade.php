@props([
    'wallets',
    'hideVoting' => false,
    'useVoteWeight' => false,
])

<x-tables.encapsulated-table
    class="hidden w-full md:block"
    :paginator="$wallets"
    sticky
>
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.address name="general.wallet.address" />
            <x-tables.headers.desktop.number name="general.wallet.balance" last-on="lg">
                <span>({{ Network::currency()}})</span>
            </x-tables.headers.desktop.number>
            <x-tables.headers.desktop.number name="general.wallet.percentage"
                class="text-right"
                breakpoint="md-lg"
                responsive
            >
                <x-ark-info :tooltip="trans('pages.wallets.supply_tooltip', ['symbol' => Network::currency()])" type="info" />
            </x-tables.headers.desktop.number>
        </tr>
    </thead>
    <tbody>
        @foreach($wallets as $wallet)
            <x-ark-tables.row  wire:key="wallet-{{ $wallet->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        :model="$wallet"
                        without-clipboard
                        without-username
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <div class="flex flex-col font-semibold text-theme-secondary-900 dark:text-theme-secondary-200">
                        <x-tables.rows.desktop.encapsulated.balance :model="$wallet" />
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right"
                    breakpoint="md-lg"
                    responsive
                >
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
