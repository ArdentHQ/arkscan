@props([
    'rowCount' => 15,
])

<x-tables.mobile.includes.encapsulated>
    @for ($i = 0; $i < $rowCount; $i++)
        <x-tables.rows.mobile
            wire:key="skeleton-validator-monitor-{{ $i }}"
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
                                class="sm:hidden sm:ml-3"
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
    @endfor
</x-tables.mobile.includes.encapsulated>
