<x-navbar.dropdown>
    <x-slot name="button">
        <span x-show="theme === 'dark'">
            <x-ark-icon name="underline-moon" />
        </span>

        <span x-show="theme === 'light'">
            <x-ark-icon name="underline-sun" />
        </span>
    </x-slot>

    <x-general.dropdown.alpine-list-item
        id="light"
        variable-name="theme"
    >
        @lang('general.navbar.theme.light')
    </x-general.dropdown.alpine-list-item>

    <x-general.dropdown.alpine-list-item
        id="dark"
        variable-name="theme"
    >
        @lang('general.navbar.theme.dark')
    </x-general.dropdown.alpine-list-item>

    <x-general.dropdown.alpine-list-item
        id="dim"
        variable-name="theme"
    >
        @lang('general.navbar.theme.dim')
    </x-general.dropdown.alpine-list-item>
</x-navbar.dropdown>
