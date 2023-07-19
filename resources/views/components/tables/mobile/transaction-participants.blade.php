@props(['transaction'])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('transaction-participants-mobile') }}"
    class="px-3 sm:hidden"
>
    @foreach ($transaction->participants() as $participant)
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.address :model="$participant" />

                <x-clipboard
                    :value="$participant->address()"
                    :tooltip="trans('pages.wallet.address_copied')"
                />
            </x-slot>
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
