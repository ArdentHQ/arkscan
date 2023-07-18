@props([
    'transaction',
    'wallet' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('transaction-recipients-mobile') }}"
    class="sm:hidden px-3"
>
    @foreach ($transaction->payments(true) as $payment)
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.address :model="$payment" />

                <x-clipboard
                    :value="$payment->address()"
                    :tooltip="trans('pages.wallet.address_copied')"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.amount
                :model="$payment"
                :wallet="$wallet"
                without-fee
                with-network-currency
            />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
