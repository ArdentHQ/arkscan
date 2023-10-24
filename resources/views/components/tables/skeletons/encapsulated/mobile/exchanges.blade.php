<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <div class="flex items-center space-x-2">
                    <x-tables.rows.mobile.skeletons.circle size="h-8 w-8" />

                    <x-tables.rows.mobile.skeletons.text />
                </div>

                <x-loading.text
                    width="w-4"
                    height="h-4"
                />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            <x-tables.rows.mobile.skeletons.encapsulated.text />
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
