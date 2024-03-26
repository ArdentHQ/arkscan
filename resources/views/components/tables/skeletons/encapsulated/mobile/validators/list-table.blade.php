<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile
            expandable
            expand-disabled
        >
            <x-slot name="header">
                <div class="flex flex-1 items-center min-w-0 h-5 divide-x divide-theme-secondary-300 sm:h-[21px] dark:divide-theme-dark-700">
                    <x-tables.rows.mobile.skeletons.encapsulated.number />

                    <div class="flex flex-1 justify-between items-center pl-3 ml-3 min-w-0">
                        <x-tables.rows.mobile.skeletons.text class="pr-2" />

                        <div class="flex items-center sm:space-x-3 sm:divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                            <x-tables.rows.mobile.skeletons.text class="hidden sm:block" />

                            <x-tables.rows.mobile.skeletons.text class="sm:ml-3" />
                        </div>
                    </div>
                </div>
            </x-slot>

            <div class="hidden sm:flex sm:justify-between sm:items-center sm:w-full">
                <x-tables.rows.mobile.skeletons.encapsulated.badge />

                <x-tables.rows.mobile.skeletons.encapsulated.text />

                <x-tables.rows.mobile.skeletons.encapsulated.text />

                <x-tables.rows.mobile.skeletons.encapsulated.badge />
            </div>
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
