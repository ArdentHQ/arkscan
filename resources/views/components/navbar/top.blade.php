<div class="hidden sticky top-0 z-30 bg-white md:flex md:flex-col dark:bg-theme-secondary-900">
    <div class="flex justify-between items-center py-3 w-full content-container">
        <div class="flex items-center">
            <div class="flex font-semibold dark:text-white text-theme-secondary-900">
                <livewire:price-ticker />
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <x-ark-input-with-prefix
                icon="magnifying-glass"
                type="text"
                id="search"
                name="search"
                class="w-[340px] border border-transparent bg-theme-secondary-200 dark:bg-theme-secondary-900 hover:bg-white focus-within:bg-white hover:border-theme-primary-100 focus-within:border-theme-primary-100 group rounded-md transition-default hover:dark:border-theme-secondary-700 focus-within:dark:border-theme-secondary-700"
                :placeholder="trans('general.navbar.search_placeholder')"
                container-class="flex pl-1 border border-transparent hover:border-theme-primary-100 focus-within:border-theme-primary-100 dark:border-theme-secondary-800 group-hover:dark:border-theme-secondary-700 focus-within:dark:border-theme-secondary-700"
                wrapper-class-override="relative rounded"
                field-class-override="block w-full border-0 rounded outline-none appearance-none pl-2 pr-4 py-[7px] text-sm leading-4 placeholder:text-theme-secondary-700 text-theme-secondary-900 dark:text-theme-secondary-400 bg-transparent"
                hide-label
                disable-dirty-styling
                iconSize="sm"
            />

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
