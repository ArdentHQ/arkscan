<x-navbar.dropdown
    class="w-8"
    without-dropdown-icon
>
    <x-slot name="button">
        <div class="dim:text-theme-dark-300">
            <span x-show="theme === 'dark'" x-cloak>
                <x-ark-icon
                    name="moon"
                    size="sm"
                />
            </span>

            <span x-show="theme === 'light'" x-cloak>
                <x-ark-icon
                    name="sun"
                    size="sm"
                />
            </span>

            <span x-show="theme === 'dim'" x-cloak>
                <x-ark-icon
                    name="moon-stars"
                    size="sm"
                />
            </span>
        </div>
    </x-slot>

    <x-general.dropdown.alpine-list-item
        id="light"
        variable-name="theme"
        active-class="border-theme-primary-600 dark:border-theme-dark-blue-500 bg-theme-primary-50 dark:bg-theme-dark-900 dim:bg-theme-dark-950 text-theme-secondary-900 dark:text-theme-dark-50 font-semibold"
        inactive-class="font-semibold border-transparent text-theme-secondary-700 dark:text-theme-dark-50 hover:text-theme-secondary-700 hover:bg-theme-secondary-100 hover:dark:bg-theme-dark-900"
    >
        @lang('general.navbar.theme.light')
    </x-general.dropdown.alpine-list-item>

    <x-general.dropdown.alpine-list-item
        id="dark"
        variable-name="theme"
        inactive-class="font-semibold border-transparent text-theme-secondary-700 dark:text-theme-dark-50 hover:text-theme-secondary-700 hover:bg-theme-secondary-100 hover:dark:bg-theme-dark-900"
    >
        @lang('general.navbar.theme.dark')
    </x-general.dropdown.alpine-list-item>

    <x-general.dropdown.alpine-list-item
        id="dim"
        variable-name="theme"
        inactive-class="font-semibold border-transparent text-theme-secondary-700 dark:text-theme-dark-50 hover:text-theme-secondary-700 hover:bg-theme-secondary-100 hover:dark:bg-theme-dark-900"
    >
        @lang('general.navbar.theme.dim')
    </x-general.dropdown.alpine-list-item>
</x-navbar.dropdown>
