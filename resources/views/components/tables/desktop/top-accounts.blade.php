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
            <x-tables.headers.desktop.text
                name="general.wallet.rank"
                class="text-left"
            />

            <x-tables.headers.desktop.address name="general.wallet.address" />

            <x-tables.headers.desktop.text name="general.wallet.name" />

            <x-tables.headers.desktop.icon
                name="general.wallet.type"
                class="text-center"
                breakpoint="md-lg"
                responsive
            />

            @unless($hideVoting)
                <x-tables.headers.desktop.icon
                    name="general.wallet.voting"
                    class="text-center"
                    breakpoint="lg"
                    responsive
                />
            @endunless

            <x-tables.headers.desktop.number
                name="general.wallet.balance_currency"
                :name-properties="['currency' => Network::currency()]"
                last-on="lg"
                class="last-until-lg"
            />

            <x-tables.headers.desktop.number name="general.wallet.percentage"
                class="text-right"
                breakpoint="md-lg"
                responsive
                :tooltip="trans('pages.wallets.supply_tooltip', ['symbol' => Network::currency()])"
            />
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

                <x-ark-tables.cell
                    class="text-center"
                    breakpoint="md-lg"
                    responsive
                >
                    <x-tables.rows.desktop.wallet-type :model="$wallet" />
                </x-ark-tables.cell>

                @unless($hideVoting)
                    <x-ark-tables.cell
                        class="text-center"
                        breakpoint="lg"
                        responsive
                    >
                        <x-tables.rows.desktop.encapsulated.voting :model="$wallet" />
                    </x-ark-tables.cell>
                @endunless

                <x-ark-tables.cell
                    class="text-right"
                    last-on="lg"
                >
                    <div class="flex flex-col font-semibold text-theme-secondary-900 dark:text-theme-secondary-200">
                        <x-tables.rows.desktop.encapsulated.balance :model="$wallet" />

                        <span class="mt-1 text-xs font-semibold text-theme-secondary-500 md-lg:hidden">
                            @if($useVoteWeight)
                                <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                            @else
                                <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                            @endif
                        </span>
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
