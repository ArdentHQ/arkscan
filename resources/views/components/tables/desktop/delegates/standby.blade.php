<x-ark-tables.table sticky class="w-full">
    <thead>
        <tr>
            <x-tables.headers.desktop.text name="general.delegates.rank" width="70" />
            <x-tables.headers.desktop.address name="general.delegates.name" />
            <x-tables.headers.desktop.number name="general.delegates.votes" />
            <x-tables.headers.desktop.text />
        </tr>
    </thead>
    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row wire:key="{{ Helpers::generateId($delegate->username(), $delegate->rank()) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.rank :model="$delegate" />
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
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.vote :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
