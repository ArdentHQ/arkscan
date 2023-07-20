@props(['transaction'])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('transaction-participants') }}"
    class="hidden w-full rounded-b-xl sm:block"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.address name="tables.transactions.address" />
        </tr>
    </thead>
    <tbody>
        @foreach($transaction->participants() as $participant)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-participants-item', $loop->index) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        class="!justify-start space-x-2"
                        :model="$participant"
                        without-truncate
                    />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
