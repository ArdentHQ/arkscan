@props([
    'wallets',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('voters-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($wallets as $wallet)
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.address :model="$wallet" />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.balance :model="$wallet" class="sm:flex-1" />

            <x-tables.rows.mobile.encapsulated.vote-percentage :model="$wallet" />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
