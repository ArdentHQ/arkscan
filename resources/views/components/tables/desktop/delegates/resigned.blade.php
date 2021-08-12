<x-ark-tables.table sticky class="w-full">
    <thead wire:ignore>
        <tr>
            <x-tables.headers.desktop.id name="general.delegates.id" />
            <x-tables.headers.desktop.address name="general.delegates.name"/>
            <x-tables.headers.desktop.number name="general.delegates.votes"/>
        </tr>
    </thead>
    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row wire:key="{{ Helpers::generateId($delegate->username(), $delegate->resignationId()) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.resignation-id :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <span class="hidden md:inline">
                        <x-tables.rows.desktop.username :model="$delegate" />
                    </span>
                    <span class="md:hidden">
                        <x-tables.rows.mobile.username-with-avatar :model="$delegate" />
                    </span>
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right">
                    <span class="hidden sm:inline">
                        <x-tables.rows.desktop.votes :model="$delegate" />
                    </span>
                    <span class="sm:hidden">
                        <x-tables.rows.mobile.votes :model="$delegate" />
                    </span>
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
