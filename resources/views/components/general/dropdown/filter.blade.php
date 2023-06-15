<x-general.dropdown.dropdown
    placement="right-start"
    :placement-fallbacks="['bottom', 'bottom-end', 'left-start']"
    dropdown-class="w-full px-6 md:w-[284px] table-filter md:px-8"
    :close-on-click="false"
    class="flex-1"
    dropdown-wrapper-class="w-full"
    dropdown-background="bg-white dark:bg-theme-secondary-900 dark:border dark:border-theme-secondary-800"
    dropdown-padding="py-1"
>
    <x-slot name="button" class="w-full rounded">
        <div class="md:p-2 button-secondary flex items-center sm:py-1.5 sm:px-4 flex-1 sm:flex-none justify-center">
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="md:hidden ml-2">
                @lang('actions.filter')
            </div>
        </div>
    </x-slot>

    {{ $slot }}
</x-general.dropdown.dropdown>
