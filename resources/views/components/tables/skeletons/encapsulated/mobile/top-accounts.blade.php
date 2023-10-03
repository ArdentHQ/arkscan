<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.rows.mobile.skeletons.text />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            {{-- A third even though wallets don't always have a name --}}
            <x-tables.rows.mobile.skeletons.encapsulated.text />
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
