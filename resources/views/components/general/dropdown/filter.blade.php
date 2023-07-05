<x-general.dropdown.dropdown
    placement="right-start"
    :placement-fallbacks="['bottom', 'bottom-end', 'left-start']"
    dropdown-class="px-6 w-full md:px-8 table-filter md:w-[284px]"
    :close-on-click="false"
    class=""
    dropdown-wrapper-class="w-full"
    dropdown-background="bg-white dark:bg-theme-secondary-900 dark:border dark:border-theme-secondary-800"
    dropdown-padding="py-1"
    button-class="flex flex-1 justify-center items-center w-full rounded sm:flex-none sm:py-1.5 sm:px-4 md:p-2 button-secondary"
    active-button-class=""
    button-wrapper-class="w-full h-5 md:h-4"
>
    <x-slot name="button">
        <div class="inline-flex items-center whitespace-nowrap">
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="ml-2 md:hidden">
                @lang('actions.filter')
            </div>
        </div>
    </x-slot>

    {{ $slot }}
</x-general.dropdown.dropdown>
