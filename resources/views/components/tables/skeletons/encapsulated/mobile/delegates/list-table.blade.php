<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile
            expand-class="space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700"
            expandable
            expand-disabled
        >
            <x-slot name="header">
                <div class="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                    <x-tables.rows.mobile.skeletons.encapsulated.number />

                    <div class="flex flex-1 justify-between items-center pl-3 min-w-0 ml-3">
                        <x-tables.rows.mobile.skeletons.text class="pr-2" />

                        <div class="flex items-center sm:space-x-3 sm:divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                            <x-tables.rows.mobile.skeletons.text class="hidden sm:block" />

                            <x-tables.rows.mobile.skeletons.text class="sm:pl-3" />
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
