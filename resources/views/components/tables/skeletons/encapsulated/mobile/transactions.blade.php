<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.rows.mobile.skeletons.text />

                <x-tables.rows.mobile.skeletons.text />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.addressing generic />

            <div class="flex flex-col space-y-4 sm:flex-row sm:items-start sm:space-y-0 sm:w-1/2">
                <x-tables.rows.mobile.skeletons.encapsulated.text />

                <x-tables.rows.mobile.skeletons.encapsulated.text />
            </div>
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
