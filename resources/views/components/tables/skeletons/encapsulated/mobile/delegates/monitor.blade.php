<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile
            expandable
            expand-disabled
        >
            <x-slot name="header">
                <div class="flex flex-1 items-center min-w-0 divide-x divide-theme-secondary-300 h-[21px] dark:divide-theme-dark-700">
                    <x-tables.rows.mobile.skeletons.encapsulated.number />

                    <div class="flex flex-1 justify-between items-center pl-3 ml-3 min-w-0">
                        <x-tables.rows.mobile.skeletons.text class="pr-2" />

                        <div class="flex items-center">
                            <x-loading.text
                                width="w-[140px]"
                                height="h-[21px]"
                                class="hidden sm:block"
                            />

                            <x-loading.text
                                width="w-3"
                                height="h-3"
                                class="sm:ml-3 sm:hidden"
                            />
                        </div>
                    </div>
                </div>
            </x-slot>

            <div class="hidden sm:flex sm:justify-between sm:items-center sm:w-full">
                <x-tables.rows.mobile.skeletons.encapsulated.text />

                <x-tables.rows.mobile.skeletons.encapsulated.text />
            </div>
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
