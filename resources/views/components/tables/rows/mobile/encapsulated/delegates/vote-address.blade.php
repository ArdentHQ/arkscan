@props([
    'transaction',
    'valueClass' => null,
])

<x-tables.rows.mobile.encapsulated.cell :attributes="$attributes">
    <x-slot name="label">
        <div class="text-sm font-semibold leading-4.25 dark:text-theme-secondary-500">
            <x-general.encapsulated.transaction-type :transaction="$transaction" />
        </div>
    </x-slot>

    @php
        $delegate = $transaction->voted();
        if ($transaction->isUnvote()) {
            $delegate = $transaction->unvoted();
        }
    @endphp

    <x-general.identity
        :model="$delegate"
        :class="$valueClass"
    />
</x-tables.rows.mobile.encapsulated.cell>
