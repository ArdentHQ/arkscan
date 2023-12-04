@props([
    'icon',
    'title',
])

<x-general.dropdown.dropdown
    class="flex-1 lg:flex-none group"
    dropdown-class="px-6 w-full md:px-10 md-lg:px-0 md-lg:w-50"
    dropdown-wrapper-class="w-full"
    activeButtonClass="hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-800"
>
    <x-slot name="button" class="w-full font-semibold rounded border border-theme-secondary-300 text-theme-secondary-900 md-lg:w-50 dark:border-theme-dark-700 dark:text-theme-dark-50">
        <div class="flex justify-between items-center py-3 px-4 w-full">
            <div class="flex items-center space-x-2">
                <x-ark-icon
                    :name="$icon"
                    class="text-theme-secondary-700 dark:text-theme-dark-300 group-hover:dark:text-theme-dark-200"
                />

                <span class="leading-5">{{ $title }}</span>
            </div>

            <span
                class="transition-default"
                :class="{ 'rotate-180': dropdownOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-3 h-3"
                    class="text-theme-secondary-700 dark:text-theme-dark-200"
                />
            </span>
        </div>
    </x-slot>

    {{ $slot }}
</x-general.dropdown.dropdown>
