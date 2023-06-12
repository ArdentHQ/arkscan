@props([
    'transactions',
    'wallet' => null,
    'useDirection' => false,
    'excludeItself' => false,
    'useConfirmations' => false,
    'isSent' => null,
    'isReceived' => null,
    'state' => [],
])

<x-tables.mobile.includes.encapsulated wire:key="{{ Helpers::generateId('transactions-mobile', ...$state) }}">
    @foreach ($transactions as $transaction)
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.rows.mobile.encapsulated.transaction-id :model="$transaction" />

                <x-tables.rows.mobile.encapsulated.age
                    :model="$transaction"
                    class="leading-[17px]"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.transaction :model="$transaction" />

            <x-tables.rows.mobile.encapsulated.amount
                :model="$transaction"
                :wallet="$wallet"
                :is-sent="$isSent"
                :is-received="$isReceived"
            />

            <x-tables.rows.mobile.encapsulated.fee :model="$transaction" />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
