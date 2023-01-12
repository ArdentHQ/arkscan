@props([
    'transactions',
    'state' => [],
])

<x-ark-tables.table sticky class="hidden md:block" wire:key="{{ Helpers::generateId('transactions', ...$state) }}">
    <thead>
        <tr>
            <x-tables.headers.desktop.id name="general.transaction.id" />
            <x-tables.headers.desktop.text name="general.transaction.timestamp" responsive />
            <x-tables.headers.desktop.address name="general.transaction.sender" icon />
            <x-tables.headers.desktop.number name="general.transaction.amount" />
            <x-tables.headers.desktop.number name="general.transaction.fee" />
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-item', $transaction->id(), ...$state) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.transaction-id :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell responsive>
                    <x-tables.rows.desktop.timestamp :model="$transaction" shortened />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.sender :model="$transaction" dynamic-truncate />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.amount :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.fee :model="$transaction" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
