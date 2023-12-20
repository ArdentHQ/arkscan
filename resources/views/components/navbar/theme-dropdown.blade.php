<x-navbar.dropdown
    class="w-8"
    without-dropdown-icon
>
    <x-slot name="button">
        <div>
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
        inactive-class="font-semibold"
    >
        @lang('general.navbar.theme.light')
    </x-general.dropdown.alpine-list-item>

    <x-general.dropdown.alpine-list-item
        id="dark"
        variable-name="theme"
        inactive-class="font-semibold"
    >
        @lang('general.navbar.theme.dark')
    </x-general.dropdown.alpine-list-item>

    <x-general.dropdown.alpine-list-item
        id="dim"
        variable-name="theme"
        inactive-class="font-semibold"
    >
        @lang('general.navbar.theme.dim')
    </x-general.dropdown.alpine-list-item>
</x-navbar.dropdown>
