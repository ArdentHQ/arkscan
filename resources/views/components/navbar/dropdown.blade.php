@props([
    'button',
    'withoutDropdownIcon' => false,
])

<x-general.dropdown.dropdown
    active-button-class="space-x-1.5"
    button-wrapper-class=""
    :button-class="$attributes->class(
        'justify-center p-2 space-x-1.5 h-8 text-sm font-semibold rounded md:px-3 md:bg-white md:border bg-theme-secondary-200 text-theme-secondary-700 md:hover:text-theme-secondary-700 md:border-theme-secondary-300 md:dark:border-theme-dark-700 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-200 md:hover:text-theme-secondary-900 dark:bg-theme-dark-800 dim:bg-theme-dark-700 dark:hover:bg-theme-dark-700 dark:text-theme-dark-50 hover:bg-theme-secondary-200 dim:bg-theme-dark-900 dim:hover:bg-theme-dark-700'
    )->get('class')"
>
    <x-slot name="button">
        {{ $button }}

        @unless ($withoutDropdownIcon)
            <span
                class="transition-default"
                :class="{ 'rotate-180': dropdownOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-2.5 h-2.5"
                />
            </span>
        @endunless
    </x-slot>

    {{ $slot }}
</x-general.dropdown>
