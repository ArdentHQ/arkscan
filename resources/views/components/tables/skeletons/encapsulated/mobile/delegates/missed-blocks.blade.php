<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                <x-tables.rows.mobile.skeletons.text class="sm:flex-1" />

                <x-tables.rows.mobile.skeletons.text class="hidden sm:block sm:flex-1" />

                <x-tables.rows.mobile.skeletons.text class="sm:text-right leading-4.25 sm:min-w-[110px]" />
                </div>
            </x-slot>

            <div class="hidden sm:flex sm:justify-between sm:items-center sm:w-full">
                <x-tables.rows.mobile.skeletons.encapsulated.text class="sm:flex-1" />

                <div class="sm:flex-1">
                    <x-tables.rows.mobile.skeletons.encapsulated.text />
                </div>

                <div class="sm:flex sm:justify-end sm:min-w-[110px]">
                    <x-tables.rows.mobile.skeletons.encapsulated.text />
                </div>
            </div>
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
