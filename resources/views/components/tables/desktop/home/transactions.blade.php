@props([
    'transactions',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('transactions') }}"
    class="hidden w-full md:block"
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
                last-on="lg"
                class="last-until-lg"
            />
            <x-tables.headers.desktop.number
                name="tables.transactions.fee"
                :name-properties="['currency' => Network::currency()]"
                responsive
                breakpoint="lg"
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
                    <x-tables.rows.desktop.encapsulated.addressing-generic :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    last-on="lg"
                >
                    <x-tables.rows.desktop.encapsulated.amount
                        :model="$transaction"
                        breakpoint="lg"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="lg"
                >
                    <x-tables.rows.desktop.encapsulated.fee :model="$transaction" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
