<x-tables.mobile.includes.encapsulated class="px-3 sm:hidden">
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.rows.mobile.skeletons.text />

                <x-loading.text
                    width="w-4"
                    height="h-4"
                />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text />
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
