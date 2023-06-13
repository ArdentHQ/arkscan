@props([
    'transactions',
    'wallet' => null,
    'isSent' => null,
    'isReceived' => null,
    'state' => [],
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('transactions', ...$state) }}"
    class="hidden w-full md:block"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.id name="tables.transactions.id" />
            <x-tables.headers.desktop.text name="tables.transactions.age" responsive breakpoint="xl" />
            <x-tables.headers.desktop.text name="tables.transactions.type" />
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
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-item', $transaction->id(), ...$state) }}">
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
                        :is-received="(($wallet && $transaction->isReceived($wallet->address())) || $isReceived === true) && $isSent !== true"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    last-on="md-lg"
                >
                    <x-tables.rows.desktop.encapsulated.amount
                        :model="$transaction"
                        :is-received="(($wallet && $transaction->isReceived($wallet->address())) || $isReceived === true) && $isSent !== true"
                        :is-sent="(($wallet && $transaction->isSent($wallet->address())) || $isSent === true) && $isReceived !== true"
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
