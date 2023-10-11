<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile
            expandable
            expand-disabled
        >
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                    <x-tables.rows.mobile.skeletons.text />

                    <x-tables.rows.mobile.skeletons.text />
                </div>
            </x-slot>

            <div class="hidden sm:flex sm:justify-between sm:items-center sm:w-full">
                <x-tables.rows.mobile.skeletons.encapsulated.addressing />

                <x-tables.rows.mobile.skeletons.encapsulated.text />
            </div>
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
