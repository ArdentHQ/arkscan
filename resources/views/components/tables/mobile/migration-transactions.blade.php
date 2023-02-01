@props([
    'transactions',
    'state' => [],
])

<div class="divide-y table-list-mobile" wire:key="{{ Helpers::generateId('transactions-mobile', ...$state) }}">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <x-tables.rows.mobile.transaction-id :model="$transaction" />

            <x-tables.rows.mobile.timestamp :model="$transaction" />

            <x-tables.rows.mobile.sender :model="$transaction" />

            <x-tables.rows.mobile.amount :model="$transaction" />

            <x-tables.rows.mobile.fee :model="$transaction" />
        </div>
    @endforeach
</div>
