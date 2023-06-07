<div class="hidden relative z-40 bg-white md:flex md:flex-col dark:bg-theme-secondary-900">
    <div class="flex justify-between items-center py-3 w-full content-container">
        <div class="flex items-center">
            <div class="flex font-semibold dark:text-white text-theme-secondary-900">
                <livewire:price-ticker />
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <livewire:navbar.search />

            <livewire:navbar.dark-mode-toggle
                active-icon="underline-moon"
                inactive-icon="underline-sun"
                setting="darkTheme"
            />

            <x-navbar.network-dropdown>
                <x-ark-icon
                    name="networks.ark"
                    size="sm"
                />
            </x-navbar.network-dropdown>
        </div>
    </div>

    <div class="absolute bottom-0 w-full border-b border-theme-secondary-300 dark:border-theme-secondary-800"></div>
</div>
