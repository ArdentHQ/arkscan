@props([
    'transaction',
    'wallet' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('transaction-recipients') }}"
    class="hidden w-full sm:block rounded-b-xl"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.address name="tables.transactions.address" />
            <x-tables.headers.desktop.number name="tables.transactions.amount_no_currency" />
        </tr>
    </thead>
    <tbody>
        @foreach($transaction->payments(true) as $payment)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-recipients-item', $loop->index) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        class="!justify-start space-x-2"
                        :model="$payment"
                        without-truncate
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.amount
                        :model="$payment"
                        :wallet="$wallet"
                        without-fee
                        with-network-currency
                    />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
