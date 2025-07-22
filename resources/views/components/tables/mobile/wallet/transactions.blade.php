@props([
    'transactions',
    'wallet' => null,
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('transactions-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($transactions as $transaction)
        <x-tables.rows.mobile wire:key="{{ Helpers::generateId('transactions-mobile-row', $transaction->hash()) }}">
            <x-slot name="header">
                <x-tables.rows.mobile.encapsulated.transaction-id :model="$transaction" />

                <x-tables.rows.mobile.encapsulated.age
                    :model="$transaction"
                    class="leading-4.25"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.transaction
                :model="$transaction"
                :wallet="$wallet"
                class="sm:flex-1"
            />

            <x-tables.rows.mobile.encapsulated.amount
                :model="$transaction"
                :wallet="$wallet"
            />

            <div class="sm:flex sm:flex-1 sm:justify-end">
                <x-tables.rows.mobile.encapsulated.fee :model="$transaction" />
            </div>
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
