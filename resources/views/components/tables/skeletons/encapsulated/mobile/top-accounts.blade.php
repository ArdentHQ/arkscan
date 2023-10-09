<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.rows.mobile.skeletons.encapsulated.number />

                <x-tables.rows.mobile.skeletons.encapsulated.address />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            {{-- A third even though wallets don't always have a name --}}
            <x-tables.rows.mobile.skeletons.encapsulated.text />
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
