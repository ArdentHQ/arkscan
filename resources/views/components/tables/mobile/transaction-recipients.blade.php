@props(['recipients'])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('transaction-recipients-mobile') }}"
    class="px-3 sm:hidden"
>
    @foreach ($recipients as $index => $recipient)
        <x-tables.rows.mobile wire:key="{{ Helpers::generateId('recipients-mobile-row', $index) }}">
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.address :model="$recipient" />

                <x-clipboard
                    :value="$recipient->address()"
                    :tooltip="trans('pages.wallet.address_copied')"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.amount
                :model="$recipient"
                without-fee
                with-network-currency
            />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
