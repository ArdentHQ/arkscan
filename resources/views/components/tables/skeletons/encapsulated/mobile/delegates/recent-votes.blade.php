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
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
