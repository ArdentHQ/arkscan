@props(['block'])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('block-transactions') }}"
    class="hidden w-full rounded-b-xl md:block"
>
    <thead>
        <tr>
            <x-tables.headers.desktop.id
                name="tables.transactions.id"
                class="whitespace-nowrap md-lg:w-[130px] lg:w-[150px]"
            />
            <x-tables.headers.desktop.text
                name="tables.transactions.type"
                class="md-lg:w-[100px] lg:w-[150px]"
            />
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
        @foreach($block->transactions() as $transaction)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-item', $transaction->id()) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-id
                        :model="$transaction"
                        without-age
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-type :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.addressing-generic :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    last-on="md-lg"
                >
                    <x-tables.rows.desktop.encapsulated.amount :model="$transaction" />
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
