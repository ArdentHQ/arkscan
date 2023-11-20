@props([
    'wallets',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    class="hidden w-full md:block"
    :rounded="false"
    :paginator="$wallets"
    :no-results-message="$noResultsMessage"
    sticky
>
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.address name="general.wallet.address" />
            <x-tables.headers.desktop.number
                name="tables.wallets.balance_currency"
                :name-properties="['currency' => Network::currency()]"
            />
            <x-tables.headers.desktop.number
                name="general.wallet.percentage"
                class="text-right"
                :tooltip="trans('pages.wallets.percentage_tooltip')"
            />
        </tr>
    </thead>
    <tbody>
        @foreach($wallets as $wallet)
            <x-ark-tables.row  wire:key="wallet-{{ $wallet->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        :model="$wallet"
                        without-clipboard
                        without-truncate
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <div class="flex flex-col font-semibold text-theme-secondary-900 dark:text-theme-dark-200">
                        <x-tables.rows.desktop.encapsulated.balance :model="$wallet" />
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <span class="font-semibold">
                        <x-tables.rows.desktop.encapsulated.vote-percentage :model="$wallet" />
                    </span>
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
