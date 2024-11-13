@props([
    'transactions',
    'wallet' => null,
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('transactions') }}"
    class="hidden w-full rounded-t-none md:block"
    :rounded="false"
    :paginator="$transactions"
    :no-results-message="$noResultsMessage"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.id
                name="tables.transactions.id"
                class="whitespace-nowrap"
            />
            <x-tables.headers.desktop.text
                name="tables.transactions.age"
                breakpoint="xl"
                responsive
            />
            <x-tables.headers.desktop.text name="tables.transactions.method" />
            <x-tables.headers.desktop.text name="tables.transactions.addressing" />
            <x-tables.headers.desktop.number
                name="tables.transactions.amount"
                :name-properties="['currency' => Network::currency()]"
                last-on="md-lg"
                class="last-until-md-lg"
            />
            <x-tables.headers.desktop.number
                name="tables.transactions.fee"
                :name-properties="['currency' => Network::currency()]"
                responsive
                breakpoint="md-lg"
            />
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-item', $transaction->id()) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-id :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell responsive breakpoint="xl">
                    <x-tables.rows.desktop.encapsulated.age :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-type :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.addressing
                        :model="$transaction"
                        :wallet="$wallet"
                        :without-link="$transaction->isSentToSelf($wallet->address())"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    last-on="md-lg"
                >
                    <x-tables.rows.desktop.encapsulated.amount
                        :model="$transaction"
                        :wallet="$wallet"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="md-lg"
                >
                    <x-tables.rows.desktop.encapsulated.fee :model="$transaction" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
