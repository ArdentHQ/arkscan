@props([
    'icon',
    'title',
])

<x-general.dropdown.dropdown class="flex-1 lg:flex-none">
    <x-slot name="button" class="w-full md-lg:w-50 font-semibold rounded px-4 py-3 border border-theme-secondary-300 dark:border-theme-secondary-800 dark:text-theme-secondary-200">
        <button class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-2">
                <x-ark-icon :name="$icon" />

                <span>{{ $title }}</span>
            </div>

            <span
                class="transition-default"
                :class="{ 'rotate-180': isOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-2.5 h-2.5"
                />
            </span>
        </button>
    </x-slot>

    <x-slot name="content" class="w-full">
        {{ $slot }}
    </x-slot>
</x-general.dropdown.dropdown>
