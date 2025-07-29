@props(['transactions'])

<x-tables.mobile.includes.encapsulated wire:key="{{ Helpers::generateId('block-transactions-mobile') }}">
    @foreach ($transactions as $transaction)
        <x-tables.rows.mobile wire:key="{{ Helpers::generateId('transactions-mobile-row', $transaction->hash()) }}">
            <x-slot name="header">
                <x-tables.rows.mobile.encapsulated.transaction-id
                    :model="$transaction"
                    without-age
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.transaction-generic
                :model="$transaction"
                class="sm:flex-1"
            />

            <div class="flex flex-col space-y-4 sm:flex-row sm:items-start sm:space-y-0 sm:w-1/2">
                <x-tables.rows.mobile.encapsulated.amount
                    :model="$transaction"
                    class="sm:flex-1"
                />

                <x-tables.rows.mobile.encapsulated.fee :model="$transaction" />
            </div>
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
